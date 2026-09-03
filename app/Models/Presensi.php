<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = [
        'sesi_presensi_id',
        'siswa_id',
        'status',
        'waktu_scan',
        'keterangan',
    ];

    protected $casts = [
        'waktu_scan' => 'datetime',
    ];

    /** Sesi presensi tempat rekap ini berada */
    public function sesiPresensi()
    {
        return $this->belongsTo(SesiPresensi::class, 'sesi_presensi_id');
    }

    /** Siswa yang diabsen */
    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    /** Helper: apakah siswa hadir */
    public function isHadir(): bool
    {
        return $this->status === 'hadir';
    }

    /** Helper: label status dalam Bahasa Indonesia */
    public function labelStatus(): string
    {
        return match ($this->status) {
            'hadir' => 'Hadir',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpa' => 'Alpa',
            default => '-',
        };
    }

    /** Log perubahan presensi ini */
    public function logPresensi()
    {
        return $this->hasMany(LogPresensi::class, 'presensi_id')->latest();
    }
}
