<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajarans';

    protected $fillable = [
        'nama_mapel',
    ];

    public function sesiPresensi()
    {
        return $this->hasMany(SesiPresensi::class, 'mapel_id');
    }
}
