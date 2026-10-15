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
        Schema::create('jadwal_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
            $table->unsignedTinyInteger('jam_ke');
            $table->string('waktu', 50)->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('kode_guru', 20)->nullable();
            $table->string('nama_guru', 255)->nullable();
            $table->foreignId('guru_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_mapel', 255);
            $table->foreignId('mapel_id')->nullable()->constrained('mata_pelajarans')->nullOnDelete();
            $table->string('ruang', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['kelas_id', 'hari', 'jam_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_pelajarans');
    }
};
