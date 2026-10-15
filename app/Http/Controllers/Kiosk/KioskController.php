<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\LogPresensi;
use App\Models\Presensi;
use App\Models\SchoolSetting;
use App\Models\SesiPresensi;
use App\Models\User;
use App\Services\FaceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KioskController extends Controller
{
    /**
     * Tampilkan halaman antarmuka Tablet Kiosk Gerbang (Full-screen Face ID).
     */
    public function index()
    {
        $schoolSetting = SchoolSetting::getSettings();

        $totalSiswa = User::where('role', 'siswa')->count();
        $totalEnrolled = User::where('role', 'siswa')->whereNotNull('face_descriptor')->count();

        // Jumlah siswa hadir hari ini lewat sesi kelas
        $hadirHariIniCount = Presensi::whereHas('sesiPresensi', function ($q) {
            $q->whereDate('tanggal', today());
        })->where('status', 'hadir')->count();

        // 6 Presensi Terakhir Hari Ini untuk live feed
        $recentAttendances = Presensi::whereHas('sesiPresensi', function ($q) {
            $q->whereDate('tanggal', today());
        })
            ->where('status', 'hadir')
            ->with(['siswa.kelas', 'sesiPresensi'])
            ->latest('waktu_scan')
            ->take(6)
            ->get()
            ->map(function ($item) {
                return [
                    'id'         => $item->id,
                    'name'       => $item->siswa?->name ?? 'Siswa',
                    'nisn'       => $item->siswa?->nisn ?? '-',
                    'kelas'      => $item->siswa?->kelas?->nama_kelas ?? 'Umum',
                    'avatar_url' => $item->siswa?->avatar_url,
                    'jam'        => $item->waktu_scan ? $item->waktu_scan->format('H:i') : ($item->created_at ? $item->created_at->format('H:i') : '-'),
                ];
            });

        return view('kiosk.index', compact(
            'schoolSetting',
            'totalSiswa',
            'totalEnrolled',
            'hadirHariIniCount',
            'recentAttendances'
        ));
    }

    /**
     * Endpoint API Kiosk: Identifikasi Wajah (1:N) & Catat Presensi Seketika.
     */
    public function identifyAndAttend(Request $request)
    {
        $request->validate([
            'face_image' => 'required|string', // base64 JPEG/PNG
            'nisn'       => 'nullable|string', // Optional fallback NISN
        ]);

        $faceService = new FaceService();
        $user = null;
        $identifyResult = null;

        // MODE 1: Jika NISN diinput/discan via Barcode/QR
        if ($request->filled('nisn')) {
            $nisn = trim($request->nisn);
            $user = User::where('nisn', $nisn)->where('role', 'siswa')->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => "Siswa dengan NISN {$nisn} tidak ditemukan dalam sistem.",
                ], 404);
            }

            if (!$user->isFaceEnrolled()) {
                return response()->json([
                    'success' => false,
                    'message' => "Siswa {$user->name} belum mendaftarkan biometrik wajah. Harap lakukan pendaftaran terlebih dahulu.",
                ], 422);
            }

            // Bandingkan 1:1
            $compareRes = $faceService->compare($user->face_descriptor, $request->face_image);
            if (!$compareRes['success'] || !($compareRes['match'] ?? false)) {
                return response()->json([
                    'success'  => false,
                    'message'  => $compareRes['reason'] ?? 'Wajah tidak cocok dengan data pemilik NISN.',
                    'distance' => $compareRes['distance'] ?? null,
                ], 422);
            }

            $identifyResult = $compareRes;
        } else {
            // MODE 2: Auto Face ID (1:N Matching terhadap seluruh database siswa)
            $identifyResult = $faceService->identify($request->face_image, null, 0.42);

            if (!$identifyResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $identifyResult['error'] ?? 'Gagal memproses gambar pada server AI.',
                ], 500);
            }

            if (!($identifyResult['match'] ?? false) || empty($identifyResult['user_id'])) {
                return response()->json([
                    'success'  => false,
                    'message'  => $identifyResult['reason'] ?? 'Wajah tidak dikenali atau belum terdaftar.',
                    'distance' => $identifyResult['distance'] ?? null,
                ], 404);
            }

            $user = User::find($identifyResult['user_id']);
            if (!$user || $user->role !== 'siswa') {
                return response()->json([
                    'success' => false,
                    'message' => 'Data identifikasi tidak valid.',
                ], 422);
            }
        }

        // Pastikan siswa memiliki kelas
        if (!$user->kelas_id) {
            return response()->json([
                'success' => false,
                'message' => "Siswa {$user->name} belum terdaftar di kelas manapun.",
            ], 422);
        }

        // Dapatkan atau buat otomatis Sesi Presensi Pagi (tipe: kelas) untuk hari ini sesuai jadwal pelajaran
        $mapHari = [1 => 'senin', 2 => 'selasa', 3 => 'rabu', 4 => 'kamis', 5 => 'jumat'];
        $hariKey = $mapHari[now()->dayOfWeek] ?? 'senin';

        $jadwalPertama = \App\Models\JadwalPelajaran::where('kelas_id', $user->kelas_id)
            ->where('hari', $hariKey)
            ->where('kode_guru', '!=', 'SEKOLAH')
            ->orderBy('jam_ke')
            ->first();

        if (!$jadwalPertama) {
            $jadwalPertama = \App\Models\JadwalPelajaran::where('kelas_id', $user->kelas_id)
                ->where('hari', $hariKey)
                ->orderBy('jam_ke')
                ->first();
        }

        $defaultMapelId = $jadwalPertama?->mapel_id;
        $defaultMapelName = $jadwalPertama?->nama_mapel;
        $defaultGuruId = $jadwalPertama?->guru_id ?? User::where('role', 'guru')->first()?->id ?? 1;
        $defaultJamLabel = $jadwalPertama 
            ? "{$defaultMapelName} (Jam ke-{$jadwalPertama->jam_ke} • {$jadwalPertama->waktu})" 
            : 'Presensi Pagi (Gerbang)';

        $sesi = SesiPresensi::firstOrCreate(
            [
                'kelas_id' => $user->kelas_id,
                'tanggal'  => today(),
                'tipe'     => 'kelas',
            ],
            [
                'guru_id'       => $defaultGuruId,
                'mapel_id'      => $defaultMapelId,
                'jam_pelajaran' => $defaultJamLabel,
                'barcode_token' => Str::random(32),
                'is_active'     => true,
            ]
        );

        // Cari atau buat record presensi siswa di sesi ini
        $presensi = Presensi::firstOrCreate(
            [
                'sesi_presensi_id' => $sesi->id,
                'siswa_id'         => $user->id,
            ],
            [
                'status' => 'alpa',
            ]
        );

        // Jika siswa sudah tercatat HADIR hari ini
        if ($presensi->status === 'hadir') {
            $waktuFormatted = $presensi->waktu_scan ? $presensi->waktu_scan->format('H:i') : ($presensi->updated_at ? $presensi->updated_at->format('H:i') : '-');
            return response()->json([
                'success'          => true,
                'already_attended' => true,
                'student'          => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'nisn'       => $user->nisn ?? '-',
                    'kelas'      => $user->kelas?->nama_kelas ?? 'Reguler',
                    'avatar_url' => $user->avatar_url,
                ],
                'waktu_scan'       => $waktuFormatted . ' WIB',
                'message'          => "Halo {$user->name}, kamu sudah tercatat HADIR hari ini pada pukul {$waktuFormatted} WIB.",
                'confidence'       => $identifyResult['confidence'] ?? 'high',
                'distance'         => $identifyResult['distance'] ?? null,
            ]);
        }

        // Simpan foto snapshot presensi ke storage
        $fotoPath = null;
        try {
            $rawImg = $request->face_image;
            if (str_contains($rawImg, ',')) {
                $rawImg = explode(',', $rawImg, 2)[1];
            }
            $decoded = base64_decode($rawImg);
            if ($decoded) {
                $folder = 'face_scans/' . date('Y-m-d');
                $filename = $folder . '/kiosk_' . $user->id . '_' . time() . '.jpg';
                Storage::disk('public')->put($filename, $decoded);
                $fotoPath = $filename;
            }
        } catch (\Throwable $e) {
            Log::warning('[KioskController] Gagal menyimpan foto scan: ' . $e->getMessage());
        }

        $statusSebelumnya = $presensi->status;
        $now = now();

        $presensi->update([
            'status'     => 'hadir',
            'waktu_scan' => $now,
            'keterangan' => $fotoPath ? "Scan Face ID Kiosk Gerbang (Tersimpan: {$fotoPath})" : 'Scan Face ID Kiosk Gerbang',
        ]);

        // Catat ke LogPresensi
        LogPresensi::create([
            'presensi_id'       => $presensi->id,
            'guru_id'           => $sesi->guru_id,
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru'       => 'hadir',
            'keterangan'        => sprintf(
                'Presensi Face ID Kiosk Gerbang (confidence: %s, distance: %.4f)',
                $identifyResult['confidence'] ?? 'high',
                $identifyResult['distance'] ?? 0
            ),
        ]);

        return response()->json([
            'success'          => true,
            'already_attended' => false,
            'student'          => [
                'id'         => $user->id,
                'name'       => $user->name,
                'nisn'       => $user->nisn ?? '-',
                'kelas'      => $user->kelas?->nama_kelas ?? 'Reguler',
                'avatar_url' => $user->avatar_url,
                'scan_url'   => $fotoPath ? asset('storage/' . $fotoPath) : null,
            ],
            'waktu_scan'       => $now->format('H:i') . ' WIB',
            'message'          => "Selamat pagi, {$user->name}! Presensi berhasil dicatat.",
            'confidence'       => $identifyResult['confidence'] ?? 'high',
            'distance'         => $identifyResult['distance'] ?? null,
        ]);
    }

    /**
     * Sinkronkan database biometrik siswa ke RAM server secara manual / on-demand.
     */
    public function syncCandidates()
    {
        $faceService = new FaceService();
        $result = $faceService->syncAllEnrolledStudents();

        return response()->json($result);
    }

    /**
     * Endpoint Polling Statistik & Live Feed Kiosk
     */
    public function liveStats()
    {
        $hadirHariIniCount = Presensi::whereHas('sesiPresensi', function ($q) {
            $q->whereDate('tanggal', today());
        })->where('status', 'hadir')->count();

        $recent = Presensi::whereHas('sesiPresensi', function ($q) {
            $q->whereDate('tanggal', today());
        })
            ->where('status', 'hadir')
            ->with(['siswa.kelas'])
            ->latest('waktu_scan')
            ->take(6)
            ->get()
            ->map(function ($item) {
                return [
                    'id'         => $item->id,
                    'name'       => $item->siswa?->name ?? 'Siswa',
                    'nisn'       => $item->siswa?->nisn ?? '-',
                    'kelas'      => $item->siswa?->kelas?->nama_kelas ?? 'Reguler',
                    'avatar_url' => $item->siswa?->avatar_url,
                    'jam'        => $item->waktu_scan ? $item->waktu_scan->format('H:i') : ($item->created_at ? $item->created_at->format('H:i') : '-'),
                ];
            });

        return response()->json([
            'success' => true,
            'hadir_count' => $hadirHariIniCount,
            'recent' => $recent,
        ]);
    }
}
