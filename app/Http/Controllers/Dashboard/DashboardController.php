<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\SesiAbsensi;
use App\Models\User;
use App\Models\LogAbsensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
                'siswa'         => User::where('role', 'siswa')->count(),
                'guru'          => User::where('role', 'guru')->count(),
                'kelas'         => Kelas::count(),
                'hadir_hari_ini'=> Absensi::where('status', 'hadir')
                    ->whereHas('sesiAbsensi', fn ($q) => $q->where('tanggal', today()))
                    ->count(),
            ];

            // Chart: 7 hari terakhir
            $chartLabels = [];
            $chartHadir  = [];
            $chartAlpa   = [];
            for ($i = 6; $i >= 0; $i--) {
                $date          = Carbon::today()->subDays($i);
                $chartLabels[] = $date->format('d/m');
                $chartHadir[]  = Absensi::where('status', 'hadir')
                    ->whereHas('sesiAbsensi', fn ($q) => $q->where('tanggal', $date))->count();
                $chartAlpa[]   = Absensi::where('status', 'alpa')
                    ->whereHas('sesiAbsensi', fn ($q) => $q->where('tanggal', $date))->count();
            }

            $recentSesi = SesiAbsensi::with(['kelas', 'guru', 'mataPelajaran'])
                ->latest('tanggal')->take(5)->get();
            $sesiHariIni = null;
            $absensiHariIni = null;
        } else {
            // Guru
            $sesiHariIni = SesiAbsensi::with(['kelas', 'mataPelajaran'])->where('guru_id', $user->id)->where('tanggal', today())->get();
            
            $absensiHariIni = Absensi::with(['siswa', 'sesiAbsensi.kelas'])
                ->whereHas('sesiAbsensi', fn ($q) => $q->where('tanggal', today())->where('guru_id', $user->id))
                ->get();

            $stats = [
                'sesi_hari_ini' => $sesiHariIni->count(),
                'sesi_aktif'    => $sesiHariIni->where('is_active', true)->count(),
                'hadir_hari_ini'=> $absensiHariIni->where('status', 'hadir')->count(),
                'alpa_hari_ini' => $absensiHariIni->where('status', 'alpa')->count(),
            ];
            $chartLabels = $chartHadir = $chartAlpa = [];
            $recentSesi = SesiAbsensi::with(['kelas', 'mataPelajaran'])
                ->where('guru_id', $user->id)
                ->latest('tanggal')->take(5)->get();
        }

        return view('dashboard.index', compact(
            'user', 'stats', 'chartLabels', 'chartHadir', 'chartAlpa', 'recentSesi', 'sesiHariIni', 'absensiHariIni'
        ));
    }

    // =========================================================
    //  ABSENSI — LIST SESI
    // =========================================================
    public function absensiIndex(Request $request)
    {
        $user  = auth()->user();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mapel = \App\Models\MataPelajaran::orderBy('nama_mapel')->get();

        $query = SesiAbsensi::with(['kelas', 'guru', 'mataPelajaran'])
            ->withCount(['absensi as hadir_count' => fn ($q) => $q->where('status', 'hadir')])
            ->withCount(['absensi as total_count'])
            ->latest('tanggal');

        // Guru dan Admin bisa melihat semua sesi (agar guru lain bisa mengedit kehadiran di hari yang sama)
        // Hapus limitasi guru_id = user->id
        if ($request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->mapel_id) {
            $query->where('mapel_id', $request->mapel_id);
        }
        // Default to today if no date provided
        $tanggal = $request->tanggal ?? today()->toDateString();
        $query->where('tanggal', $tanggal);

        $sesiList = $query->paginate(15)->withQueryString();

        return view('dashboard.absensi.index', compact('sesiList', 'kelas', 'mapel', 'user', 'tanggal'));
    }

    // =========================================================
    //  ABSENSI — BUAT SESI BARU
    // =========================================================
    public function storeSesi(Request $request)
    {
        $request->validate([
            'kelas_id'      => 'required|exists:kelas,id',
            'tanggal'       => 'required|date',
            'mapel_id'      => 'nullable|exists:mata_pelajarans,id',
            'jam_pelajaran' => 'nullable|string|max:50',
        ]);

        // Cek apakah di kelas, tanggal, mapel, dan jam tersebut sudah ada sesi yang sama
        $existing = SesiAbsensi::where('kelas_id', $request->kelas_id)
            ->where('tanggal', $request->tanggal)
            ->where('mapel_id', $request->mapel_id)
            ->where('jam_pelajaran', $request->jam_pelajaran)
            ->first();

        if ($existing) {
            return back()->with('error', 'Sesi absensi untuk kelas, mata pelajaran, dan jam ini sudah ada!');
        }

        $sesi = SesiAbsensi::create([
            'guru_id'       => auth()->id(),
            'kelas_id'      => $request->kelas_id,
            'mapel_id'      => $request->mapel_id,
            'jam_pelajaran' => $request->jam_pelajaran,
            'tanggal'       => $request->tanggal,
            'barcode_token' => Str::random(32),
            'is_active'     => true, // Selalu aktif agar barcode bisa langsung discan/ditampilkan
        ]);

        // Cari sesi pagi (sesi utama wali kelas) untuk kelas dan tanggal yang sama jika ini sesi mapel
        $sesiPagi = null;
        if (!empty($request->mapel_id)) {
            $sesiPagi = SesiAbsensi::with('absensi')
                ->where('kelas_id', $request->kelas_id)
                ->where('tanggal', $request->tanggal)
                ->whereNull('mapel_id')
                ->first();
        }

        // Create absensi records for all students in this kelas
        $siswaList = User::where('role', 'siswa')->where('kelas_id', $request->kelas_id)->get();
        
        foreach ($siswaList as $siswa) {
            $status = 'alpa'; // default

            // Jika ada sesi pagi, copy status dari sesi pagi
            if ($sesiPagi) {
                $absenPagi = $sesiPagi->absensi->where('siswa_id', $siswa->id)->first();
                if ($absenPagi) {
                    $status = $absenPagi->status;
                }
            }

            Absensi::create([
                'sesi_absensi_id' => $sesi->id,
                'siswa_id'        => $siswa->id,
                'status'          => $status,
            ]);
        }

        return redirect()->route('dashboard.absensi.detail', $sesi)
            ->with('success', 'Sesi absensi berhasil dibuat!');
    }

    // =========================================================
    //  ABSENSI — DETAIL SESI
    // =========================================================
    public function absensiDetail(SesiAbsensi $sesiAbsensi)
    {
        $sesiAbsensi->load(['kelas', 'guru', 'mataPelajaran', 'absensi.siswa', 'absensi.logAbsensi.guru']);
        return view('dashboard.absensi.detail', compact('sesiAbsensi'));
    }

    public function exportPdf(SesiAbsensi $sesiAbsensi)
    {
        $sesiAbsensi->load(['kelas', 'guru', 'mataPelajaran', 'absensi.siswa']);
        return view('dashboard.absensi.print', compact('sesiAbsensi'));
    }

    public function exportPdfHarian(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal'  => 'required|date',
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);
        $tanggal = Carbon::parse($request->tanggal);

        // Ambil semua sesi pada tanggal dan kelas tersebut, urutkan: Sesi Pagi (tanpa mapel) dulu, lalu Sesi Mapel
        $sesiList = SesiAbsensi::with(['mataPelajaran', 'absensi.siswa'])
            ->where('kelas_id', $kelas->id)
            ->where('tanggal', $tanggal->toDateString())
            ->orderByRaw('mapel_id IS NOT NULL, created_at ASC')
            ->get();

        if ($sesiList->isEmpty()) {
            return back()->with('error', 'Tidak ada sesi absensi pada tanggal tersebut.');
        }

        // Ambil daftar siswa unik dari semua sesi (berjaga-jaga jika ada siswa pindahan)
        // Biasanya ambil dari tabel User langsung
        $siswaList = User::where('role', 'siswa')->where('kelas_id', $kelas->id)->orderBy('name')->get();

        return view('dashboard.absensi.print-harian', compact('kelas', 'tanggal', 'sesiList', 'siswaList'));
    }

    /**
     * Live Polling Data Presensi Sesi (JSON) untuk update realtime tanpa reload
     */
    public function absensiLiveJson(SesiAbsensi $sesiAbsensi)
    {
        $sesiAbsensi->load(['absensi.siswa', 'absensi.logAbsensi.guru']);

        $stats = [
            'total' => $sesiAbsensi->absensi->count(),
            'hadir' => $sesiAbsensi->absensi->where('status', 'hadir')->count(),
            'izin'  => $sesiAbsensi->absensi->where('status', 'izin')->count(),
            'sakit' => $sesiAbsensi->absensi->where('status', 'sakit')->count(),
            'alpa'  => $sesiAbsensi->absensi->where('status', 'alpa')->count(),
        ];

        $items = $sesiAbsensi->absensi->sortBy('siswa.name')->values()->map(function ($absen) {
            return [
                'id'          => $absen->id,
                'siswa_id'    => $absen->siswa_id,
                'name'        => $absen->siswa->name,
                'nisn'        => $absen->siswa->nisn ?: '-',
                'status'      => $absen->status,
                'label'       => $absen->labelStatus(),
                'keterangan'  => $absen->keterangan ?: '',
                'waktu_scan'  => $absen->waktu_scan ? $absen->waktu_scan->format('H:i') : null,
                'log_count'   => $absen->logAbsensi->count(),
            ];
        });

        return response()->json([
            'success'   => true,
            'is_active' => $sesiAbsensi->is_active,
            'stats'     => $stats,
            'items'     => $items,
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
            'bulan'    => 'required|date_format:Y-m',
        ]);

        $kelas        = Kelas::findOrFail($request->kelas_id);
        $bulanDate    = Carbon::parse($request->bulan . '-01');
        $startOfMonth = $bulanDate->copy()->startOfMonth();
        $endOfMonth   = $bulanDate->copy()->endOfMonth();

        // Ambil semua sesi kelas pada bulan tersebut (prioritaskan sesi kelas/pagi jika ada)
        $sesiList = SesiAbsensi::with(['absensi.siswa'])
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
            ->map(fn($t) => is_object($t) ? $t->format('Y-m-d') : substr($t, 0, 10))
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
            foreach ($sesi->absensi as $absen) {
                if (!isset($matrix[$absen->siswa_id][$tglStr]) || $sesi->mapel_id === null) {
                    $matrix[$absen->siswa_id][$tglStr] = $absen->status;
                }
            }
        }

        // Daftar semua hari dalam bulan ini
        $daysInMonth = $bulanDate->daysInMonth;
        $hariList    = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currentDate = $bulanDate->copy()->day($d);
            $tglStr      = $currentDate->format('Y-m-d');
            $hariList[]  = [
                'date'        => $tglStr,
                'tgl'         => $d,
                'nama'        => $currentDate->translatedFormat('D'),
                'libur'       => in_array($currentDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]),
                'has_session' => in_array($tglStr, $activeDates),
            ];
        }

        return view('dashboard.absensi.print-bulanan-kelas', compact(
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
            'bulan'    => 'required|date_format:Y-m',
        ]);

        $kelas     = Kelas::findOrFail($request->kelas_id);
        $mapel     = \App\Models\MataPelajaran::findOrFail($request->mapel_id);
        $bulanDate = Carbon::parse($request->bulan . '-01');
        $startOfMonth = $bulanDate->copy()->startOfMonth();
        $endOfMonth   = $bulanDate->copy()->endOfMonth();

        // Ambil semua sesi mapel pada bulan tersebut
        $sesiList = SesiAbsensi::with(['absensi.siswa', 'guru'])
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
        $guruNik  = optional($sesiList->first()?->guru)->nik ?? auth()->user()->nik ?? '-';

        // Matrix: [siswa_id][sesi_id] = status
        $matrix = [];
        foreach ($siswaList as $siswa) {
            $matrix[$siswa->id] = [];
        }
        foreach ($sesiList as $sesi) {
            foreach ($sesi->absensi as $absen) {
                $matrix[$absen->siswa_id][$sesi->id] = $absen->status;
            }
        }

        return view('dashboard.absensi.print-bulanan-mapel', compact(
            'kelas', 'mapel', 'bulanDate', 'sesiList', 'siswaList', 'matrix', 'guruNama', 'guruNik'
        ));
    }

    public function closeSesi(SesiAbsensi $sesiAbsensi)
    {
        $sesiAbsensi->update(['is_active' => !$sesiAbsensi->is_active]);
        $statusText = $sesiAbsensi->is_active ? 'diaktifkan kembali. Barcode sudah bisa discan.' : 'ditutup. Barcode dinonaktifkan.';
        return back()->with('success', 'Sesi absensi berhasil ' . $statusText);
    }

    public function resetAbsenSesi(SesiAbsensi $sesiAbsensi)
    {
        $this->adminOnly();
        
        // Hapus log perubahan terkait sesi ini
        \App\Models\LogAbsensi::whereHas('absensi', function($q) use ($sesiAbsensi) {
            $q->where('sesi_absensi_id', $sesiAbsensi->id);
        })->delete();

        // Reset semua absensi kembali ke alpa
        $sesiAbsensi->absensi()->update([
            'status' => 'alpa',
            'waktu_scan' => null,
            'keterangan' => null
        ]);

        return back()->with('success', 'Riwayat absensi untuk sesi kelas ini telah di-reset kembali ke Alpa.');
    }

    public function deleteAllSesi()
    {
        $this->adminOnly();
        
        // Disable foreign key checks temporarily if needed, but Eloquent delete works if we fetch or just DB::statement
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\LogAbsensi::truncate();
        \App\Models\Absensi::truncate();
        \App\Models\SesiAbsensi::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return back()->with('success', 'Semua riwayat sesi absensi berhasil dihapus permanen.');
    }

    // =========================================================
    //  ABSENSI — UPDATE STATUS RECORD
    // =========================================================
    public function updateRecord(Request $request, Absensi $absensi)
    {
        $request->validate([
            'status'     => 'required|in:hadir,izin,sakit,alpa',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $statusSebelumnya = $absensi->status;
        $statusBaru = $request->status;

        // Cek jika ada perubahan
        if ($statusSebelumnya !== $statusBaru || $absensi->keterangan !== $request->keterangan) {
            
            // Catat log
            \App\Models\LogAbsensi::create([
                'absensi_id'        => $absensi->id,
                'guru_id'           => auth()->id(),
                'status_sebelumnya' => $statusSebelumnya,
                'status_baru'       => $statusBaru,
                'keterangan'        => $request->keterangan,
            ]);

            // Update absensi
            $absensi->update([
                'status'     => $statusBaru,
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
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username',
            'email'    => 'nullable|email|unique:users,email',
            'nisn'     => 'required|string|digits:10|unique:users,nisn',
            'kelas_id' => 'nullable|exists:kelas,id',
            'password' => 'required|string|min:6',
        ]);

        User::create(array_merge($v, [
            'role'     => 'siswa',
            'password' => Hash::make($v['password']),
        ]));

        return redirect()->route('dashboard.siswa')->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function updateSiswa(Request $request, User $siswa)
    {
        $this->adminOnly();
        abort_if($siswa->role !== 'siswa', 403);

        $v = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|alpha_dash|unique:users,username,'.$siswa->id,
            'email'    => 'nullable|email|unique:users,email,'.$siswa->id,
            'nisn'     => 'required|string|digits:10|unique:users,nisn,'.$siswa->id,
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
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email'    => 'nullable|email|unique:users,email',
            'nik'      => 'required|string|max:20|unique:users,nik',
            'password' => 'required|string|min:6',
        ]);

        User::create(array_merge($v, [
            'role'     => 'guru',
            'password' => Hash::make($v['password']),
        ]));

        return redirect()->route('dashboard.guru')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function updateGuru(Request $request, User $guru)
    {
        $this->adminOnly();
        abort_if($guru->role !== 'guru', 403);

        $v = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,'.$guru->id,
            'email'    => 'nullable|email|unique:users,email,'.$guru->id,
            'nik'      => 'required|string|max:20|unique:users,nik,'.$guru->id,
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
            'tingkat'    => 'required|in:X,XI,XII',
            'jurusan'    => 'nullable|string|max:50',
        ]);

        Kelas::create($v);

        return redirect()->route('dashboard.kelas')->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function updateKelas(Request $request, Kelas $kelas)
    {
        $this->adminOnly();

        $v = $request->validate([
            'nama_kelas' => 'required|string|max:50|unique:kelas,nama_kelas,'.$kelas->id,
            'tingkat'    => 'required|in:X,XI,XII',
            'jurusan'    => 'nullable|string|max:50',
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
    public function logAbsensiIndex(Request $request)
    {
        $this->adminOnly();
        $query = LogAbsensi::with([
            'absensi.siswa',
            'absensi.sesiAbsensi.kelas',
            'absensi.sesiAbsensi.mataPelajaran',
            'guru'
        ])->latest();

        if ($request->kelas_id) {
            $query->whereHas('absensi.sesiAbsensi', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->mapel_id) {
            $query->whereHas('absensi.sesiAbsensi', function ($q) use ($request) {
                $q->where('mapel_id', $request->mapel_id);
            });
        }

        if ($request->guru_id) {
            $query->where('guru_id', $request->guru_id);
        }

        if ($request->tanggal) {
            $query->whereHas('absensi.sesiAbsensi', function ($q) use ($request) {
                $q->where('tanggal', $request->tanggal);
            });
        }

        $logs  = $query->paginate(20)->withQueryString();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mapel = \App\Models\MataPelajaran::orderBy('nama_mapel')->get();
        $guruList = User::where('role', 'guru')->orderBy('name')->get();

        return view('dashboard.log', compact('logs', 'kelas', 'mapel', 'guruList'));
    }

    public function resetSesi()
    {
        $this->adminOnly();
        // Akhiri semua sesi aktif (atau hapus yang tidak digunakan)
        // Kita cukup set is_active = false untuk semua sesi yang masih aktif
        SesiAbsensi::where('is_active', true)->update(['is_active' => false]);
        
        return redirect()->back()->with('success', 'Semua riwayat kelas/sesi yang aktif telah direset (ditutup).');
    }

    // =========================================================
    //  HELPER
    // =========================================================
    private function adminOnly(): void
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat mengakses fitur ini.');
    }
}
