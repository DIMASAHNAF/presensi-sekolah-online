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
        'polygon_coordinates',
        'geofence_mode',
        'is_geofencing_active',
        'is_ip_whitelist_active',
        'allowed_ips',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'radius_meters' => 'integer',
        'polygon_coordinates' => 'array',
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
            'latitude' => 3.58660000,
            'longitude' => 98.88130000,
            'radius_meters' => 200,
            'is_geofencing_active' => true,
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
     * Mendapatkan IP klien yang telah divalidasi oleh framework (mencegah spoofing header mentah).
     * Kompatibel dengan Reverse Proxy (Nginx, Traefik, Cloudflare).
     */
    public static function getClientIp($request): string
    {
        $ip = $request->ip() ?: '127.0.0.1';

        // Deteksi apakah IP yang dihasilkan Laravel merupakan internal proxy / loopback
        $isInternal = in_array($ip, ['127.0.0.1', '::1'], true)
            || str_starts_with($ip, '10.')
            || str_starts_with($ip, '192.168.')
            || (bool) preg_match('/^172\.(1[6-9]|2[0-9]|3[0-1])\./', $ip);

        // Jika IP terdeteksi sebagai internal proxy, periksa header yang diteruskan oleh proxy internal terpercaya
        if ($isInternal) {
            if ($request->hasHeader('CF-Connecting-IP') && filter_var($request->header('CF-Connecting-IP'), FILTER_VALIDATE_IP)) {
                $ip = trim($request->header('CF-Connecting-IP'));
            } elseif ($request->hasHeader('X-Real-IP') && filter_var($request->header('X-Real-IP'), FILTER_VALIDATE_IP)) {
                $ip = trim($request->header('X-Real-IP'));
            }
        }

        if (in_array($ip, ['127.0.0.1', '::1'], true)) {
            \Illuminate\Support\Facades\Log::debug("[SchoolSetting::getClientIp] Request terdeteksi menggunakan loopback/internal IP: {$ip}");
        }

        return $ip;
    }

    /**
     * Memeriksa apakah suatu titik koordinat berada di dalam area geofence sekolah.
     * Mendukung mode Polygon (Ray-Casting algorithm) dan Radius (Haversine Formula).
     */
    public function validateGeofence(float $userLat, float $userLng): array
    {
        // 1. Jika mode polygon aktif dan koordinat poligon tersedia (minimal 3 titik)
        if ($this->geofence_mode === 'polygon' && !empty($this->polygon_coordinates)) {
            $polygon = is_array($this->polygon_coordinates)
                ? $this->polygon_coordinates
                : json_decode($this->polygon_coordinates, true);

            if (is_array($polygon) && count($polygon) >= 3) {
                $isInside = $this->isPointInPolygon($userLat, $userLng, $polygon);
                $centerDist = $this->calculateHaversineDistance($userLat, $userLng, (float) $this->latitude, (float) $this->longitude);

                return [
                    'allowed'  => $isInside,
                    'mode'     => 'polygon',
                    'distance' => round($centerDist),
                    'message'  => $isInside
                        ? 'Lokasi berada di dalam batas pagar sekolah.'
                        : 'Presensi ditolak: Anda berada di luar batas pagar SMKN 1 Beringin.',
                ];
            }
        }

        // 2. Fallback / Default: Mode Radius Lingkaran (Haversine Formula)
        $dist = $this->calculateHaversineDistance($userLat, $userLng, (float) $this->latitude, (float) $this->longitude);
        $isInside = $dist <= $this->radius_meters;

        return [
            'allowed'  => $isInside,
            'mode'     => 'radius',
            'distance' => round($dist),
            'message'  => $isInside
                ? 'Lokasi berada di dalam radius sekolah.'
                : sprintf('Presensi ditolak: Anda berada di luar area SMKN 1 Beringin. Jarak: %d meter (Batas radius: %d meter).', round($dist), $this->radius_meters),
        ];
    }

    /**
     * Algoritma Ray-Casting untuk mengecek apakah titik (lat, lng) berada di dalam poligon.
     */
    public function isPointInPolygon(float $lat, float $lng, array $polygon): bool
    {
        $inside = false;
        $numPoints = count($polygon);

        for ($i = 0, $j = $numPoints - 1; $i < $numPoints; $j = $i++) {
            $xi = (float) ($polygon[$i]['lat'] ?? $polygon[$i][0] ?? 0);
            $yi = (float) ($polygon[$i]['lng'] ?? $polygon[$i][1] ?? 0);
            $xj = (float) ($polygon[$j]['lat'] ?? $polygon[$j][0] ?? 0);
            $yj = (float) ($polygon[$j]['lng'] ?? $polygon[$j][1] ?? 0);

            $intersect = (($yi > $lng) !== ($yj > $lng))
                && ($lat < ($xj - $xi) * ($lng - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    /**
     * Perhitungan jarak dua koordinat bumi menggunakan Haversine Formula (dalam meter).
     */
    public function calculateHaversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // meter
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
