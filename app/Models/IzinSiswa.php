<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class IzinSiswa extends Model
{
    protected $table = 'izin_siswa';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'diajukan_oleh',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'keterangan',
        'surat_file',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    /** Siswa yang berhalangan hadir */
    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    /** Kelas siswa */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /** Wali Kelas atau Admin yang menginput */
    public function diajukanOleh()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    /** Seluruh record presensi yang tertaut dengan izin ini */
    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'izin_siswa_id');
    }

    /**
     * Scope untuk mencari izin yang aktif pada tanggal tertentu.
     */
    public function scopeActiveOnDate($query, $date)
    {
        return $query->where('status', 'disetujui')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->whereDate('tanggal_selesai', '>=', $date);
    }

    /**
     * URL file surat izin jika ada.
     */
    public function getSuratUrlAttribute(): ?string
    {
        if (empty($this->surat_file)) {
            return null;
        }

        if (str_starts_with($this->surat_file, 'http://') || str_starts_with($this->surat_file, 'https://')) {
            return $this->surat_file;
        }

        return Storage::url($this->surat_file);
    }

    /**
     * Cek apakah surat berformat gambar.
     */
    public function getIsImageAttribute(): bool
    {
        if (!$this->surat_file) return false;
        $ext = strtolower(pathinfo($this->surat_file, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    }

    /**
     * Cek apakah surat berformat PDF.
     */
    public function getIsPdfAttribute(): bool
    {
        if (!$this->surat_file) return false;
        $ext = strtolower(pathinfo($this->surat_file, PATHINFO_EXTENSION));
        return $ext === 'pdf';
    }
}

