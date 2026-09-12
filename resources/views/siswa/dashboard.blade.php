<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Presensi">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
    <title>Portal Presensi Siswa — SMKN 1 Beringin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/dist/face-api.min.js"></script>
    <style>
        body,
        button,
        input,
        select {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        h1,
        h2,
        h3,
        .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        [x-cloak] {
            display: none !important;
        }

        body {
            background-color: #f1f5f9;
            min-height: 100vh;
        }

        /* ── Profile Header ── */
        .profile-hero {
            background: #1d4ed8;
            position: relative;
            overflow: hidden;
        }
        .profile-hero::before {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            top: -60px; right: -40px;
        }

        /* ── Stat pill cards ── */
        .stat-pill {
            border-radius: 1rem;
            padding: 1rem 0.5rem 0.85rem;
            text-align: center;
            transition: transform 0.15s ease;
        }
        .stat-pill:hover { transform: translateY(-2px); }
        .stat-pill.hadir  { background: #dcfce7; border: 1.5px solid #86efac; }
        .stat-pill.izin   { background: #fef9c3; border: 1.5px solid #fde047; }
        .stat-pill.sakit  { background: #dbeafe; border: 1.5px solid #93c5fd; }
        .stat-pill.alpa   { background: #fee2e2; border: 1.5px solid #fca5a5; }

        /* ── Micro Badges ── */
        .badge-hadir {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-izin {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .badge-sakit {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .badge-alpa {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.65rem;
            border-radius: 8px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            animation: sesi-pulse 1.8s ease infinite;
        }

        @keyframes sesi-pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }

            50% {
                box-shadow: 0 0 0 8px rgba(16, 185, 129, 0);
            }
        }

        /* ── Face Oval HUD ── */
        .face-oval {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -55%);
            width: 190px;
            height: 240px;
            border-radius: 50% / 45%;
            border: 2.5px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.55);
            pointer-events: none;
            z-index: 5;
            transition: border-color .3s, box-shadow .3s;
        }

        .face-oval.detected {
            border-color: #10b981;
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.55), 0 0 20px rgba(16, 185, 129, 0.6);
        }

        .face-oval.success {
            border-color: #10b981;
            box-shadow: 0 0 0 9999px rgba(6, 78, 59, 0.6), 0 0 30px rgba(16, 185, 129, 0.85);
        }

        #video-scan {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        @keyframes checkmark-pop {
            0% {
                transform: scale(0) rotate(-20deg);
                opacity: 0;
            }

            60% {
                transform: scale(1.15) rotate(4deg);
                opacity: 1;
            }

            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        .checkmark-pop {
            animation: checkmark-pop .45s cubic-bezier(.17, .67, .4, 1.2) forwards;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            20%,
            60% {
                transform: translateX(-5px);
            }

            40%,
            80% {
                transform: translateX(5px);
            }
        }

        .shake {
            animation: shake .4s ease;
        }
    </style>
</head>

<body class="text-slate-800 antialiased" x-data="dashboardApp()" x-init="init()">

    <x-page-loader />

    {{-- TOP NAVBAR --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white border border-slate-200 rounded-xl p-1 flex items-center justify-center shadow-xs">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1 Beringin" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="font-heading font-extrabold text-xs uppercase tracking-wider block text-slate-900">SMKN 1 BERINGIN</span>
                    <span class="text-[10px] text-blue-600 font-bold block">Portal Presensi Siswa</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs font-bold text-slate-500 hover:text-rose-600 px-3 py-1.5 rounded-xl hover:bg-rose-50 transition border border-slate-200 hover:border-rose-200 flex items-center gap-1.5">
                    <i class="fas fa-arrow-right-from-bracket text-[11px]"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </header>

    {{-- MAIN CONTAINER --}}
    <main class="max-w-xl mx-auto px-4 py-6 pb-24 space-y-5">

        {{-- KARTU PROFIL SISWA — Clean Card Style --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" data-aos="fade-down">

            {{-- Top bar: Status + Jam --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 pulse-dot"></span>
                    <span class="text-xs font-semibold text-slate-600">
                        @if($user->isFaceEnrolled()) Face ID Aktif @else Belum Rekam Wajah @endif
                    </span>
                </div>
                <div class="flex items-center gap-1.5 text-slate-500 text-xs font-mono">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/>
                    </svg>
                    <span id="siswa-clock">--:--</span>
                </div>
            </div>

            {{-- Main content --}}
            <div class="px-5 py-5">
                @php
                    $words = explode(' ', trim($user->name));
                    $initials = count($words) >= 2
                        ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1)
                        : mb_substr($words[0], 0, 2);
                @endphp

                {{-- Avatar + Info --}}
                <div class="flex items-center gap-4 mb-5">
                    <div class="relative shrink-0">
                        <div class="w-16 h-16 rounded-full bg-blue-700 text-white font-heading font-extrabold text-xl flex items-center justify-center border-2 border-slate-100 shadow-sm">
                            {{ strtoupper($initials) }}
                        </div>
                        @if($user->isFaceEnrolled())
                            <span class="absolute -bottom-0.5 -right-0.5 w-5 h-5 bg-emerald-500 rounded-full border-2 border-white flex items-center justify-center">
                                <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg font-heading font-bold text-slate-900 leading-tight truncate">{{ $user->name }}</h1>
                        <p class="text-sm text-slate-500 font-medium mt-0.5">Siswa SMKN 1 Beringin</p>
                    </div>
                </div>

                {{-- Info pills (2 kolom mirip tombol referensi) --}}
                <div class="grid grid-cols-2 gap-2.5">
                    <div class="flex items-center gap-2.5 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5">
                        <div class="w-7 h-7 bg-white border border-slate-200 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wide">Kelas</p>
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $user->kelas ? $user->kelas->nama_kelas : 'Tanpa Kelas' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5">
                        <div class="w-7 h-7 bg-white border border-slate-200 rounded-lg flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wide">NISN</p>
                            <p class="text-xs font-bold text-slate-800 font-mono truncate">{{ $user->nisn ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bottom accent strip (mirip referensi) --}}
            @if(!$user->isFaceEnrolled())
                <div class="bg-amber-500 px-5 py-2.5 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-white text-xs font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        Wajah belum terdaftar — Daftarkan sekarang
                    </div>
                    <a href="{{ route('siswa.enroll') }}" class="shrink-0 text-amber-700 bg-white text-xs font-bold px-2.5 py-1 rounded-lg hover:bg-amber-50 transition">
                        Daftar
                    </a>
                </div>
            @else
                <div class="bg-blue-700 px-5 py-2.5 flex items-center gap-2 text-white text-xs font-semibold">
                    <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Sistem presensi biometrik aktif
                </div>
            @endif
        </div>

        {{-- PWA Install Banner --}}
        <div x-show="showInstallPrompt" x-cloak
            class="bg-gradient-to-r from-blue-50 to-emerald-50 rounded-2xl p-4 text-slate-800 shadow-sm flex items-center justify-between gap-3 border border-blue-200/80"
            data-aos="fade-down">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20">
                    <i class="fas fa-mobile-screen-button text-lg"></i>
                </div>
                <div>
                    <p class="font-extrabold text-xs text-slate-900 font-heading">Pasang Aplikasi Presensi (PWA)</p>
                    <p class="text-[11px] text-slate-600">Akses instan dari layar utama HP Anda</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button @click="installApp()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs transition">
                    Pasang
                </button>
                <button @click="showInstallPrompt = false" class="text-slate-400 hover:text-slate-700 text-xs p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div
                class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-4 py-3.5 text-xs font-bold flex items-center gap-2.5 shadow-2xs">
                <i class="fas fa-circle-check text-emerald-600 text-base"></i> {{ session('success') }}
            </div>
        @endif

        {{-- STAT CARDS — Colored Pills --}}
        <div class="grid grid-cols-4 gap-2.5" data-aos="fade-up">
            <div class="stat-pill hadir shadow-sm">
                <div class="w-9 h-9 bg-emerald-600 text-white rounded-xl flex items-center justify-center mx-auto mb-2 shadow-md">
                    <i class="fas fa-user-check text-sm"></i>
                </div>
                <p class="text-2xl font-heading font-extrabold text-emerald-800 leading-none">{{ $stats['hadir'] }}</p>
                <p class="text-[11px] text-emerald-700 font-bold mt-1">Hadir</p>
            </div>
            <div class="stat-pill izin shadow-sm">
                <div class="w-9 h-9 bg-amber-500 text-white rounded-xl flex items-center justify-center mx-auto mb-2 shadow-md">
                    <i class="fas fa-envelope-open-text text-sm"></i>
                </div>
                <p class="text-2xl font-heading font-extrabold text-amber-800 leading-none">{{ $stats['izin'] }}</p>
                <p class="text-[11px] text-amber-700 font-bold mt-1">Izin</p>
            </div>
            <div class="stat-pill sakit shadow-sm">
                <div class="w-9 h-9 bg-sky-500 text-white rounded-xl flex items-center justify-center mx-auto mb-2 shadow-md">
                    <i class="fas fa-hospital-user text-sm"></i>
                </div>
                <p class="text-2xl font-heading font-extrabold text-sky-800 leading-none">{{ $stats['sakit'] }}</p>
                <p class="text-[11px] text-sky-700 font-bold mt-1">Sakit</p>
            </div>
            <div class="stat-pill alpa shadow-sm">
                <div class="w-9 h-9 bg-rose-500 text-white rounded-xl flex items-center justify-center mx-auto mb-2 shadow-md">
                    <i class="fas fa-user-xmark text-sm"></i>
                </div>
                <p class="text-2xl font-heading font-extrabold text-rose-800 leading-none">{{ $stats['alpa'] }}</p>
                <p class="text-[11px] text-rose-700 font-bold mt-1">Alpa</p>
            </div>
        </div>

        {{-- SESI PRESENSI AKTIF CARD --}}
        <div data-aos="fade-up" data-aos-delay="50">
            {{-- Loading State --}}
            <div x-show="sesiLoading"
                class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 flex flex-col items-center justify-center gap-3">
                <div class="w-12 h-12 rounded-full border-4 border-blue-100 border-t-blue-600 animate-spin"></div>
                <span class="text-xs font-bold text-slate-500">Menghubungkan ke server presensi...</span>
            </div>

            {{-- 1. Ada sesi aktif, SUDAH HADIR --}}
            <div x-show="!sesiLoading && sesiData && sudahHadir" x-cloak
                class="bg-blue-600 rounded-2xl shadow-sm border border-blue-700 overflow-hidden text-white">
                <div class="px-5 py-4 flex items-start justify-between">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/20 text-white text-[10px] font-extrabold font-mono uppercase tracking-wider mb-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 pulse-dot"></div>
                            <span>SESI SEDANG BERLANGSUNG</span>
                        </div>
                        <h2 x-text="sesiData?.kelas" class="font-heading font-black text-xl text-white tracking-tight"></h2>
                        <p x-text="'Guru: ' + sesiData?.guru" class="text-blue-100 text-xs mt-0.5 font-medium"></p>
                    </div>
                    <div class="bg-white text-emerald-600 rounded-xl px-3.5 py-2.5 text-center shadow-sm shrink-0 border border-white/60">
                        <i class="fas fa-circle-check text-xl text-emerald-600"></i>
                        <p class="text-[10px] font-extrabold mt-0.5 font-mono text-emerald-600">HADIR</p>
                    </div>
                </div>
                {{-- Body (The verified banner) --}}
                <div class="bg-emerald-50 border-t border-emerald-100 px-5 py-3 flex items-center gap-2.5 text-emerald-900">
                    <i class="fas fa-shield-halved text-emerald-600 text-base shrink-0"></i>
                    <span class="text-xs font-semibold">Kehadiran Anda telah terverifikasi biometrik. Selamat belajar!</span>
                </div>
            </div>

            {{-- 2. Ada sesi aktif, BELUM HADIR --}}
            <div x-show="!sesiLoading && sesiData && !sudahHadir" x-cloak
                class="bg-blue-600 rounded-2xl shadow-sm border border-blue-700 overflow-hidden text-white">
                {{-- Top Section --}}
                <div class="px-5 py-4 flex items-start justify-between">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/20 text-white text-[10px] font-extrabold font-mono uppercase tracking-wider mb-2">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 pulse-dot"></div>
                            <span>SESI PRESENSI DIBUKA</span>
                        </div>
                        <h2 x-text="sesiData?.kelas" class="font-heading font-black text-xl text-white tracking-tight"></h2>
                        <p x-text="sesiData?.tanggal" class="text-blue-100 text-xs mt-0.5 font-mono font-medium"></p>
                        <p x-text="'Pengampu: ' + sesiData?.guru" class="text-blue-200 text-xs mt-0.5 font-medium"></p>
                    </div>
                    {{-- Matching white badge --}}
                    <div class="bg-white text-amber-600 rounded-xl px-3.5 py-2.5 text-center shadow-sm shrink-0 border border-white/60">
                        <i class="far fa-clock text-xl text-amber-500"></i>
                        <p class="text-[10px] font-extrabold mt-0.5 font-mono text-amber-600">BELUM</p>
                    </div>
                </div>

                {{-- Action / Radar Section --}}
                <div class="bg-white text-slate-800 p-5 border-t border-blue-500/20">

                    {{-- WiFi Sekolah IP Whitelist Radar --}}
                    <div x-show="ipWhitelistActive" class="mb-4 pb-3.5 border-b border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-slate-700 font-bold flex items-center gap-1.5">
                                <i class="fas fa-wifi text-blue-600"></i> Jaringan WiFi Sekolah:
                            </span>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded font-bold"
                                  :class="isIpAllowed ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'">
                                IP: <span x-text="clientIp"></span>
                            </span>
                        </div>

                        {{-- Valid WiFi --}}
                        <div x-show="isIpAllowed"
                            class="bg-emerald-50 border border-emerald-300 rounded-xl px-3.5 py-2.5 text-xs text-emerald-800 flex items-center gap-2">
                            <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                            <span>Terhubung ke Jaringan WiFi Resmi SMKN 1 Beringin.</span>
                        </div>

                        {{-- Outside WiFi / Data Seluler --}}
                        <div x-show="!isIpAllowed"
                            class="bg-rose-50 border border-rose-300 rounded-xl px-3.5 py-2.5 text-xs text-rose-800 space-y-1">
                            <div class="flex items-center gap-2 font-bold text-rose-700">
                                <i class="fas fa-triangle-exclamation text-rose-600"></i>
                                <span>Bukan WiFi Sekolah</span>
                            </div>
                            <p class="text-[11px] text-rose-700 leading-relaxed">
                                Presensi wajib menggunakan WiFi sekolah. Harap hubungkan perangkat Anda ke jaringan WiFi resmi SMKN 1 Beringin.
                            </p>
                        </div>
                    </div>

                    {{-- Geofencing GPS Radar --}}
                    <div x-show="geofencingActive" class="mb-4 pb-3.5 border-b border-slate-100">
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-xs text-slate-700 font-bold flex items-center gap-1.5">
                                <i class="fas fa-satellite text-blue-600"></i> Radar Lokasi Sekolah (GPS):
                            </span>
                            <button type="button" @click="checkLocation(true)" title="Perbarui titik GPS"
                                class="text-blue-600 hover:text-blue-800 text-xs px-2.5 py-1 rounded-lg hover:bg-blue-50 transition flex items-center gap-1 font-bold border border-blue-100">
                                <i class="fas fa-arrows-rotate text-[10px]" :class="isRequestingGeo ? 'fa-spin' : ''"></i>
                                Refresh GPS
                            </button>
                        </div>

                        {{-- Checking --}}
                        <div x-show="geoStatus === 'checking'"
                            class="bg-blue-50 border border-blue-200 rounded-xl px-3.5 py-2.5 text-xs text-blue-800 flex items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin text-blue-600"></i>
                            <span>Menghitung jarak koordinat GPS Anda ke sekolah...</span>
                        </div>

                        {{-- Valid / In Radius --}}
                        <div x-show="geoStatus === 'valid'"
                            class="bg-emerald-50 border border-emerald-300 rounded-xl px-3.5 py-2.5 text-xs text-emerald-800 flex items-center gap-2">
                            <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                            <span>Dalam Zona Sekolah (Jarak: <strong x-text="geoDistance + ' m'"
                                    class="font-mono"></strong>, Maks: <span x-text="schoolRadius + 'm'"
                                    class="font-mono"></span>)</span>
                        </div>

                        {{-- Outside Radius Warning --}}
                        <div x-show="geoStatus === 'outside'"
                            class="bg-rose-50 border border-rose-300 rounded-xl px-3.5 py-2.5 text-xs text-rose-800 space-y-1">
                            <div class="flex items-center gap-2 font-bold text-rose-700">
                                <i class="fas fa-triangle-exclamation text-rose-600"></i>
                                <span>Di Luar Radius Sekolah (<span x-text="formatDistance(geoDistance)"
                                        class="font-mono"></span>)</span>
                            </div>
                            <p class="text-[11px] text-rose-700 leading-relaxed">
                                Presensi hanya dapat dilakukan di dalam zona sekolah (maksimal <span x-text="schoolRadius"
                                    class="font-mono"></span> meter).
                            </p>
                        </div>

                        {{-- GPS Error --}}
                        <div x-show="geoStatus === 'error'"
                            class="bg-amber-50 border border-amber-300 rounded-xl px-3.5 py-2.5 text-xs text-amber-800 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-location-slash text-amber-600"></i>
                                <span>Akses GPS perangkat belum diizinkan.</span>
                            </div>
                            <button type="button" @click="checkLocation(true)"
                                class="underline font-bold text-amber-900 hover:text-amber-950">
                                Izinkan
                            </button>
                        </div>
                    </div>

                    {{-- Tombol Mulai Scan Wajah --}}
                    <button @click="openFaceScanner()"
                        :disabled="(geofencingActive && geoStatus === 'outside') || (ipWhitelistActive && !isIpAllowed)"
                        :class="(geofencingActive && geoStatus === 'outside') || (ipWhitelistActive && !isIpAllowed) ? 'opacity-50 cursor-not-allowed bg-slate-200 text-slate-400' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm hover:shadow-md active:scale-[0.99]'"
                        class="w-full font-extrabold py-3.5 rounded-xl flex items-center justify-center gap-2.5 text-sm transition-all">
                        <i class="fas text-base" :class="(geofencingActive && geoStatus === 'outside') || (ipWhitelistActive && !isIpAllowed) ? 'fa-lock' : 'fa-camera'"></i>
                        <span x-text="ipWhitelistActive && !isIpAllowed ? 'Terkunci: Harus Pakai WiFi Sekolah' : (geofencingActive && geoStatus === 'outside' ? 'Terkunci: Di Luar Sekolah' : 'Mulai Verifikasi Wajah')"></span>
                    </button>
                </div>
            </div>

            {{-- 3. Tidak ada sesi aktif --}}
            <div x-show="!sesiLoading && !sesiData" x-cloak
                class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 px-6 py-8 text-center border-b border-slate-100">
                    <div class="w-16 h-16 bg-blue-100 text-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hourglass-half text-2xl"></i>
                    </div>
                    <h2 class="text-base font-heading font-extrabold text-slate-800 mb-1">Belum Ada Sesi Aktif</h2>
                    <p class="text-xs text-slate-500 leading-relaxed max-w-xs mx-auto">Menunggu guru pengampu membuka sesi presensi kelas hari ini.</p>
                </div>
                <div class="border-t border-slate-100 px-6 py-3 flex items-center justify-center gap-2">
                    <i class="fas fa-arrows-rotate text-blue-400 text-[10px] fa-spin"></i>
                    <span class="text-[11px] text-slate-400 font-mono">Auto-sinkron setiap 5 detik</span>
                </div>
            </div>
        </div>

        {{-- RIWAYAT PRESENSI --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-heading font-extrabold text-slate-800 text-sm flex items-center gap-2">
                    <div class="w-7 h-7 bg-blue-600 text-white rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock-rotate-left text-xs"></i>
                    </div>
                    Riwayat Presensi
                </h3>
                <span class="text-[10px] font-mono font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg">10 Terakhir</span>
            </div>

            @if($riwayat->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3 text-slate-300">
                        <i class="fas fa-inbox text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-600">Belum ada riwayat presensi</p>
                    <p class="text-[11px] text-slate-400 mt-1">Lakukan verifikasi wajah saat sesi kelas dibuka.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($riwayat as $item)
                        @php $st = $item->status; @endphp
                        <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3">
                                {{-- Status icon circle --}}
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0
                                    {{ $st==='hadir' ? 'bg-emerald-100 text-emerald-600' : ($st==='izin' ? 'bg-amber-100 text-amber-600' : ($st==='sakit' ? 'bg-sky-100 text-sky-600' : 'bg-rose-100 text-rose-500')) }}">
                                    <i class="fas text-sm
                                        {{ $st==='hadir' ? 'fa-check' : ($st==='izin' ? 'fa-envelope' : ($st==='sakit' ? 'fa-hospital' : 'fa-times')) }}"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800">
                                        {{ optional(optional($item->sesiPresensi)->kelas)->nama_kelas ?? '-' }}
                                        @if(optional($item->sesiPresensi)->mataPelajaran)
                                            <span class="bg-blue-50 text-blue-700 border border-blue-200 px-1.5 py-0.5 rounded text-[10px] font-bold ml-1">
                                                {{ $item->sesiPresensi->mataPelajaran->nama_mapel }}
                                            </span>
                                        @endif
                                    </p>
                                    <p class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ optional($item->sesiPresensi)->tanggal?->format('d M Y') ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            <span class="badge text-[11px] font-bold
                                {{ $st==='hadir' ? 'badge-hadir' : ($st==='izin' ? 'badge-izin' : ($st==='sakit' ? 'badge-sakit' : 'badge-alpa')) }}">
                                {{ ucfirst($st) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </main>

    {{-- ──────────────────────────────────────────── --}}
    {{-- FACE SCANNER MODAL (ROUNDED-3XL LIGHT THEME) --}}
    {{-- ──────────────────────────────────────────── --}}
    <div x-show="isScanning" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
        <div @click.away="closeFaceScanner()"
            class="bg-white rounded-3xl w-full max-w-sm shadow-2xl border border-slate-200 overflow-hidden relative">

            {{-- Header Modal --}}
            <div class="bg-white px-5 py-4 flex justify-between items-center border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <i class="fas fa-camera text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-sm text-slate-900">Verifikasi Wajah</h3>
                        <p class="text-slate-500 text-[11px]">Posisikan wajah di dalam bingkai oval</p>
                    </div>
                </div>
                <button @click="closeFaceScanner()"
                    class="text-slate-400 hover:text-slate-700 transition w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Camera Viewport --}}
            <div class="relative bg-slate-950" style="height: 290px;">
                <video id="video-scan" autoplay playsinline muted class="w-full h-full object-cover"
                    style="transform:scaleX(-1)"></video>
                <canvas id="canvas-scan" class="hidden"></canvas>

                {{-- Oval HUD --}}
                <div class="face-oval"
                    :class="scanState === 'detected' ? 'detected' : (scanState === 'success' ? 'success' : '')"></div>

                {{-- Overlays status --}}
                <div x-show="scanState === 'idle'" class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                    <div
                        class="bg-black/70 backdrop-blur-sm text-white text-xs px-4 py-1.5 rounded-full flex items-center gap-2 border border-white/10">
                        <i class="fas fa-circle-notch fa-spin text-blue-400 text-xs"></i> Menyiapkan kamera & AI...
                    </div>
                </div>
                <div x-show="scanState === 'detecting'"
                    class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                    <div
                        class="bg-black/70 backdrop-blur-sm text-white text-xs px-4 py-1.5 rounded-full font-semibold border border-white/10 flex items-center gap-1.5 shadow-xl ring-1 ring-white/20">
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-400 animate-ping"></span>
                        <span class="font-bold tracking-wide uppercase text-yellow-300" x-text="getLivenessText()"></span>
                    </div>
                </div>
                <div x-show="scanState === 'detected'"
                    class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                    <div
                        class="bg-emerald-600 text-white text-xs px-4 py-1.5 rounded-full font-bold shadow-md flex items-center gap-1.5">
                        <i class="fas fa-check"></i> Wajah Terdeteksi!
                    </div>
                </div>
                <div x-show="scanState === 'processing'"
                    class="absolute inset-0 bg-slate-900/85 flex items-center justify-center backdrop-blur-xs z-30">
                    <x-loading-school />
                </div>
                <div x-show="scanState === 'success'"
                    class="absolute inset-0 bg-emerald-950/85 flex items-center justify-center z-30">
                    <div class="text-center text-white checkmark-pop">
                        <i class="fas fa-circle-check text-5xl text-emerald-400"></i>
                        <p class="mt-2 font-heading font-extrabold text-lg text-white">TERVERIFIKASI HADIR ✓</p>
                    </div>
                </div>
                <div x-show="scanState === 'failed'"
                    class="absolute inset-0 bg-rose-950/85 flex items-center justify-center z-30">
                    <div class="text-center text-white shake">
                        <i class="fas fa-circle-xmark text-5xl text-rose-400"></i>
                        <p class="mt-2 font-heading font-bold text-sm">Wajah Tidak Dikenali</p>
                    </div>
                </div>
            </div>

            {{-- Bottom Action & Feedback --}}
            <div class="p-5 bg-white">
                <div x-show="scanMessage" class="text-xs font-bold p-3 rounded-xl mb-3 text-center transition-all"
                    :class="scanSuccess ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : (scanState === 'idle' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-rose-50 text-rose-700 border border-rose-200')">
                    <span x-text="scanMessage"></span>
                </div>

                {{-- Tombol Verifikasi Instan Dihilangkan agar siswa wajib melewati tantangan liveness --}}
                <div x-show="scanState === 'detecting' || scanState === 'idle'">
                    <p class="text-[11px] text-slate-500 text-center mt-2 font-medium bg-slate-50 p-3 rounded-lg border border-slate-100">
                        <i class="fas fa-shield-halved text-emerald-600 mr-1"></i> Sistem Keamanan Aktif. Silakan ikuti instruksi di layar untuk memverifikasi kehadiran.
                    </p>
                </div>

                <div x-show="scanState === 'failed'">
                    <button @click="retryScan()"
                        class="w-full bg-slate-800 hover:bg-slate-900 text-white font-extrabold py-3.5 rounded-xl transition flex items-center justify-center gap-2 text-sm shadow-sm">
                        <i class="fas fa-rotate-right"></i> Coba Pindai Ulang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true, duration: 400, offset: 20 });

        // Realtime clock di profile hero
        function updateSiswaClock() {
            const el = document.getElementById('siswa-clock');
            if (el) el.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) + ' WIB';
        }
        updateSiswaClock();
        setInterval(updateSiswaClock, 60000);


        // ── Web Audio API Synthesizer (No external MP3 files needed) ──
        const audioFx = {
            ctx: null,
            getCtx() {
                if (!this.ctx) {
                    const AudioCtx = window.AudioContext || window.webkitAudioContext;
                    if (AudioCtx) this.ctx = new AudioCtx();
                }
                if (this.ctx && this.ctx.state === 'suspended') {
                    this.ctx.resume();
                }
                return this.ctx;
            },
            playBlink() {
                try {
                    const ctx = this.getCtx();
                    if (!ctx) return;
                    const now = ctx.currentTime;
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(750, now);
                    osc.frequency.exponentialRampToValueAtTime(1250, now + 0.07);
                    gain.gain.setValueAtTime(0.25, now);
                    gain.gain.exponentialRampToValueAtTime(0.01, now + 0.07);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.07);
                } catch (e) { console.warn(e); }
            },
            playSuccess() {
                try {
                    const ctx = this.getCtx();
                    if (!ctx) return;
                    const now = ctx.currentTime;

                    const osc1 = ctx.createOscillator();
                    const gain1 = ctx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(659.25, now);
                    gain1.gain.setValueAtTime(0.3, now);
                    gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                    osc1.connect(gain1);
                    gain1.connect(ctx.destination);
                    osc1.start(now);
                    osc1.stop(now + 0.3);

                    const osc2 = ctx.createOscillator();
                    const gain2 = ctx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(880, now + 0.1);
                    gain2.gain.setValueAtTime(0.35, now + 0.1);
                    gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.start(now + 0.1);
                    osc2.stop(now + 0.6);
                } catch (e) { console.warn(e); }
            },
            playError() {
                try {
                    const ctx = this.getCtx();
                    if (!ctx) return;
                    const now = ctx.currentTime;
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(220, now);
                    osc.frequency.setValueAtTime(160, now + 0.1);
                    gain.gain.setValueAtTime(0.3, now);
                    gain.gain.exponentialRampToValueAtTime(0.01, now + 0.3);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now);
                    osc.stop(now + 0.3);
                } catch (e) { console.warn(e); }
            }
        };

        function dashboardApp() {
            return {
                // ── Sesi polling state ──
                sesiLoading: true,
                sesiData: null,
                sudahHadir: false,
                currentSesiId: null,
                pollInterval: null,

                // ── Geofencing state ──
                schoolLat: {{ $schoolSetting->latitude }},
                schoolLng: {{ $schoolSetting->longitude }},
                schoolRadius: {{ $schoolSetting->radius_meters }},
                geofencingActive: {{ $schoolSetting->is_geofencing_active ? 'true' : 'false' }},
                userLat: null,
                userLng: null,
                geoDistance: null,
                geoStatus: 'checking',
                isRequestingGeo: false,

                // ── IP Whitelist state ──
                ipWhitelistActive: {{ $schoolSetting->is_ip_whitelist_active ? 'true' : 'false' }},
                clientIp: '{{ \App\Models\SchoolSetting::getClientIp(request()) }}',
                isIpAllowed: {{ $schoolSetting->isIpAllowed(\App\Models\SchoolSetting::getClientIp(request())) ? 'true' : 'false' }},

                // ── PWA install prompt ──
                showInstallPrompt: false,
                deferredPrompt: null,

                // ── Face scanner state ──
                isScanning: false,
                scanState: 'idle',
                scanMessage: '',
                scanSuccess: false,
                videoStream: null,
                detectionInterval: null,
                livenessChallenge: '',
                livenessState: 'init',

                getLivenessText() {
                    if (this.livenessChallenge === 'open_mouth') return 'BUKA MULUT LEBAR (MENGANGA)';
                    if (this.livenessChallenge === 'smile') return 'SENYUM YANG LEBAR';
                    if (this.livenessChallenge === 'turn_left') return 'TOLEHKAN KEPALA KE KIRI';
                    if (this.livenessChallenge === 'turn_right') return 'TOLEHKAN KEPALA KE KANAN';
                    return 'DETEKSI WAJAH...';
                },

                init() {
                    this.pollSesiAktif();
                    this.pollInterval = setInterval(() => this.pollSesiAktif(), 5000);
                    if (this.geofencingActive) {
                        this.checkLocation();
                    } else {
                        this.geoStatus = 'disabled';
                    }

                    if ('serviceWorker' in navigator) {
                        window.addEventListener('load', () => {
                            navigator.serviceWorker.register('/sw.js').catch(console.warn);
                        });
                    }

                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        this.deferredPrompt = e;
                        this.showInstallPrompt = true;
                    });
                },

                installApp() {
                    if (!this.deferredPrompt) return;
                    this.deferredPrompt.prompt();
                    this.deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            this.showInstallPrompt = false;
                        }
                        this.deferredPrompt = null;
                    });
                },

                checkLocation(force = false) {
                    if (!this.geofencingActive && !force) {
                        this.geoStatus = 'disabled';
                        return;
                    }

                    if (!navigator.geolocation) {
                        this.geoStatus = 'error';
                        return;
                    }

                    this.isRequestingGeo = true;
                    if (force) this.geoStatus = 'checking';

                    navigator.geolocation.getCurrentPosition(
                        (pos) => {
                            this.isRequestingGeo = false;
                            this.userLat = pos.coords.latitude;
                            this.userLng = pos.coords.longitude;

                            const R = 6371000;
                            const dLat = (this.schoolLat - this.userLat) * Math.PI / 180;
                            const dLon = (this.schoolLng - this.userLng) * Math.PI / 180;
                            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                                Math.cos(this.userLat * Math.PI / 180) * Math.cos(this.schoolLat * Math.PI / 180) *
                                Math.sin(dLon / 2) * Math.sin(dLon / 2);
                            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                            this.geoDistance = Math.round(R * c);

                            if (!this.geofencingActive || this.geoDistance <= this.schoolRadius) {
                                this.geoStatus = 'valid';
                            } else {
                                this.geoStatus = 'outside';
                            }
                        },
                        (err) => {
                            this.isRequestingGeo = false;
                            this.geoStatus = 'error';
                            console.warn('Geolocation error:', err);
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 10000 }
                    );
                },

                formatDistance(m) {
                    if (!m && m !== 0) return '-';
                    if (m >= 1000) return (m / 1000).toFixed(1) + ' km';
                    return m + ' meter';
                },

                pollSesiAktif() {
                    fetch('{{ route('siswa.sesiaktif') }}')
                        .then(r => r.json())
                        .then(data => {
                            this.sesiLoading = false;
                            if (data.success && data.sesi) {
                                this.sesiData = data.sesi;
                                this.currentSesiId = data.sesi.id;
                                this.sudahHadir = data.sudah_hadir;
                            } else {
                                this.sesiData = null;
                                this.currentSesiId = null;
                                this.sudahHadir = false;
                            }

                            if (data.geofencing) {
                                this.geofencingActive = data.geofencing.active;
                                this.schoolLat = data.geofencing.latitude;
                                this.schoolLng = data.geofencing.longitude;
                                this.schoolRadius = data.geofencing.radius_meters;
                            }

                            if (data.network) {
                                this.ipWhitelistActive = data.network.active;
                                this.clientIp = data.network.client_ip;
                                this.isIpAllowed = data.network.is_allowed;
                            }
                        })
                        .catch(() => { this.sesiLoading = false; });
                },

                // ── Buka Modal Pemindai Wajah ──
                async openFaceScanner() {
                    if (this.ipWhitelistActive && !this.isIpAllowed) {
                        audioFx.playError();
                        alert(`Presensi ditolak: Anda harus terhubung ke jaringan WiFi resmi SMKN 1 Beringin.\nIP jaringan Anda (${this.clientIp}) tidak terdaftar.`);
                        return;
                    }

                    if (this.geofencingActive && this.geoStatus === 'outside') {
                        audioFx.playError();
                        alert(`Presensi ditolak: Anda berada di luar radius sekolah (${this.formatDistance(this.geoDistance)}). Batas maksimal: ${this.schoolRadius} meter.`);
                        return;
                    }

                    if (this.geofencingActive && !this.userLat) {
                        this.checkLocation(true);
                    }

                    this.isScanning = true;
                    this.scanState = 'idle';
                    this.scanMessage = 'Memuat Model AI...';
                    this.scanSuccess = false;
                    this.blinkState = 'open';

                    try {
                        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                            throw new Error("Akses kamera diblokir browser. Pastikan Anda menggunakan HTTPS atau Localhost.");
                        }
                        this.videoStream = await navigator.mediaDevices.getUserMedia({
                            video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                            audio: false
                        });
                        const video = document.getElementById('video-scan');
                        video.srcObject = this.videoStream;

                        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/model/';
                        if (typeof faceapi !== 'undefined') {
                            await Promise.all([
                                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL)
                            ]);
                        }

                        this.scanMessage = '';

                        video.onloadedmetadata = () => {
                            video.width = video.videoWidth;
                            video.height = video.videoHeight;
                        };

                        await video.play();
                        this.scanState = 'detecting';
                        this.startDetectionLoop(video);

                    } catch (err) {
                        audioFx.playError();
                        this.scanMessage = 'Gagal mengakses kamera/AI: ' + err.message;
                        this.scanState = 'failed';
                    }
                },

                startDetectionLoop(video) {
                    const challenges = ['open_mouth', 'smile', 'turn_left', 'turn_right'];
                    this.livenessChallenge = challenges[Math.floor(Math.random() * challenges.length)];
                    this.livenessState = 'waiting';

                    this.detectionInterval = setInterval(async () => {
                        if (this.scanState !== 'detecting') return;
                        if (typeof faceapi === 'undefined') return;

                        try {
                            const detections = await faceapi.detectSingleFace(
                                video,
                                new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.4, inputSize: 224 })
                            ).withFaceLandmarks();

                            if (detections) {
                                const box = detections.detection.box;
                                if (box.width > 75) {
                                    const landmarks = detections.landmarks;
                                    let passed = false;

                                    if (this.livenessChallenge === 'open_mouth') {
                                        const mouth = landmarks.getMouth();
                                        // mouth[14] = inner top lip, mouth[18] = inner bottom lip
                                        const innerUpper = mouth[14];
                                        const innerLower = mouth[18];
                                        const mouthOpenDist = Math.hypot(innerUpper.x - innerLower.x, innerUpper.y - innerLower.y);
                                        const faceHeight = box.height;
                                        if (mouthOpenDist / faceHeight > 0.08) passed = true;
                                    } 
                                    else if (this.livenessChallenge === 'smile') {
                                        const mouth = landmarks.getMouth();
                                        const mouthWidth = Math.hypot(mouth[0].x - mouth[6].x, mouth[0].y - mouth[6].y);
                                        const faceWidth = box.width;
                                        if (mouthWidth / faceWidth > 0.35) passed = true;
                                    }
                                    else if (this.livenessChallenge === 'turn_left' || this.livenessChallenge === 'turn_right') {
                                        const nose = landmarks.getNose()[3];
                                        const jaw = landmarks.getJawOutline();
                                        const leftJaw = jaw[0];
                                        const rightJaw = jaw[16];
                                        const distLeft = Math.hypot(nose.x - leftJaw.x, nose.y - leftJaw.y);
                                        const distRight = Math.hypot(nose.x - rightJaw.x, nose.y - rightJaw.y);
                                        
                                        // Kamera menggunakan scale(-1, 1) / mirror
                                        if (this.livenessChallenge === 'turn_left' && distRight < distLeft * 0.6) passed = true;
                                        if (this.livenessChallenge === 'turn_right' && distLeft < distRight * 0.6) passed = true;
                                    }

                                    if (passed) {
                                        audioFx.playBlink();
                                        this.scanState = 'detected';
                                        clearInterval(this.detectionInterval);
                                        setTimeout(() => {
                                            this.captureAndSend();
                                        }, 400);
                                    }
                                }
                            }
                        } catch (e) {
                            console.warn(e);
                        }
                    }, 150);
                },

                captureAndSend() {
                    const video = document.getElementById('video-scan');
                    const canvas = document.getElementById('canvas-scan');
                    if (!video || !canvas) return;

                    canvas.width = video.videoWidth || 640;
                    canvas.height = video.videoHeight || 480;
                    const ctx = canvas.getContext('2d');
                    ctx.save();
                    ctx.scale(-1, 1);
                    ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                    ctx.restore();

                    const imageB64 = canvas.toDataURL('image/jpeg', 0.85);

                    this.scanState = 'processing';

                    fetch('{{ route('siswa.scanwajah') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            sesi_id: this.currentSesiId,
                            face_image: imageB64,
                            latitude: this.userLat,
                            longitude: this.userLng,
                        })
                    })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                audioFx.playSuccess();
                                this.scanState = 'success';
                                this.scanSuccess = true;
                                this.scanMessage = '🎉 Berhasil! Anda tercatat HADIR.';
                                this.sudahHadir = true;
                                this.stopCamera();
                                setTimeout(() => {
                                    this.isScanning = false;
                                    window.location.reload();
                                }, 1800);
                            } else {
                                audioFx.playError();
                                this.scanState = 'failed';
                                this.scanSuccess = false;
                                this.scanMessage = data.message || 'Wajah tidak cocok dengan profil biometrik Anda.';
                            }
                        })
                        .catch(() => {
                            audioFx.playError();
                            this.scanState = 'failed';
                            this.scanMessage = 'Terjadi kesalahan koneksi server. Coba lagi.';
                        });
                },

                retryScan() {
                    this.scanState = 'detecting';
                    this.scanMessage = '';
                    const video = document.getElementById('video-scan');
                    if (video) this.startDetectionLoop(video);
                },

                closeFaceScanner() {
                    this.isScanning = false;
                    this.stopCamera();
                    if (this.scanSuccess) window.location.reload();
                },

                stopCamera() {
                    if (this.detectionInterval) {
                        clearInterval(this.detectionInterval);
                        this.detectionInterval = null;
                    }
                    if (this.videoStream) {
                        this.videoStream.getTracks().forEach(t => t.stop());
                        this.videoStream = null;
                    }
                },
            };
        }
    </script>
</body>

</html>