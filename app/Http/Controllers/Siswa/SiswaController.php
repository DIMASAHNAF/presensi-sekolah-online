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
use Illuminate\Support\Facades\Hash;
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

        $presensiStats = Presensi::where('siswa_id', $user->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = [
            'hadir' => $presensiStats->get('hadir', 0),
            'izin'  => $presensiStats->get('izin', 0),
            'sakit' => $presensiStats->get('sakit', 0),
            'alpa'  => $presensiStats->get('alpa', 0),
        ];

        $totalPresensi = $riwayatSemua->count();
        $persentaseKehadiran = $totalPresensi > 0 ? round(($stats['hadir'] / $totalPresensi) * 100) : 100;

        $temanSekelas = collect();
        if ($user->kelas_id) {
            $temanSekelas = User::where('kelas_id', $user->kelas_id)
                ->where('role', 'siswa')
                ->where('id', '!=', $user->id)
                ->select(['id', 'name', 'username', 'avatar', 'banner', 'bio', 'website', 'kelas_id'])
                ->orderBy('name')
                ->get();

            // Resolve N+1: Get all presensi for these friends today in a single query
            $absenHariIniList = Presensi::whereIn('siswa_id', $temanSekelas->pluck('id'))
                ->whereHas('sesiPresensi', function ($q) {
                    $q->where('tanggal', today());
                })
                ->orderBy('created_at', 'asc') // So keyBy keeps the latest one
                ->get()
                ->keyBy('siswa_id');

            $temanSekelas->transform(function ($teman) use ($absenHariIniList) {
                $absenHariIni = $absenHariIniList->get($teman->id);
                $teman->status_hari_ini = $absenHariIni ? $absenHariIni->status : 'belum';
                $teman->jam_absen = ($absenHariIni && $absenHariIni->created_at) ? $absenHariIni->created_at->format('H:i') : null;
                $teman->makeHidden(['password', 'remember_token', 'face_descriptor', 'nisn', 'nik', 'email']);
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
                    'message' => 'Gagal menyimpan foto profil ke storage NFS (/mnt/nfs/data-presensi). Error: ' . $e->getMessage(),
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
                    'message' => 'Gagal menyimpan banner cover ke storage NFS (/mnt/nfs/data-presensi). Error: ' . $e->getMessage(),
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

    /**
     * Update Password Siswa (Onboarding pertama kali / perubahan mandiri)
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->password_changed_at = now();
        $user->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Password berhasil disimpan! Akun Anda kini terlindungi.',
            ]);
        }

        return back()->with('success', 'Password berhasil disimpan! Akun Anda kini terlindungi.');
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

        $hasSetPassword = $user->hasChangedPassword();
        $isFaceEnrolled = $user->isFaceEnrolled();
        $canAttend = $hasSetPassword && $isFaceEnrolled;
        $lockReason = null;
        if (!$hasSetPassword) {
            $lockReason = 'Wajib membuat password baru terlebih dahulu.';
        } elseif (!$isFaceEnrolled) {
            $lockReason = 'Wajib merekam biometrik wajah terlebih dahulu.';
        }

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
            'sudah_hadir'      => $sudahHadir,
            'face_enrolled'    => $isFaceEnrolled,
            'has_set_password' => $hasSetPassword,
            'can_attend'       => $canAttend,
            'lock_reason'      => $lockReason,
            'geofencing'    => [
                'active'        => $schoolSetting->is_geofencing_active,
                'mode'          => $schoolSetting->geofence_mode ?? 'radius',
                'school_name'   => $schoolSetting->school_name,
                'latitude'      => $schoolSetting->latitude,
                'longitude'     => $schoolSetting->longitude,
                'radius_meters' => $schoolSetting->radius_meters,
                'polygon'       => $schoolSetting->polygon_coordinates,
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
            'sesi_id'       => 'required|integer',
            'face_image'    => 'required|string', // base64 JPEG/PNG
            'latitude'      => 'nullable|numeric',
            'longitude'     => 'nullable|numeric',
            'accuracy'      => 'nullable|numeric',
            'is_mock'       => 'nullable|boolean',
            'gps_timestamp' => 'nullable|numeric',
        ]);

        $schoolSetting = SchoolSetting::getSettings();
        // IDOR Protection: Identitas siswa diambil murni dari sesi/token auth yang sedang login
        $user = auth()->user();
        $clientIp = SchoolSetting::getClientIp($request);

        // 1. Cek Validasi IP WiFi Sekolah jika aktif (Dual-Factor Network Verification)
        if ($schoolSetting->is_ip_whitelist_active) {
            if (!$schoolSetting->isIpAllowed($clientIp)) {
                return response()->json([
                    'success' => false,
                    'message' => "Presensi ditolak: Anda wajib terhubung ke jaringan WiFi resmi SMKN 1 Beringin. IP jaringan Anda ({$clientIp}) tidak terdaftar.",
                ], 403);
            }
        }

        // 2. Cek Anti-Fake GPS & Geofencing jika aktif
        $distance = null;
        $accuracy = null;
        if ($schoolSetting->is_geofencing_active) {
            // Anti-Mock: Tolak jika browser / sistem mendeteksi aplikasi Fake GPS aktif
            if ($request->boolean('is_mock')) {
                \Log::warning("Percobaan Fake GPS oleh siswa ID {$user->id} ({$user->name}) NISN: {$user->nisn}");
                return response()->json([
                    'success' => false,
                    'message' => 'Presensi DITOLAK: Terdeteksi aplikasi Fake GPS / Mock Location aktif pada perangkat Anda. Matikan aplikasi pemalsu lokasi untuk melanjutkan.',
                ], 403);
            }

            if (!$request->filled('latitude') || !$request->filled('longitude')) {
                // Jika membuka dari Laptop atau ruang kelas kedap sinyal tetapi terhubung ke WiFi / Jaringan Resmi SMKN 1 Beringin:
                if ($schoolSetting->is_ip_whitelist_active && $schoolSetting->isIpAllowed($clientIp)) {
                    $userLat = (float) $schoolSetting->latitude;
                    $userLng = (float) $schoolSetting->longitude;
                    $distance = 0;
                    $accuracy = 10;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Koordinat GPS satelit perangkat Anda diperlukan untuk memverifikasi kehadiran di SMKN 1 Beringin (atau sambungkan perangkat ke WiFi resmi sekolah).',
                    ], 422);
                }
            } else {
                $userLat = (float) $request->latitude;
                $userLng = (float) $request->longitude;
            }

            // Validasi akurasi sinyal satelit / WiFi GPS (toleransi diperketat menjadi 80m untuk GPS satelit)
            if ($request->filled('accuracy')) {
                $accuracy = (float) $request->accuracy;
                $maxTolerance = ($schoolSetting->is_ip_whitelist_active && $schoolSetting->isIpAllowed($clientIp)) ? 150 : 80;
                if ($accuracy <= 0 || $accuracy > $maxTolerance) {
                    return response()->json([
                        'success' => false,
                        'message' => sprintf(
                            'Presensi ditolak: Akurasi sinyal lokasi GPS Anda (±%d meter) belum stabil (batas toleransi satelit %dm). Silakan bergeser ke area terbuka atau gunakan WiFi sekolah.',
                            round($accuracy),
                            $maxTolerance
                        ),
                    ], 422);
                }
            }

            // Validasi kesegaran timestamp GPS (anti-replay coordinates)
            if ($request->filled('gps_timestamp')) {
                $gpsTime = (float) $request->gps_timestamp;
                $nowMs = round(microtime(true) * 1000);
                $diffSec = abs($nowMs - $gpsTime) / 1000;
                if ($diffSec > 300) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Presensi ditolak: Data koordinat GPS Anda kedaluwarsa. Silakan refresh lokasi pada aplikasi.',
                    ], 422);
                }
            }

            if (!isset($userLat)) {
                $userLat = (float) $request->latitude;
                $userLng = (float) $request->longitude;
            }

            // Koordinat batas kelayakan Indonesia
            if ($userLat < -11.5 || $userLat > 6.5 || $userLng < 94.5 || $userLng > 141.5) {
                return response()->json([
                    'success' => false,
                    'message' => 'Presensi ditolak: Titik koordinat GPS tidak valid.',
                ], 422);
            }

            // Validasi Geofence (Polygon Ray-Casting atau Radius Lingkaran)
            $geoCheck = $schoolSetting->validateGeofence($userLat, $userLng);
            $distance = $geoCheck['distance'] ?? null;

            if (!$geoCheck['allowed']) {
                return response()->json([
                    'success'  => false,
                    'message'  => $geoCheck['message'],
                    'distance' => $distance,
                    'mode'     => $geoCheck['mode'],
                ], 422);
            }
        }

        $user = auth()->user();

        // 0. Cek apakah siswa sudah mengamankan akun dengan membuat password baru
        if (!$user->hasChangedPassword()) {
            return response()->json([
                'success' => false,
                'needs_password' => true,
                'message' => 'Presensi ditolak: Anda wajib mengamankan akun dengan membuat password baru terlebih dahulu.',
            ], 403);
        }

        // 1. Cek siswa sudah enroll wajah
        if (!$user->isFaceEnrolled()) {
            return response()->json([
                'success' => false,
                'needs_face' => true,
                'message' => 'Anda belum mendaftarkan wajah. Silakan rekam foto biometrik wajah terlebih dahulu.',
            ], 422);
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

        // 4. Cek/buat presensi record via firstOrCreate (Anti Race-Condition)
        $presensi = Presensi::firstOrCreate(
            [
                'sesi_presensi_id' => $sesi->id,
                'siswa_id'         => $user->id,
            ],
            [
                'status' => 'alpa',
            ]
        );

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
                'message'    => $result['reason'] ?? 'Wajah tidak dikenali. Pastikan wajah terlihat jelas dan cahaya cukup.',
                'distance'   => $result['distance'] ?? null,
                'confidence' => $result['confidence'] ?? null,
            ]);
        }

        // 6. Update jadi HADIR & Arsipkan foto scan ke storage NFS (/mnt/nfs/data-presensi)
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

        $gpsAudit = '';
        if ($distance !== null) {
            $gpsAudit = sprintf(' • Jarak: %dm (±%dm)', round($distance), round($accuracy ?? 0));
        }

        $presensi->update([
            'status'     => 'hadir',
            'waktu_scan' => now(),
            'keterangan' => ($fotoPath ? 'Scan Wajah (Tersimpan: ' . $fotoPath . ')' : 'Scan Wajah (Sistem)') . $gpsAudit,
        ]);

        // 7. Catat log
        LogPresensi::create([
            'presensi_id'       => $presensi->id,
            'guru_id'           => $sesi->guru_id,
            'status_sebelumnya' => $statusSebelumnya,
            'status_baru'       => 'hadir',
            'keterangan'        => sprintf(
                'Siswa melakukan scan wajah mandiri (confidence: %s, distance: %.4f)%s [IP: %s]',
                $result['confidence'] ?? 'N/A',
                $result['distance'] ?? 0,
                $gpsAudit,
                SchoolSetting::getClientIp($request)
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

        // Wajib amankan akun dengan password baru terlebih dahulu
        if (!$user->hasChangedPassword()) {
            return redirect()->route('siswa.dashboard')->with('warning', 'Harap buat password akun Anda terlebih dahulu sebelum merekam wajah.');
        }

        // Keamanan Anti-Joki: Jika siswa sudah merekam wajah, kunci akses
        if ($user->isFaceEnrolled()) {
            return redirect()->route('siswa.dashboard')->with('error', 'Biometrik wajah Anda sudah terdaftar dan dikunci permanen demi keamanan. Hubungi Admin atau Guru Piket jika memerlukan reset wajah.');
        }

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

        // Wajib amankan akun dengan password baru terlebih dahulu
        if (!$user->hasChangedPassword()) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran wajah ditolak: Anda wajib membuat password akun baru terlebih dahulu.',
            ], 403);
        }

        // Keamanan Anti-Joki: Tolak jika siswa sudah pernah enroll biometrik
        if ($user->isFaceEnrolled()) {
            return response()->json([
                'success' => false,
                'message' => 'Biometrik wajah Anda sudah terdaftar dan terkunci. Perekaman ulang mandiri dinonaktifkan demi mencegah joki absensi.',
            ], 403);
        }

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

        // ── ANTI-JOKI / CEK DUPLIKASI BIOMETRIK ──
        // Mencegah siswa mendaftarkan wajah yang sama ke 2 akun berbeda (misal joki teman)
        $newDescriptor = $result['descriptor'];
        $existingStudents = User::whereNotNull('face_descriptor')
            ->where('id', '!=', $user->id)
            ->with('kelas')
            ->get(['id', 'name', 'kelas_id', 'face_descriptor']);

        foreach ($existingStudents as $existing) {
            $existingDesc = is_array($existing->face_descriptor)
                ? $existing->face_descriptor
                : json_decode($existing->face_descriptor, true);

            if (is_array($existingDesc) && in_array(count($existingDesc), [128, 512])) {
                $dupDist = $faceService->calculateEuclideanDistance($newDescriptor, $existingDesc);
                // Toleransi deteksi duplikasi: 0.48 untuk 128-D (menangkap wajah dengan kacamata/sudut miring) & 0.60 untuk 512-D
                $dupThreshold = count($existingDesc) === 512 ? 0.60 : 0.48;
                if ($dupDist !== null && $dupDist < $dupThreshold) {
                    $otherKelas = $existing->kelas?->nama_kelas ?? 'Siswa';
                    \Log::warning("Percobaan duplikasi wajah: Siswa {$user->name} (ID: {$user->id}) mencoba mendaftarkan wajah yang identik dengan {$existing->name} (ID: {$existing->id}) jarak: {$dupDist}");
                    return response()->json([
                        'success' => false,
                        'message' => "Pendaftaran Ditolak: Wajah ini sudah terdaftar dalam sistem atas nama siswa lain ({$existing->name} - {$otherKelas}). Satu biometrik wajah hanya boleh digunakan untuk satu akun!",
                    ], 422);
                }
            }
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
            'face_descriptor'  => $newDescriptor,
            'face_enrolled_at' => now(),
        ]);

        // Auto-sync wajah baru ini ke RAM server AI agar langsung dikenali Kiosk Gerbang
        try {
            $faceService->syncAllEnrolledStudents();
        } catch (\Throwable $e) {
            \Log::warning('Auto-sync biometrik setelah enroll: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Wajah berhasil didaftarkan dengan model AI resolusi tinggi! Anda sekarang bisa absen di Kiosk Gerbang.',
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
