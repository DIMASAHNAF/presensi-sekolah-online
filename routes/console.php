<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Otomatisasi generate Sesi Presensi jam 05:00 WIB subuh setiap hari sekolah sesuai Roster Jadwal Pelajaran
Schedule::command('presensi:generate-daily-sessions')
    ->dailyAt('05:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
