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
        // ADMIN — ubah password sebelum production!
        // =============================================
        User::updateOrCreate(
            ['nik' => 'ADMIN001'],
            [
                'name'     => 'Administrator',
                'username' => 'admin',
                'email'    => 'admin@sekolah.sch.id',
                'nik'      => 'ADMIN001',
                'role'     => 'admin',
                'password' => Hash::make('Admin@1234'),
            ]
        );

        $this->command->info('✅ Admin seeded: NIK=ADMIN001 | Password=Admin@1234');
    }
}
