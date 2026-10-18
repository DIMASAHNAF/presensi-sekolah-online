<?php

namespace App\Console\Commands;

use App\Models\Kelas;
use App\Models\JadwalPelajaran;
use App\Models\SesiPresensi;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateDailySessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'presensi:generate-daily-sessions {--hari= : Paksa hari tertentu (senin, selasa, rabu, kamis, jumat)} {--force : Timpa sesi yang sudah ada}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SesiPresensi harian otomatis untuk seluruh kelas SMKN 1 Beringin pada jam 06:00 WIB sesuai jadwal pelajaran';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inputHari = $this->option('hari');
        if ($inputHari) {
            $hari = strtolower($inputHari);
        } else {
            $dayOfWeek = now()->dayOfWeek; // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            if ($dayOfWeek === 0 || $dayOfWeek === 6) {
                $this->info("Hari ini adalah akhir pekan (hari libur). Sesi presensi reguler tidak di-generate.");
                return 0;
            }

            $mapHari = [
                1 => 'senin',
                2 => 'selasa',
                3 => 'rabu',
                4 => 'kamis',
                5 => 'jumat',
            ];
            $hari = $mapHari[$dayOfWeek] ?? 'senin';
        }

        $this->info("=== GENERATE SESI PRESENSI OTOMATIS: HARI " . strtoupper($hari) . " (" . today()->format('d-m-Y') . ") ===");

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $fallbackGuruId = User::where('role', 'guru')->first()?->id ?? User::where('role', 'admin')->first()?->id ?? 1;

        $created = 0;
        $skipped = 0;

        foreach ($kelasList as $kelas) {
            // Cek apakah sudah ada sesi hari ini
            $existingSesi = SesiPresensi::where('kelas_id', $kelas->id)
                ->where('tanggal', today())
                ->where('tipe', 'kelas')
                ->first();

            if ($existingSesi && !$this->option('force')) {
                $skipped++;
                continue;
            }

            // Ambil jadwal pelajaran pertama hari ini untuk kelas ini (kecuali upacara)
            $jadwalPertama = JadwalPelajaran::where('kelas_id', $kelas->id)
                ->where('hari', $hari)
                ->where('kode_guru', '!=', 'SEKOLAH')
                ->orderBy('jam_ke')
                ->first();

            // Jika semua slot kode_guru == SEKOLAH (misal hari upacara/kegiatan khusus), ambil slot jam pertama
            if (!$jadwalPertama) {
                $jadwalPertama = JadwalPelajaran::where('kelas_id', $kelas->id)
                    ->where('hari', $hari)
                    ->orderBy('jam_ke')
                    ->first();
            }

            $mapelId = $jadwalPertama?->mapel_id;
            $namaMapel = $jadwalPertama?->nama_mapel ?? 'Presensi Pagi';
            $jamLabel = $jadwalPertama ? "{$namaMapel} (Jam ke-{$jadwalPertama->jam_ke} • {$jadwalPertama->waktu})" : "Presensi Pagi (07.00 - 08.00)";
            $guruId = $jadwalPertama?->guru_id ?? $fallbackGuruId;

            if ($existingSesi && $this->option('force')) {
                $existingSesi->update([
                    'guru_id'       => $guruId,
                    'mapel_id'      => $mapelId,
                    'jam_pelajaran' => $jamLabel,
                    'is_active'     => true,
                ]);
                $sesi = $existingSesi;
                $created++;
            } elseif (!$existingSesi) {
                $sesi = SesiPresensi::create([
                    'kelas_id'      => $kelas->id,
                    'tanggal'       => today(),
                    'tipe'          => 'kelas',
                    'guru_id'       => $guruId,
                    'mapel_id'      => $mapelId,
                    'jam_pelajaran' => $jamLabel,
                    'barcode_token' => Str::random(32),
                    'is_active'     => true,
                ]);
                $created++;
            } else {
                $sesi = $existingSesi;
            }

            // Otomatis daftarkan seluruh siswa di kelas ini ke dalam sesi presensi (default: alpa)
            if ($sesi) {
                $siswaList = User::where('role', 'siswa')->where('kelas_id', $kelas->id)->get(['id']);
                foreach ($siswaList as $siswa) {
                    Presensi::firstOrCreate(
                        [
                            'sesi_presensi_id' => $sesi->id,
                            'siswa_id'         => $siswa->id,
                        ],
                        [
                            'status' => 'alpa',
                        ]
                    );
                }
            }
        }

        $this->info("Berhasil men-generate {$created} sesi presensi kelas untuk hari " . strtoupper($hari) . ".");
        if ($skipped > 0) {
            $this->comment("Dilewati {$skipped} kelas karena sesi hari ini sudah aktif (gunakan --force untuk memperbarui).");
        }

        return 0;
    }
}
