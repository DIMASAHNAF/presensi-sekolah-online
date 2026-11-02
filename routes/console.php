<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Otomatisasi generate Sesi Presensi jam 05:00 WIB subuh setiap hari sekolah sesuai Roster Jadwal Pelajaran
Schedule::command('presensi:generate-daily-sessions')
    ->dailyAt('05:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();

// Auto-sync biometrik siswa ke RAM face-api setiap pagi jam 05:05 WIB
// (setelah sesi di-generate) agar operator tidak perlu klik sync manual tiap hari
Schedule::call(function () {
    try {
        $faceService = app(\App\Services\FaceService::class);
        $result = $faceService->syncAllEnrolledStudents();
        Log::info('[AutoSync] Biometrik siswa berhasil di-sync ke RAM: ' . ($result['total'] ?? 0) . ' siswa');
    } catch (\Throwable $e) {
        Log::error('[AutoSync] Gagal sync biometrik: ' . $e->getMessage());
    }
})
    ->dailyAt('05:05')
    ->timezone('Asia/Jakarta')
    ->name('auto-sync-biometric')
    ->withoutOverlapping();

// Bersihkan cache hadir hari ini setiap tengah malam (reset untuk hari baru)
Schedule::call(function () {
    \Illuminate\Support\Facades\Cache::forget('hadir_count_' . today()->toDateString());
    \Illuminate\Support\Facades\Cache::forget('hadir_count_' . now()->subDay()->toDateString());
})
    ->dailyAt('00:01')
    ->timezone('Asia/Jakarta')
    ->name('reset-hadir-cache');
