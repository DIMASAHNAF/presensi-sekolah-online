<?php

// app/Models/JamPelajaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JamPelajaran extends Model
{
    protected $table = 'jam_pelajarans';

    protected $fillable = [
        'nama', 'nomor', 'jam_mulai', 'jam_selesai',
        'hari', 'keterangan', 'is_istirahat',
    ];

    protected $casts = [
        'is_istirahat' => 'boolean',
    ];

    /** Label lengkap untuk dropdown */
    public function getLabelAttribute(): string
    {
        return "{$this->nama} ({$this->jam_mulai} – {$this->jam_selesai})"
               .($this->keterangan ? " — {$this->keterangan}" : '');
    }

    public function getLabelShortAttribute(): string
    {
        return "{$this->nama} ({$this->jam_mulai}–{$this->jam_selesai})";
    }
}
