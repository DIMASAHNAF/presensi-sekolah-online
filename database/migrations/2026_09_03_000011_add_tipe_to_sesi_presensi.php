<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sesi_presensi', function (Blueprint $table) {
            // Tambah kolom tipe: kelas = absen pagi (siswa scan), mapel = absen per mata pelajaran (guru kelola)
            $table->enum('tipe', ['kelas', 'mapel'])->default('kelas')->after('is_active');
            // FK ke jam_pelajarans (nullable, optional)
            $table->foreignId('jam_pelajaran_id')
                  ->nullable()
                  ->after('jam_pelajaran')
                  ->constrained('jam_pelajarans')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sesi_presensi', function (Blueprint $table) {
            $table->dropForeign(['jam_pelajaran_id']);
            $table->dropColumn(['tipe', 'jam_pelajaran_id']);
        });
    }
};
