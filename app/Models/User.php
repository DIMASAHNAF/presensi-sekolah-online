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
        'nisn', 'nik', 'nip', 'nuptk', 'role', 'no_hp_ortu', 'nama_ortu', 'password', 'password_changed_at', 'kelas_id',
        'face_descriptor', 'face_enrolled_at',
    ];

    /**
     * Accessor yang otomatis disertakan saat model di-serialize ke JSON / Array
     */
    protected $appends = [
        'avatar_url',
        'banner_url',
        'short_name',
    ];

    /**
     * Nama Pendek / Panggilan Cerdas (Title Case, menangani awalan umum seperti M., Muhammad, Nur, Dwi, dll.)
     */
    public function getShortNameAttribute(): string
    {
        return self::formatShortName($this->name);
    }

    public static function formatShortName(?string $fullName): string
    {
        if (empty($fullName)) {
            return '';
        }

        $parts = array_values(array_filter(preg_split('/\s+/', trim($fullName))));
        if (empty($parts)) {
            return '';
        }

        $toTitle = function ($str) {
            return mb_convert_case(mb_strtolower($str), MB_CASE_TITLE, 'UTF-8');
        };

        $firstRaw = $parts[0];
        $firstLower = strtolower(rtrim($firstRaw, '.'));

        $muhammadVariants = ['muhammad', 'muhamad', 'mochammad', 'mohammad', 'moch', 'moh', 'm'];
        $commonPrefixes = ['nur', 'dwi', 'tri', 'syifa', 'syifaa', 'siti', 'putri', 'putra', 'ahmad', 'achmad', 'abdul'];
        $hasSecond = isset($parts[1]) && trim($parts[1]) !== '';

        if (in_array($firstLower, $muhammadVariants)) {
            return $hasSecond ? 'M. ' . $toTitle($parts[1]) : 'Muhammad';
        }

        if (in_array($firstLower, $commonPrefixes)) {
            return $hasSecond ? $toTitle($firstRaw) . ' ' . $toTitle($parts[1]) : $toTitle($firstRaw);
        }

        return $toTitle($firstRaw);
    }

    /**
     * Format nama lengkap menjadi Title Case yang rapi
     */
    public static function formatTitleCase(?string $name): string
    {
        if (empty($name)) {
            return '';
        }
        return mb_convert_case(mb_strtolower(trim($name)), MB_CASE_TITLE, 'UTF-8');
    }

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
        'nik', // Proteksi PII guru & staf
        'face_descriptor', // Jangan ekspos descriptor di API response
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'password_changed_at' => 'datetime',
            'face_descriptor'     => 'array',   // JSON ↔ PHP array otomatis
            'face_enrolled_at'    => 'datetime',
        ];
    }

    /**
     * Apakah siswa sudah pernah mengubah/membuat password sendiri?
     */
    public function hasChangedPassword(): bool
    {
        return !is_null($this->password_changed_at);
    }

    /**
     * Apakah siswa membutuhkan panduan onboarding awal (buat password / rekam wajah)?
     */
    public function needsOnboarding(): bool
    {
        if (!$this->isSiswa()) {
            return false;
        }
        return !$this->hasChangedPassword() || !$this->isFaceEnrolled();
    }

    /**
     * Apakah siswa sudah mendaftarkan wajah (enroll)?
     */
    public function isFaceEnrolled(): bool
    {
        return !is_null($this->face_descriptor) && in_array(count((array) $this->face_descriptor), [128, 512]);
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

    public function isWaliKelas(): bool
    {
        return $this->role === 'walikelas';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /** Guru mapel atau wali kelas */
    public function isGuruOrWali(): bool
    {
        return in_array($this->role, ['guru', 'walikelas']);
    }

    /** Guru, wali kelas, atau admin */
    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'walikelas', 'guru']);
    }

    /** Label role yang rapi */
    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'walikelas' => 'Wali Kelas',
            'guru' => 'Guru Pengampu',
            'siswa' => 'Siswa',
            default => ucfirst($this->role),
        };
    }

    /** Kelas siswa (hanya untuk role siswa) */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /** Kelas yang dibina jika user adalah Wali Kelas */
    public function kelasBinaan()
    {
        return $this->hasOne(Kelas::class, 'wali_kelas_id');
    }

    /** Izin/Sakit yang pernah diajukan oleh user ini */
    public function perizinanDiajukan()
    {
        return $this->hasMany(IzinSiswa::class, 'diajukan_oleh');
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

    /** Rekap nilai siswa */
    public function nilaiSiswa()
    {
        return $this->hasMany(NilaiSiswa::class, 'siswa_id');
    }
}
