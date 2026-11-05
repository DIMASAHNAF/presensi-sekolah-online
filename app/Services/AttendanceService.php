<?php

namespace App\Services;

use App\Models\Presensi;
use App\Models\SesiPresensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{
    /**
     * Menghitung status kehadiran ('ONTIME' atau 'LATE') dan menit keterlambatan
     * berdasarkan jam server resmi (WIB / Asia/Jakarta).
     */
    public function calculateLate(SesiPresensi $sesi, ?Carbon $now = null): array
    {
        $now = $now ? $now->copy()->setTimezone('Asia/Jakarta') : Carbon::now('Asia/Jakarta');
        $dayOfWeek = $now->dayOfWeek; // 0 = Sunday, 5 = Friday

        // 1. Tentukan batas jam masuk sekolah resmi SMKN 1 Beringin (07:30 WIB)
        $thresholdTimeStr = config('services.fonnte.jam_masuk', '07:30');
        if (strlen($thresholdTimeStr) === 5) {
            $thresholdTimeStr .= ':00';
        }

        // Jika sesi memiliki jam pelajaran spesifik (misal sesi mapel di les tertentu)
        if ($sesi->jam_pelajaran_id && $sesi->jamPelajaran) {
            $thresholdTimeStr = $sesi->jamPelajaran->jam_mulai;
        }

        $thresholdToday = Carbon::createFromTimeString($thresholdTimeStr, 'Asia/Jakarta');

        $checkInStatus = 'ONTIME';
        $lateMinutes = 0;

        if ($now->greaterThan($thresholdToday)) {
            $diffMinutes = (int) $thresholdToday->diffInMinutes($now, false);
            if ($diffMinutes > 0) {
                $checkInStatus = 'LATE';
                $lateMinutes = $diffMinutes;
            }
        }

        return [
            'check_in_status' => $checkInStatus,
            'late_minutes'    => $lateMinutes,
            'threshold_time'  => $thresholdTimeStr,
            'server_time'     => $now->format('H:i:s'),
        ];
    }

    /**
     * Mengompresi foto snapshot dari base64 ke format WebP (maksimal 150KB)
     * dan menyimpannya di storage disk public.
     */
    public function compressAndStorePhoto(
        string $base64Image,
        int $userId,
        int $sesiId,
        string $subfolder = 'face_scans'
    ): ?string {
        try {
            if (str_contains($base64Image, ',')) {
                $base64Image = explode(',', $base64Image, 2)[1];
            }

            $binary = base64_decode($base64Image);
            if (!$binary) {
                return null;
            }

            // Gunakan GD Image untuk konversi dan resize jika perlu
            $sourceImage = @imagecreatefromstring($binary);
            if (!$sourceImage) {
                return null;
            }

            $origWidth = imagesx($sourceImage);
            $origHeight = imagesy($sourceImage);

            // Batasi resolusi maksimum ke 640px lebar agar ukuran tetap kompak
            $targetWidth = min(640, $origWidth);
            $targetHeight = (int) round(($targetWidth / $origWidth) * $origHeight);

            if ($targetWidth !== $origWidth || $targetHeight !== $origHeight) {
                $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);
                imagecopyresampled(
                    $resizedImage,
                    $sourceImage,
                    0, 0, 0, 0,
                    $targetWidth, $targetHeight,
                    $origWidth, $origHeight
                );
                imagedestroy($sourceImage);
                $imageToSave = $resizedImage;
            } else {
                $imageToSave = $sourceImage;
            }

            // Kompresi ke format WebP dengan batas maksimal 150KB
            $quality = 82;
            $webpData = null;

            do {
                ob_start();
                imagewebp($imageToSave, null, $quality);
                $webpData = ob_get_clean();
                $quality -= 10;
            } while (strlen($webpData) > 153600 && $quality >= 40); // 150 KB = 153,600 bytes

            imagedestroy($imageToSave);

            if (!$webpData) {
                return null;
            }

            $today = date('Y-m-d');
            $folder = "{$subfolder}/{$today}";
            $filename = "{$folder}/{$userId}_{$sesiId}_" . time() . '.webp';

            Storage::disk('public')->put($filename, $webpData);

            return $filename;
        } catch (\Throwable $e) {
            Log::warning("[AttendanceService] Gagal kompresi foto WebP: " . $e->getMessage());
            return null;
        }
    }
}

