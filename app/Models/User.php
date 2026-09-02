<?php
// app/Models/User.php

namespace App\Models;

use App\Models\Presensi;
use App\Models\Kelas;
use App\Models\SesiPresensi;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'name', 'username', 'email', 'nisn', 'nik', 'password', 'role', 'kelas_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Kelas siswa (hanya untuk role siswa) */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /** Sesi presensi yang dibuat guru */
    public function sesiPresensi()
    {
        return $this->hasMany(SesiPresensi::class, 'guru_id');
    }

    /** Rekap presensi siswa */
    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'siswa_id');
    }
}