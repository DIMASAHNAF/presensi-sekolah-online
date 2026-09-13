<?php

// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'avatar', 'banner', 'bio', 'website',
        'nisn', 'nik', 'password', 'role', 'kelas_id',
        'face_descriptor', 'face_enrolled_at',
    ];

    /**
     * Accessor yang otomatis disertakan saat model di-serialize ke JSON / Array
     */
    protected $appends = [
        'avatar_url',
        'banner_url',
    ];

    /**
     * URL Foto Avatar Pengguna
     */
    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar && $this->avatar !== '0') {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            return asset('storage/' . $this->avatar);
        }
        return null;
    }

    /**
     * URL Banner Profil Pengguna (Null jika belum di-set, frontend menggunakan gradient)
     */
    public function getBannerUrlAttribute(): ?string
    {
        if ($this->banner && $this->banner !== '0') {
            if (str_starts_with($this->banner, 'http://') || str_starts_with($this->banner, 'https://')) {
                return $this->banner;
            }
            return asset('storage/' . $this->banner);
        }
        return null;
    }

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

    /**
     * Folder penyimpanan berkas per-akun di storage public
     * Format: accounts/{nisn_atau_nik_atau_id}_{username_atau_name}
     */
    public function getStorageFolder(): string
    {
        $identifier = $this->nisn ?? $this->nik ?? ('user_' . $this->id);
        $slug = Str::slug($this->username ?? $this->name ?? 'akun', '_');
        return 'accounts/' . $identifier . '_' . $slug;
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
