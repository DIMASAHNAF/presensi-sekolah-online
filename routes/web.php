<?php

// routes/web.php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\IzinSiswaController;
use App\Http\Controllers\Kiosk\KioskController;
use App\Http\Controllers\Siswa\SiswaController;
use Illuminate\Support\Facades\Route;

// ============================================================
//  GUEST ROUTES (Rate Limited)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'chooseRole'])->name('choose-role');

    // Siswa auth
    Route::get('/login/siswa', [AuthController::class, 'showLoginSiswa'])->name('login.siswa');
    Route::post('/login/siswa', [AuthController::class, 'loginSiswa'])->middleware('throttle:login');
    Route::get('/register/siswa', [AuthController::class, 'showRegisterSiswa'])->name('register.siswa');
    Route::post('/register/siswa', [AuthController::class, 'registerSiswa'])->middleware('throttle:register');

    // Guru + Admin auth (1 form, NIK + password)
    Route::get('/login/guru', [AuthController::class, 'showLoginGuru'])->name('login.guru');
    Route::post('/login/guru', [AuthController::class, 'loginGuru'])->middleware('throttle:login');
});

// ============================================================
//  PANDUAN PENGGUNAAN & SOP PRESENSI (PUBLIC PDF & WEB)
// ============================================================
Route::get('/panduan', function () {
    $pdfPath = public_path('docs/Panduan_Penggunaan_Presensi_SMKN1_Beringin.pdf');
    if (file_exists($pdfPath) && request('view') !== 'html') {
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Buku_Panduan_Presensi_SMKN1_Beringin.pdf"'
        ]);
    }
    return view('panduan.index');
})->name('panduan');

Route::get('/panduan/download', function () {
    $pdfPath = public_path('docs/Panduan_Penggunaan_Presensi_SMKN1_Beringin.pdf');
    if (file_exists($pdfPath)) {
        return response()->download($pdfPath, 'Buku_Panduan_Presensi_SMKN1_Beringin.pdf');
    }
    return redirect()->route('panduan');
})->name('panduan.download');

Route::get('/panduan/pdf', function () {
    $pdfPath = public_path('docs/Panduan_Penggunaan_Presensi_SMKN1_Beringin.pdf');
    if (file_exists($pdfPath)) {
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Buku_Saku_Panduan_Presensi_SMKN1_Beringin.pdf"'
        ]);
    }
    return redirect()->route('panduan');
})->name('panduan.pdf');

//  SISWA DASHBOARD (Rate Limited for Heavy Endpoints)
Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/siswa/dashboard', [SiswaController::class, 'dashboard'])->name('siswa.dashboard');
    Route::get('/siswa/sesi-aktif', [SiswaController::class, 'getSesiAktif'])->name('siswa.sesiaktif')->middleware('throttle:60,1');
    Route::post('/siswa/scan-wajah', [SiswaController::class, 'scanWajah'])->name('siswa.scanwajah')->middleware('throttle:scan');
    // Re-enroll wajah untuk siswa yang sudah ada (belum punya face_descriptor)
    Route::get('/siswa/enroll-wajah', [SiswaController::class, 'showEnrollWajah'])->name('siswa.enroll');
    Route::post('/siswa/enroll-wajah', [SiswaController::class, 'enrollWajah'])->name('siswa.enroll.store')->middleware('throttle:enroll');
    Route::post('/siswa/profile', [SiswaController::class, 'updateProfile'])->name('siswa.profile.update')->middleware('throttle:30,1');
    Route::post('/siswa/update-password', [SiswaController::class, 'updatePassword'])->name('siswa.password.update');
});

