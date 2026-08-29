<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiAbsensi extends Model
{
    protected $table = 'sesi_absensi';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'tanggal',
        'barcode_token',
        'is_active',
    ];

    protected $casts = [
        'tanggal'   => 'date',
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

    /** Semua rekap absensi di sesi ini */
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'sesi_absensi_id');
    }
}
