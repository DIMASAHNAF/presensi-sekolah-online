<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FaceService
{
    protected string $apiUrl;

    public function __construct()
    {
        // Gunakan port 8000 tempat FastAPI berjalan
        $this->apiUrl = config('face.api_url', 'http://127.0.0.1:8000');
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
