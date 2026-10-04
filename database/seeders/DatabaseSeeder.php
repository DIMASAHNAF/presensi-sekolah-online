<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KelasSeeder::class,  // Harus pertama (users butuh referensi kelas)
            AdminSeeder::class,
            GuruSeeder::class,
            MataPelajaranSeeder::class,
            JamPelajaranSeeder::class,
        ]);
    }
}
