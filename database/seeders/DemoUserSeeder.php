<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1 Akun Guru Demo
        User::updateOrCreate(
            ['username' => 'guru.demo'],
            [
                'name'     => 'Guru Demo SMKN 1',
                'username' => 'guru.demo',
                'nik'      => '1234567890123456',
                'email'    => 'guru.demo@smkn1beringin.sch.id',
                'password' => Hash::make('password123'),
                'role'     => 'guru',
            ]
        );

        // Cari ID Kelas yang tersedia
        $kelasPplg1 = Kelas::where('nama_kelas', 'like', '%PPLG 1%')->first()?->id ?? 10;
        $kelasTjkt1 = Kelas::where('nama_kelas', 'like', '%TJKT 1%')->first()?->id ?? 1;
        $kelasHotel = Kelas::where('nama_kelas', 'like', '%Perhotelan%')->first()?->id ?? 19;
        $kelasKuliner = Kelas::where('nama_kelas', 'like', '%Kuliner%')->first()?->id ?? 28;
        $kelasBusana = Kelas::where('nama_kelas', 'like', '%Busana%')->first()?->id ?? 34;

        // 5 Akun Siswa Demo
        $siswas = [
            [
                'name'     => 'Ahmad Rizky (Demo)',
                'username' => 'siswa.demo1',
                'nisn'     => '0000000001',
                'email'    => 'siswa.demo1@smkn1beringin.sch.id',
                'kelas_id' => $kelasPplg1,
                'password' => Hash::make('password123'),
                'role'     => 'siswa',
            ],
            [
                'name'     => 'Bunga Lestari (Demo)',
                'username' => 'siswa.demo2',
                'nisn'     => '0000000002',
                'email'    => 'siswa.demo2@smkn1beringin.sch.id',
                'kelas_id' => $kelasTjkt1,
                'password' => Hash::make('password123'),
                'role'     => 'siswa',
            ],
            [
                'name'     => 'Citra Dewi (Demo)',
                'username' => 'siswa.demo3',
                'nisn'     => '0000000003',
                'email'    => 'siswa.demo3@smkn1beringin.sch.id',
                'kelas_id' => $kelasHotel,
                'password' => Hash::make('password123'),
                'role'     => 'siswa',
            ],
            [
                'name'     => 'Dimas Pratama (Demo)',
                'username' => 'siswa.demo4',
                'nisn'     => '0000000004',
                'email'    => 'siswa.demo4@smkn1beringin.sch.id',
                'kelas_id' => $kelasKuliner,
                'password' => Hash::make('password123'),
                'role'     => 'siswa',
            ],
            [
                'name'     => 'Eka Saputra (Demo)',
                'username' => 'siswa.demo5',
                'nisn'     => '0000000005',
                'email'    => 'siswa.demo5@smkn1beringin.sch.id',
                'kelas_id' => $kelasBusana,
                'password' => Hash::make('password123'),
                'role'     => 'siswa',
            ],
        ];

        foreach ($siswas as $siswa) {
            User::updateOrCreate(
                ['username' => $siswa['username']],
                $siswa
            );
        }

        $this->command->info('✅ Akun Demo Berhasil Dibuat: 1 Guru & 5 Siswa');
    }
}
