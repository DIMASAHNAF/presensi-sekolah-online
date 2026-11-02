<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambahkan index performa untuk query kiosk yang paling sering dijalankan:
     *
     * 1. sesi_presensi.tanggal  — dipakai di WHERE DATE(tanggal) = today() setiap poll kiosk
     * 2. presensi(sesi_presensi_id, status) — dipakai di hadirHariIniCount & liveStats query
     * 3. presensi.waktu_scan    — dipakai di ORDER BY waktu_scan DESC untuk live feed
     * 4. users(role, kelas_id)  — dipakai di totalSiswa & totalEnrolled query
     */
    public function up(): void
    {
        // Index 1: sesi_presensi.tanggal (jika belum ada)
        if (!$this->indexExists('sesi_presensi', 'idx_sesi_presensi_tanggal')) {
            Schema::table('sesi_presensi', function (Blueprint $table) {
                $table->index('tanggal', 'idx_sesi_presensi_tanggal');
            });
        }

        // Index 2: presensi compound (sesi_presensi_id, status) — cover query hadir count
        if (!$this->indexExists('presensi', 'idx_presensi_sesi_status_cover')) {
            Schema::table('presensi', function (Blueprint $table) {
                $table->index(['sesi_presensi_id', 'status', 'waktu_scan'], 'idx_presensi_sesi_status_cover');
            });
        }

        // Index 3: users(role) untuk count queries
        if (!$this->indexExists('users', 'idx_users_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role', 'idx_users_role');
            });
        }
    }

    public function down(): void
    {
        Schema::table('sesi_presensi', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_sesi_presensi_tanggal');
        });
        Schema::table('presensi', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_presensi_sesi_status_cover');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_users_role');
        });
    }

    /**
     * Cek apakah index sudah ada di tabel (cegah error duplicate key).
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $indices = DB::select(
            "SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS 
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?",
            [$table, $indexName]
        );
        return count($indices) > 0;
    }
};
