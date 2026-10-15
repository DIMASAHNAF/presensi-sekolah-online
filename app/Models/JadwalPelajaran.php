<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;

    protected $table = 'jadwal_pelajarans';

    protected $fillable = [
        'kelas_id',
        'hari',
        'jam_ke',
        'waktu',
        'jam_mulai',
        'jam_selesai',
        'kode_guru',
        'nama_guru',
        'guru_id',
        'nama_mapel',
        'mapel_id',
        'ruang',
        'is_active',
    ];

    protected $casts = [
        'jam_ke' => 'integer',
        'is_active' => 'boolean',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }
}
