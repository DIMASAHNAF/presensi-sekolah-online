<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\LogPresensi;
use App\Models\Presensi;
use App\Models\SchoolSetting;
use App\Models\SesiPresensi;
use App\Models\User;
use App\Services\FaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user()->load('kelas');

        // 10 Riwayat Terakhir untuk widget
        $riwayat = Presensi::where('siswa_id', $user->id)
            ->with(['sesiPresensi.kelas', 'sesiPresensi.mataPelajaran'])
            ->latest()
            ->take(10)
            ->get();

        // Seluruh riwayat presensi untuk tabel rekap lengkap siswa
        $riwayatSemua = Presensi::where('siswa_id', $user->id)
            ->with(['sesiPresensi.kelas', 'sesiPresensi.mataPelajaran', 'sesiPresensi.guru'])
            ->latest()
            ->get();

        $riwayatSemuaFormatted = $riwayatSemua->map(function ($item) {
            $sesi = $item->sesiPresensi;
            $tanggalObj = $sesi?->tanggal ?? $item->created_at;
            return [
                'id'          => $item->id,
                'status'      => $item->status,
                'tanggal'     => $tanggalObj ? $tanggalObj->format('d M Y') : '-',
                'tanggal_raw' => $tanggalObj ? $tanggalObj->format('Y-m-d') : '',
                'bulan'       => $tanggalObj ? $tanggalObj->format('Y-m') : '',
                'jam'         => $item->created_at ? $item->created_at->format('H:i') . ' WIB' : '-',
                'kelas'       => $sesi?->kelas?->nama_kelas ?? 'Reguler',
                'mapel'       => $sesi?->mataPelajaran?->nama_mapel ?? 'Presensi Umum',
                'guru'        => $sesi?->guru?->name ?? 'Wali Kelas',
                'metode'      => $item->face_matched ? 'Wajah AI' : 'Sistem / Guru',
                'keterangan'  => $item->keterangan ?? '-',
            ];
        });

        $stats = [
            'hadir' => Presensi::where('siswa_id', $user->id)->where('status', 'hadir')->count(),
            'izin'  => Presensi::where('siswa_id', $user->id)->where('status', 'izin')->count(),
            'sakit' => Presensi::where('siswa_id', $user->id)->where('status', 'sakit')->count(),
            'alpa'  => Presensi::where('siswa_id', $user->id)->where('status', 'alpa')->count(),
        ];

        $totalPresensi = $riwayatSemua->count();
        $persentaseKehadiran = $totalPresensi > 0 ? round(($stats['hadir'] / $totalPresensi) * 100) : 100;

        // Teman Sekelas (Social Classroom row)
        $temanSekelas = collect();
        if ($user->kelas_id) {
            $temanSekelas = User::where('kelas_id', $user->kelas_id)
                ->where('role', 'siswa')
                ->where('id', '!=', $user->id)
                ->orderBy('name')
                ->get()
                ->map(function ($teman) {
                    $absenHariIni = Presensi::where('siswa_id', $teman->id)
                        ->whereHas('sesiPresensi', function ($q) {
                            $q->where('tanggal', today());
                        })
                        ->latest()
                        ->first();

                    $teman->status_hari_ini = $absenHariIni ? $absenHariIni->status : 'belum';
                    $teman->jam_absen = ($absenHariIni && $absenHariIni->created_at) ? $absenHariIni->created_at->format('H:i') : null;
                    return $teman;
                });
        }

        $schoolSetting = SchoolSetting::getSettings();

        return view('siswa.dashboard', compact(
            'user',
            'riwayat',
            'riwayatSemua',
            'riwayatSemuaFormatted',
            'stats',
            'persentaseKehadiran',
            'temanSekelas',
            'schoolSetting'
        ));
    }

    /**
     * Update Profile Siswa (Avatar, Banner, Bio, Website/Instagram, Username)
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'username'      => 'required|string|max:50|unique:users,username,' . $user->id,
            'bio'           => 'nullable|string|max:180',
            'website'       => 'nullable|string|max:255',
            'avatar'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'remove_banner' => 'nullable',
            'remove_avatar' => 'nullable',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username ini sudah dipakai oleh akun lain.',
            'avatar.image'      => 'File foto profil harus berupa gambar.',
            'avatar.max'        => 'Ukuran foto profil maksimal 2MB.',
            'banner.image'      => 'File banner cover harus berupa gambar.',
            'banner.max'        => 'Ukuran banner cover maksimal 4MB.',
            'bio.max'           => 'Bio maksimal 180 karakter.',
        ]);

        $user->username = trim($request->username);
        $user->bio      = $request->bio ? trim($request->bio) : null;
        $user->website  = $request->website ? trim($request->website) : null;

        // Avatar Upload
        if ($request->hasFile('avatar')) {
            try {
                if ($user->avatar && $user->avatar !== '0' && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $avatarPath = $request->file('avatar')->store($user->getStorageFolder() . '/avatar', 'public');
                if (!$avatarPath) {
                    throw new \Exception('Sistem gagal menyimpan file gambar avatar.');
                }
                $user->avatar = $avatarPath;
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan foto profil ke storage 100GB (/mnt/data-presensi-smk). Error: ' . $e->getMessage(),
                ], 422);
            }
        } elseif ($request->boolean('remove_avatar') || $request->input('remove_avatar') === '1') {
            if ($user->avatar && $user->avatar !== '0' && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = null;
        }

        // Banner Upload
        if ($request->hasFile('banner')) {
            try {
                if ($user->banner && $user->banner !== '0' && Storage::disk('public')->exists($user->banner)) {
                    Storage::disk('public')->delete($user->banner);
                }
                $bannerPath = $request->file('banner')->store($user->getStorageFolder() . '/banner', 'public');
                if (!$bannerPath) {
                    throw new \Exception('Sistem gagal menyimpan file cover banner.');
                }
                $user->banner = $bannerPath;
            } catch (\Throwable $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan banner cover ke storage 100GB (/mnt/data-presensi-smk). Error: ' . $e->getMessage(),
                ], 422);
            }
        } elseif ($request->boolean('remove_banner') || $request->input('remove_banner') === '1') {
            if ($user->banner && $user->banner !== '0' && Storage::disk('public')->exists($user->banner)) {
                Storage::disk('public')->delete($user->banner);
            }
            $user->banner = null;
        }

        $user->save();
        $user->refresh();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui!',
                'user'    => [
                    'name'       => $user->name,
                    'username'   => $user->username,
                    'bio'        => $user->bio,
                    'website'    => $user->website,
                    'avatar_url' => $user->avatar_url,
                    'banner_url' => $user->banner_url,
                ],
            ]);
        }

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
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

        $schoolSetting = SchoolSetting::getSettings();
        $clientIp = SchoolSetting::getClientIp(request());
        $isIpAllowed = $schoolSetting->isIpAllowed($clientIp);

        return response()->json([
            'success' => true,
            'sesi'    => [
                'id'         => $sesi->id,
                'kelas'      => $sesi->kelas->nama_kelas,
                'guru'       => $sesi->guru->name,
                'tanggal'    => \Carbon\Carbon::parse($sesi->tanggal)->isoFormat('dddd, D MMMM Y'),
                'jam'        => $sesi->jam_pelajaran ?? '-',
                'mapel'      => $sesi->mataPelajaran?->nama_mapel,
                'created_at' => $sesi->created_at->toISOString(),
            ],
            'sudah_hadir'   => $sudahHadir,
            'face_enrolled' => $user->isFaceEnrolled(),
            'geofencing'    => [
                'active'        => $schoolSetting->is_geofencing_active,
                'school_name'   => $schoolSetting->school_name,
                'latitude'      => $schoolSetting->latitude,
                'longitude'     => $schoolSetting->longitude,
                'radius_meters' => $schoolSetting->radius_meters,
            ],
            'network'       => [
                'active'     => $schoolSetting->is_ip_whitelist_active,
                'client_ip'  => $clientIp,
                'is_allowed' => $isIpAllowed,
            ],
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
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
        ]);

        $schoolSetting = SchoolSetting::getSettings();

        // 1. Cek Validasi IP WiFi Sekolah jika aktif
        if ($schoolSetting->is_ip_whitelist_active) {
            $clientIp = SchoolSetting::getClientIp($request);
            if (!$schoolSetting->isIpAllowed($clientIp)) {
                return response()->json([
                    'success' => false,
                    'message' => "Presensi ditolak: Anda harus terhubung ke jaringan WiFi resmi SMKN 1 Beringin. IP Anda ({$clientIp}) tidak terdaftar dalam jaringan sekolah.",
                ]);
            }
        }

        // 2. Cek Geofencing jika aktif
        if ($schoolSetting->is_geofencing_active) {
            if (!$request->filled('latitude') || !$request->filled('longitude')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Koordinat GPS perangkat Anda diperlukan untuk memverifikasi lokasi presensi.',
                ]);
            }

            $distance = $this->calculateDistance(
                (float) $request->latitude,
                (float) $request->longitude,
                (float) $schoolSetting->latitude,
                (float) $schoolSetting->longitude
            );

            if ($distance > $schoolSetting->radius_meters) {
                return response()->json([
                    'success'  => false,
                    'message'  => sprintf(
                        'Presensi ditolak: Anda berada di luar radius sekolah. Jarak: %d meter (Batas: %d meter).',
                        round($distance),
                        $schoolSetting->radius_meters
                    ),
                    'distance' => round($distance),
                ]);
            }
        }

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

        // 6. Update jadi HADIR & Arsipkan foto scan ke storage 100GB (/mnt/data-presensi-smk/storage_public)
        $statusSebelumnya = $presensi->status;
        $fotoPath = null;

        if ($request->filled('face_image')) {
            try {
                $rawImg = $request->face_image;
                if (str_contains($rawImg, ',')) {
                    $rawImg = explode(',', $rawImg, 2)[1];
                }
                $decoded = base64_decode($rawImg);
                if ($decoded) {
                    $folder = 'face_scans/' . date('Y-m-d');
                    $filename = $folder . '/' . $user->id . '_' . $sesi->id . '_' . time() . '.jpg';
                    Storage::disk('public')->put($filename, $decoded);
                    $fotoPath = $filename;
                }
            } catch (\Throwable $e) {
                \Log::warning('Gagal menyimpan foto scan wajah ke mount /mnt: ' . $e->getMessage());
            }
        }

        $presensi->update([
            'status'     => 'hadir',
            'waktu_scan' => now(),
            'keterangan' => $fotoPath ? 'Scan Wajah (Tersimpan: ' . $fotoPath . ')' : 'Scan Wajah (Sistem)',
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

        // Arsipkan dataset foto pendaftaran wajah siswa ke storage 100GB
        try {
            foreach ($imagesArray as $idx => $imgB64) {
                if (str_contains($imgB64, ',')) {
                    $imgB64 = explode(',', $imgB64, 2)[1];
                }
                $decoded = base64_decode($imgB64);
                if ($decoded) {
                    $folder = $user->getStorageFolder() . '/face_enrollments';
                    $filename = $folder . '/photo_' . ($idx + 1) . '_' . time() . '.jpg';
                    Storage::disk('public')->put($filename, $decoded);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Gagal mengarsipkan foto enroll wajah ke mount /mnt: ' . $e->getMessage());
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

    /**
     * Hitung jarak dua titik koordinat bumi (Haversine Formula) dalam meter.
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meter
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
