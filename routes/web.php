<?php
// routes/web.php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Siswa\SiswaController;
use Illuminate\Support\Facades\Route;

// ============================================================
//  GUEST ROUTES
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'chooseRole'])->name('choose-role');

    // Siswa auth
    Route::get('/login/siswa',    [AuthController::class, 'showLoginSiswa'])->name('login.siswa');
    Route::post('/login/siswa',   [AuthController::class, 'loginSiswa']);
    Route::get('/register/siswa', [AuthController::class, 'showRegisterSiswa'])->name('register.siswa');
    Route::post('/register/siswa',[AuthController::class, 'registerSiswa']);

    // Guru + Admin auth (1 form, NIK + password)
    Route::get('/login/guru',  [AuthController::class, 'showLoginGuru'])->name('login.guru');
    Route::post('/login/guru', [AuthController::class, 'loginGuru']);
});

// ============================================================
//  SISWA DASHBOARD
// ============================================================
Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/siswa/dashboard', [SiswaController::class, 'dashboard'])->name('siswa.dashboard');
    Route::post('/siswa/scan',     [SiswaController::class, 'scanBarcode'])->name('siswa.scan');
});

// ============================================================
//  GURU & ADMIN DASHBOARD
// ============================================================
Route::middleware(['auth', 'role:guru,admin'])->prefix('dashboard')->name('dashboard')->group(function () {
    // Overview
    Route::get('/', [DashboardController::class, 'index'])->name('');

    // Absensi
    Route::get('/absensi',                 [DashboardController::class, 'absensiIndex'])->name('.absensi');
    Route::post('/absensi',                [DashboardController::class, 'storeSesi'])->name('.absensi.store');
    Route::get('/absensi/{sesiAbsensi}',   [DashboardController::class, 'absensiDetail'])->name('.absensi.detail');
    Route::patch('/absensi/record/{absensi}', [DashboardController::class, 'updateRecord'])->name('.absensi.record.update');
    Route::patch('/absensi/{sesiAbsensi}/close', [DashboardController::class, 'closeSesi'])->name('.absensi.close');

    // Admin-only CRUD
    Route::get('/siswa',         [DashboardController::class, 'siswaIndex'])->name('.siswa');
    Route::post('/siswa',        [DashboardController::class, 'storeSiswa'])->name('.siswa.store');
    Route::put('/siswa/{siswa}', [DashboardController::class, 'updateSiswa'])->name('.siswa.update');
    Route::delete('/siswa/{siswa}', [DashboardController::class, 'destroySiswa'])->name('.siswa.destroy');

    Route::get('/guru',         [DashboardController::class, 'guruIndex'])->name('.guru');
    Route::post('/guru',        [DashboardController::class, 'storeGuru'])->name('.guru.store');
    Route::put('/guru/{guru}',  [DashboardController::class, 'updateGuru'])->name('.guru.update');
    Route::delete('/guru/{guru}',[DashboardController::class, 'destroyGuru'])->name('.guru.destroy');

    Route::get('/kelas',          [DashboardController::class, 'kelasIndex'])->name('.kelas');
    Route::post('/kelas',         [DashboardController::class, 'storeKelas'])->name('.kelas.store');
    Route::put('/kelas/{kelas}',  [DashboardController::class, 'updateKelas'])->name('.kelas.update');
    Route::delete('/kelas/{kelas}',[DashboardController::class, 'destroyKelas'])->name('.kelas.destroy');
});

// ============================================================
//  AUTH
// ============================================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});