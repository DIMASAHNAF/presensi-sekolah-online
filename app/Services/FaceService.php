<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FaceService
{
    protected string $apiUrl;

    public function __construct()
    {
        // Gunakan port 8005 tempat FastAPI berjalan
        $this->apiUrl = config('face.api_url', 'http://127.0.0.1:8005');
    }

    /**
     * Identifikasi 1:N (Face ID Kiosk Gerbang).
     * Mencocokkan foto snapshot dengan seluruh siswa terdaftar.
     */
    public function identify(string $imageB64, ?array $candidates = null, ?float $threshold = null): array
    {
        $payload = [
            'image_b64' => $imageB64,
        ];
        if ($candidates !== null) {
            $payload['candidates'] = $candidates;
        }
        if ($threshold !== null) {
            $payload['threshold'] = $threshold;
        }

        $res = $this->call('identify', $payload);

        // Jika FastAPI melaporkan database kosong di RAM, lakukan auto-sync sekali lalu coba ulang
        if (isset($res['need_sync']) && $res['need_sync'] === true) {
            $syncRes = $this->syncAllEnrolledStudents();
            if ($syncRes['success'] && ($syncRes['total'] ?? 0) > 0) {
                // Retry identify setelah disinkronkan
                return $this->call('identify', $payload);
            }
        }

        return $res;
    }

    /**
     * Sinkronkan daftar kandidat ke RAM server FastAPI.
     */
    public function syncCandidates(array $candidates): array
    {
        return $this->call('sync-candidates', ['candidates' => $candidates]);
    }

    /**
     * Ambil status cache kandidat di FastAPI.
     */
    public function getCandidatesStats(): array
    {
        return $this->call('candidates-stats', []);
    }

    /**
     * Ambil seluruh siswa aktif yang memiliki face_descriptor dari DB,
     * lalu sinkronkan langsung ke memori RAM FastAPI.
     */
    public function syncAllEnrolledStudents(): array
    {
        try {
            $students = \App\Models\User::where('role', 'siswa')
                ->whereNotNull('face_descriptor')
                ->get(['id', 'face_descriptor']);

            $candidates = [];
            foreach ($students as $s) {
                $desc = is_array($s->face_descriptor)
                    ? $s->face_descriptor
                    : json_decode($s->face_descriptor, true);

                if (is_array($desc) && in_array(count($desc), [128, 512])) {
                    $candidates[] = [
                        'id' => (int) $s->id,
                        'descriptor' => array_values($desc),
                    ];
                }
            }

            // Jika tidak ada siswa terdaftar, kosongkan memori Python agar data reset terhapus tuntas
            if (empty($candidates)) {
                $this->syncCandidates([]);
                return [
                    'success' => true,
                    'message' => 'Seluruh database wajah di RAM berhasil dikosongkan.',
                    'total' => 0,
                ];
            }

            return $this->syncCandidates($candidates);
        } catch (\Throwable $e) {
            Log::error('[FaceService] syncAllEnrolledStudents failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'total' => 0,
            ];
        }
    }

    /**
     * Hitung jarak Euclidean antara dua vektor (128-D atau 512-D) untuk deteksi duplikasi wajah.
     */
    public function calculateEuclideanDistance(array $a, array $b): ?float
    {
        $len = count($a);
        if ($len !== count($b) || !in_array($len, [128, 512])) {
            return null;
        }
        $sum = 0.0;
        for ($i = 0; $i < $len; $i++) {
            $diff = (float) $a[$i] - (float) $b[$i];
            $sum += $diff * $diff;
        }
        return sqrt($sum);
    }

    /**
     * Ekstrak face descriptor dari satu gambar base64.
     * Return: ['success' => bool, 'descriptor' => array|null, 'error' => string|null]
     */
    public function extract(string $imageB64): array
    {
        return $this->call('extract', ['image_b64' => $imageB64]);
    }

    /**
     * Enroll wajah dari array base64 gambar (multi-angle).
     * Return: ['success' => bool, 'descriptor' => array|null, 'processed' => int, 'error' => string|null]
     */
    public function enroll(array $imagesB64): array
    {
        return $this->call('enroll', ['images_b64' => $imagesB64]);
    }

    /**
     * Bandingkan stored descriptor dengan foto baru.
     * Return: ['success' => bool, 'match' => bool, 'distance' => float, 'confidence' => string]
     */
    public function compare(array $storedDescriptor, string $imageB64): array
    {
        return $this->call('compare', [
            'stored'    => $storedDescriptor,
            'image_b64' => $imageB64,
        ]);
    }

    /**
     * Test apakah semua dependensi Python sudah terinstall.
     */
    public function test(): array
    {
        return $this->call('test', []);
    }

    /**
     * Panggil Python FastAPI menggunakan HTTP.
     */
    protected function call(string $mode, array $payload): array
    {
        $url = rtrim($this->apiUrl, '/') . '/' . $mode;

        try {
            if ($mode === 'test') {
                $response = Http::timeout(5)->get($url);
            } else {
                $response = Http::timeout(10)->post($url, $payload);
            }

            if ($response->successful()) {
                return $response->json();
            }

            Log::error("[FaceService] API returned status {$response->status()} at {$url}: {$response->body()}");
            return ['success' => false, 'error' => 'Gagal terhubung ke API Face Recognition (Status ' . $response->status() . ').'];
        } catch (\Exception $e) {
            Log::error("[FaceService] API Exception at {$url}: " . $e->getMessage());
            return ['success' => false, 'error' => 'API Face Recognition mati atau tidak merespons. Pastikan service PM2/Python berjalan.'];
        }
    }
}
