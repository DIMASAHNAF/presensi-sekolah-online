<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();
            $table->date('tanggal');
            $table->foreignId('mapel_id')->nullable()->constrained('mata_pelajarans')->nullOnDelete();
            $table->string('jam_pelajaran')->nullable(); // Contoh: "Les 1 - 2"
            $table->string('barcode_token')->unique(); // token unik untuk QR/barcode
            $table->boolean('is_active')->default(true); // sesi aktif / ditutup guru
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_presensi');
    }
};
