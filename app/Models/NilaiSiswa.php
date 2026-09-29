<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSiswa extends Model
{
    use HasFactory;

    protected $table = 'nilai_siswa';

    protected $fillable = [
        'siswa_id',
        'guru_id',
        'kelas_id',
        'mapel_id',
        'jenis_penilaian',
        'judul',
        'nilai',
        'tanggal',
        'catatan',
    ];

    protected $casts = [
        'nilai' => 'float',
        'tanggal' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    /**
     * Label representatif jenis penilaian
     */
    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis_penilaian) {
            'tugas' => 'Tugas',
            'ulangan_harian' => 'Ulangan Harian (UH)',
            'uts' => 'PTS / UTS',
            'uas' => 'PAS / UAS',
            'praktik' => 'Praktik / Portofolio',
            'sikap' => 'Sikap & Karakter',
            default => ucfirst(str_replace('_', ' ', $this->jenis_penilaian ?? 'Tugas')),
        };
    }

    /**
     * Predikat nilai sederhana
     */
    public function getPredikatAttribute(): string
    {
        $val = $this->nilai;
        if ($val >= 90) return 'A';
        if ($val >= 80) return 'B';
        if ($val >= 70) return 'C';
        if ($val >= 60) return 'D';
        return 'E';
    }
}
