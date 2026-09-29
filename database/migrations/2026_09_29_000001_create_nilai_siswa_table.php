<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mapel_id')->nullable()->constrained('mata_pelajarans')->nullOnDelete();
            $table->string('jenis_penilaian', 50); // tugas, ulangan_harian, uts, uas, praktik, sikap
            $table->string('judul', 150); // e.g. "Tugas 1 - Algoritma", "UH Bab 2"
            $table->decimal('nilai', 5, 2); // 0.00 - 100.00
            $table->date('tanggal')->nullable();
            $table->text('catatan')->nullable(); // feedback guru
            $table->timestamps();

            $table->index(['kelas_id', 'mapel_id']);
            $table->index(['siswa_id', 'jenis_penilaian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_siswa');
    }
};
