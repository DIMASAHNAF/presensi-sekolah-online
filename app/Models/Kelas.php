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
    ];

    /** Semua siswa di kelas ini */
    public function siswa()
    {
        return $this->hasMany(User::class, 'kelas_id')->where('role', 'siswa');
    }

    /** Semua sesi presensi yang pernah dibuat untuk kelas ini */
    public function sesiPresensi()
    {
        return $this->hasMany(SesiPresensi::class, 'kelas_id');
    }

    /**
     * Wali kelas — guru yang paling sering membuat sesi PAGI (tanpa mapel) di kelas ini.
     * Jika belum ada sesi, return null.
     */
    public function getWaliKelasAttribute()
    {
        $guru = SesiPresensi::where('kelas_id', $this->id)
            ->whereNull('mapel_id')
            ->select('guru_id', DB::raw('COUNT(*) as total'))
            ->groupBy('guru_id')
            ->orderByDesc('total')
            ->first();

        return $guru ? User::find($guru->guru_id) : null;
    }
}