//  GURU & ADMIN DASHBOARD
Route::middleware(['auth', 'role:guru,walikelas,admin'])->prefix('dashboard')->name('dashboard')->group(function () {
    // Overview
    Route::get('/', [DashboardController::class, 'index'])->name('');
    Route::get('/stats-json', [DashboardController::class, 'statsJson'])->name('.stats-json');

    // Presensi
    Route::get('/presensi', [DashboardController::class, 'presensiIndex'])->name('.presensi');
    Route::post('/presensi', [DashboardController::class, 'storeSesi'])->name('.presensi.store');
    Route::get('/presensi/{sesiPresensi}', [DashboardController::class, 'presensiDetail'])->name('.presensi.detail');
    Route::get('/presensi/{sesiPresensi}/live', [DashboardController::class, 'presensiLiveJson'])->name('.presensi.live');
    Route::get('/presensi/{sesiPresensi}/pdf', [DashboardController::class, 'exportPdf'])->name('.presensi.pdf');
    Route::get('/presensi/{sesiPresensi}/excel', [DashboardController::class, 'exportExcel'])->name('.presensi.excel');
    Route::get('/presensi/export/harian', [DashboardController::class, 'exportPdfHarian'])->name('.presensi.pdf.harian');
    Route::get('/presensi/export/harian/excel', [DashboardController::class, 'exportExcelHarian'])->name('.presensi.excel.harian');
    Route::get('/presensi/export/bulanan-kelas', [DashboardController::class, 'exportBulananKelas'])->name('.presensi.pdf.bulanan.kelas');
    Route::get('/presensi/export/bulanan-kelas/excel', [DashboardController::class, 'exportExcelBulananKelas'])->name('.presensi.excel.bulanan.kelas');
    Route::get('/presensi/export/bulanan-mapel', [DashboardController::class, 'exportBulananMapel'])->name('.presensi.pdf.bulanan.mapel');
    Route::get('/presensi/export/bulanan-mapel/excel', [DashboardController::class, 'exportExcelBulananMapel'])->name('.presensi.excel.bulanan.mapel');
    Route::patch('/presensi/record/{presensi}', [DashboardController::class, 'updateRecord'])->name('.presensi.record.update');
    Route::post('/presensi/{sesiPresensi}/sync-siswa', [DashboardController::class, 'syncSiswaSesi'])->name('.presensi.sync-siswa');
    Route::patch('/presensi/{sesiPresensi}/close', [DashboardController::class, 'closeSesi'])->name('.presensi.close');
    Route::post('/presensi/{sesiPresensi}/reset', [DashboardController::class, 'resetAbsenSesi'])->name('.presensi.reset');
    Route::delete('/presensi/delete-all', [DashboardController::class, 'deleteAllSesi'])->name('.presensi.delete-all');

    // Buku Izin & Sakit Siswa (Surat Izin / Sakit)
    Route::get('/izin', [IzinSiswaController::class, 'index'])->name('.izin');
    Route::post('/izin', [IzinSiswaController::class, 'store'])->name('.izin.store');
    Route::delete('/izin/{izin}', [IzinSiswaController::class, 'destroy'])->name('.izin.destroy');

    // Rekap & CRUD Nilai Siswa (Dashboard Guru & Admin)
    Route::get('/nilai', [DashboardController::class, 'nilaiIndex'])->name('.nilai');
    Route::get('/kelas/{kelas}/siswa-json', [DashboardController::class, 'siswaPerKelasJson'])->name('.kelas.siswa-json');
    Route::post('/nilai', [DashboardController::class, 'storeNilai'])->name('.nilai.store');
    Route::post('/nilai/batch', [DashboardController::class, 'batchStoreNilai'])->name('.nilai.batch-store');
    Route::get('/nilai/export-leger/excel', [DashboardController::class, 'exportLegerExcel'])->name('.nilai.excel.leger');
    Route::get('/nilai/export-leger/print', [DashboardController::class, 'printLeger'])->name('.nilai.print.leger');
    Route::put('/nilai/{nilaiSiswa}', [DashboardController::class, 'updateNilai'])->name('.nilai.update');
    Route::delete('/nilai/{nilaiSiswa}', [DashboardController::class, 'destroyNilai'])->name('.nilai.destroy');

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

    // Pengaturan Lokasi & Geofencing
    Route::get('/pengaturan-lokasi', [DashboardController::class, 'lokasiIndex'])->name('.lokasi');
    Route::post('/pengaturan-lokasi', [DashboardController::class, 'updateLokasi'])->name('.lokasi.update');

    // Manajemen Storage Mount 100GB
    Route::get('/storage', [DashboardController::class, 'storageIndex'])->name('.storage');
    Route::post('/storage/cleanup', [DashboardController::class, 'cleanupStorage'])->name('.storage.cleanup');
    Route::post('/storage/organize-legacy', [DashboardController::class, 'organizeLegacyStorage'])->name('.storage.organize');
    Route::delete('/storage/file', [DashboardController::class, 'deleteStudentFile'])->name('.storage.file.delete');
});

// ============================================================
//  KIOSK GERBANG (FACE ID TABLET ATTENDANCE)
//  Catatan keamanan:
//  - /kiosk (GET) = public agar tablet tidak perlu login
//  - /kiosk/identify = throttle 360/menit, proteksi IP whitelist di controller
//  - /kiosk/sync = throttle ketat + cek IP whitelist di controller (dipindahkan ke sini)
//  - /kiosk/live-stats = throttle ketat (polling tiap 15 detik, max 8/menit per IP)
// ============================================================
Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk.index');
Route::get('/kiosk/gerbang', [KioskController::class, 'index'])->name('kiosk.gerbang');
Route::post('/kiosk/identify', [KioskController::class, 'identifyAndAttend'])->name('kiosk.identify')->middleware('throttle:360,1');
Route::post('/kiosk/sync', [KioskController::class, 'syncCandidates'])->name('kiosk.sync')->middleware('throttle:5,1');
Route::get('/kiosk/live-stats', [KioskController::class, 'liveStats'])->name('kiosk.stats')->middleware('throttle:30,1');

//  AUTH
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

