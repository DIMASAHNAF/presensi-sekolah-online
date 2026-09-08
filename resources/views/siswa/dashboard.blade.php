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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/dist/face-api.min.js"></script>
    <style>
        body, button, input, select { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }

        body {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.05) 0px, transparent 50%);
            min-height: 100vh;
        }

        /* ── Micro Badges ── */
        .badge-hadir { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-izin  { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-sakit { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-alpa  { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
        .badge { display: inline-flex; align-items: center; padding: 0.25rem 0.65rem; border-radius: 8px; font-size: 0.72rem; font-weight: 700; }

        .pulse-dot {
            width: 8px; height: 8px; border-radius: 50%; background: #10b981;
            animation: sesi-pulse 1.8s ease infinite;
        }
        @keyframes sesi-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            50%      { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        }

        /* ── Face Oval HUD ── */
        .face-oval {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -55%);
            width: 190px; height: 240px;
            border-radius: 50% / 45%;
            border: 2.5px solid rgba(255,255,255,0.6);
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

        #video-scan { width:100%; height:100%; object-fit:cover; transform:scaleX(-1); }

        @keyframes checkmark-pop {
            0%   { transform: scale(0) rotate(-20deg); opacity: 0; }
            60%  { transform: scale(1.15) rotate(4deg);  opacity: 1; }
            100% { transform: scale(1) rotate(0deg);  opacity: 1; }
        }
        .checkmark-pop { animation: checkmark-pop .45s cubic-bezier(.17,.67,.4,1.2) forwards; }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%,60% { transform: translateX(-5px); }
            40%,80% { transform: translateX(5px); }
        }
        .shake { animation: shake .4s ease; }
    </style>
</head>
<body class="text-slate-800 antialiased" x-data="dashboardApp()" x-init="init()">

    <x-page-loader />

    {{-- TOP NAVBAR --}}
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30">
        <div class="max-w-xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-50 border border-blue-100 rounded-xl p-1 flex items-center justify-center shadow-2xs">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1 Beringin" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="font-heading font-extrabold text-xs uppercase tracking-wider block text-slate-900">SMKN 1 BERINGIN</span>
                    <span class="text-[10px] text-blue-600 font-bold block font-mono">PORTAL PRESENSI SISWA</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs font-bold text-slate-500 hover:text-rose-600 px-3 py-1.5 rounded-xl hover:bg-rose-50 transition border border-slate-200 hover:border-rose-200 flex items-center gap-1.5 shadow-2xs">
                    <i class="fas fa-arrow-right-from-bracket text-[11px]"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </header>

    {{-- MAIN CONTAINER --}}
    <main class="max-w-xl mx-auto px-4 py-6 pb-24 space-y-5">

        {{-- KARTU PROFIL SISWA (CERAH & BERSIH) --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/90 relative overflow-hidden" data-aos="fade-down">
            {{-- Accent line atas --}}
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-600 via-teal-500 to-emerald-500"></div>

            <div class="flex items-start gap-4">
                @php
                    $words = explode(' ', trim($user->name));
                    $initials = count($words) >= 2 
                        ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1) 
                        : mb_substr($words[0], 0, 2);
                @endphp
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-emerald-600 text-white font-heading font-extrabold text-xl flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20 relative">
                    {{ strtoupper($initials) }}
                    @if($user->isFaceEnrolled())
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-2 border-white flex items-center justify-center text-[9px] text-white shadow-xs" title="Wajah Terdaftar">
                            <i class="fas fa-check"></i>
                        </span>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="px-2.5 py-0.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-[10px] font-bold font-mono uppercase">
                            {{ $user->kelas ? $user->kelas->nama_kelas : 'Tanpa Kelas' }}
                        </span>
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-500 text-[11px] font-mono">NISN: {{ $user->nisn ?? '-' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-heading font-extrabold text-slate-900 tracking-tight truncate">
                        {{ $user->name }}
                    </h1>
                    <div class="flex items-center gap-2 text-slate-500 text-xs mt-1 font-medium flex-wrap">
                        <span class="inline-flex items-center gap-1.5 text-slate-600">
                            <i class="fas fa-graduation-cap text-blue-600 text-xs"></i> SMKN 1 Beringin
                        </span>
                        <span class="text-slate-300">|</span>
                        @if($user->isFaceEnrolled())
                            <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold text-[11px]">
                                <i class="fas fa-shield-halved text-xs"></i> Face ID Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-amber-600 font-semibold text-[11px]">
                                <i class="fas fa-triangle-exclamation text-xs"></i> Belum Rekam Wajah
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            @if(!$user->isFaceEnrolled())
                <div class="mt-4 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl p-4 text-xs flex items-start gap-3 shadow-2xs">
                    <i class="fas fa-triangle-exclamation text-amber-500 text-base mt-0.5 shrink-0"></i>
                    <div class="flex-1">
                        <p class="font-bold text-amber-900">Biometrik Wajah Belum Terdaftar</p>
                        <p class="text-amber-800 text-[11px] mt-0.5 leading-relaxed">Daftarkan wajah Anda sekali untuk mengaktifkan pemindaian presensi biometrik otomatis.</p>
                        <a href="{{ route('siswa.enroll') }}"
                           class="inline-flex items-center gap-1.5 mt-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-extrabold text-xs px-4 py-2 rounded-xl transition shadow-xs">
                            <i class="fas fa-camera text-[11px]"></i> Daftarkan Wajah Sekarang
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- PWA Install Banner --}}
        <div x-show="showInstallPrompt" x-cloak
             class="bg-gradient-to-r from-blue-50 to-emerald-50 rounded-2xl p-4 text-slate-800 shadow-sm flex items-center justify-between gap-3 border border-blue-200/80"
             data-aos="fade-down">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20">
                    <i class="fas fa-mobile-screen-button text-lg"></i>
                </div>
                <div>
                    <p class="font-extrabold text-xs text-slate-900 font-heading">Pasang Aplikasi Presensi (PWA)</p>
                    <p class="text-[11px] text-slate-600">Akses instan dari layar utama HP Anda</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button @click="installApp()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs transition">
                    Pasang
                </button>
                <button @click="showInstallPrompt = false" class="text-slate-400 hover:text-slate-700 text-xs p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-4 py-3.5 text-xs font-bold flex items-center gap-2.5 shadow-2xs">
                <i class="fas fa-circle-check text-emerald-600 text-base"></i> {{ session('success') }}
            </div>
        @endif

        {{-- STAT CARDS (4 KOTAK CERAH) --}}
        <div class="grid grid-cols-4 gap-2.5 sm:gap-3" data-aos="fade-up">
            <div class="bg-white rounded-2xl p-3.5 shadow-2xs text-center border border-slate-200/90 hover:border-emerald-300 transition group">
                <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mx-auto mb-1.5 group-hover:scale-105 transition">
                    <i class="fas fa-user-check text-xs"></i>
                </div>
                <p class="text-xl font-heading font-extrabold text-slate-900 font-mono">{{ $stats['hadir'] }}</p>
                <p class="text-[11px] text-emerald-700 font-bold mt-0.5">Hadir</p>
            </div>
            <div class="bg-white rounded-2xl p-3.5 shadow-2xs text-center border border-slate-200/90 hover:border-amber-300 transition group">
                <div class="w-8 h-8 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mx-auto mb-1.5 group-hover:scale-105 transition">
                    <i class="fas fa-envelope-open-text text-xs"></i>
                </div>
                <p class="text-xl font-heading font-extrabold text-slate-900 font-mono">{{ $stats['izin'] }}</p>
                <p class="text-[11px] text-amber-700 font-bold mt-0.5">Izin</p>
            </div>
            <div class="bg-white rounded-2xl p-3.5 shadow-2xs text-center border border-slate-200/90 hover:border-blue-300 transition group">
                <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-1.5 group-hover:scale-105 transition">
                    <i class="fas fa-hospital-user text-xs"></i>
                </div>
                <p class="text-xl font-heading font-extrabold text-slate-900 font-mono">{{ $stats['sakit'] }}</p>
                <p class="text-[11px] text-blue-700 font-bold mt-0.5">Sakit</p>
            </div>
            <div class="bg-white rounded-2xl p-3.5 shadow-2xs text-center border border-slate-200/90 hover:border-rose-300 transition group">
                <div class="w-8 h-8 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center mx-auto mb-1.5 group-hover:scale-105 transition">
                    <i class="fas fa-user-xmark text-xs"></i>
                </div>
                <p class="text-xl font-heading font-extrabold text-slate-900 font-mono">{{ $stats['alpa'] }}</p>
                <p class="text-[11px] text-rose-700 font-bold mt-0.5">Alpa</p>
            </div>
        </div>

        {{-- SESI PRESENSI AKTIF CARD --}}
        <div data-aos="fade-up" data-aos-delay="50">
            {{-- Loading State --}}
            <div x-show="sesiLoading" class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8 flex flex-col items-center justify-center gap-3">
                <i class="fas fa-circle-notch fa-spin text-blue-600 text-2xl"></i>
                <span class="text-xs font-bold text-slate-600">Menghubungkan ke server presensi...</span>
            </div>

            {{-- 1. Ada sesi aktif, SUDAH HADIR --}}
            <div x-show="!sesiLoading && sesiData && sudahHadir" x-cloak
                 class="bg-gradient-to-br from-white via-emerald-50/40 to-teal-50/40 rounded-3xl shadow-sm border-2 border-emerald-300 p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100/80 text-emerald-800 text-[10px] font-extrabold font-mono uppercase tracking-wider mb-2 border border-emerald-300/60">
                            <div class="pulse-dot"></div>
                            <span>Sesi Sedang Berlangsung</span>
                        </div>
                        <p x-text="sesiData?.kelas" class="font-heading font-extrabold text-xl text-slate-900 tracking-tight"></p>
                        <p x-text="'Guru: ' + sesiData?.guru" class="text-slate-600 text-xs mt-1 font-medium"></p>
                    </div>
                    <div class="bg-emerald-500 text-white rounded-2xl px-3 py-2 text-center shadow-md shadow-emerald-500/20">
                        <i class="fas fa-circle-check text-xl"></i>
                        <p class="text-[10px] font-extrabold mt-0.5 font-mono">HADIR</p>
                    </div>
                </div>
                <div class="mt-4 bg-emerald-100/60 border border-emerald-300/70 rounded-2xl px-4 py-3 text-xs text-emerald-800 flex items-center gap-2.5">
                    <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                    <span class="font-semibold">Kehadiran Anda telah terverifikasi biometrik. Selamat belajar!</span>
                </div>
            </div>

            {{-- 2. Ada sesi aktif, BELUM HADIR --}}
            <div x-show="!sesiLoading && sesiData && !sudahHadir" x-cloak
                 class="bg-white rounded-3xl shadow-md border-2 border-blue-300 p-6 relative overflow-hidden">
                
                {{-- Decorative top bar --}}
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-600 to-emerald-500"></div>

                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-[10px] font-extrabold font-mono uppercase tracking-wider mb-2 border border-blue-200">
                            <div class="pulse-dot"></div>
                            <span>Sesi Presensi Dibuka</span>
                        </div>
                        <p x-text="sesiData?.kelas" class="font-heading font-extrabold text-xl text-slate-900 tracking-tight"></p>
                        <p x-text="sesiData?.tanggal" class="text-slate-500 text-xs mt-0.5 font-mono"></p>
                        <p x-text="'Pengampu: ' + sesiData?.guru" class="text-slate-600 text-xs mt-0.5 font-medium"></p>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl px-3 py-2 text-center text-amber-700">
                        <i class="fas fa-clock text-lg"></i>
                        <p class="text-[10px] font-extrabold mt-0.5 font-mono">BELUM</p>
                    </div>
                </div>

                {{-- Geofencing GPS Radar --}}
                <div x-show="geofencingActive" class="my-4 pt-3.5 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-xs text-slate-700 font-bold flex items-center gap-1.5">
                            <i class="fas fa-satellite text-blue-600"></i> Radar Lokasi Sekolah (GPS):
                        </span>
                        <button type="button" @click="checkLocation(true)" title="Perbarui titik GPS"
                                class="text-blue-600 hover:text-blue-800 text-xs px-2.5 py-1 rounded-lg hover:bg-blue-50 transition flex items-center gap-1 font-bold border border-blue-100">
                            <i class="fas fa-arrows-rotate text-[10px]" :class="isRequestingGeo ? 'fa-spin' : ''"></i> Refresh GPS
                        </button>
                    </div>

                    {{-- Checking --}}
                    <div x-show="geoStatus === 'checking'" class="bg-blue-50 border border-blue-200 rounded-xl px-3.5 py-2.5 text-xs text-blue-800 flex items-center gap-2">
                        <i class="fas fa-circle-notch fa-spin text-blue-600"></i>
                        <span>Menghitung jarak koordinat GPS Anda ke sekolah...</span>
                    </div>

                    {{-- Valid / In Radius --}}
                    <div x-show="geoStatus === 'valid'" class="bg-emerald-50 border border-emerald-300 rounded-xl px-3.5 py-2.5 text-xs text-emerald-800 flex items-center gap-2">
                        <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                        <span>Dalam Zona Sekolah (Jarak: <strong x-text="geoDistance + ' m'" class="font-mono"></strong>, Maks: <span x-text="schoolRadius + 'm'" class="font-mono"></span>)</span>
                    </div>

                    {{-- Outside Radius Warning --}}
                    <div x-show="geoStatus === 'outside'" class="bg-rose-50 border border-rose-300 rounded-xl px-3.5 py-2.5 text-xs text-rose-800 space-y-1">
                        <div class="flex items-center gap-2 font-bold text-rose-700">
                            <i class="fas fa-triangle-exclamation text-rose-600"></i>
                            <span>Di Luar Radius Sekolah (<span x-text="formatDistance(geoDistance)" class="font-mono"></span>)</span>
                        </div>
                        <p class="text-[11px] text-rose-700 leading-relaxed">
                            Presensi hanya dapat dilakukan di dalam zona sekolah (maksimal <span x-text="schoolRadius" class="font-mono"></span> meter).
                        </p>
                    </div>

                    {{-- GPS Error --}}
                    <div x-show="geoStatus === 'error'" class="bg-amber-50 border border-amber-300 rounded-xl px-3.5 py-2.5 text-xs text-amber-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-location-slash text-amber-600"></i>
                            <span>Akses GPS perangkat belum diizinkan.</span>
                        </div>
                        <button type="button" @click="checkLocation(true)" class="underline font-bold text-amber-900 hover:text-amber-950">
                            Izinkan
                        </button>
                    </div>
                </div>

                {{-- Tombol Mulai Scan Wajah --}}
                <button @click="openFaceScanner()"
                        :disabled="geofencingActive && geoStatus === 'outside'"
                        :class="geofencingActive && geoStatus === 'outside' ? 'opacity-60 cursor-not-allowed bg-slate-300 text-slate-500 shadow-none' : 'bg-gradient-to-r from-blue-600 to-emerald-600 hover:from-blue-700 hover:to-emerald-700 text-white shadow-lg shadow-blue-500/25 active:scale-[0.99]'"
                        class="w-full font-extrabold py-4 rounded-2xl flex items-center justify-center gap-2.5 text-base transition-all">
                    <i class="fas" :class="geofencingActive && geoStatus === 'outside' ? 'fa-lock text-sm' : 'fa-camera text-lg'"></i>
                    <span x-text="geofencingActive && geoStatus === 'outside' ? 'Terkunci: Di Luar Sekolah' : 'Mulai Verifikasi Wajah'"></span>
                </button>
            </div>

            {{-- 3. Tidak ada sesi aktif --}}
            <div x-show="!sesiLoading && !sesiData" x-cloak
                 class="bg-white rounded-3xl shadow-sm border border-slate-200/90 p-8 text-center">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-blue-100 shadow-2xs">
                    <i class="fas fa-hourglass-half text-xl"></i>
                </div>
                <h2 class="text-base font-heading font-extrabold text-slate-900 mb-1">Belum Ada Sesi Presensi Aktif</h2>
                <p class="text-xs text-slate-500 leading-relaxed max-w-xs mx-auto">Menunggu guru pengampu membuka sesi presensi kelas hari ini.</p>
                <div class="mt-4 inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-mono">
                    <i class="fas fa-arrows-rotate text-[9px] fa-spin"></i> Auto-sinkron aktif setiap 5 detik
                </div>
            </div>
        </div>

        {{-- RIWAYAT PRESENSI ANDA --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/90 overflow-hidden" data-aos="fade-up" data-aos-delay="100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/60">
                <h3 class="font-heading font-extrabold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-blue-600"></i> Riwayat Presensi Anda
                </h3>
                <span class="text-[10px] font-mono font-bold text-slate-500 bg-white px-2.5 py-1 rounded-lg border border-slate-200">10 Terakhir</span>
            </div>

            @if($riwayat->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-2 text-slate-400 border border-slate-200">
                        <i class="fas fa-inbox text-lg"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-700">Belum ada riwayat presensi</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Lakukan verifikasi wajah saat sesi kelas dibuka.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($riwayat as $item)
                        <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50/70 transition-colors">
                            <div>
                                <p class="text-xs font-extrabold text-slate-900 font-mono">
                                    {{ optional($item->sesiPresensi)->tanggal?->format('d M Y') ?? '-' }}
                                </p>
                                <p class="text-[11px] text-slate-500 mt-1 flex items-center flex-wrap gap-1.5 font-medium">
                                    <span class="text-slate-700">{{ optional(optional($item->sesiPresensi)->kelas)->nama_kelas ?? '-' }}</span>
                                    @if(optional($item->sesiPresensi)->mataPelajaran)
                                        <span class="bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-md text-[10px] font-bold">
                                            {{ $item->sesiPresensi->mataPelajaran->nama_mapel }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                @if($item->status === 'hadir')
                                    <span class="badge badge-hadir"><i class="fas fa-check-circle mr-1 text-[10px]"></i> Hadir</span>
                                @elseif($item->status === 'izin')
                                    <span class="badge badge-izin"><i class="fas fa-envelope mr-1 text-[10px]"></i> Izin</span>
                                @elseif($item->status === 'sakit')
                                    <span class="badge badge-sakit"><i class="fas fa-hospital mr-1 text-[10px]"></i> Sakit</span>
                                @else
                                    <span class="badge badge-alpa"><i class="fas fa-times-circle mr-1 text-[10px]"></i> Alpa</span>
                                @endif
                            </div>
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
        <div @click.away="closeFaceScanner()" class="bg-white rounded-3xl w-full max-w-sm shadow-2xl border border-slate-200 overflow-hidden relative">

            {{-- Header Modal --}}
            <div class="bg-white px-5 py-4 flex justify-between items-center border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <i class="fas fa-camera text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-sm text-slate-900">Verifikasi Wajah</h3>
                        <p class="text-slate-500 text-[11px]">Posisikan wajah di dalam bingkai oval</p>
                    </div>
                </div>
                <button @click="closeFaceScanner()" class="text-slate-400 hover:text-slate-700 transition w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Camera Viewport --}}
            <div class="relative bg-slate-950" style="height: 290px;">
                <video id="video-scan" autoplay playsinline muted class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                <canvas id="canvas-scan" class="hidden"></canvas>

                {{-- Oval HUD --}}
                <div class="face-oval" :class="scanState === 'detected' ? 'detected' : (scanState === 'success' ? 'success' : '')"></div>

                {{-- Overlays status --}}
                <div x-show="scanState === 'idle'" class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                    <div class="bg-black/70 backdrop-blur-sm text-white text-xs px-4 py-1.5 rounded-full flex items-center gap-2 border border-white/10">
                        <i class="fas fa-circle-notch fa-spin text-blue-400 text-xs"></i> Menyiapkan kamera & AI...
                    </div>
                </div>
                <div x-show="scanState === 'detecting'" class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                    <div class="bg-black/70 backdrop-blur-sm text-white text-xs px-4 py-1.5 rounded-full font-semibold border border-white/10 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-yellow-400 animate-pulse"></span>
                        <span>Kedipkan mata atau klik tombol verifikasi</span>
                    </div>
                </div>
                <div x-show="scanState === 'detected'" class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                    <div class="bg-emerald-600 text-white text-xs px-4 py-1.5 rounded-full font-bold shadow-md flex items-center gap-1.5">
                        <i class="fas fa-check"></i> Wajah Terdeteksi!
                    </div>
                </div>
                <div x-show="scanState === 'processing'" class="absolute inset-0 bg-slate-900/85 flex items-center justify-center backdrop-blur-xs z-30">
                    <x-loading-school />
                </div>
                <div x-show="scanState === 'success'" class="absolute inset-0 bg-emerald-950/85 flex items-center justify-center z-30">
                    <div class="text-center text-white checkmark-pop">
                        <i class="fas fa-circle-check text-5xl text-emerald-400"></i>
                        <p class="mt-2 font-heading font-extrabold text-lg text-white">TERVERIFIKASI HADIR ✓</p>
                    </div>
                </div>
                <div x-show="scanState === 'failed'" class="absolute inset-0 bg-rose-950/85 flex items-center justify-center z-30">
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

                {{-- Tombol Verifikasi Instan (Memastikan siswa tidak pernah macet) --}}
                <div x-show="scanState === 'idle' || scanState === 'detecting' || scanState === 'detected'">
                    <button type="button" @click="captureAndSend()"
                            class="w-full bg-gradient-to-r from-blue-600 to-emerald-600 hover:from-blue-700 hover:to-emerald-700 text-white font-extrabold py-3.5 px-4 rounded-xl shadow-md shadow-blue-500/20 flex items-center justify-center gap-2 text-sm transition active:scale-[0.99]">
                        <i class="fas fa-camera text-base"></i>
                        <span>Verifikasi Sekarang</span>
                    </button>
                    <p class="text-[11px] text-slate-400 text-center mt-2 font-medium">
                        <i class="fas fa-info-circle mr-1"></i> Pastikan wajah menghadap kamera dan pencahayaan terang
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
            blinkState: 'open',

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
                            this.sesiData      = data.sesi;
                            this.currentSesiId = data.sesi.id;
                            this.sudahHadir    = data.sudah_hadir;
                        } else {
                            this.sesiData      = null;
                            this.currentSesiId = null;
                            this.sudahHadir    = false;
                        }

                        if (data.geofencing) {
                            this.geofencingActive = data.geofencing.active;
                            this.schoolLat = data.geofencing.latitude;
                            this.schoolLng = data.geofencing.longitude;
                            this.schoolRadius = data.geofencing.radius_meters;
                        }
                    })
                    .catch(() => { this.sesiLoading = false; });
            },

            // ── Buka Modal Pemindai Wajah ──
            async openFaceScanner() {
                if (this.geofencingActive && this.geoStatus === 'outside') {
                    audioFx.playError();
                    alert(`Presensi ditolak: Anda berada di luar radius sekolah (${this.formatDistance(this.geoDistance)}). Batas maksimal: ${this.schoolRadius} meter.`);
                    return;
                }

                if (this.geofencingActive && !this.userLat) {
                    this.checkLocation(true);
                }

                this.isScanning   = true;
                this.scanState    = 'idle';
                this.scanMessage  = 'Memuat Model AI...';
                this.scanSuccess  = false;
                this.blinkState   = 'open';

                try {
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
                this.detectionInterval = setInterval(async () => {
                    if (this.scanState !== 'detecting') return;
                    if (typeof faceapi === 'undefined') return;
                    
                    try {
                        const detections = await faceapi.detectSingleFace(
                            video, 
                            new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.3, inputSize: 224 })
                        ).withFaceLandmarks();
                        
                        if (detections) {
                            const box = detections.detection.box;
                            if (box.width > 75) {
                                const landmarks = detections.landmarks;
                                const leftEye = landmarks.getLeftEye();
                                const rightEye = landmarks.getRightEye();
                                
                                const getEAR = (eye) => {
                                    const width = Math.hypot(eye[0].x - eye[3].x, eye[0].y - eye[3].y);
                                    const h1 = Math.hypot(eye[1].x - eye[5].x, eye[1].y - eye[5].y);
                                    const h2 = Math.hypot(eye[2].x - eye[4].x, eye[2].y - eye[4].y);
                                    return (h1 + h2) / (2.0 * width);
                                };
                                
                                const avgEAR = (getEAR(leftEye) + getEAR(rightEye)) / 2;
                                
                                if (avgEAR < 0.27) {
                                    this.blinkState = 'closed';
                                } else if (avgEAR > 0.28 && this.blinkState === 'closed') {
                                    audioFx.playBlink();
                                    this.blinkState = 'open';
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
                const video  = document.getElementById('video-scan');
                const canvas = document.getElementById('canvas-scan');
                if (!video || !canvas) return;

                canvas.width  = video.videoWidth  || 640;
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
                        sesi_id:    this.currentSesiId,
                        face_image: imageB64,
                        latitude:   this.userLat,
                        longitude:  this.userLng,
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        audioFx.playSuccess();
                        this.scanState   = 'success';
                        this.scanSuccess = true;
                        this.scanMessage = '🎉 Berhasil! Anda tercatat HADIR.';
                        this.sudahHadir  = true;
                        this.stopCamera();
                        setTimeout(() => {
                            this.isScanning = false;
                            window.location.reload();
                        }, 1800);
                    } else {
                        audioFx.playError();
                        this.scanState   = 'failed';
                        this.scanSuccess = false;
                        this.scanMessage = data.message || 'Wajah tidak cocok dengan profil biometrik Anda.';
                    }
                })
                .catch(() => {
                    audioFx.playError();
                    this.scanState   = 'failed';
                    this.scanMessage = 'Terjadi kesalahan koneksi server. Coba lagi.';
                });
            },

            retryScan() {
                this.scanState   = 'detecting';
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