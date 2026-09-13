<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\JamPelajaran;
use App\Models\Kelas;
use App\Models\LogPresensi;
use App\Models\MataPelajaran;
use App\Models\Presensi;
use App\Models\SchoolSetting;
use App\Models\SesiPresensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSiswaRequest;
use App\Http\Requests\UpdateSiswaRequest;
use App\Http\Requests\StoreGuruRequest;
use App\Http\Requests\UpdateGuruRequest;
use App\Http\Requests\StoreKelasRequest;
use App\Http\Requests\UpdateKelasRequest;
use App\Http\Requests\UpdateLokasiRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    // =========================================================
    //  OVERVIEW
    // =========================================================
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $stats = [
                'siswa' => User::where('role', 'siswa')->count(),
                'guru' => User::where('role', 'guru')->count(),
                'kelas' => Kelas::count(),
                'hadir_hari_ini' => Presensi::where('status', 'hadir')
                    ->whereHas('sesiPresensi', fn ($q) => $q->where('tanggal', today()))
                    ->count(),
            ];

            // Chart: 7 hari terakhir
            $chartLabels = [];
            $chartHadir = [];
            $chartAlpa = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $chartLabels[] = $date->format('d/m');
                $chartHadir[] = Presensi::where('status', 'hadir')
                    ->whereHas('sesiPresensi', fn ($q) => $q->where('tanggal', $date))->count();
                $chartAlpa[] = Presensi::where('status', 'alpa')
                    ->whereHas('sesiPresensi', fn ($q) => $q->where('tanggal', $date))->count();
            }

            $recentSesi = SesiPresensi::with(['kelas', 'guru', 'mataPelajaran'])
                ->latest('tanggal')->latest('created_at')->take(5)->get();
            $sesiHariIni = SesiPresensi::with(['kelas', 'guru', 'mataPelajaran'])
                ->where('tanggal', today())
                ->latest('created_at')
                ->get();
            $presensiHariIni = Presensi::with(['siswa', 'sesiPresensi.kelas', 'sesiPresensi.guru', 'sesiPresensi.mataPelajaran'])
                ->whereHas('sesiPresensi', fn ($q) => $q->where('tanggal', today()))
                ->get();
        } else {
            // Guru — Sinkronkan dengan seluruh sesi presensi & kehadiran sekolah hari ini
            $sesiHariIni = SesiPresensi::with(['kelas', 'mataPelajaran', 'guru'])
                ->where('tanggal', today())
                ->latest('created_at')
                ->get();

            $presensiHariIni = Presensi::with(['siswa', 'sesiPresensi.kelas', 'sesiPresensi.guru', 'sesiPresensi.mataPelajaran'])
                ->whereHas('sesiPresensi', fn ($q) => $q->where('tanggal', today()))
                ->get();

            $stats = [
                'sesi_hari_ini' => $sesiHariIni->count(),
                'sesi_aktif' => $sesiHariIni->where('is_active', true)->count(),
                'hadir_hari_ini' => $presensiHariIni->where('status', 'hadir')->count(),
                'alpa_hari_ini' => $presensiHariIni->where('status', 'alpa')->count(),
            ];
            $chartLabels = $chartHadir = $chartAlpa = [];
            $recentSesi = SesiPresensi::with(['kelas', 'mataPelajaran', 'guru'])
                ->latest('tanggal')->latest('created_at')->take(5)->get();
        }

        return view('dashboard.index', compact(
            'user', 'stats', 'chartLabels', 'chartHadir', 'chartAlpa', 'recentSesi', 'sesiHariIni', 'presensiHariIni'
        ));
    }

    // =========================================================
    //  STATS JSON — Realtime polling untuk dashboard overview
    // =========================================================
    public function statsJson()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return response()->json([
                'hadir_hari_ini' => Presensi::where('status', 'hadir')
                    ->whereHas('sesiPresensi', fn ($q) => $q->where('tanggal', today()))
                    ->count(),
                'alpa_hari_ini' => Presensi::where('status', 'alpa')
                    ->whereHas('sesiPresensi', fn ($q) => $q->where('tanggal', today()))
                    ->count(),
                'sesi_aktif' => SesiPresensi::where('tanggal', today())->where('is_active', true)->count(),
            ]);
        } else {
            // Guru realtime polling tersinkronisasi dengan seluruh sesi hari ini
            $sesiHariIni = SesiPresensi::where('tanggal', today())->get();
            $presensiHariIni = Presensi::whereHas('sesiPresensi', fn ($q) => $q->where('tanggal', today()))->get();
            return response()->json([
                'sesi_hari_ini'  => $sesiHariIni->count(),
                'sesi_aktif'     => $sesiHariIni->where('is_active', true)->count(),
                'hadir_hari_ini' => $presensiHariIni->where('status', 'hadir')->count(),
                'alpa_hari_ini'  => $presensiHariIni->where('status', 'alpa')->count(),
            ]);
        }
    }

    // =========================================================
    //  PRESENSI — LIST SESI
    // =========================================================
    public function presensiIndex(Request $request)
    {
        $user = auth()->user();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mapel = MataPelajaran::orderBy('nama_mapel')->get();
        $jamPelajarans = JamPelajaran::orderBy('nomor')->get();

        $query = SesiPresensi::with(['kelas', 'guru', 'mataPelajaran'])
            ->withCount(['presensi as hadir_count' => fn ($q) => $q->where('status', 'hadir')])
            ->withCount(['presensi as total_count'])
            ->latest('tanggal')
            ->latest('created_at');

        // Guru dan Admin bisa melihat semua sesi
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        }

        // Filter Tanggal Spesifik (opsional)
        if ($request->filled('tanggal')) {
            $query->where('tanggal', $request->tanggal);
        }
        // Filter Bulan (opsional, contoh: 2026-09)
        elseif ($request->filled('bulan')) {
            $bulanDate = Carbon::parse($request->bulan . '-01');
            $query->whereBetween('tanggal', [
                $bulanDate->copy()->startOfMonth()->toDateString(),
                $bulanDate->copy()->endOfMonth()->toDateString(),
            ]);
        }
        // Filter Periode Cepat
        elseif ($request->periode === 'hari_ini') {
            $query->where('tanggal', today()->toDateString());
        }

        $sesiList = $query->paginate(15)->withQueryString();

        $tanggal = $request->tanggal;
        $bulan = $request->bulan;
        $periode = $request->periode;

        $gurus = User::where('role', 'guru')->orderBy('name')->get();

        return view('dashboard.presensi.index', compact('sesiList', 'kelas', 'mapel', 'user', 'tanggal', 'bulan', 'periode', 'jamPelajarans', 'gurus'));
    }

    // =========================================================
    //  PRESENSI — BUAT SESI BARU
    // =========================================================
    public function storeSesi(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:kelas,mapel',
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
            'guru_id' => 'nullable|exists:users,id',
            'mapel_id' => 'nullable|exists:mata_pelajarans,id',
            'jam_pelajaran_id' => 'nullable|exists:jam_pelajarans,id',
        ]);

        // Cek apakah di kelas, tanggal, mapel, dan jam tersebut sudah ada sesi yang sama
        $existing = SesiPresensi::where('kelas_id', $request->kelas_id)
            ->where('tanggal', $request->tanggal)
            ->where('tipe', $request->tipe)
            ->when($request->tipe == 'mapel', function ($q) use ($request) {
                return $q->where('mapel_id', $request->mapel_id)
                    ->where('jam_pelajaran_id', $request->jam_pelajaran_id);
            })
            ->first();

        if ($existing) {
            return back()->with('error', 'Sesi presensi untuk parameter tersebut sudah ada!');
        }

        // Jika tipe = mapel, pastikan sesi kelas sudah ada di hari tersebut
        $sesiKelas = null;
        if ($request->tipe === 'mapel') {
            $sesiKelas = SesiPresensi::with('presensi')
                ->where('kelas_id', $request->kelas_id)
                ->where('tanggal', $request->tanggal)
                ->where('tipe', 'kelas')
                ->first();

            if (! $sesiKelas) {
                return back()->with('error', 'Tidak bisa membuat Sesi Mapel! Sesi Kelas (Presensi Pagi) untuk hari ini belum dibuat oleh Wali Kelas.');
            }
        }

        $jamPelajaran = null;
        if ($request->jam_pelajaran_id) {
            $jp = JamPelajaran::find($request->jam_pelajaran_id);
            if ($jp) {
                $jamPelajaran = $jp->label_short;
            }
        }

        $guruId = auth()->id();
        if ($request->filled('guru_id') && auth()->user()->isAdmin()) {
            $guruId = $request->guru_id;
        }

        $sesi = SesiPresensi::create([
            'guru_id' => $guruId,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->tipe === 'mapel' ? $request->mapel_id : null,
            'jam_pelajaran_id' => $request->jam_pelajaran_id,
            'jam_pelajaran' => $jamPelajaran,
            'tanggal' => $request->tanggal,
            'barcode_token' => Str::random(32),
            'is_active' => true,
            'tipe' => $request->tipe,
        ]);

        // Create presensi records for all students in this kelas
        $siswaList = User::where('role', 'siswa')->where('kelas_id', $request->kelas_id)->get();

        foreach ($siswaList as $siswa) {
            $status = 'alpa'; // default

            // Jika ada sesi kelas, copy status darinya
            if ($sesiKelas) {
                $absenKelas = $sesiKelas->presensi->where('siswa_id', $siswa->id)->first();
                if ($absenKelas) {
                    $status = $absenKelas->status;
                }
            }

            Presensi::create([
                'sesi_presensi_id' => $sesi->id,
                'siswa_id' => $siswa->id,
                'status' => $status,
            ]);
        }

        return redirect()->route('dashboard.presensi.detail', $sesi)
            ->with('success', 'Sesi presensi berhasil dibuat!');
    }

    // =========================================================
    //  PRESENSI — DETAIL SESI
    // =========================================================
    public function presensiDetail(SesiPresensi $sesiPresensi)
    {
        $sesiPresensi->load(['kelas', 'guru', 'mataPelajaran', 'presensi.siswa', 'presensi.logPresensi.guru']);

        // Data statistik siswa untuk sinkronisasi susulan (Retroactive Sync)
        $existingStudentIds = $sesiPresensi->presensi->pluck('siswa_id')->toArray();
        $totalSiswaKelas = User::where('role', 'siswa')->where('kelas_id', $sesiPresensi->kelas_id)->count();
        $missingStudents = User::where('role', 'siswa')
            ->where('kelas_id', $sesiPresensi->kelas_id)
            ->whereNotIn('id', $existingStudentIds)
            ->orderBy('name')
            ->get();
        $missingCount = $missingStudents->count();

        return view('dashboard.presensi.detail', compact(
            'sesiPresensi', 'totalSiswaKelas', 'missingStudents', 'missingCount'
        ));
    }

    /**
     * Retroactive Sync — Menambahkan siswa susulan (baru mendaftar/belum tercatat) ke sesi ini,
     * serta memperbarui status sesi mapel dari sesi kelas pagi jika tersedia.
     */
    public function syncSiswaSesi(SesiPresensi $sesiPresensi)
    {
        // 1. Ambil ID siswa yang sudah ada di sesi ini
        $existingStudentIds = $sesiPresensi->presensi()->pluck('siswa_id')->toArray();

        // 2. Cari siswa kelas ini yang belum terdaftar di sesi ini
        $missingStudents = User::where('role', 'siswa')
            ->where('kelas_id', $sesiPresensi->kelas_id)
            ->whereNotIn('id', $existingStudentIds)
            ->get();

        // Cari sesi kelas (pagi) jika ini sesi mapel
        $sesiKelas = null;
        if ($sesiPresensi->tipe === 'mapel') {
            $sesiKelas = SesiPresensi::with('presensi')
                ->where('kelas_id', $sesiPresensi->kelas_id)
                ->where('tanggal', $sesiPresensi->tanggal)
                ->where('tipe', 'kelas')
                ->first();
        }

        $addedCount = 0;
        $updatedFromKelasCount = 0;

        // 3. Masukkan siswa susulan
        foreach ($missingStudents as $siswa) {
            $status = 'alpa';
            $keterangan = 'Siswa susulan (Sync Retroaktif)';

            // Jika sesi mapel dan ada sesi kelas pagi, salin status dari sesi kelas
            if ($sesiKelas) {
                $absenKelas = $sesiKelas->presensi->where('siswa_id', $siswa->id)->first();
                if ($absenKelas) {
                    $status = $absenKelas->status;
                    $keterangan = 'Salin dari Sesi Kelas pagi';
                }
            }

            $newPresensi = Presensi::create([
                'sesi_presensi_id' => $sesiPresensi->id,
                'siswa_id'         => $siswa->id,
                'status'           => $status,
                'keterangan'       => $keterangan,
            ]);

            LogPresensi::create([
                'presensi_id'       => $newPresensi->id,
                'guru_id'           => auth()->id(),
                'status_sebelumnya' => 'belum terdaftar',
                'status_baru'       => $status,
                'keterangan'        => 'Ditambahkan via Sinkronisasi Retroaktif oleh ' . auth()->user()->name,
            ]);

            $addedCount++;
        }

        // 4. Jika sesi mapel, perbarui juga siswa yang berstatus 'alpa' di mapel tapi sudah diset 'sakit'/'izin' di sesi kelas pagi
        if ($sesiPresensi->tipe === 'mapel' && $sesiKelas) {
            $sesiPresensi->load('presensi');
            foreach ($sesiPresensi->presensi as $absenMapel) {
                if ($absenMapel->status === 'alpa') {
                    $absenKelas = $sesiKelas->presensi->where('siswa_id', $absenMapel->siswa_id)->first();
                    if ($absenKelas && in_array($absenKelas->status, ['sakit', 'izin', 'hadir'])) {
                        $oldStatus = $absenMapel->status;
                        $absenMapel->update([
                            'status'     => $absenKelas->status,
                            'keterangan' => 'Disinkronkan dari Sesi Kelas pagi',
                        ]);

                        LogPresensi::create([
                            'presensi_id'       => $absenMapel->id,
                            'guru_id'           => auth()->id(),
                            'status_sebelumnya' => $oldStatus,
                            'status_baru'       => $absenKelas->status,
                            'keterangan'        => 'Sinkronisasi status dari Sesi Kelas pagi oleh ' . auth()->user()->name,
                        ]);

                        $updatedFromKelasCount++;
                    }
                }
            }
        }

        if ($addedCount === 0 && $updatedFromKelasCount === 0) {
            return back()->with('info', 'Semua siswa di kelas ini sudah tersinkronisasi lengkap.');
        }

        $msg = [];
        if ($addedCount > 0) {
            $msg[] = "Berhasil menambahkan {$addedCount} siswa susulan ke sesi ini.";
        }
        if ($updatedFromKelasCount > 0) {
            $msg[] = "Berhasil memperbarui status {$updatedFromKelasCount} siswa dari Sesi Kelas pagi.";
        }

        return back()->with('success', implode(' ', $msg) . ' Silakan sesuaikan status kehadiran pada tabel jika diperlukan.');
    }

    public function exportPdf(SesiPresensi $sesiPresensi)
    {
        $sesiPresensi->load(['kelas', 'guru', 'mataPelajaran', 'presensi.siswa']);

        return view('dashboard.presensi.print', compact('sesiPresensi'));
    }

    public function exportExcel(SesiPresensi $sesiPresensi)
    {
        $sesiPresensi->load(['kelas', 'guru', 'mataPelajaran', 'presensi.siswa']);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PresensiSesiExport($sesiPresensi),
            'Presensi_Sesi_' . $sesiPresensi->kelas->nama_kelas . '_' . \Carbon\Carbon::parse($sesiPresensi->tanggal)->format('Ymd') . '.xlsx'
        );
    }

    public function exportPdfHarian(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $tanggal = Carbon::parse($request->tanggal);

        // Ambil semua sesi pada tanggal dan kelas tersebut, urutkan: Sesi Pagi (tanpa mapel) dulu, lalu Sesi Mapel
        $sesiList = SesiPresensi::with(['mataPelajaran', 'presensi.siswa'])
            ->where('kelas_id', $kelas->id)
            ->where('tanggal', $tanggal->toDateString())
            ->orderByRaw('mapel_id IS NOT NULL, created_at ASC')
            ->get();

        if ($sesiList->isEmpty()) {
            return back()->with('error', 'Tidak ada sesi presensi pada tanggal tersebut.');
        }

        // Ambil daftar siswa unik dari semua sesi (berjaga-jaga jika ada siswa pindahan)
        // Biasanya ambil dari tabel User langsung
        $siswaList = User::where('role', 'siswa')->where('kelas_id', $kelas->id)->orderBy('name')->get();

        return view('dashboard.presensi.print-harian', compact('kelas', 'tanggal', 'sesiList', 'siswaList'));
    }

    public function exportExcelHarian(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal' => 'required|date',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $tanggal = Carbon::parse($request->tanggal);

        $sesiList = SesiPresensi::with(['mataPelajaran', 'presensi.siswa'])
            ->where('kelas_id', $kelas->id)
            ->where('tanggal', $tanggal->toDateString())
            ->orderByRaw('mapel_id IS NOT NULL, created_at ASC')
            ->get();

        if ($sesiList->isEmpty()) {
            return back()->with('error', 'Tidak ada sesi presensi pada tanggal tersebut.');
        }

        $siswaList = User::where('role', 'siswa')->where('kelas_id', $kelas->id)->orderBy('name')->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PresensiHarianExport($kelas, $tanggal, $sesiList, $siswaList),
            'Presensi_Harian_' . $kelas->nama_kelas . '_' . $tanggal->format('Ymd') . '.xlsx'
        );
    }

    /**
     * Live Polling Data Presensi Sesi (JSON) untuk update realtime tanpa reload
     */
    public function presensiLiveJson(SesiPresensi $sesiPresensi)
    {
        $sesiPresensi->load(['presensi.siswa', 'presensi.logPresensi.guru']);

        $stats = [
            'total' => $sesiPresensi->presensi->count(),
            'hadir' => $sesiPresensi->presensi->where('status', 'hadir')->count(),
            'izin' => $sesiPresensi->presensi->where('status', 'izin')->count(),
            'sakit' => $sesiPresensi->presensi->where('status', 'sakit')->count(),
            'alpa' => $sesiPresensi->presensi->where('status', 'alpa')->count(),
        ];

        $items = $sesiPresensi->presensi->sortBy('siswa.name')->values()->map(function ($absen) {
            return [
                'id' => $absen->id,
                'siswa_id' => $absen->siswa_id,
                'name' => $absen->siswa->name,
                'nisn' => $absen->siswa->nisn ?: '-',
                'status' => $absen->status,
                'label' => $absen->labelStatus(),
                'keterangan' => $absen->keterangan ?: '',
                'waktu_scan' => $absen->waktu_scan ? $absen->waktu_scan->format('H:i') : null,
                'log_count' => $absen->logPresensi->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'is_active' => $sesiPresensi->is_active,
            'stats' => $stats,
            'items' => $items,
        ]);
    }

    /**
     * Export Rekap Bulanan Kelas (Wali Kelas)
     * Format: Rekapitulasi Presensi Lengkap & Rapi untuk Wali Kelas
     */
    public function exportBulananKelas(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'bulan' => 'required|date_format:Y-m',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $bulanDate = Carbon::parse($request->bulan.'-01');
        $startOfMonth = $bulanDate->copy()->startOfMonth();
        $endOfMonth = $bulanDate->copy()->endOfMonth();

        // Ambil semua sesi kelas pada bulan tersebut (prioritaskan sesi kelas/pagi jika ada)
        $sesiList = SesiPresensi::with(['presensi.siswa'])
            ->where('kelas_id', $kelas->id)
            ->whereBetween('tanggal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('tanggal')
            ->orderByRaw('mapel_id IS NULL DESC, created_at ASC')
            ->get();

        $siswaList = User::where('role', 'siswa')
            ->where('kelas_id', $kelas->id)
            ->orderBy('name')
            ->get();

        // Ambil daftar tanggal aktif yang punya sesi
        $activeDates = $sesiList->pluck('tanggal')
            ->map(fn ($t) => is_object($t) ? $t->format('Y-m-d') : substr($t, 0, 10))
            ->unique()
            ->values()
            ->all();

        // Matrix: [siswa_id][tanggal] = status
        $matrix = [];
        foreach ($siswaList as $siswa) {
            $matrix[$siswa->id] = [];
        }
        foreach ($sesiList as $sesi) {
            $tglStr = is_object($sesi->tanggal) ? $sesi->tanggal->format('Y-m-d') : substr($sesi->tanggal, 0, 10);
            foreach ($sesi->presensi as $absen) {
                if (! isset($matrix[$absen->siswa_id][$tglStr]) || $sesi->mapel_id === null) {
                    $matrix[$absen->siswa_id][$tglStr] = $absen->status;
                }
            }
        }

        // Daftar semua hari dalam bulan ini
        $daysInMonth = $bulanDate->daysInMonth;
        $hariList = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currentDate = $bulanDate->copy()->day($d);
            $tglStr = $currentDate->format('Y-m-d');
            $hariList[] = [
                'date' => $tglStr,
                'tgl' => $d,
                'nama' => $currentDate->translatedFormat('D'),
                'libur' => in_array($currentDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]),
                'has_session' => in_array($tglStr, $activeDates),
            ];
        }

        return view('dashboard.presensi.print-bulanan-kelas', compact(
            'kelas', 'bulanDate', 'siswaList', 'hariList', 'activeDates', 'matrix', 'sesiList'
        ));
    }

    public function exportExcelBulananKelas(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'bulan' => 'required|date_format:Y-m',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $bulanDate = Carbon::parse($request->bulan.'-01');
        $startOfMonth = $bulanDate->copy()->startOfMonth();
        $endOfMonth = $bulanDate->copy()->endOfMonth();

        $sesiList = SesiPresensi::with(['presensi.siswa'])
            ->where('kelas_id', $kelas->id)
            ->whereBetween('tanggal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('tanggal')
            ->orderByRaw('mapel_id IS NULL DESC, created_at ASC')
            ->get();

        $siswaList = User::where('role', 'siswa')->where('kelas_id', $kelas->id)->orderBy('name')->get();

        $activeDates = $sesiList->pluck('tanggal')->map(fn ($t) => is_object($t) ? $t->format('Y-m-d') : substr($t, 0, 10))->unique()->values()->all();

        $matrix = [];
        foreach ($siswaList as $siswa) $matrix[$siswa->id] = [];
        foreach ($sesiList as $sesi) {
            $tglStr = is_object($sesi->tanggal) ? $sesi->tanggal->format('Y-m-d') : substr($sesi->tanggal, 0, 10);
            foreach ($sesi->presensi as $absen) {
                if (! isset($matrix[$absen->siswa_id][$tglStr]) || $sesi->mapel_id === null) {
                    $matrix[$absen->siswa_id][$tglStr] = $absen->status;
                }
            }
        }

        $daysInMonth = $bulanDate->daysInMonth;
        $hariList = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currentDate = $bulanDate->copy()->day($d);
            $tglStr = $currentDate->format('Y-m-d');
            $hariList[] = [
                'date' => $tglStr,
                'tgl' => $d,
                'nama' => $currentDate->translatedFormat('D'),
                'libur' => in_array($currentDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]),
                'has_session' => in_array($tglStr, $activeDates),
            ];
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PresensiBulananKelasExport($kelas, $bulanDate, $siswaList, $hariList, $activeDates, $matrix, $sesiList),
            'Rekap_Bulanan_Kelas_' . $kelas->nama_kelas . '_' . $bulanDate->format('Ym') . '.xlsx'
        );
    }

    /**
     * Export Rekap Bulanan Mapel (Guru Mata Pelajaran)
     * Format: Berbasis Pertemuan (P.1, P.2, P.3, ...) dengan Tanggal & Jam Pelajaran
     */
    public function exportBulananMapel(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajarans,id',
            'bulan' => 'required|date_format:Y-m',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $mapel = MataPelajaran::findOrFail($request->mapel_id);
        $bulanDate = Carbon::parse($request->bulan.'-01');
        $startOfMonth = $bulanDate->copy()->startOfMonth();
        $endOfMonth = $bulanDate->copy()->endOfMonth();

        // Ambil semua sesi mapel pada bulan tersebut
        $sesiList = SesiPresensi::with(['presensi.siswa', 'guru'])
            ->where('kelas_id', $kelas->id)
            ->where('mapel_id', $mapel->id)
            ->whereBetween('tanggal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('tanggal')
            ->orderBy('created_at')
            ->get();

        $siswaList = User::where('role', 'siswa')
            ->where('kelas_id', $kelas->id)
            ->orderBy('name')
            ->get();

        // Guru Pengampu
        $guruNama = optional($sesiList->first()?->guru)->name ?? auth()->user()->name;
        $guruNik = optional($sesiList->first()?->guru)->nik ?? auth()->user()->nik ?? '-';

        // Matrix: [siswa_id][sesi_id] = status
        $matrix = [];
        foreach ($siswaList as $siswa) {
            $matrix[$siswa->id] = [];
        }
        foreach ($sesiList as $sesi) {
            foreach ($sesi->presensi as $absen) {
                $matrix[$absen->siswa_id][$sesi->id] = $absen->status;
            }
        }

        return view('dashboard.presensi.print-bulanan-mapel', compact(
            'kelas', 'mapel', 'bulanDate', 'sesiList', 'siswaList', 'matrix', 'guruNama', 'guruNik'
        ));
    }

    public function exportExcelBulananMapel(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajarans,id',
            'bulan' => 'required|date_format:Y-m',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $mapel = MataPelajaran::findOrFail($request->mapel_id);
        $bulanDate = Carbon::parse($request->bulan.'-01');
        $startOfMonth = $bulanDate->copy()->startOfMonth();
        $endOfMonth = $bulanDate->copy()->endOfMonth();

        $sesiList = SesiPresensi::with(['presensi.siswa', 'guru'])
            ->where('kelas_id', $kelas->id)
            ->where('mapel_id', $mapel->id)
            ->whereBetween('tanggal', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('tanggal')
            ->orderBy('created_at')
            ->get();

        $siswaList = User::where('role', 'siswa')->where('kelas_id', $kelas->id)->orderBy('name')->get();

        $guruNama = optional($sesiList->first()?->guru)->name ?? auth()->user()->name;
        $guruNik = optional($sesiList->first()?->guru)->nik ?? auth()->user()->nik ?? '-';

        $matrix = [];
        foreach ($siswaList as $siswa) $matrix[$siswa->id] = [];
        foreach ($sesiList as $sesi) {
            foreach ($sesi->presensi as $absen) {
                $matrix[$absen->siswa_id][$sesi->id] = $absen->status;
            }
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PresensiBulananMapelExport($kelas, $mapel, $bulanDate, $sesiList, $siswaList, $matrix, $guruNama, $guruNik),
            'Rekap_Bulanan_Mapel_' . $mapel->nama_mapel . '_' . $kelas->nama_kelas . '_' . $bulanDate->format('Ym') . '.xlsx'
        );
    }

    public function closeSesi(SesiPresensi $sesiPresensi)
    {
        $sesiPresensi->update(['is_active' => ! $sesiPresensi->is_active]);
        $statusText = $sesiPresensi->is_active ? 'diaktifkan kembali. Barcode sudah bisa discan.' : 'ditutup. Barcode dinonaktifkan.';

        return back()->with('success', 'Sesi presensi berhasil '.$statusText);
    }

    public function resetAbsenSesi(SesiPresensi $sesiPresensi)
    {
        $this->adminOnly();

        // Hapus log perubahan terkait sesi ini
        LogPresensi::whereHas('presensi', function ($q) use ($sesiPresensi) {
            $q->where('sesi_presensi_id', $sesiPresensi->id);
        })->delete();

        // Reset semua presensi kembali ke alpa
        $sesiPresensi->presensi()->update([
            'status' => 'alpa',
            'waktu_scan' => null,
            'keterangan' => null,
        ]);

        return back()->with('success', 'Riwayat presensi untuk sesi kelas ini telah di-reset kembali ke Alpa.');
    }

    public function deleteAllSesi()
    {
        $this->adminOnly();

        // Disable foreign key checks temporarily if needed, but Eloquent delete works if we fetch or just DB::statement
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        LogPresensi::truncate();
        Presensi::truncate();
        SesiPresensi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return back()->with('success', 'Semua riwayat sesi presensi berhasil dihapus permanen.');
    }

    // =========================================================
    //  PRESENSI — UPDATE STATUS RECORD
    // =========================================================
    public function updateRecord(Request $request, Presensi $presensi)
    {
        $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $statusSebelumnya = $presensi->status;
        $statusBaru = $request->status;

        // Cek jika ada perubahan
        if ($statusSebelumnya !== $statusBaru || $presensi->keterangan !== $request->keterangan) {

            // Catat log
            LogPresensi::create([
                'presensi_id' => $presensi->id,
                'guru_id' => auth()->id(),
                'status_sebelumnya' => $statusSebelumnya,
                'status_baru' => $statusBaru,
                'keterangan' => $request->keterangan,
            ]);

            // Update presensi
            $presensi->update([
                'status' => $statusBaru,
                'keterangan' => $request->keterangan,
            ]);
        }

        return back()->with('success', 'Status kehadiran diperbarui!');
    }

    // =========================================================
    //  KELOLA SISWA (ADMIN)
    // =========================================================
    public function siswaIndex(Request $request)
    {
        $this->adminOnly();

        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        // Technical Stats Bar
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalEnrolled = User::where('role', 'siswa')->whereNotNull('face_enrolled_at')->count();
        $totalNotEnrolled = $totalSiswa - $totalEnrolled;
        $totalCustomAvatar = User::where('role', 'siswa')->whereNotNull('avatar')->where('avatar', '!=', '0')->count();
        $stats = [
            'total' => $totalSiswa,
            'enrolled' => $totalEnrolled,
            'not_enrolled' => $totalNotEnrolled,
            'custom_avatar' => $totalCustomAvatar,
            'enrolled_percent' => $totalSiswa > 0 ? round(($totalEnrolled / $totalSiswa) * 100, 1) : 0,
        ];

        $query = User::where('role', 'siswa')->with('kelas');

        // Search Filter (name, nisn, username, bio)
        if ($request->filled('search')) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('nisn', 'like', '%'.$request->search.'%')
                ->orWhere('username', 'like', '%'.$request->search.'%')
                ->orWhere('bio', 'like', '%'.$request->search.'%')
            );
        }

        // Kelas Filter
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Face Biometrics Filter
        if ($request->filled('wajah')) {
            if ($request->wajah === 'enrolled') {
                $query->whereNotNull('face_enrolled_at');
            } elseif ($request->wajah === 'not_enrolled') {
                $query->whereNull('face_enrolled_at');
            }
        }

        // Avatar Profile Filter
        if ($request->filled('foto')) {
            if ($request->foto === 'has_avatar') {
                $query->whereNotNull('avatar')->where('avatar', '!=', '0');
            } elseif ($request->foto === 'no_avatar') {
                $query->where(fn ($q) => $q->whereNull('avatar')->orWhere('avatar', '0'));
            }
        }

        $siswaList = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('dashboard.siswa', compact('siswaList', 'kelas', 'stats'));
    }

    public function storeSiswa(StoreSiswaRequest $request)
    {
        $v = $request->validated();

        User::create(array_merge($v, [
            'role' => 'siswa',
            'password' => Hash::make($v['password']),
        ]));

        return redirect()->route('dashboard.siswa')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function updateSiswa(UpdateSiswaRequest $request, User $siswa)
    {
        $this->adminOnly();
        abort_if($siswa->role !== 'siswa', 403);

        $v = $request->validated();

        if ($request->filled('password')) {
            $v['password'] = Hash::make($request->password);
        } else {
            unset($v['password']);
        }

        if ($request->has('reset_wajah')) {
            $v['face_descriptor'] = null;
            $v['face_enrolled_at'] = null;
            // Hapus file fisik foto biometrik di storage
            Storage::disk('public')->deleteDirectory($siswa->getStorageFolder() . '/face_enrollments');
            Storage::disk('public')->deleteDirectory('face_enrollments/' . $siswa->id);
        }

        if ($request->boolean('remove_avatar')) {
            if ($siswa->avatar && Storage::disk('public')->exists($siswa->avatar)) {
                Storage::disk('public')->delete($siswa->avatar);
            }
            $v['avatar'] = null;
        }

        if ($request->boolean('remove_banner')) {
            if ($siswa->banner && Storage::disk('public')->exists($siswa->banner)) {
                Storage::disk('public')->delete($siswa->banner);
            }
            $v['banner'] = null;
        }

        $siswa->update($v);

        return redirect()->route('dashboard.siswa')->with('success', "Data siswa {$siswa->name} berhasil diperbarui!");
    }

    public function destroySiswa(User $siswa)
    {
        $this->adminOnly();
        abort_if($siswa->role !== 'siswa', 403);

        // Hapus seluruh berkas penyimpanan akun di storage
        Storage::disk('public')->deleteDirectory($siswa->getStorageFolder());
        Storage::disk('public')->deleteDirectory('face_enrollments/' . $siswa->id);
        if ($siswa->avatar && Storage::disk('public')->exists($siswa->avatar)) {
            Storage::disk('public')->delete($siswa->avatar);
        }
        if ($siswa->banner && Storage::disk('public')->exists($siswa->banner)) {
            Storage::disk('public')->delete($siswa->banner);
        }

        $siswa->delete();

        return redirect()->route('dashboard.siswa')->with('success', "Akun dan seluruh berkas siswa {$siswa->name} berhasil dihapus!");
    }

    public function resetFaceSiswa(User $siswa)
    {
        $this->adminOnly();
        abort_if($siswa->role !== 'siswa', 403);
        
        // 1. Reset kolom biometrik di database
        $siswa->update([
            'face_descriptor' => null,
            'face_enrolled_at' => null
        ]);

        // 2. Hapus berkas foto referensi wajah fisik di storage
        Storage::disk('public')->deleteDirectory($siswa->getStorageFolder() . '/face_enrollments');
        Storage::disk('public')->deleteDirectory('face_enrollments/' . $siswa->id);

        return redirect()->route('dashboard.siswa')->with('success', "Wajah dan berkas referensi biometrik siswa {$siswa->name} berhasil direset dari database dan storage!");
    }

    public function resetAllFaces()
    {
        $this->adminOnly();
        
        // 1. Reset seluruh kolom biometrik siswa di database
        User::where('role', 'siswa')->update([
            'face_descriptor' => null,
            'face_enrolled_at' => null
        ]);

        // 2. Hapus folder berkas referensi wajah fisik di seluruh akun
        $accountDirs = Storage::disk('public')->directories('accounts');
        foreach ($accountDirs as $dir) {
            Storage::disk('public')->deleteDirectory($dir . '/face_enrollments');
        }
        Storage::disk('public')->deleteDirectory('face_enrollments');

        return redirect()->route('dashboard.siswa')->with('success', 'Semua data biometrik dan berkas foto referensi wajah siswa berhasil direset dari database dan storage. Siswa harus scan ulang!');
    }


    // =========================================================
    //  KELOLA GURU (ADMIN)
    // =========================================================
    public function guruIndex(Request $request)
    {
        $this->adminOnly();

        $query = User::where('role', 'guru');
        if ($request->search) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('nik', 'like', '%'.$request->search.'%')
            );
        }
        $guruList = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('dashboard.guru', compact('guruList'));
    }

    public function storeGuru(StoreGuruRequest $request)
    {
        $v = $request->validated();

        User::create(array_merge($v, [
            'role' => 'guru',
            'password' => Hash::make($v['password']),
        ]));

        return redirect()->route('dashboard.guru')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function updateGuru(UpdateGuruRequest $request, User $guru)
    {
        abort_if($guru->role !== 'guru', 403);

        $v = $request->validated();

        $guru->update($v);

        return redirect()->route('dashboard.guru')->with('success', 'Data guru diperbarui!');
    }

    public function destroyGuru(User $guru)
    {
        $this->adminOnly();
        abort_if($guru->role !== 'guru', 403);
        $guru->delete();

        return redirect()->route('dashboard.guru')->with('success', 'Guru dihapus!');
    }

    // =========================================================
    //  KELOLA KELAS (ADMIN)
    // =========================================================
    public function kelasIndex()
    {
        $this->adminOnly();
        $kelasList = Kelas::withCount('siswa')->orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('dashboard.kelas', compact('kelasList'));
    }

    public function storeKelas(StoreKelasRequest $request)
    {
        $v = $request->validated();

        Kelas::create($v);

        return redirect()->route('dashboard.kelas')->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function updateKelas(UpdateKelasRequest $request, Kelas $kelas)
    {
        $v = $request->validated();

        $kelas->update($v);

        return redirect()->route('dashboard.kelas')->with('success', 'Kelas diperbarui!');
    }

    public function destroyKelas(Kelas $kelas)
    {
        $this->adminOnly();
        $kelas->delete();

        return redirect()->route('dashboard.kelas')->with('success', 'Kelas dihapus!');
    }

    // =========================================================
    //  LOG PERUBAHAN & RESET KELAS (ADMIN)
    // =========================================================
    public function logPresensiIndex(Request $request)
    {
        $this->adminOnly();
        $query = LogPresensi::with([
            'presensi.siswa',
            'presensi.sesiPresensi.kelas',
            'presensi.sesiPresensi.mataPelajaran',
            'guru',
        ])->latest();

        if ($request->kelas_id) {
            $query->whereHas('presensi.sesiPresensi', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->mapel_id) {
            $query->whereHas('presensi.sesiPresensi', function ($q) use ($request) {
                $q->where('mapel_id', $request->mapel_id);
            });
        }

        if ($request->guru_id) {
            $query->where('guru_id', $request->guru_id);
        }

        if ($request->tanggal) {
            $query->whereHas('presensi.sesiPresensi', function ($q) use ($request) {
                $q->where('tanggal', $request->tanggal);
            });
        }

        $logs = $query->paginate(20)->withQueryString();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mapel = MataPelajaran::orderBy('nama_mapel')->get();
        $guruList = User::where('role', 'guru')->orderBy('name')->get();

        return view('dashboard.log-presensi', compact('logs', 'kelas', 'mapel', 'guruList'));
    }

    public function resetSesi()
    {
        $this->adminOnly();
        // Akhiri semua sesi aktif (atau hapus yang tidak digunakan)
        // Kita cukup set is_active = false untuk semua sesi yang masih aktif
        SesiPresensi::where('is_active', true)->update(['is_active' => false]);

        return redirect()->back()->with('success', 'Semua riwayat kelas/sesi yang aktif telah direset (ditutup).');
    }

    // =========================================================
    //  PENGATURAN LOKASI & GEOFENCING (ADMIN ONLY)
    // =========================================================
    public function lokasiIndex(Request $request)
    {
        $this->adminOnly();
        $setting = SchoolSetting::getSettings();
        $clientIp = SchoolSetting::getClientIp($request);
        return view('dashboard.pengaturan.lokasi', compact('setting', 'clientIp'));
    }

    public function updateLokasi(UpdateLokasiRequest $request)
    {
        $request->validated();

        $setting = SchoolSetting::getSettings();
        $setting->update([
            'school_name' => $request->school_name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meters' => $request->radius_meters,
            'is_geofencing_active' => $request->has('is_geofencing_active'),
            'is_ip_whitelist_active' => $request->has('is_ip_whitelist_active'),
            'allowed_ips' => $request->allowed_ips,
        ]);

        return redirect()->back()->with('success', 'Pengaturan lokasi sekolah & keamanan jaringan WiFi berhasil diperbarui.');
    }

    // =========================================================
    //  MANAJEMEN STORAGE 100GB (ADMIN ONLY)
    // =========================================================
    public function storageIndex(Request $request)
    {
        $this->adminOnly();

        $mountPath = '/mnt/data-presensi-smk/storage_public';
        $storagePath = is_dir($mountPath) ? $mountPath : storage_path('app/public');

        // Disk Capacity Metrics (OS Native)
        $diskTotalBytes = @disk_total_space($storagePath) ?: (100 * 1024 * 1024 * 1024);
        $diskFreeBytes = @disk_free_space($storagePath) ?: (95 * 1024 * 1024 * 1024);
        $diskUsedBytes = max(0, $diskTotalBytes - $diskFreeBytes);
        
        $diskUsedPercent = $diskTotalBytes > 0 ? round(($diskUsedBytes / $diskTotalBytes) * 100, 1) : 0;
        $diskFreePercent = round(100 - $diskUsedPercent, 1);

        // Calculate Category Sizes
        $getDirStats = function ($relativePath) {
            $files = Storage::disk('public')->allFiles($relativePath);
            $totalBytes = 0;
            foreach ($files as $f) {
                try {
                    $totalBytes += Storage::disk('public')->size($f);
                } catch (\Throwable $e) {}
            }
            return [
                'count' => count($files),
                'bytes' => $totalBytes,
                'mb' => round($totalBytes / (1024 * 1024), 2),
            ];
        };

        $accountStats = $getDirStats('accounts');
        $enrollLegacyStats = $getDirStats('face_enrollments');
        $faceScanStats = $getDirStats('face_scans');
        $profileLegacyStats = $getDirStats('profiles');

        $categories = [
            'accounts' => [
                'name' => 'Akun Terorganisir (Baru)',
                'count' => $accountStats['count'],
                'bytes' => $accountStats['bytes'],
                'mb' => $accountStats['mb'],
                'icon' => 'fa-folder-tree',
                'color' => 'blue',
            ],
            'enrollments' => [
                'name' => 'Biometrik Wajah (Enrollments)',
                'count' => $accountStats['count'] + $enrollLegacyStats['count'],
                'bytes' => $accountStats['bytes'] + $enrollLegacyStats['bytes'],
                'mb' => round(($accountStats['bytes'] + $enrollLegacyStats['bytes']) / (1024 * 1024), 2),
                'icon' => 'fa-face-viewfinder',
                'color' => 'emerald',
            ],
            'scans' => [
                'name' => 'Snapshot Presensi Harian',
                'count' => $faceScanStats['count'],
                'bytes' => $faceScanStats['bytes'],
                'mb' => $faceScanStats['mb'],
                'icon' => 'fa-camera-rotate',
                'color' => 'indigo',
            ],
            'profiles' => [
                'name' => 'Avatar & Banner Profil',
                'count' => $profileLegacyStats['count'],
                'bytes' => $profileLegacyStats['bytes'],
                'mb' => $profileLegacyStats['mb'],
                'icon' => 'fa-images',
                'color' => 'purple',
            ],
        ];

        // Format Disk Info
        $diskInfo = [
            'total_gb' => round($diskTotalBytes / (1024 * 1024 * 1024), 1),
            'free_gb' => round($diskFreeBytes / (1024 * 1024 * 1024), 1),
            'used_gb' => round($diskUsedBytes / (1024 * 1024 * 1024), 1),
            'used_percent' => $diskUsedPercent,
            'free_percent' => $diskFreePercent,
            'mount_path' => $storagePath,
            'is_writable' => is_writable($storagePath),
            'is_mount' => is_dir($mountPath),
        ];

        // Legacy folders detector
        $hasLegacyFolders = count(Storage::disk('public')->directories('face_enrollments')) > 0;

        // Student Account File Explorer
        $query = User::where('role', 'siswa')->with('kelas');
        if ($request->filled('search')) {
            $query->where(fn($q) => $q
                ->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('nisn', 'like', '%'.$request->search.'%')
                ->orWhere('username', 'like', '%'.$request->search.'%')
            );
        }
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $students = $query->orderBy('name')->paginate(12)->withQueryString();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        // Calculate student files data for current page
        $students->getCollection()->transform(function ($student) {
            $folder = $student->getStorageFolder();
            $files = [];
            $totalBytes = 0;

            // Check Avatar
            if ($student->avatar && $student->avatar !== '0' && Storage::disk('public')->exists($student->avatar)) {
                $sz = Storage::disk('public')->size($student->avatar);
                $files[] = [
                    'type' => 'Avatar Profil',
                    'path' => $student->avatar,
                    'url' => asset('storage/' . $student->avatar),
                    'size_kb' => round($sz / 1024, 1),
                ];
                $totalBytes += $sz;
            }

            // Check Banner
            if ($student->banner && $student->banner !== '0' && Storage::disk('public')->exists($student->banner)) {
                $sz = Storage::disk('public')->size($student->banner);
                $files[] = [
                    'type' => 'Cover Banner',
                    'path' => $student->banner,
                    'url' => asset('storage/' . $student->banner),
                    'size_kb' => round($sz / 1024, 1),
                ];
                $totalBytes += $sz;
            }

            // Check Face Enrollments (new path or legacy)
            $enrollFolder = $folder . '/face_enrollments';
            if (!Storage::disk('public')->exists($enrollFolder)) {
                $enrollFolder = 'face_enrollments/' . $student->id;
            }

            if (Storage::disk('public')->exists($enrollFolder)) {
                $enrollFiles = Storage::disk('public')->files($enrollFolder);
                foreach ($enrollFiles as $ef) {
                    $sz = Storage::disk('public')->size($ef);
                    $files[] = [
                        'type' => 'Foto Referensi Wajah',
                        'path' => $ef,
                        'url' => asset('storage/' . $ef),
                        'size_kb' => round($sz / 1024, 1),
                    ];
                    $totalBytes += $sz;
                }
            }

            $student->storage_folder = $folder;
            $student->storage_files = $files;
            $student->storage_total_files = count($files);
            $student->storage_total_kb = round($totalBytes / 1024, 1);

            return $student;
        });

        return view('dashboard.storage', compact('diskInfo', 'categories', 'students', 'kelas', 'hasLegacyFolders'));
    }

    public function cleanupStorage(Request $request)
    {
        $this->adminOnly();

        $days = $request->input('days', '60');
        $deletedCount = 0;
        $freedBytes = 0;

        $dateFolders = Storage::disk('public')->directories('face_scans');
        $totalFilesBefore = count(Storage::disk('public')->allFiles('face_scans'));

        if ($days === 'all' || $days === '0') {
            // Hapus seluruh file snapshot presensi harian (reset total)
            foreach ($dateFolders as $df) {
                $files = Storage::disk('public')->allFiles($df);
                foreach ($files as $f) {
                    try {
                        $freedBytes += Storage::disk('public')->size($f);
                    } catch (\Throwable $e) {}
                    Storage::disk('public')->delete($f);
                    $deletedCount++;
                }
                Storage::disk('public')->deleteDirectory($df);
            }
            $freedMb = round($freedBytes / (1024 * 1024), 2);
            return redirect()->back()->with('success', "Pembersihan total selesai! {$deletedCount} berkas snapshot absensi harian berhasil dihapus. Ruang disk terbebas: {$freedMb} MB.");
        }

        $daysInt = max(1, (int) $days);
        $cutoffDate = now()->subDays($daysInt)->format('Y-m-d');

        foreach ($dateFolders as $df) {
            $folderName = basename($df);
            // Folder name is YYYY-MM-DD
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $folderName) && $folderName < $cutoffDate) {
                $files = Storage::disk('public')->allFiles($df);
                foreach ($files as $f) {
                    try {
                        $freedBytes += Storage::disk('public')->size($f);
                    } catch (\Throwable $e) {}
                    Storage::disk('public')->delete($f);
                    $deletedCount++;
                }
                Storage::disk('public')->deleteDirectory($df);
            }
        }

        $freedMb = round($freedBytes / (1024 * 1024), 2);

        if ($deletedCount === 0) {
            return redirect()->back()->with('info', "Tidak ada snapshot presensi yang lebih lama dari {$daysInt} hari. Saat ini berkas snapshot yang ada di server masih berumur baru ({$totalFilesBefore} berkas). Pilih opsi 'Hapus Semua Snapshot' jika ingin menghapus seluruhnya.");
        }

        return redirect()->back()->with('success', "Pembersihan selesai! {$deletedCount} berkas snapshot presensi lama (> {$daysInt} hari) berhasil dihapus. Ruang disk terbebas: {$freedMb} MB.");
    }

    public function organizeLegacyStorage()
    {
        $this->adminOnly();

        $migratedCount = 0;
        $migratedFiles = 0;

        // 1. Migrasi folder legacy face_enrollments/{id}
        $legacyFolders = Storage::disk('public')->directories('face_enrollments');
        foreach ($legacyFolders as $lf) {
            $userId = basename($lf);
            if (is_numeric($userId)) {
                $user = User::find($userId);
                if ($user) {
                    $newFolder = $user->getStorageFolder() . '/face_enrollments';
                    $files = Storage::disk('public')->files($lf);
                    foreach ($files as $file) {
                        $filename = basename($file);
                        $target = $newFolder . '/' . $filename;
                        Storage::disk('public')->put($target, Storage::disk('public')->get($file));
                        Storage::disk('public')->delete($file);
                        $migratedFiles++;
                    }
                    Storage::disk('public')->deleteDirectory($lf);
                    $migratedCount++;
                }
            }
        }

        // 2. Migrasi avatar dan banner profil legacy (profiles/)
        $usersWithPhotos = User::whereNotNull('avatar')->orWhereNotNull('banner')->get();
        foreach ($usersWithPhotos as $u) {
            $updated = false;
            if ($u->avatar && str_starts_with($u->avatar, 'profiles/avatars/')) {
                if (Storage::disk('public')->exists($u->avatar)) {
                    $ext = pathinfo($u->avatar, PATHINFO_EXTENSION) ?: 'jpg';
                    $target = $u->getStorageFolder() . '/avatar/avatar.' . $ext;
                    Storage::disk('public')->put($target, Storage::disk('public')->get($u->avatar));
                    Storage::disk('public')->delete($u->avatar);
                    $u->avatar = $target;
                    $updated = true;
                    $migratedFiles++;
                }
            }
            if ($u->banner && str_starts_with($u->banner, 'profiles/banners/')) {
                if (Storage::disk('public')->exists($u->banner)) {
                    $ext = pathinfo($u->banner, PATHINFO_EXTENSION) ?: 'jpg';
                    $target = $u->getStorageFolder() . '/banner/banner.' . $ext;
                    Storage::disk('public')->put($target, Storage::disk('public')->get($u->banner));
                    Storage::disk('public')->delete($u->banner);
                    $u->banner = $target;
                    $updated = true;
                    $migratedFiles++;
                }
            }
            if ($updated) {
                $u->save();
                $migratedCount++;
            }
        }

        if ($migratedCount === 0 && $migratedFiles === 0) {
            return redirect()->back()->with('info', "Semua berkas akun sudah rapi dalam struktur accounts/{nisn}_{username}/! Tidak ada berkas lama yang perlu dipindahkan lagi.");
        }

        return redirect()->back()->with('success', "Migrasi struktur sukses! {$migratedCount} akun ({$migratedFiles} berkas) berhasil dirapikan ke format accounts/{nisn}_{username}/.");
    }

    public function deleteStudentFile(Request $request)
    {
        $this->adminOnly();

        $path = $request->input('path');
        if (!$path || !Storage::disk('public')->exists($path)) {
            return redirect()->back()->with('error', 'Berkas tidak ditemukan di storage.');
        }

        // Keamanan: Hanya izinkan penghapusan di dalam accounts/, face_enrollments/, profiles/, atau face_scans/
        $allowedPrefixes = ['accounts/', 'face_enrollments/', 'profiles/', 'face_scans/'];
        $isAllowed = false;
        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            return redirect()->back()->with('error', 'Direktori berkas tidak diizinkan untuk dihapus.');
        }

        Storage::disk('public')->delete($path);

        // Jika yang dihapus adalah foto profil atau banner siswa, reset di DB
        $user = User::where('avatar', $path)->orWhere('banner', $path)->first();
        if ($user) {
            if ($user->avatar === $path) $user->avatar = null;
            if ($user->banner === $path) $user->banner = null;
            $user->save();
        }

        return redirect()->back()->with('success', "Berkas " . basename($path) . " berhasil dihapus dari penyimpanan.");
    }

    // =========================================================
    //  HELPER
    // =========================================================
    private function adminOnly(): void
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat mengakses fitur ini.');
    }
}
