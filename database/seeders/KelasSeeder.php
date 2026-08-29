<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = [
            // Kelas X
            ['nama_kelas' => 'X IPA 1',  'tingkat' => 'X',   'jurusan' => 'IPA'],
            ['nama_kelas' => 'X IPA 2',  'tingkat' => 'X',   'jurusan' => 'IPA'],
            ['nama_kelas' => 'X IPS 1',  'tingkat' => 'X',   'jurusan' => 'IPS'],
            // Kelas XI
            ['nama_kelas' => 'XI IPA 1', 'tingkat' => 'XI',  'jurusan' => 'IPA'],
            ['nama_kelas' => 'XI IPA 2', 'tingkat' => 'XI',  'jurusan' => 'IPA'],
            ['nama_kelas' => 'XI IPS 1', 'tingkat' => 'XI',  'jurusan' => 'IPS'],
            // Kelas XII
            ['nama_kelas' => 'XII IPA 1','tingkat' => 'XII', 'jurusan' => 'IPA'],
            ['nama_kelas' => 'XII IPA 2','tingkat' => 'XII', 'jurusan' => 'IPA'],
            ['nama_kelas' => 'XII IPS 1','tingkat' => 'XII', 'jurusan' => 'IPS'],
        ];

        foreach ($kelas as $k) {
            Kelas::updateOrCreate(['nama_kelas' => $k['nama_kelas']], $k);
        }

        $this->command->info('✅ Kelas seeded: ' . count($kelas) . ' kelas');
    }
}
