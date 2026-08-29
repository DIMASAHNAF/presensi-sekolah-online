<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesi_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignId('kelas_id')
                  ->constrained('kelas')
                  ->cascadeOnDelete();
            $table->date('tanggal');
            $table->string('barcode_token')->unique(); // token unik untuk QR/barcode
            $table->boolean('is_active')->default(true); // sesi aktif / ditutup guru
            $table->timestamps();

            // 1 guru hanya bisa buat 1 sesi per kelas per hari
            $table->unique(['kelas_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_absensi');
    }
};
