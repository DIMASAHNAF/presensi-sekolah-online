<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'sesi_absensi_id',
        'siswa_id',
        'status',
        'waktu_scan',
        'keterangan',
    ];

    protected $casts = [
        'waktu_scan' => 'datetime',
    ];

    /** Sesi absensi tempat rekap ini berada */
    public function sesiAbsensi()
    {
        return $this->belongsTo(SesiAbsensi::class, 'sesi_absensi_id');
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
        return match($this->status) {
            'hadir' => 'Hadir',
            'izin'  => 'Izin',
            'sakit' => 'Sakit',
            'alpa'  => 'Alpa',
            default => '-',
        };
    }
}
