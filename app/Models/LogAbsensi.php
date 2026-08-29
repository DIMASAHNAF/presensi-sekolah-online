<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogAbsensi extends Model
{
    protected $table = 'log_absensi';

    protected $fillable = [
        'absensi_id',
        'guru_id',
        'status_sebelumnya',
        'status_baru',
        'keterangan',
    ];

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'absensi_id');
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
