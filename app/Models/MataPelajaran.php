<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajarans';

    protected $fillable = [
        'nama_mapel',
    ];

    public function sesiAbsensi()
    {
        return $this->hasMany(SesiAbsensi::class, 'mapel_id');
    }
}
