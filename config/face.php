<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Python Binary Path
    |--------------------------------------------------------------------------
    | Path ke executable Python 3. Biasanya 'python3' atau '/usr/bin/python3'.
    | Bisa di-override via .env: FACE_PYTHON_BIN=/usr/local/bin/python3
    */
    'python_bin' => env('FACE_PYTHON_BIN', 'python3'),

    /*
    |--------------------------------------------------------------------------
    | Euclidean Distance Threshold
    |--------------------------------------------------------------------------
    | Makin kecil = makin ketat. 0.45 adalah nilai yang cukup ketat.
    | Nilai ini juga di-hardcode di face_service.py untuk konsistensi.
    */
    'threshold' => env('FACE_THRESHOLD', 0.45),

    /*
    |--------------------------------------------------------------------------
    | Minimum Face Samples untuk Enrollment
    |--------------------------------------------------------------------------
    | Jumlah minimum foto yang harus berhasil diproses saat enrollment.
    */
    'min_enroll_samples' => env('FACE_MIN_SAMPLES', 3),

    /*
    |--------------------------------------------------------------------------
    | Face API URL
    |--------------------------------------------------------------------------
    | URL endpoint FastAPI Face Recognition.
    */
    'api_url' => env('FACE_API_URL', 'http://127.0.0.1:8005'),

    /*
    |--------------------------------------------------------------------------
    | Anti-Spoofing & Liveness Settings
    |--------------------------------------------------------------------------
    | Ambang batas MiniFASNet V2 (0.20 optimal untuk webcam/kamera HP siswa).
    | check_liveness_siswa: jika false, mengandalkan active liveness di browser (senyum/kedip).
    */
    'liveness_threshold'   => env('LIVENESS_THRESHOLD', 0.20),
    'check_liveness_siswa' => env('FACE_CHECK_LIVENESS_SISWA', true),
    'threshold_512'        => (float) env('THRESHOLD_512', 0.88),
];
