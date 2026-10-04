<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // KREDENSIAL ADMIN UTAMA
        // Ubah variabel di bawah ini jika ingin mengganti
        // =============================================
        $username = 'admin';
        $password = 'Adminsmkn1beringin2k26';
        $nik      = 'Admin001';
        $email    = 'admin@sekolah.sch.id';

        User::updateOrCreate(
            ['role' => 'admin'],
            [
                'name'     => 'Administrator',
                'username' => $username,
                'email'    => $email,
                'nik'      => $nik,
                'role'     => 'admin',
                'password' => Hash::make($password),
            ]
        );

        $this->command->info("✅ Admin seeded: Username={$username} | NIK={$nik} | Password={$password}");
    }
}
