<?php

namespace Database\Seeders;

use App\Models\Kelas;
use Illuminate\Database\Seeder;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $jurusans = [
            'TJKT' => 9,
            'PPLG' => 9,
            'Perhotelan' => 6,
            'Kecantikan' => 3,
            'Kuliner' => 6,
            'Busana' => 6,
            'Pariwisata' => 2,
            'ULW' => 3,
        ];

        $kelasData = [];
        $tingkats = ['X', 'XI', 'XII'];

        foreach ($jurusans as $jurusan => $total) {
            $classesPerTingkat = $total / 3;
            foreach ($tingkats as $tingkat) {
                for ($i = 1; $i <= $classesPerTingkat; $i++) {
                    $kelasData[] = [
                        'nama_kelas' => "$tingkat $jurusan $i",
                        'tingkat' => $tingkat,
                        'jurusan' => $jurusan,
                    ];
                }
            }
        }

        foreach ($kelasData as $k) {
            Kelas::updateOrCreate(['nama_kelas' => $k['nama_kelas']], $k);
        }

        $this->command->info('✅ Kelas seeded: '.count($kelasData).' kelas');
    }
}
