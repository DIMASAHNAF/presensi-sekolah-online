<?php

namespace App\Console\Commands;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SyncGuruRosterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-guru-roster';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync wali kelas, metadata guru dari Excel Dapodik, dan link roster jadwal pelajaran';

    public function handle(): int
    {
        $this->info('=== 1. SYNC WALI KELAS DARI SK PEMBAGIAN TUGAS 2026 ===');

        $waliKelasMapping = [
            'XII TJKT 1' => 1509, // Lailan Rinsiyani, S.Pd
            'XII TJKT 2' => 1521, // Sabarina Br. Munthe, S.Kom
            'XII TJKT 3' => 1479, // Jaria, M.Pd
            'XII PPLG 1' => 1516, // Nur Azizah Br. Tambunan, S.Pd
            'XII PPLG 2' => 1519, // Rifka Fitri Anggina, S.Pd
            'XII PPLG 3' => 1554, // Vevy Sisnia, S.Kom
            'XII Perhotelan 1' => 1503, // Ira Gusniar Panjaitan, S.Pd
            'XII Perhotelan 2' => 1532, // Hennilawati, S.Pd
            'XII ULW 1' => 1558, // Lia Kartika Putri, S.Pd
            'XII Kecantikan 1' => 1518, // Retni Tri Ramasari, S.Pd
            'XII Busana 1' => 1551, // Tika Puspitasari, S.Pd
            'XII Busana 2' => 1533, // Ila Lestari, S.Pd
            'XII Kuliner 1' => 1555, // Wenti Wulandari, S.Pd
            'XII Kuliner 2' => 1512, // Mirahayani, SS

            'XI TJKT 1' => 1523, // Sumarno, S.Kom
            'XI TJKT 2' => 1547, // Siti Nurmila Rahmatillah, S.Kom
            'XI TJKT 3' => 1475, // Ernawaty Simamora, S.Pd
            'XI PPLG 1' => 1510, // Meslina, S.Pd
            'XI PPLG 2' => 1508, // Kusniarti, S.Pd
            'XI PPLG 3' => 1556, // Yuli Safira, S.Kom
            'XI Perhotelan 1' => 1527, // Dameriana Tampubolon, SE
            'XI Perhotelan 2' => 1550, // Sri Wahyuni, S.Pd
            'XI ULW 1' => 1580, // Syahfitri, SE
            'XI Kecantikan 1' => 1485, // Elis Erita, S.S
            'XI Busana 1' => 1500, // Evy Dharmayanti, S.Pd
            'XI Busana 2' => 1489, // Rejani Sulastia Harahap, S.Pd
            'XI Kuliner 1' => 1484, // Bontor Henni Waty Ambarita, S.Pd
            'XI Kuliner 2' => 1496, // Friska Debora Marpaung, S.Pd

            'X TJKT 1' => 1507, // Kartikasari AB, S.Pd
            'X TJKT 2' => 1515, // Nur Azi Rahmayani, S.Pd
            'X TJKT 3' => 1486, // Adisty Wardhani, S.Pd
            'X PPLG 1' => 1522, // Sri Wulandari, S.Pd.I
            'X PPLG 2' => 1528, // Dedy Supriatno, S.Pd (OVERRIDE: ganti Ilmiati jadi Dedy Guru Agama)
            'X PPLG 3' => 1517, // Yuli Anggraini, S.Pd
            'X Perhotelan 1' => 1545, // Romian Juni Yanti Sirait, S.Sos
            'X Perhotelan 2' => 1499, // Eka Mandasari, S.Pd
            'X ULW 1' => 1497, // Adinda Zoraya Alvin, S.Pd, Gr, M.Hum
            'X Kecantikan 1' => 1553, // Ulfa Alawiyah, S.Pd
            'X Busana 1' => 1524, // Yanti Mandasari Banjarnahor, S.Kom
            'X Busana 2' => 1531, // Eka Ayu Nanda, S.Pd
            'X Kuliner 1' => 1487, // Dina Hamidah, S.Pd
            'X Kuliner 2' => 1548, // Siti Zahara, S.Pd
        ];

        $waliUserIds = array_values($waliKelasMapping);

        // Kembalikan user non-wali yang bertipe walikelas menjadi guru
        User::where('role', 'walikelas')
            ->whereNotIn('id', $waliUserIds)
            ->update(['role' => 'guru']);

        foreach ($waliKelasMapping as $namaKelas => $userId) {
            $kelas = Kelas::where('nama_kelas', $namaKelas)->first();
            if ($kelas) {
                $kelas->update(['wali_kelas_id' => $userId]);
                $user = User::find($userId);
                if ($user && $user->role !== 'admin') {
                    $user->update(['role' => 'walikelas']);
                }
            }
        }
        $this->info('Sukses update 42 wali kelas (termasuk X PPLG 2 -> Dedy Supriatno, S.Pd).');

        $this->info('=== 2. SYNC METADATA GURU DARI DATA_GURU_SMKN1_BERINGIN.XLSX ===');
        $excelPath = base_path('Data_Guru_SMKN1_Beringin.xlsx');
        if (file_exists($excelPath)) {
            $spreadsheet = IOFactory::load($excelPath);
            $sheet = $spreadsheet->getSheetByName('Data Guru (90 Pendidik)') ?? $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            $dbTeachers = User::whereIn('role', ['guru', 'walikelas', 'admin'])->get();

            $explicitMatches = [
                31 => 1545, // Romian Juni Yanti Sirait, S.Tr. Par
                32 => 1521, // Sabarina Br Munte, S.Kom
                58 => 1544, // Risa Pertiwi, S.Tr.Par
                76 => 1547, // Siti Nurmila Rahmatilah, S.Kom
                78 => 1561, // Agnes Angelia Br. Matondang, S.Pd
                83 => 1560, // Titin Tiara Nikita Br Marbun, S.Pd
                85 => 1581, // Nira Astuty Piliang, S.ST
            ];

            $cleanNameFn = function ($s) {
                $s = strtolower($s);
                $s = preg_replace('/,?\s*(s\.pd|s\.kom|m\.pd|s\.s|s\.e|s\.pdi|s\.tr par|s\.tr\. par|amd\. par|m\.si|s\.t|s\.th|s\.st|dra\.|dr\.|drs\.|gr\.|m\.hum|s\.sos)\.?/i', '', $s);
                return preg_replace('/[^a-z0-9]/', '', $s);
            };

            $syncedCount = 0;
            for ($i = 4; $i < count($rows); $i++) {
                $row = $rows[$i];
                $no = $row[0];
                if (! $no) continue;

                $nama = trim($row[4] ?? '');
                $nipRaw = trim($row[6] ?? '');
                $nikRaw = trim($row[7] ?? '');
                $nuptkRaw = trim($row[8] ?? '');
                $mapel = trim($row[13] ?? '');

                $cleanNip = preg_replace('/[^0-9]/', '', $nipRaw);
                $cleanNik = preg_replace('/[^0-9]/', '', $nikRaw);
                $cleanNuptk = preg_replace('/[^0-9]/', '', $nuptkRaw);

                $targetUser = null;
                if (isset($explicitMatches[$no])) {
                    $targetUser = User::find($explicitMatches[$no]);
                }
                if (! $targetUser && $cleanNip && strlen($cleanNip) >= 10) {
                    foreach ($dbTeachers as $t) {
                        $tNip = preg_replace('/[^0-9]/', '', $t->nip ?? '');
                        if ($tNip && $tNip === $cleanNip) {
                            $targetUser = $t;
                            break;
                        }
                    }
                }
                if (! $targetUser) {
                    $cn = $cleanNameFn($nama);
                    foreach ($dbTeachers as $t) {
                        if ($cleanNameFn($t->name) === $cn) {
                            $targetUser = $t;
                            break;
                        }
                    }
                }
                if (! $targetUser) {
                    $cn = $cleanNameFn($nama);
                    foreach ($dbTeachers as $t) {
                        $tc = $cleanNameFn($t->name);
                        if (strlen($cn) > 4 && strlen($tc) > 4 && (str_contains($tc, $cn) || str_contains($cn, $tc))) {
                            $targetUser = $t;
                            break;
                        }
                    }
                }

                if ($targetUser) {
                    $targetUser->nip = $cleanNip ?: $targetUser->nip;
                    $targetUser->nik = $cleanNik ?: $targetUser->nik;
                    $targetUser->nuptk = $cleanNuptk ?: $targetUser->nuptk;
                    if (! $targetUser->bio && $mapel) {
                        $targetUser->bio = $mapel;
                    }
                    $targetUser->save();
                    $syncedCount++;
                }
            }
            $this->info("Sukses sync {$syncedCount} metadata pendidik (NIP, NIK, NUPTK, mapel).");
        }

        $this->info('=== 3. SYNC JADWAL ROSTER SISWA (40 KODE GURU) ===');

        $rosterMapping = [
            'HY' => ['id' => 1538, 'name' => 'Angereiny Citra Sari, S.Pd'],
            'SD' => ['id' => 1493, 'name' => 'Sudung Haposan Sinabutar, S.Kom'],
            'LW' => ['id' => 1494, 'name' => 'Anggun Desrivawany, S.Pd'],
            'WR' => ['id' => 1486, 'name' => 'Adisty Wardhani, S.Pd'],
            'YR' => ['id' => 1571, 'name' => 'Yuni Rahayu, SE'],
            'PB' => ['id' => 1505, 'name' => 'Iswadi, S.Kom'],
            'JT' => ['id' => 1478, 'name' => 'Dra. Tuti Aminingsih'],
            'HB' => ['id' => 1483, 'name' => 'Ali Sahbana Hasibuan, S.Pd'],
            'LN' => ['id' => 1509, 'name' => 'Lailan Rinsiyani, S.Pd'],
            'RL' => ['id' => 1504, 'name' => 'Irmala, S.Kom'],
            'SL' => ['id' => 1547, 'name' => 'Siti Nurmila Rahmatillah, S.Kom'],
            'PN' => ['id' => 1520, 'name' => 'Risan, S.Pd.I'],
            'NX' => ['id' => 1541, 'name' => 'Novayanti, S.Pd.I'],
            'KB' => ['id' => 1507, 'name' => 'Kartikasari AB, S.Pd'],
            'GM' => ['id' => 1526, 'name' => 'Agus Miati Harahap, S.Pd'],
            'YT' => ['id' => 1474, 'name' => 'Dra. Nurhamidah Pulungan'],
            'DJ' => ['id' => 1476, 'name' => 'Dra. Rosida Ita Simanjuntak'],
            'YF' => ['id' => 1529, 'name' => 'Yusra Afsun Arifin, S.Pd'],
            'ZZ' => ['id' => 1516, 'name' => 'Nurazizah Br Tambunan, S.PdI'],
            'ML' => ['id' => 1510, 'name' => 'Meslina, S.Pd'],
            'DH' => ['id' => 1487, 'name' => 'Dina Hamidah, S.Pd'],
            'LM' => ['id' => 1501, 'name' => 'Ilmiati, S.Kom'],
            'JN' => ['id' => 1506, 'name' => 'Juandi Ikhwan, S.Pd'],
            'RZ' => ['id' => 1491, 'name' => 'Erwin Saleh Daulay, S.Pd'],
            'NZ' => ['id' => 1515, 'name' => 'Nur Azi Rahmayani, S.Pd'],
            'RH' => ['id' => 1489, 'name' => 'Rejani Sulastia Harahap, S.Pd'],
            'MR' => ['id' => 1512, 'name' => 'Mirahayani, SS'],
            'NV' => ['id' => 1514, 'name' => 'Novika Rahmadani, S.Pd'],
            'HW' => ['id' => 1484, 'name' => 'Bontor Henni Waty Ambarita, S.Pd'],
            'VH' => ['id' => 1500, 'name' => 'Evy Dharmayanti, S.Pd'],
            'LT' => ['id' => 1533, 'name' => 'Ila Lestari, S.Pd'],
            'ZR' => ['id' => 1497, 'name' => 'Adinda Zoraya Alvin, S.Pd'],
            'DT' => ['id' => 1527, 'name' => 'Dameriana Tampubolon, SE'],
            'FT' => ['id' => 1580, 'name' => 'Syahfitri, SE'],
            'RY' => ['id' => 1545, 'name' => 'Romian Juni Yanti Sirait, S.Sos'],
            'TS' => ['id' => 1551, 'name' => 'Tika Puspitasari, S.Pd'],
            'NL' => ['id' => 1581, 'name' => 'Nira Astuty Piliang, S.ST'],
            'NK' => ['id' => 1513, 'name' => 'Novi Etika Putri Siregar, S.Pd'],
            'LZ' => ['id' => 1536, 'name' => 'Lira Oliza Octavia, S.Pd'],
            'NR' => ['id' => 1559, 'name' => 'Nanda Harafany, S.Pd'],
        ];

        $updatedSlots = 0;
        foreach ($rosterMapping as $code => $info) {
            $updatedSlots += JadwalPelajaran::where('kode_guru', $code)->update([
                'guru_id' => $info['id'],
                'nama_guru' => $info['name'],
            ]);
        }
        $this->info("Sukses update {$updatedSlots} jam pelajaran untuk 40 kode guru.");

        $this->info('Semua proses sinkronisasi guru, wali kelas, dan roster berhasil diselesaikan!');

        return 0;
    }
}

