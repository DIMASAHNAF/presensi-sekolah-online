<?php
// database/migrations/xxxx_xx_xx_add_columns_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('nisn')->unique()->nullable()->after('username'); // khusus siswa
            $table->string('nik')->unique()->nullable()->after('nisn');      // khusus guru
            $table->enum('role', ['siswa', 'guru', 'admin'])->default('siswa')->after('nik');
            $table->string('email')->nullable()->change(); // email jadi opsional
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'nisn', 'nik', 'role']);
        });
    }
};