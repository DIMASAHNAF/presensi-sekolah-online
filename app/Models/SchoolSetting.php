<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'latitude',
        'longitude',
        'radius_meters',
        'is_geofencing_active',
        'is_ip_whitelist_active',
        'allowed_ips',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
        'is_geofencing_active' => 'boolean',
        'is_ip_whitelist_active' => 'boolean',
    ];

    /**
     * Dapatkan konfigurasi sekolah (singleton record).
     */
    public static function getSettings(): self
    {
        return static::firstOrCreate([], [
            'school_name' => 'SMK Negeri 1 Beringin',
            'latitude' => -6.20876340,
            'longitude' => 106.84559900,
            'radius_meters' => 100,
            'is_geofencing_active' => false,
            'is_ip_whitelist_active' => false,
            'allowed_ips' => null,
        ]);
    }

    /**
     * Mengambil daftar IP yang diizinkan dalam bentuk array.
     */
    public function getAllowedIpsArray(): array
    {
        if (empty($this->allowed_ips)) {
            return [];
        }

        $ips = preg_split('/[\s,;]+/', trim($this->allowed_ips));
        return array_values(array_filter(array_map('trim', $ips)));
    }

    /**
     * Memeriksa apakah IP tertentu diizinkan dalam whitelist.
     */
    public function isIpAllowed(?string $ip): bool
    {
        if (!$this->is_ip_whitelist_active) {
            return true;
        }

        if (empty($ip)) {
            return false;
        }

        $allowedList = $this->getAllowedIpsArray();
        if (empty($allowedList)) {
            return false;
        }

        foreach ($allowedList as $allowed) {
            if ($this->checkIpMatches($ip, $allowed)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cek kecocokan IP (mendukung IP tunggal dan CIDR subnet).
     */
    protected function checkIpMatches(string $clientIp, string $allowedIp): bool
    {
        // Jika ada subnet CIDR (contoh: 192.168.1.0/24)
        if (str_contains($allowedIp, '/')) {
            [$subnet, $bits] = explode('/', $allowedIp, 2);
            $bits = (int) $bits;

            if (filter_var($clientIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && filter_var($subnet, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $clientLong = ip2long($clientIp);
                $subnetLong = ip2long($subnet);
                $mask = -1 << (32 - $bits);
                return ($clientLong & $mask) === ($subnetLong & $mask);
            }
        }

        // Plain string comparison
        return $clientIp === $allowedIp;
    }

    /**
     * Mendeteksi IP publik asli klien, termasuk jika di balik Cloudflare / Proxy / Tunnel.
     */
    public static function getClientIp($request): string
    {
        // 1. Cloudflare header
        if ($request->hasHeader('CF-Connecting-IP')) {
            $cfIp = trim($request->header('CF-Connecting-IP'));
            if (filter_var($cfIp, FILTER_VALIDATE_IP)) {
                return $cfIp;
            }
        }

        // 2. X-Forwarded-For (ambil IP klien pertama)
        if ($request->hasHeader('X-Forwarded-For')) {
            $forwarded = explode(',', $request->header('X-Forwarded-For'));
            $firstIp = trim($forwarded[0] ?? '');
            if (filter_var($firstIp, FILTER_VALIDATE_IP)) {
                return $firstIp;
            }
        }

        // 3. X-Real-IP
        if ($request->hasHeader('X-Real-IP')) {
            $realIp = trim($request->header('X-Real-IP'));
            if (filter_var($realIp, FILTER_VALIDATE_IP)) {
                return $realIp;
            }
        }

        // 4. Default Laravel request IP
        return $request->ip() ?: '127.0.0.1';
    }
}
