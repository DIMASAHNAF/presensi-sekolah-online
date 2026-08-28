<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash; // 1. Import Hash facade di sini

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents; // Trait digunakan di sini

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => Hash::make("admin"),
            
        ]);
    }
}