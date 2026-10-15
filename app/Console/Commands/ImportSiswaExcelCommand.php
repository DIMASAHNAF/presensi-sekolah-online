<?php

namespace App\Console\Commands;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportSiswaExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'siswa:import-excel {--file= : Path ke file Excel} {--clean-dummy : Hapus siswa dummy yang tidak ada di Excel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import 1400+ data siswa dari file Excel Dapodik ke database presensi dengan kecepatan tinggi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->option('file') ?: public_path('NAMA SISWA-SISWI SMKN 1 BERINGIN UPDATE.xlsx');

        if (!file_exists($filePath)) {
            $this->error("File Excel tidak ditemukan di: {$filePath}");
            return 1;
        }

        $this->info("Memuat file Excel: {$filePath}...");

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        $this->info("Total baris terdeteksi: {$highestRow}. Memulai pemrosesan data kilat...");

        // Preload semua kelas & siswa yang sudah ada ke memory RAM
        $classesInDb = Kelas::all()->keyBy('nama_kelas');
        $existingUsers = User::whereNotNull('nisn')->get()->keyBy('nisn');

        $createdCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        $importedNisns = [];

        DB::beginTransaction();
        try {
            for ($row = 7; $row <= $highestRow; $row++) {
                $nama   = trim((string)$sheet->getCell('B' . $row)->getValue());
                $nisn   = trim((string)$sheet->getCell('E' . $row)->getValue());
                $nik    = trim((string)$sheet->getCell('H' . $row)->getValue());
                $email  = trim((string)$sheet->getCell('U' . $row)->getValue());
                $rombel = trim((string)$sheet->getCell('AQ' . $row)->getValue());

                if ($nama === '' || $nisn === '') {
                    $skippedCount++;
                    continue;
                }

                // Map Rombel ke Kelas DB
                $dbClassName = $this->mapRombelToDbClass($rombel);
                $kelas = $dbClassName && isset($classesInDb[$dbClassName]) ? $classesInDb[$dbClassName] : null;
                $kelasId = $kelas ? $kelas->id : null;

                $validEmail = filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
                $cleanNik = ($nik !== '' && $nik !== '-' && strlen($nik) >= 8) ? $nik : null;

                $importedNisns[] = $nisn;

                if (isset($existingUsers[$nisn])) {
                    // Update yang sudah ada tanpa menimpa face biometrics
                    $user = $existingUsers[$nisn];
                    $user->name     = $nama;
                    $user->nik      = $cleanNik ?? $user->nik;
                    $user->kelas_id = $kelasId ?? $user->kelas_id;
                    $user->email    = $validEmail ?? $user->email;
                    $user->save();
                    $updatedCount++;
                } else {
                    // Buat akun baru, default password = NISN
                    $hash = password_hash($nisn, PASSWORD_BCRYPT, ['cost' => 8]);
                    User::create([
                        'name'       => $nama,
                        'username'   => $nisn,
                        'nisn'       => $nisn,
                        'nik'        => $cleanNik,
                        'email'      => $validEmail,
                        'password'   => $hash,
                        'role'       => 'siswa',
                        'kelas_id'   => $kelasId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $createdCount++;
                }
            }

            // Hapus dummy siswa percobaan jika flag aktif
            if ($this->option('clean-dummy')) {
                $deletedDummy = User::where('role', 'siswa')
                    ->whereNull('face_descriptor')
                    ->whereNotIn('nisn', $importedNisns)
                    ->delete();
                $this->warn("Dihapus {$deletedDummy} siswa dummy percobaan yang tidak ada di Excel.");
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Gagal melakukan import: " . $e->getMessage());
            return 1;
        }

        $this->info("==========================================");
        $this->info("🎉 IMPORT SELESAI DENGAN SUKSES!");
        $this->info("- Siswa Baru Dibuat    : {$createdCount}");
        $this->info("- Siswa Diperbarui     : {$updatedCount}");
        $this->info("- Baris Dilewati       : {$skippedCount}");
        $this->info("- Total Siswa di Sistem: " . User::where('role', 'siswa')->count());
        $this->info("==========================================");

        return 0;
    }

    /**
     * Mapping nama rombel dari Dapodik Excel ke nama Kelas di Database.
     */
    protected function mapRombelToDbClass(string $rombel): ?string
    {
        $r = trim(strtoupper($rombel));

        $tingkat = '';
        $rest = '';
        if (str_starts_with($r, '10 ')) {
            $tingkat = 'X';
            $rest = substr($r, 3);
        } elseif (str_starts_with($r, '11 ')) {
            $tingkat = 'XI';
            $rest = substr($r, 3);
        } elseif (str_starts_with($r, '12 ')) {
            $tingkat = 'XII';
            $rest = substr($r, 3);
        } else {
            return null;
        }

        $rest = trim($rest);

        if (preg_match('/^TKJ\s*(\d+)$/', $rest, $m)) {
            return "{$tingkat} TJKT {$m[1]}";
        }
        if (preg_match('/^RPL\s*(\d+)$/', $rest, $m)) {
            return "{$tingkat} PPLG {$m[1]}";
        }
        if (preg_match('/^PH\s*(\d+)$/', $rest, $m)) {
            return "{$tingkat} Perhotelan {$m[1]}";
        }
        if ($rest === 'KC') {
            return "{$tingkat} Kecantikan 1";
        }
        if (preg_match('/^KULINER\s*(\d+)$/', $rest, $m)) {
            return "{$tingkat} Kuliner {$m[1]}";
        }
        if (preg_match('/^BUSANA\s*(\d+)$/', $rest, $m)) {
            return "{$tingkat} Busana {$m[1]}";
        }
        if ($rest === 'ULW') {
            return "{$tingkat} ULW 1";
        }

        return null;
    }
}
