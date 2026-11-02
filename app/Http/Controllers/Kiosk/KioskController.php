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
use Illuminate\Support\Facades\Cache;
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

        // Jumlah siswa hadir hari ini — di-cache 30 detik di Redis agar tidak query DB tiap request
        $hadirHariIniCount = Cache::remember('hadir_count_' . today()->toDateString(), 30, function () {
            return Presensi::whereHas('sesiPresensi', function ($q) {
                $q->whereDate('tanggal', today());
            })->where('status', 'hadir')->count();
        });

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

        $schoolSetting = SchoolSetting::getSettings();
        $clientIp = SchoolSetting::getClientIp($request);

        // Validasi Keamanan: Jika IP Whitelist aktif, pastikan perangkat kiosk berada dalam jaringan resmi sekolah
        if ($schoolSetting->is_ip_whitelist_active) {
            if (!$schoolSetting->isIpAllowed($clientIp)) {
                Log::warning("[Kiosk] Percobaan akses presensi kiosk dari IP di luar jaringan sekolah: {$clientIp}");
                return response()->json([
                    'success' => false,
                    'message' => "Akses Kiosk Gerbang ditolak: Perangkat wajib terhubung ke jaringan resmi SMKN 1 Beringin. IP perangkat Anda ({$clientIp}) tidak terdaftar.",
                ], 403);
            }
        }

        $faceService = new FaceService();

        // MODE 1: Jika NISN diinput/discan via Barcode/QR (Single Match 1:1)
        if ($request->filled('nisn')) {
            $nisn = trim($request->nisn);
            $user = User::with('kelas')->where('nisn', $nisn)->where('role', 'siswa')->first();

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

            $singleResult = $this->processStudentAttendance($user, $compareRes, $request->face_image);

            return response()->json([
                'success'          => true,
                'is_multi'         => false,
                'already_attended' => $singleResult['already_attended'],
                'student'          => $singleResult['student'],
                'waktu_scan'       => $singleResult['waktu_scan'],
                'confidence'       => $singleResult['confidence'],
                'distance'         => $singleResult['distance'],
                'engine'           => $singleResult['engine'],
                'message'          => $singleResult['already_attended']
                    ? "Halo {$user->name}, kamu sudah tercatat HADIR hari ini."
                    : "Selamat pagi, {$user->name}! Presensi berhasil dicatat.",
            ]);
        }

        // MODE 2: Multi-Face 1:N Recognition (Walkthrough Smart Gate Kiosk)
        $identifyRes = $faceService->identifyMulti($request->face_image, null, 5);

        if (!$identifyRes['success']) {
            return response()->json([
                'success' => false,
                'message' => $identifyRes['error'] ?? 'Gagal memproses gambar pada server AI.',
            ], 500);
        }

        $matches = $identifyRes['matches'] ?? [];
        $unmatched = $identifyRes['unmatched'] ?? [];

        if (empty($matches)) {
            return response()->json([
                'success'     => false,
                'message'     => 'Wajah tidak dikenali atau belum terdaftar.',
                'total_faces' => $identifyRes['total_faces'] ?? 0,
                'unmatched'   => $unmatched,
            ], 404);
        }

        $results = [];
        foreach ($matches as $match) {
            $user = User::with('kelas')->find($match['user_id']);
            if (!$user || $user->role !== 'siswa' || !$user->kelas_id) {
                continue;
            }

            $studentResult = $this->processStudentAttendance($user, $match, $request->face_image);
            $results[] = $studentResult;
        }

        if (empty($results)) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa terdeteksi belum memiliki data kelas terdaftar.',
            ], 422);
        }

        $first = $results[0];
        $totalAttended = count($results);

        return response()->json([
            'success'          => true,
            'is_multi'         => true,
            'total_matched'    => $totalAttended,
            'results'          => $results,
            'unmatched'        => $unmatched,
            // Kompatibilitas single student
            'student'          => $first['student'],
            'already_attended' => $first['already_attended'],
            'waktu_scan'       => $first['waktu_scan'],
            'box'              => $first['box'] ?? null,
            'box_norm'         => $first['box_norm'] ?? null,
            'confidence'       => $first['confidence'],
            'distance'         => $first['distance'],
            'engine'           => $first['engine'],
            'message'          => $totalAttended > 1
                ? "{$totalAttended} siswa berhasil diverifikasi serentak!"
                : ($first['already_attended']
                    ? "Halo {$first['student']['name']}, kamu sudah tercatat HADIR hari ini."
                    : "Selamat pagi, {$first['student']['name']}! Presensi berhasil dicatat."),
        ]);
    }

    /**
     * Helper untuk memproses pencatatan presensi 1 siswa di Kiosk.
     */
    private function processStudentAttendance(User $user, array $matchData, ?string $rawImg): array
    {
        $today = today();

        // Cek cepat apakah siswa sudah tercatat HADIR hari ini (menghemat query & CPU secara signifikan)
        $existingHadir = Presensi::where('siswa_id', $user->id)
            ->where('status', 'hadir')
            ->whereHas('sesiPresensi', function ($q) use ($today) {
                $q->whereDate('tanggal', $today);
            })
            ->first();

        if ($existingHadir) {
            $waktuFormatted = $existingHadir->waktu_scan ? $existingHadir->waktu_scan->format('H:i') : now()->format('H:i');
            return [
                'student' => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'nisn'       => $user->nisn ?? '-',
                    'kelas'      => $user->kelas?->nama_kelas ?? 'Reguler',
                    'avatar_url' => $user->avatar_url,
                    'scan_url'   => null,
                ],
                'already_attended' => true,
                'waktu_scan'       => $waktuFormatted . ' WIB',
                'box'              => $matchData['box'] ?? null,
                'box_norm'         => $matchData['box_norm'] ?? null,
                'confidence'       => $matchData['confidence'] ?? 'HIGH',
                'distance'         => $matchData['distance'] ?? null,
                'engine'           => $matchData['engine'] ?? 'ArcFace 512-D YuNet',
            ];
        }

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

        $presensi = Presensi::firstOrCreate(
            [
                'sesi_presensi_id' => $sesi->id,
                'siswa_id'         => $user->id,
            ],
            [
                'status' => 'alpa',
            ]
        );

        $alreadyAttended = ($presensi->status === 'hadir');
        $waktuFormatted = $presensi->waktu_scan ? $presensi->waktu_scan->format('H:i') : now()->format('H:i');
        $fotoPath = null;

        if (!$alreadyAttended) {
            try {
                if ($rawImg) {
                    $cleanImg = $rawImg;
                    if (str_contains($cleanImg, ',')) {
                        $cleanImg = explode(',', $cleanImg, 2)[1];
                    }
                    $decoded = base64_decode($cleanImg);
                    if ($decoded) {
                        $folder = 'face_scans/' . date('Y-m-d');
                        $filename = $folder . '/kiosk_' . $user->id . '_' . time() . '.jpg';
                        Storage::disk('public')->put($filename, $decoded);
                        $fotoPath = $filename;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('[KioskController] Gagal simpan foto scan: ' . $e->getMessage());
            }

            $statusSebelumnya = $presensi->status;
            $now = now();
            $presensi->update([
                'status'     => 'hadir',
                'waktu_scan' => $now,
                'keterangan' => $fotoPath ? "Scan Face ID Kiosk Gerbang (Tersimpan: {$fotoPath})" : 'Scan Face ID Kiosk Gerbang',
            ]);

            // Invalidasi cache hadir count agar polling berikutnya mendapat angka terbaru
            Cache::forget('hadir_count_' . today()->toDateString());

            LogPresensi::create([
                'presensi_id'       => $presensi->id,
                'guru_id'           => $sesi->guru_id,
                'status_sebelumnya' => $statusSebelumnya,
                'status_baru'       => 'hadir',
                'keterangan'        => sprintf(
                    'Presensi Face ID Kiosk Gerbang (%s, confidence: %s, distance: %.4f)',
                    $matchData['engine'] ?? 'ArcFace 512-D YuNet',
                    $matchData['confidence'] ?? 'high',
                    $matchData['distance'] ?? 0
                ),
            ]);

            // Kirim notifikasi WhatsApp ke Orang Tua secara Asynchronous (Deferred) agar respon kiosk instant tanpa delay
            try {
                defer(function () use ($presensi) {
                    try {
                        app(\App\Services\WhatsAppService::class)->sendPresensiNotification($presensi);
                    } catch (\Throwable $e) {
                        Log::warning('[WhatsApp] Gagal kirim notif ortu: ' . $e->getMessage());
                    }
                });
            } catch (\Throwable $e) {
                // Fallback jika runtime defer tidak aktif
                try {
                    app(\App\Services\WhatsAppService::class)->sendPresensiNotification($presensi);
                } catch (\Throwable $e2) {}
            }
        }

        return [
            'student' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'nisn'       => $user->nisn ?? '-',
                'kelas'      => $user->kelas?->nama_kelas ?? 'Reguler',
                'avatar_url' => $user->avatar_url,
                'scan_url'   => $fotoPath ? asset('storage/' . $fotoPath) : null,
            ],
            'already_attended' => $alreadyAttended,
            'waktu_scan'       => $waktuFormatted . ' WIB',
            'box'              => $matchData['box'] ?? null,
            'box_norm'         => $matchData['box_norm'] ?? null,
            'confidence'       => $matchData['confidence'] ?? 'HIGH',
            'distance'         => $matchData['distance'] ?? null,
            'engine'           => $matchData['engine'] ?? 'ArcFace 512-D YuNet',
        ];
    }

    /**
     * Sinkronkan database biometrik siswa ke RAM server secara manual / on-demand.
     */
    public function syncCandidates()
    {
        $schoolSetting = SchoolSetting::getSettings();
        $clientIp = SchoolSetting::getClientIp(request());

        if ($schoolSetting->is_ip_whitelist_active && !$schoolSetting->isIpAllowed($clientIp)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized IP.'], 403);
        }

        $faceService = new FaceService();
        $result = $faceService->syncAllEnrolledStudents();

        return response()->json($result);
    }

    /**
     * Endpoint Polling Statistik & Live Feed Kiosk
     */
    public function liveStats()
    {
        // Cache hadir count 30 detik di Redis — invalidated setiap presensi baru tercatat
        $hadirHariIniCount = Cache::remember('hadir_count_' . today()->toDateString(), 30, function () {
            return Presensi::whereHas('sesiPresensi', function ($q) {
                $q->whereDate('tanggal', today());
            })->where('status', 'hadir')->count();
        });

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
            'success'     => true,
            'hadir_count' => $hadirHariIniCount,
            'recent'      => $recent,
        ]);
    }
}
