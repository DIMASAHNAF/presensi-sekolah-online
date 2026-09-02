<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogPresensi extends Model
{
    protected $table = 'log_presensi';

    protected $fillable = [
        'presensi_id',
        'guru_id',
        'status_sebelumnya',
        'status_baru',
        'keterangan',
    ];

    public function presensi()
    {
        return $this->belongsTo(Presensi::class, 'presensi_id');
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function labelStatusSebelumnya(): string
    {
        return ucfirst($this->status_sebelumnya);
    }

    public function labelStatusBaru(): string
    {
        return ucfirst($this->status_baru);
    }
}
