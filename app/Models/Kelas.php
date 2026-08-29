<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    /** Semua sesi absensi yang pernah dibuat untuk kelas ini */
    public function sesiAbsensi()
    {
        return $this->hasMany(SesiAbsensi::class, 'kelas_id');
    }
}
