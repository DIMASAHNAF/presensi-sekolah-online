<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\IzinSiswa;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IzinSiswaController extends Controller
{
    /**
     * Tampilkan daftar perizinan & sakit siswa (Buku Izin & Sakit)
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = IzinSiswa::with(['siswa', 'kelas', 'diajukanOleh'])->latest('id');

        // Jika user adalah wali kelas, prioritaskan filter kelas binaannya jika belum ada filter
        $kelasBinaan = null;
        if ($user->isWaliKelas()) {
            $kelasBinaan = $user->kelasBinaan;
            if ($kelasBinaan && !$request->has('kelas_id')) {
                $query->where('kelas_id', $kelasBinaan->id);
            }
        }

        // Filter Kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter Jenis (sakit / izin)
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter Tanggal
        if ($request->filled('tanggal')) {
            $query->activeOnDate($request->tanggal);
        }

        // Filter Bulan
        if ($request->filled('bulan')) {
            $query->where(function ($q) use ($request) {
                $q->where('tanggal_mulai', 'like', $request->bulan . '%')
                  ->orWhere('tanggal_selesai', 'like', $request->bulan . '%');
            });
        }

        // Filter Pencarian Siswa
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('siswa', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $perizinanList = $query->paginate(15)->withQueryString();

        // Data pendukung filter & form
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        // List siswa untuk form pengajuan: jika walikelas, urutkan rombel binaannya
        if ($user->isWaliKelas() && $kelasBinaan) {
            $siswaList = User::where('role', 'siswa')
                ->where('kelas_id', $kelasBinaan->id)
                ->orderBy('name')
                ->get(['id', 'name', 'nisn', 'kelas_id']);
        } else {
            $siswaList = User::where('role', 'siswa')
                ->with('kelas')
                ->orderBy('name')
                ->get(['id', 'name', 'nisn', 'kelas_id']);
        }

        // Ringkasan statistik hari ini
        $stats = [
            'total_izin' => IzinSiswa::where('jenis', 'izin')->activeOnDate(today())->count(),
            'total_sakit' => IzinSiswa::where('jenis', 'sakit')->activeOnDate(today())->count(),
            'surat_terlampir' => IzinSiswa::whereNotNull('surat_file')->count(),
            'total_riwayat' => IzinSiswa::count(),
        ];

        return view('dashboard.izin.index', compact('perizinanList', 'kelasList', 'siswaList', 'stats', 'kelasBinaan'));
    }

    /**
     * Simpan surat izin/sakit baru & sinkronkan otomatis ke sesi presensi
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id'        => 'required|exists:users,id',
            'jenis'           => 'required|in:sakit,izin',
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'nullable|string|max:500',
            'surat_file'      => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], [
            'siswa_id.required'              => 'Pilih siswa terlebih dahulu.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai izin tidak boleh sebelum tanggal mulai.',
            'surat_file.max'                 => 'Ukuran berkas surat maksimal 5 MB.',
            'surat_file.mimes'               => 'Format berkas surat wajib JPG, PNG, WEBP, atau PDF.',
        ]);

        $siswa = User::findOrFail($request->siswa_id);
        $kelasId = $siswa->kelas_id;

        if (!$kelasId) {
            return back()->with('error', 'Siswa ini belum terdaftar di kelas mana pun.');
        }

        // Upload file jika ada
        $filePath = null;
        if ($request->hasFile('surat_file')) {
            $file = $request->file('surat_file');
            $yearMonth = date('Y-m');
            $filename = 'surat_' . $siswa->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs("surat_izin/{$yearMonth}", $filename, 'public');
        }

        // Simpan data izin
        $izin = IzinSiswa::create([
            'siswa_id'        => $siswa->id,
            'kelas_id'        => $kelasId,
            'diajukan_oleh'   => auth()->id(),
            'jenis'           => $request->jenis,
            'tanggal_mulai'   => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan'      => $request->keterangan,
            'surat_file'      => $filePath,
            'status'          => 'disetujui',
        ]);

        // SINKRONISASI OTOMATIS:
        // Cari sesi presensi yang sudah ada di rentang tanggal tersebut untuk kelas siswa ini
        $sesiTerkait = SesiPresensi::where('kelas_id', $kelasId)
            ->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai])
            ->get();

        $syncedCount = 0;
        foreach ($sesiTerkait as $sesi) {
            $presensi = Presensi::firstOrNew([
                'sesi_presensi_id' => $sesi->id,
                'siswa_id'         => $siswa->id,
            ]);

            // Update jika belum hadir
            if ($presensi->status !== 'hadir') {
                $presensi->status = $request->jenis;
                $presensi->keterangan = $request->keterangan ?: "Dispensasi {$request->jenis} (Surat diverifikasi)";
                $presensi->surat_file = $filePath;
                $presensi->izin_siswa_id = $izin->id;
                $presensi->override_by = auth()->id();
                $presensi->override_note = "Disinkronkan otomatis dari Buku Izin ({$izin->jenis})";
                $presensi->save();
                $syncedCount++;
            }
        }

        $syncMsg = $syncedCount > 0
            ? " dan langsung disinkronkan ke {$syncedCount} sesi presensi terkait."
            : ".";

        return redirect()->route('dashboard.izin')
            ->with('success', "Surat {$request->jenis} untuk {$siswa->name} berhasil dicatat{$syncMsg}");
    }

    /**
     * Hapus berkas perizinan
     */
    public function destroy(IzinSiswa $izin)
    {
        $user = auth()->user();

        // Otorisasi: hanya admin, wali kelas kelas bersangkutan, atau penginput yang boleh hapus
        if (!$user->isAdmin() && $izin->diajukan_oleh !== $user->id) {
            if (!$user->isWaliKelas() || $user->kelasBinaan?->id !== $izin->kelas_id) {
                abort(403, 'Anda tidak memiliki hak untuk menghapus data izin ini.');
            }
        }

        // Hapus file fisik jika ada
        if ($izin->surat_file && Storage::disk('public')->exists($izin->surat_file)) {
            Storage::disk('public')->delete($izin->surat_file);
        }

        $namaSiswa = $izin->siswa->name ?? 'Siswa';
        $izin->delete();

        return redirect()->route('dashboard.izin')
            ->with('success', "Data perizinan untuk {$namaSiswa} berhasil dihapus.");
    }
}
