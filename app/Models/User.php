<?php

// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'nisn', 'nik', 'password', 'role', 'kelas_id',
        'face_descriptor', 'face_enrolled_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
        'face_descriptor', // Jangan ekspos descriptor di API response
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'face_descriptor'    => 'array',   // JSON ↔ PHP array otomatis
            'face_enrolled_at'   => 'datetime',
        ];
    }

    /**
     * Apakah siswa sudah mendaftarkan wajah (enroll)?
     */
    public function isFaceEnrolled(): bool
    {
        return !is_null($this->face_descriptor) && count((array) $this->face_descriptor) === 128;
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
