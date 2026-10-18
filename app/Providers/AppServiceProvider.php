<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!app()->runningInConsole()) {
            if (request()->header('X-Forwarded-Proto') === 'https' || request()->isSecure()) {
                URL::forceScheme('https');
            }
        }

        // Set Carbon locale ke Bahasa Indonesia
        Carbon::setLocale('id');

        // ── Custom Rate Limiting: Mencegah Brute-Force tanpa memblokir IP bersama (WiFi Sekolah/CGNAT) ──
        RateLimiter::for('login', function (Request $request) {
            $identifier = strtolower(trim((string) (
                $request->input('identifier') ??
                $request->input('nisn') ??
                $request->input('nik') ??
                $request->input('username') ??
                $request->input('email') ??
                'guest'
            )));

            return Limit::perMinute(5)->by($identifier . '|' . $request->ip());
        });

        RateLimiter::for('register', function (Request $request) {
            $nisn = trim((string) ($request->input('nisn') ?? 'guest'));
            return [
                Limit::perMinute(5)->by($nisn . '|' . $request->ip()),
                Limit::perMinute(120)->by($request->ip()),
            ];
        });

        // Pendaftaran Wajah (Enroll): Berdasarkan Akun Siswa (Bukan IP WiFi bersama)
        RateLimiter::for('enroll', function (Request $request) {
            $key = $request->user()?->id ? ('user:' . $request->user()->id) : ('ip:' . $request->ip());
            return Limit::perMinute(20)->by($key);
        });

        // Presensi Scan Wajah: Berdasarkan Akun Siswa (Bukan IP WiFi bersama)
        RateLimiter::for('scan', function (Request $request) {
            $key = $request->user()?->id ? ('user:' . $request->user()->id) : ('ip:' . $request->ip());
            return Limit::perMinute(30)->by($key);
        });
    }
}
