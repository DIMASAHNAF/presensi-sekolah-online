<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiPresensi extends Model
{
    protected $table = 'sesi_presensi';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'mapel_id',
        'jam_pelajaran_id',
        'jam_pelajaran',
        'tanggal',
        'barcode_token',
        'is_active',
        'tipe',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_active' => 'boolean',
    ];

    /** Guru yang membuat sesi ini */
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    /** Kelas yang sedang diabsen */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /** Semua rekap presensi di sesi ini */
    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'sesi_presensi_id');
    }

    /** Mata Pelajaran sesi ini (opsional) */
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    /** Jam Pelajaran sesi ini (relasi ke DB) */
    public function jamPelajaranRelation()
    {
        return $this->belongsTo(JamPelajaran::class, 'jam_pelajaran_id');
    }
}
