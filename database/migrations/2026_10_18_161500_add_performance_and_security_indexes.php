<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Index pada tabel sesi_presensi untuk query traffic pagi
        Schema::table('sesi_presensi', function (Blueprint $table) {
            $table->index('tanggal', 'idx_sesi_tanggal');
            $table->index(['kelas_id', 'tanggal', 'tipe'], 'idx_sesi_kelas_tanggal_tipe');
            $table->index(['tanggal', 'is_active'], 'idx_sesi_tanggal_active');
        });

        // 2. Index pada tabel presensi untuk optimasi status & riwayat scan
        Schema::table('presensi', function (Blueprint $table) {
            $table->index('status', 'idx_presensi_status');
            $table->index('waktu_scan', 'idx_presensi_waktu_scan');
            $table->index(['siswa_id', 'status'], 'idx_presensi_siswa_status');
        });

        // 3. Index pada tabel users untuk filtering role dan kelas siswa
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'idx_users_role');
            $table->index(['kelas_id', 'role'], 'idx_users_kelas_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sesi_presensi', function (Blueprint $table) {
            $table->dropIndex('idx_sesi_tanggal');
            $table->dropIndex('idx_sesi_kelas_tanggal_tipe');
            $table->dropIndex('idx_sesi_tanggal_active');
        });

        Schema::table('presensi', function (Blueprint $table) {
            $table->dropIndex('idx_presensi_status');
            $table->dropIndex('idx_presensi_waktu_scan');
            $table->dropIndex('idx_presensi_siswa_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_kelas_role');
        });
    }
};
