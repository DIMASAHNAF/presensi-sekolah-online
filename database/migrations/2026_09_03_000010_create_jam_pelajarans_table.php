<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jam_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama');           // "Les 1", "Les 2", "ISHOMA 1", dll
            $table->unsignedTinyInteger('nomor'); // urutan sort (1-20)
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('hari', ['semua', 'senin', 'selasa', 'rabu', 'kamis', 'jumat'])->default('semua');
            $table->string('keterangan')->nullable(); // "Upacara Bendera", "ISHOMA", dll
            $table->boolean('is_istirahat')->default(false); // true = ISHOMA/istirahat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_pelajarans');
    }
};
