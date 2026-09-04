<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\LogPresensi;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Services\FaceService;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user()->load('kelas');

        $riwayat = Presensi::where('siswa_id', $user->id)
            ->with(['sesiPresensi.kelas', 'sesiPresensi.mataPelajaran'])
            ->latest()
            ->take(10)
            ->get();

        $stats = [
            'hadir' => Presensi::where('siswa_id', $user->id)->where('status', 'hadir')->count(),
            'izin'  => Presensi::where('siswa_id', $user->id)->where('status', 'izin')->count(),
            'sakit' => Presensi::where('siswa_id', $user->id)->where('status', 'sakit')->count(),
            'alpa'  => Presensi::where('siswa_id', $user->id)->where('status', 'alpa')->count(),
        ];

        return view('siswa.dashboard', compact('user', 'riwayat', 'stats'));
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /siswa/sesi-aktif
    //  Polling endpoint: return sesi aktif untuk kelas siswa ini
    // ──────────────────────────────────────────────────────────────
    public function getSesiAktif()
    {
        $user = auth()->user();

        if (!$user->kelas_id) {
            return response()->json([
                'success' => true,
                'sesi'    => null,
                'message' => 'Anda belum terdaftar di kelas manapun.',
            ]);
        }

        // Hanya sesi kelas (pagi) hari ini yang aktif milik kelas siswa
        $sesi = SesiPresensi::where('kelas_id', $user->kelas_id)
            ->where('tipe', 'kelas')
            ->where('is_active', true)
            ->where('tanggal', today())
            ->with(['guru', 'kelas', 'mataPelajaran'])
            ->first();

        if (!$sesi) {
            return response()->json(['success' => true, 'sesi' => null]);
        }

        // Cek apakah siswa ini sudah absen di sesi ini
        $presensi   = Presensi::where('sesi_presensi_id', $sesi->id)
            ->where('siswa_id', $user->id)
            ->first();
        $sudahHadir = $presensi && $presensi->status === 'hadir';

        return response()->json([
            'success' => true,
            'sesi'    => [
                'id'         => $sesi->id,
                'kelas'      => $sesi->kelas->nama_kelas,
                'guru'       => $sesi->guru->name,
                'tanggal'    => $sesi->tanggal->isoFormat('dddd, D MMMM Y'),
                'jam'        => $sesi->jam_pelajaran ?? '-',
                'mapel'      => $sesi->mataPelajaran?->nama_mapel,
                'created_at' => $sesi->created_at->toISOString(),
            ],
            'sudah_hadir'   => $sudahHadir,
            'face_enrolled' => $user->isFaceEnrolled(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  POST /siswa/scan-wajah
    //  Terima base64 foto + sesi_id → verify via Python → catat hadir
    // ──────────────────────────────────────────────────────────────
    public function scanWajah(Request $request)
    {
        $request->validate([
            'sesi_id'    => 'required|integer',
            'face_image' => 'required|string', // base64 JPEG/PNG
        ]);

        $user = auth()->user();

        // 1. Cek siswa sudah enroll wajah
        if (!$user->isFaceEnrolled()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum mendaftarkan wajah. Silakan hubungi admin atau daftar ulang.',
            ]);
        }

        // 2. Cari sesi
        $sesi = SesiPresensi::where('id', $request->sesi_id)
            ->where('is_active', true)
            ->first();

        if (!$sesi) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi presensi tidak ditemukan atau sudah ditutup.',
            ]);
        }

        // 3. Cek kelas siswa cocok dengan kelas sesi
        if ($sesi->kelas_id !== $user->kelas_id) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi ini bukan untuk kelas Anda.',
            ]);
        }

        // 4. Cek/buat presensi record
        $presensi = Presensi::where('sesi_presensi_id', $sesi->id)
            ->where('siswa_id', $user->id)
            ->first();

        if (!$presensi) {
            $presensi = Presensi::create([
                'sesi_presensi_id' => $sesi->id,
                'siswa_id'         => $user->id,
                'status'           => 'alpa',
            ]);
        }

        if ($presensi->status === 'hadir') {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah tercatat HADIR di sesi ini.',
            ]);
        }

        // 5. Panggil Python FaceService untuk compare
        $faceService = new FaceService();
        $result      = $faceService->compare($user->face_descriptor, $request->face_image);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses gambar: ' . ($result['error'] ?? 'Error tidak diketahui.'),
            ]);
        }

        if (!$result['match']) {
            return response()->json([
                'success'    => false,
                'message'    => 'Wajah tidak dikenali. Pastikan wajah terlihat jelas dan cahaya cukup.',
                'distance'   => $result['distance'] ?? null,
                'confidence' => $result['confidence'] ?? null,
            ]);
        }

        // 6. Update jadi HADIR
        $statusSebelumnya = $presensi->status;

        $presensi->update([
            'status'     => 'hadir',
            'waktu_scan' => now(),
            'keterangan' => 'Scan Wajah (Sistem)',
        ]);

        // 7. Catat log
        LogPresensi::create([
            'presensi_id'       => $presensi->id,
            'guru_id'           => $sesi->guru_id,
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru'       => 'hadir',
            'keterangan'        => sprintf(
                'Siswa melakukan scan wajah mandiri (confidence: %s, distance: %.4f)',
                $result['confidence'] ?? 'N/A',
                $result['distance'] ?? 0
            ),
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Berhasil! Anda tercatat HADIR hari ini.',
            'confidence' => $result['confidence'] ?? null,
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    //  GET /siswa/enroll-wajah
    //  Halaman pendaftaran wajah untuk siswa existing (belum enroll)
    // ──────────────────────────────────────────────────────────────
    public function showEnrollWajah()
    {
        $user = auth()->user();
        return view('siswa.enroll-wajah', compact('user'));
    }

    // ──────────────────────────────────────────────────────────────
    //  POST /siswa/enroll-wajah
    //  Proses foto wajah + simpan descriptor untuk siswa existing
    // ──────────────────────────────────────────────────────────────
    public function enrollWajah(Request $request)
    {
        $request->validate([
            'face_images' => 'required|string',
        ]);

        $user = auth()->user();

        try {
            $imagesArray = json_decode($request->face_images, true);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Data gambar tidak valid.']);
        }

        if (!is_array($imagesArray) || count($imagesArray) < 3) {
            return response()->json(['success' => false, 'message' => 'Minimal 3 foto diperlukan.']);
        }

        $faceService = new FaceService();
        $result      = $faceService->enroll($imagesArray);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses wajah: ' . ($result['error'] ?? 'Coba lagi dengan pencahayaan lebih baik.'),
            ]);
        }

        $user->update([
            'face_descriptor'  => $result['descriptor'],
            'face_enrolled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Wajah berhasil didaftarkan! Anda sekarang bisa absen dengan scan wajah.',
        ]);
    }
}
