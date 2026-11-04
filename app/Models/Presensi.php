<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Presensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = [
        'sesi_presensi_id',
        'siswa_id',
        'status',
        'check_in_status',
        'late_minutes',
        'check_in_lat',
        'check_in_lng',
        'check_in_distance',
        'check_in_photo',
        'waktu_scan',
        'check_out_time',
        'check_out_lat',
        'check_out_lng',
        'check_out_photo',
        'keterangan',
        'override_by',
        'override_note',
    ];

    protected $casts = [
        'waktu_scan'        => 'datetime',
        'check_out_time'    => 'datetime',
        'late_minutes'      => 'integer',
        'check_in_lat'      => 'float',
        'check_in_lng'      => 'float',
        'check_in_distance' => 'float',
        'check_out_lat'     => 'float',
        'check_out_lng'     => 'float',
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

    /** Guru/Admin yang melakukan manual override */
    public function overrideBy()
    {
        return $this->belongsTo(User::class, 'override_by');
    }

    /** Helper: apakah siswa hadir */
    public function isHadir(): bool
    {
        return $this->status === 'hadir';
    }

    /** Helper: apakah siswa hadir terlambat */
    public function isLate(): bool
    {
        return $this->check_in_status === 'LATE' || $this->late_minutes > 0;
    }

    /** Helper: label status dalam Bahasa Indonesia */
    public function labelStatus(): string
    {
        if ($this->status === 'hadir' && $this->isLate()) {
            return $this->late_minutes > 0 ? "Terlambat ({$this->late_minutes}m)" : 'Terlambat';
        }

        return match ($this->status) {
            'hadir' => 'Hadir',
            'izin'  => 'Izin',
            'sakit' => 'Sakit',
            'alpa'  => 'Alpa',
            default => '-',
        };
    }

    /** Helper: URL foto snapshot kehadiran */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->check_in_photo) return null;
        return Storage::url($this->check_in_photo);
    }

    /** Log perubahan presensi ini */
    public function logPresensi()
    {
        return $this->hasMany(LogPresensi::class, 'presensi_id')->latest();
    }
}
