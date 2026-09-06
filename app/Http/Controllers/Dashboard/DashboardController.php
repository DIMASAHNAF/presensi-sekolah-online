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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
                ->latest('tanggal')->take(5)->get();
            $sesiHariIni = null;
            $presensiHariIni = null;
        } else {
            // Guru
            $sesiHariIni = SesiPresensi::with(['kelas', 'mataPelajaran'])->where('guru_id', $user->id)->where('tanggal', today())->get();

            $presensiHariIni = Presensi::with(['siswa', 'sesiPresensi.kelas'])
                ->whereHas('sesiPresensi', fn ($q) => $q->where('tanggal', today())->where('guru_id', $user->id))
                ->get();

            $stats = [
                'sesi_hari_ini' => $sesiHariIni->count(),
                'sesi_aktif' => $sesiHariIni->where('is_active', true)->count(),
                'hadir_hari_ini' => $presensiHariIni->where('status', 'hadir')->count(),
                'alpa_hari_ini' => $presensiHariIni->where('status', 'alpa')->count(),
            ];
            $chartLabels = $chartHadir = $chartAlpa = [];
            $recentSesi = SesiPresensi::with(['kelas', 'mataPelajaran'])
                ->where('guru_id', $user->id)
                ->latest('tanggal')->take(5)->get();
        }

        return view('dashboard.index', compact(
            'user', 'stats', 'chartLabels', 'chartHadir', 'chartAlpa', 'recentSesi', 'sesiHariIni', 'presensiHariIni'
        ));
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

        return view('dashboard.presensi.index', compact('sesiList', 'kelas', 'mapel', 'user', 'tanggal', 'bulan', 'periode', 'jamPelajarans'));
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

        $sesi = SesiPresensi::create([
            'guru_id' => auth()->id(),
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
        $query = User::where('role', 'siswa')->with('kelas');

        if ($request->search) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('nisn', 'like', '%'.$request->search.'%')
                ->orWhere('username', 'like', '%'.$request->search.'%')
            );
        }
        if ($request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $siswaList = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('dashboard.siswa', compact('siswaList', 'kelas'));
    }

    public function storeSiswa(Request $request)
    {
        $this->adminOnly();

        $v = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'nisn' => 'required|string|digits:10|unique:users,nisn',
            'kelas_id' => 'nullable|exists:kelas,id',
            'password' => 'required|string|min:6',
        ]);

        User::create(array_merge($v, [
            'role' => 'siswa',
            'password' => Hash::make($v['password']),
        ]));

        return redirect()->route('dashboard.siswa')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function updateSiswa(Request $request, User $siswa)
    {
        $this->adminOnly();
        abort_if($siswa->role !== 'siswa', 403);

        $v = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,'.$siswa->id,
            'email' => 'nullable|email|unique:users,email,'.$siswa->id,
            'nisn' => 'required|string|digits:10|unique:users,nisn,'.$siswa->id,
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $siswa->update($v);

        return redirect()->route('dashboard.siswa')->with('success', 'Data siswa diperbarui!');
    }

    public function destroySiswa(User $siswa)
    {
        $this->adminOnly();
        abort_if($siswa->role !== 'siswa', 403);
        $siswa->delete();

        return redirect()->route('dashboard.siswa')->with('success', 'Siswa dihapus!');
    }

    public function resetFaceSiswa(User $siswa)
    {
        $this->adminOnly();
        abort_if($siswa->role !== 'siswa', 403);
        
        $siswa->update([
            'face_descriptor' => null,
            'face_enrolled_at' => null
        ]);

        return redirect()->route('dashboard.siswa')->with('success', "Wajah siswa {$siswa->name} berhasil direset!");
    }

    public function resetAllFaces()
    {
        $this->adminOnly();
        
        User::where('role', 'siswa')->update([
            'face_descriptor' => null,
            'face_enrolled_at' => null
        ]);

        return redirect()->route('dashboard.siswa')->with('success', 'Semua data wajah siswa berhasil direset. Siswa harus scan ulang!');
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

    public function storeGuru(Request $request)
    {
        $this->adminOnly();

        $v = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'nullable|email|unique:users,email',
            'nik' => 'required|string|max:20|unique:users,nik',
            'password' => 'required|string|min:6',
        ]);

        User::create(array_merge($v, [
            'role' => 'guru',
            'password' => Hash::make($v['password']),
        ]));

        return redirect()->route('dashboard.guru')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function updateGuru(Request $request, User $guru)
    {
        $this->adminOnly();
        abort_if($guru->role !== 'guru', 403);

        $v = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,'.$guru->id,
            'email' => 'nullable|email|unique:users,email,'.$guru->id,
            'nik' => 'required|string|max:20|unique:users,nik,'.$guru->id,
        ]);

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

    public function storeKelas(Request $request)
    {
        $this->adminOnly();

        $v = $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas',
            'tingkat' => 'required|in:X,XI,XII',
            'jurusan' => 'nullable|string|max:50',
        ]);

        Kelas::create($v);

        return redirect()->route('dashboard.kelas')->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function updateKelas(Request $request, Kelas $kelas)
    {
        $this->adminOnly();

        $v = $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,'.$kelas->id,
            'tingkat' => 'required|in:X,XI,XII',
            'jurusan' => 'nullable|string|max:50',
        ]);

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
    public function lokasiIndex()
    {
        $this->adminOnly();
        $setting = SchoolSetting::getSettings();
        return view('dashboard.pengaturan.lokasi', compact('setting'));
    }

    public function updateLokasi(Request $request)
    {
        $this->adminOnly();
        $request->validate([
            'school_name' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:5000',
        ]);

        $setting = SchoolSetting::getSettings();
        $setting->update([
            'school_name' => $request->school_name,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius_meters' => $request->radius_meters,
            'is_geofencing_active' => $request->has('is_geofencing_active'),
        ]);

        return redirect()->back()->with('success', 'Pengaturan lokasi sekolah & radius geofencing berhasil diperbarui.');
    }

    // =========================================================
    //  HELPER
    // =========================================================
    private function adminOnly(): void
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat mengakses fitur ini.');
    }
}
