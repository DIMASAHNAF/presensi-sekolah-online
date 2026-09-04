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
    Route::get('/login/siswa', [AuthController::class, 'showLoginSiswa'])->name('login.siswa');
    Route::post('/login/siswa', [AuthController::class, 'loginSiswa']);
    Route::get('/register/siswa', [AuthController::class, 'showRegisterSiswa'])->name('register.siswa');
    Route::post('/register/siswa', [AuthController::class, 'registerSiswa']);

    // Guru + Admin auth (1 form, NIK + password)
    Route::get('/login/guru', [AuthController::class, 'showLoginGuru'])->name('login.guru');
    Route::post('/login/guru', [AuthController::class, 'loginGuru']);
});

//  SISWA DASHBOARD
Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/siswa/dashboard', [SiswaController::class, 'dashboard'])->name('siswa.dashboard');
    Route::get('/siswa/sesi-aktif', [SiswaController::class, 'getSesiAktif'])->name('siswa.sesiaktif');
    Route::post('/siswa/scan-wajah', [SiswaController::class, 'scanWajah'])->name('siswa.scanwajah');
    // Re-enroll wajah untuk siswa yang sudah ada (belum punya face_descriptor)
    Route::get('/siswa/enroll-wajah', [SiswaController::class, 'showEnrollWajah'])->name('siswa.enroll');
    Route::post('/siswa/enroll-wajah', [SiswaController::class, 'enrollWajah'])->name('siswa.enroll.store');
});

//  GURU & ADMIN DASHBOARD
Route::middleware(['auth', 'role:guru,admin'])->prefix('dashboard')->name('dashboard')->group(function () {
    // Overview
    Route::get('/', [DashboardController::class, 'index'])->name('');

    // Presensi
    Route::get('/presensi', [DashboardController::class, 'presensiIndex'])->name('.presensi');
    Route::post('/presensi', [DashboardController::class, 'storeSesi'])->name('.presensi.store');
    Route::get('/presensi/{sesiPresensi}', [DashboardController::class, 'presensiDetail'])->name('.presensi.detail');
    Route::get('/presensi/{sesiPresensi}/live', [DashboardController::class, 'presensiLiveJson'])->name('.presensi.live');
    Route::get('/presensi/{sesiPresensi}/pdf', [DashboardController::class, 'exportPdf'])->name('.presensi.pdf');
    Route::get('/presensi/export/harian', [DashboardController::class, 'exportPdfHarian'])->name('.presensi.pdf.harian');
    Route::get('/presensi/export/bulanan-kelas', [DashboardController::class, 'exportBulananKelas'])->name('.presensi.pdf.bulanan.kelas');
    Route::get('/presensi/export/bulanan-mapel', [DashboardController::class, 'exportBulananMapel'])->name('.presensi.pdf.bulanan.mapel');
    Route::patch('/presensi/record/{presensi}', [DashboardController::class, 'updateRecord'])->name('.presensi.record.update');
    Route::patch('/presensi/{sesiPresensi}/close', [DashboardController::class, 'closeSesi'])->name('.presensi.close');
    Route::post('/presensi/{sesiPresensi}/reset', [DashboardController::class, 'resetAbsenSesi'])->name('.presensi.reset');
    Route::delete('/presensi/delete-all', [DashboardController::class, 'deleteAllSesi'])->name('.presensi.delete-all');

    // Admin-only CRUD
    Route::get('/siswa', [DashboardController::class, 'siswaIndex'])->name('.siswa');
    Route::post('/siswa', [DashboardController::class, 'storeSiswa'])->name('.siswa.store');
    Route::post('/siswa/reset-all-faces', [DashboardController::class, 'resetAllFaces'])->name('.siswa.reset-all-faces');
    Route::put('/siswa/{siswa}', [DashboardController::class, 'updateSiswa'])->name('.siswa.update');
    Route::delete('/siswa/{siswa}', [DashboardController::class, 'destroySiswa'])->name('.siswa.destroy');
    Route::post('/siswa/{siswa}/reset-face', [DashboardController::class, 'resetFaceSiswa'])->name('.siswa.reset-face');

    Route::get('/guru', [DashboardController::class, 'guruIndex'])->name('.guru');
    Route::post('/guru', [DashboardController::class, 'storeGuru'])->name('.guru.store');
    Route::put('/guru/{guru}', [DashboardController::class, 'updateGuru'])->name('.guru.update');
    Route::delete('/guru/{guru}', [DashboardController::class, 'destroyGuru'])->name('.guru.destroy');

    Route::get('/kelas', [DashboardController::class, 'kelasIndex'])->name('.kelas');
    Route::post('/kelas', [DashboardController::class, 'storeKelas'])->name('.kelas.store');
    Route::put('/kelas/{kelas}', [DashboardController::class, 'updateKelas'])->name('.kelas.update');
    Route::delete('/kelas/{kelas}', [DashboardController::class, 'destroyKelas'])->name('.kelas.destroy');

    Route::get('/log', [DashboardController::class, 'logPresensiIndex'])->name('.log');
    Route::post('/reset-sesi', [DashboardController::class, 'resetSesi'])->name('.reset-sesi');
});

//  AUTH
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
