<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Modifikasi ENUM role di tabel users untuk mendukung role 'walikelas'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('siswa','guru','walikelas','admin') NOT NULL DEFAULT 'siswa'");

        // 2. Tambah wali_kelas_id pada tabel kelas
        if (!Schema::hasColumn('kelas', 'wali_kelas_id')) {
            Schema::table('kelas', function (Blueprint $table) {
                $table->foreignId('wali_kelas_id')->nullable()->after('jurusan')->constrained('users')->nullOnDelete();
            });
        }

        // 3. Buat tabel izin_siswa
        if (!Schema::hasTable('izin_siswa')) {
            Schema::create('izin_siswa', function (Blueprint $table) {
                $table->id();
                $table->foreignId('siswa_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
                $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('jenis', ['sakit', 'izin'])->default('izin');
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai');
                $table->text('keterangan')->nullable();
                $table->string('surat_file', 255)->nullable();
                $table->enum('status', ['disetujui', 'pending', 'ditolak'])->default('disetujui');
                $table->timestamps();

                $table->index(['siswa_id', 'tanggal_mulai', 'tanggal_selesai'], 'idx_izin_siswa_dates');
                $table->index(['kelas_id', 'tanggal_mulai'], 'idx_izin_kelas_date');
            });
        }

        // 4. Tambah kolom surat_file dan izin_siswa_id pada tabel presensi
        Schema::table('presensi', function (Blueprint $table) {
            if (!Schema::hasColumn('presensi', 'surat_file')) {
                $table->string('surat_file', 255)->nullable()->after('override_note');
            }
            if (!Schema::hasColumn('presensi', 'izin_siswa_id')) {
                $table->foreignId('izin_siswa_id')->nullable()->after('surat_file')->constrained('izin_siswa')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi', function (Blueprint $table) {
            if (Schema::hasColumn('presensi', 'izin_siswa_id')) {
                $table->dropForeign(['izin_siswa_id']);
                $table->dropColumn('izin_siswa_id');
            }
            if (Schema::hasColumn('presensi', 'surat_file')) {
                $table->dropColumn('surat_file');
            }
        });

        Schema::dropIfExists('izin_siswa');

        if (Schema::hasColumn('kelas', 'wali_kelas_id')) {
            Schema::table('kelas', function (Blueprint $table) {
                $table->dropForeign(['wali_kelas_id']);
                $table->dropColumn('wali_kelas_id');
            });
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('siswa','guru','admin') NOT NULL DEFAULT 'siswa'");
    }
};

