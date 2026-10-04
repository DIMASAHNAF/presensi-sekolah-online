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
        Schema::table('users', function (Blueprint $table) {
            // Simpan face descriptor (128-float vector dari dlib) sebagai JSON
            $table->longText('face_descriptor')->nullable()->after('kelas_id');
            // Timestamp kapan siswa pertama kali mendaftarkan wajah
            $table->timestamp('face_enrolled_at')->nullable()->after('face_descriptor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_descriptor', 'face_enrolled_at']);
        });
    }
};
