<?php
// routes/web.php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'chooseRole'])->name('choose-role');

    // Siswa
    Route::get('/login/siswa', [AuthController::class, 'showLoginSiswa'])->name('login.siswa');
    Route::post('/login/siswa', [AuthController::class, 'loginSiswa']);
    Route::get('/register/siswa', [AuthController::class, 'showRegisterSiswa'])->name('register.siswa');
    Route::post('/register/siswa', [AuthController::class, 'registerSiswa']);

    // Guru
    Route::get('/login/guru', [AuthController::class, 'showLoginGuru'])->name('login.guru');
    Route::post('/login/guru', [AuthController::class, 'loginGuru']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/siswa/dashboard', fn () => view('siswa.dashboard'))->name('siswa.dashboard');
    Route::get('/guru/dashboard', fn () => view('guru.dashboard'))->name('guru.dashboard');
});