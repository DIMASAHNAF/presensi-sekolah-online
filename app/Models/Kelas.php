<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan',
        'wali_kelas_id',
    ];

    /** Relasi langsung ke Wali Kelas */
    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    /** Semua siswa di kelas ini */
    public function siswa()
    {
        return $this->hasMany(User::class, 'kelas_id')->where('role', 'siswa');
    }

    /** Semua perizinan siswa di kelas ini */
    public function izinSiswa()
    {
        return $this->hasMany(IzinSiswa::class, 'kelas_id');
    }

    /** Semua sesi presensi yang pernah dibuat untuk kelas ini */
    public function sesiPresensi()
    {
        return $this->hasMany(SesiPresensi::class, 'kelas_id');
    }

    /**
     * Wali kelas — diprioritaskan dari kolom wali_kelas_id, atau fallback otomatis ke
     * guru yang paling sering membuat sesi PAGI (tanpa mapel) di kelas ini.
     */
    public function getWaliKelasAttribute()
    {
        if ($this->wali_kelas_id) {
            return $this->waliKelas;
        }

        $guru = SesiPresensi::where('kelas_id', $this->id)
            ->whereNull('mapel_id')
            ->select('guru_id', DB::raw('COUNT(*) as total'))
            ->groupBy('guru_id')
            ->orderByDesc('total')
            ->first();

        return $guru ? User::find($guru->guru_id) : null;
    }

    /** Rekap nilai seluruh siswa di kelas ini */
    public function nilaiSiswa()
    {
        return $this->hasMany(NilaiSiswa::class, 'kelas_id');
    }
}
