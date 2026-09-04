<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class FaceService
{
    protected string $pythonBin;
    protected string $scriptPath;

    public function __construct()
    {
        $this->pythonBin  = config('face.python_bin', 'python3');
        $this->scriptPath = base_path('python/face_service.py');
    }

    /**
     * Ekstrak face descriptor dari satu gambar base64.
     * Return: ['success' => bool, 'descriptor' => array|null, 'error' => string|null]
     */
    public function extract(string $imageB64): array
    {
        $payload = json_encode(['image_b64' => $imageB64]);
        return $this->call('extract', $payload);
    }

    /**
     * Enroll wajah dari array base64 gambar (multi-angle).
     * Return: ['success' => bool, 'descriptor' => array|null, 'processed' => int, 'error' => string|null]
     */
    public function enroll(array $imagesB64): array
    {
        $payload = json_encode(['images_b64' => $imagesB64]);
        return $this->call('enroll', $payload);
    }

    /**
     * Bandingkan stored descriptor dengan foto baru.
     * Return: ['success' => bool, 'match' => bool, 'distance' => float, 'confidence' => string]
     */
    public function compare(array $storedDescriptor, string $imageB64): array
    {
        $payload = json_encode([
            'stored'    => $storedDescriptor,
            'image_b64' => $imageB64,
        ]);

        return $this->call('compare', $payload);
    }

    /**
     * Test apakah semua dependensi Python sudah terinstall.
     */
    public function test(): array
    {
        return $this->call('test', null);
    }

    /**
     * Panggil Python script dan decode hasilnya.
     */
    protected function call(string $mode, ?string $payloadJson): array
    {
        if (!file_exists($this->scriptPath)) {
            return ['success' => false, 'error' => 'Python face_service.py tidak ditemukan.'];
        }

        $escapedScript  = escapeshellarg($this->scriptPath);
        $escapedMode    = escapeshellarg($mode);
        $escapedPayload = $payloadJson ? escapeshellarg($payloadJson) : '';

        if ($payloadJson) {
            $cmd = "{$this->pythonBin} {$escapedScript} {$escapedMode} {$escapedPayload} 2>&1";
        } else {
            $cmd = "{$this->pythonBin} {$escapedScript} {$escapedMode} 2>&1";
        }

        $output     = null;
        $returnCode = 0;

        exec($cmd, $outputLines, $returnCode);
        $output = implode("\n", $outputLines);

        // Ambil baris JSON terakhir (Python mungkin print warning dulu)
        $lines = array_filter(array_map('trim', $outputLines));
        $jsonLine = '';
        foreach (array_reverse(array_values($lines)) as $line) {
            if (str_starts_with($line, '{')) {
                $jsonLine = $line;
                break;
            }
        }

        if (!$jsonLine) {
            Log::error("[FaceService] No JSON output from Python. Output: {$output}");
            return ['success' => false, 'error' => 'Python script tidak menghasilkan output JSON.'];
        }

        $result = json_decode($jsonLine, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error("[FaceService] Invalid JSON from Python: {$jsonLine}");
            return ['success' => false, 'error' => 'Output Python tidak valid.'];
        }

        if ($returnCode !== 0 && !isset($result['success'])) {
            Log::error("[FaceService] Python returned non-zero exit: {$returnCode}. Output: {$output}");
        }

        return $result;
    }
}
