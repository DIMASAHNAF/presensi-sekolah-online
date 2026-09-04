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
];
