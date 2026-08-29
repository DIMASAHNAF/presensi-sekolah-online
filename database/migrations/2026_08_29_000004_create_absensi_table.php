<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_absensi_id')
                  ->constrained('sesi_absensi')
                  ->cascadeOnDelete();
            $table->foreignId('siswa_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alpa'])->default('hadir');
            $table->timestamp('waktu_scan')->nullable(); // waktu siswa scan barcode
            $table->text('keterangan')->nullable();      // diisi guru saat edit izin/sakit
            $table->timestamps();

            // 1 siswa hanya boleh absen 1x per sesi
            $table->unique(['sesi_absensi_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};
