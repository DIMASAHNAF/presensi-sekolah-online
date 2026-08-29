<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    public function run(): void
    {
        $gurus = [
            [
                'name'     => 'Budi Santoso, S.Pd',
                'username' => 'budi.guru',
                'nik'      => '3201010101800001',
                'email'    => 'budi@sekolah.sch.id',
                'password' => Hash::make('Guru@1234'),
            ],
            [
                'name'     => 'Siti Rahayu, M.Pd',
                'username' => 'siti.guru',
                'nik'      => '3201010101850002',
                'email'    => 'siti@sekolah.sch.id',
                'password' => Hash::make('Guru@1234'),
            ],
        ];

        foreach ($gurus as $data) {
            User::updateOrCreate(
                ['nik' => $data['nik']],
                array_merge($data, ['role' => 'guru'])
            );
        }

        $this->command->info('✅ Guru seeded: ' . count($gurus) . ' akun | Default password: Guru@1234');
    }
}
