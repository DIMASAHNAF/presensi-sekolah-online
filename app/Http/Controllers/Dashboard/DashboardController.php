<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\SesiAbsensi;
use App\Models\User;
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

            $recentSesi = SesiAbsensi::with(['kelas', 'guru'])
                ->latest('tanggal')->take(5)->get();
        } else {
            // Guru
            $stats = [
                'total_sesi'    => SesiAbsensi::where('guru_id', $user->id)->count(),
                'sesi_aktif'    => SesiAbsensi::where('guru_id', $user->id)->where('is_active', true)->count(),
                'hadir_hari_ini'=> Absensi::where('status', 'hadir')
                    ->whereHas('sesiAbsensi', fn ($q) => $q->where('tanggal', today())->where('guru_id', $user->id))
                    ->count(),
                'alpa_hari_ini' => Absensi::where('status', 'alpa')
                    ->whereHas('sesiAbsensi', fn ($q) => $q->where('tanggal', today())->where('guru_id', $user->id))
                    ->count(),
            ];
            $chartLabels = $chartHadir = $chartAlpa = [];
            $recentSesi = SesiAbsensi::with(['kelas'])
                ->where('guru_id', $user->id)
                ->latest('tanggal')->take(5)->get();
        }

        return view('dashboard.index', compact(
            'user', 'stats', 'chartLabels', 'chartHadir', 'chartAlpa', 'recentSesi'
        ));
    }

    // =========================================================
    //  ABSENSI — LIST SESI
    // =========================================================
    public function absensiIndex(Request $request)
    {
        $user  = auth()->user();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        $query = SesiAbsensi::with(['kelas', 'guru'])
            ->withCount(['absensi as hadir_count' => fn ($q) => $q->where('status', 'hadir')])
            ->withCount(['absensi as total_count'])
            ->latest('tanggal');

        // Guru dan Admin bisa melihat semua sesi (agar guru lain bisa mengedit kehadiran di hari yang sama)
        // Hapus limitasi guru_id = user->id
        if ($request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->tanggal) {
            $query->where('tanggal', $request->tanggal);
        }

        $sesiList = $query->paginate(15)->withQueryString();

        return view('dashboard.absensi.index', compact('sesiList', 'kelas', 'user'));
    }

    // =========================================================
    //  ABSENSI — BUAT SESI BARU
    // =========================================================
    public function storeSesi(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tanggal'  => 'required|date',
        ]);

        $existing = SesiAbsensi::where('kelas_id', $request->kelas_id)
            ->where('tanggal', $request->tanggal)->first();

        if ($existing) {
            return back()->with('error', 'Sesi absensi untuk kelas dan tanggal ini sudah ada!');
        }

        $sesi = SesiAbsensi::create([
            'guru_id'       => auth()->id(),
            'kelas_id'      => $request->kelas_id,
            'tanggal'       => $request->tanggal,
            'barcode_token' => Str::random(32),
            'is_active'     => true,
        ]);

        // Auto-create alpa records for all students in this kelas
        User::where('role', 'siswa')->where('kelas_id', $request->kelas_id)
            ->each(function ($siswa) use ($sesi) {
                Absensi::create([
                    'sesi_absensi_id' => $sesi->id,
                    'siswa_id'        => $siswa->id,
                    'status'          => 'alpa',
                ]);
            });

        return redirect()->route('dashboard.absensi.detail', $sesi)
            ->with('success', 'Sesi absensi berhasil dibuat!');
    }

    // =========================================================
    //  ABSENSI — DETAIL SESI
    // =========================================================
    public function absensiDetail(SesiAbsensi $sesiAbsensi)
    {
        $sesiAbsensi->load(['kelas', 'guru', 'absensi.siswa', 'absensi.logAbsensi.guru']);
        return view('dashboard.absensi.detail', compact('sesiAbsensi'));
    }

    public function closeSesi(SesiAbsensi $sesiAbsensi)
    {
        $sesiAbsensi->update(['is_active' => false]);
        return back()->with('success', 'Sesi berhasil ditutup. Barcode tidak bisa di-scan lagi.');
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
    //  HELPER
    // =========================================================
    private function adminOnly(): void
    {
        abort_if(! auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat mengakses fitur ini.');
    }
}
