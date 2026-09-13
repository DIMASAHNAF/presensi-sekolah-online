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
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <link rel="stylesheet" href="{{ asset('css/theme-dark.css') }}">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
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
    @php
        $initials = strtoupper(mb_substr($user->name, 0, 2));
    @endphp

    <x-page-loader />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 flex flex-col lg:flex-row text-slate-800 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white">

        {{-- ────────────────────────────────────────────── --}}
        {{-- 1. DESKTOP SIDEBAR (lg:flex)                    --}}
        {{-- ────────────────────────────────────────────── --}}
        <aside class="hidden lg:flex flex-col w-64 xl:w-72 shrink-0 sticky top-0 h-screen bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 p-5 justify-between z-30 select-none">
            <div>
                {{-- Brand Header --}}
                <div class="flex items-center gap-3 pb-5 border-b border-slate-100 dark:border-slate-800">
                    <div class="w-10 h-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-1.5 flex items-center justify-center shadow-xs shrink-0">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1 Beringin" class="w-full h-full object-contain">
                    </div>
                    <div class="min-w-0">
                        <span class="font-heading font-black text-xs uppercase tracking-wider block text-slate-900 dark:text-white truncate">SMKN 1 BERINGIN</span>
                        <span class="text-[10px] text-blue-600 dark:text-blue-400 font-extrabold block truncate">Portal Siswa</span>
                    </div>
                </div>

                {{-- Mini User Card --}}
                <div @click="switchTab('profil')"
                     class="mt-4 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800 flex items-center gap-3 cursor-pointer hover:bg-blue-50/50 dark:hover:bg-slate-800 transition group">
                    <div class="relative w-10 h-10 rounded-full border-2 border-white dark:border-slate-700 overflow-hidden shadow-xs shrink-0 bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                        <template x-if="profileAvatarUrl">
                            <img :src="profileAvatarUrl" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!profileAvatarUrl">
                            <span>{{ strtoupper($initials) }}</span>
                        </template>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">{{ $user->name }}</p>
                        <p class="text-[10px] font-mono text-slate-400 dark:text-slate-400 truncate">{{ '@' . ($user->username ?? 'siswa') }} &bull; {{ $user->kelas->nama_kelas ?? 'Siswa' }}</p>
                    </div>
                </div>

                {{-- Navigation Links --}}
                <nav class="mt-6 space-y-1.5">
                    {{-- 1. Beranda --}}
                    <button type="button" @click="switchTab('beranda')"
                        class="w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-heading text-xs font-extrabold transition-all"
                        :class="activeTab === 'beranda' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70'">
                        <i class="fas fa-house-chimney text-sm w-5 text-center" :class="activeTab === 'beranda' ? 'text-white' : 'text-blue-600 dark:text-blue-400'"></i>
                        <div class="text-left flex-1">
                            <span class="block leading-tight">Beranda</span>
                            <span class="text-[10px] block opacity-75 font-normal font-sans">Presensi & Wajah</span>
                        </div>
                    </button>

                    {{-- 2. Kehadiran & Riwayat --}}
                    <button type="button" @click="switchTab('kehadiran')"
                        class="w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-heading text-xs font-extrabold transition-all"
                        :class="activeTab === 'kehadiran' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70'">
                        <i class="fas fa-chart-pie text-sm w-5 text-center" :class="activeTab === 'kehadiran' ? 'text-white' : 'text-emerald-600 dark:text-emerald-400'"></i>
                        <div class="text-left flex-1">
                            <span class="block leading-tight">Kehadiran</span>
                            <span class="text-[10px] block opacity-75 font-normal font-sans">Rekap & Riwayat</span>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full font-bold"
                              :class="activeTab === 'kehadiran' ? 'bg-white/20 text-white' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400'">
                            {{ $persentaseKehadiran }}%
                        </span>
                    </button>

                    {{-- 3. Teman Sekelas --}}
                    <button type="button" @click="switchTab('teman')"
                        class="w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-heading text-xs font-extrabold transition-all"
                        :class="activeTab === 'teman' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70'">
                        <i class="fas fa-user-group text-sm w-5 text-center" :class="activeTab === 'teman' ? 'text-white' : 'text-indigo-600 dark:text-indigo-400'"></i>
                        <div class="text-left flex-1">
                            <span class="block leading-tight">Teman Sekelas</span>
                            <span class="text-[10px] block opacity-75 font-normal font-sans">Direktori Siswa</span>
                        </div>
                        @if($temanSekelas->count() > 0)
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full font-bold"
                                  :class="activeTab === 'teman' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'">
                                {{ $temanSekelas->count() }}
                            </span>
                        @endif
                    </button>

                    {{-- 4. Akun & Profil --}}
                    <button type="button" @click="switchTab('profil')"
                        class="w-full flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl font-heading text-xs font-extrabold transition-all"
                        :class="activeTab === 'profil' ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/70'">
                        <i class="fas fa-id-card text-sm w-5 text-center" :class="activeTab === 'profil' ? 'text-white' : 'text-purple-600 dark:text-purple-400'"></i>
                        <div class="text-left flex-1">
                            <span class="block leading-tight">Akun Saya</span>
                            <span class="text-[10px] block opacity-75 font-normal font-sans">Profil & Pengaturan</span>
                        </div>
                    </button>
                </nav>
            </div>

            {{-- Sidebar Footer --}}
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="flex items-center justify-between px-2">
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300 flex items-center gap-2">
                        <i class="fas fa-circle-half-stroke text-blue-500"></i> Mode Gelap
                    </span>
                    <x-sky-toggle size="8px" id="siswa-sky-toggle-sidebar" />
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-rose-300 dark:hover:border-rose-800 text-slate-600 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50/50 dark:hover:bg-rose-950/30 text-xs font-bold transition">
                        <i class="fas fa-arrow-right-from-bracket text-xs"></i>
                        <span>Keluar dari Akun</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- ────────────────────────────────────────────── --}}
        {{-- 2. RIGHT WRAPPER (Topbars + Content + Bottom)   --}}
        {{-- ────────────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col min-w-0 min-h-screen">

            {{-- Mobile Top Navbar (lg:hidden) --}}
            <header class="lg:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-0 z-30 shadow-xs">
                <div class="px-4 py-2.5 flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 flex items-center justify-center shadow-xs shrink-0">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1 Beringin" class="w-full h-full object-contain">
                        </div>
                        <div class="min-w-0">
                            <span class="font-heading font-extrabold text-[11px] uppercase tracking-wider block text-slate-900 dark:text-white truncate">SMKN 1 BERINGIN</span>
                            <span class="text-[9px] text-blue-600 dark:text-blue-400 font-extrabold block truncate">Portal Siswa</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <x-sky-toggle size="8px" id="siswa-sky-toggle-mobile" />
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-slate-500 dark:text-slate-300 hover:text-rose-600 dark:hover:text-rose-400 p-1.5 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-950/40 transition" title="Keluar">
                                <i class="fas fa-arrow-right-from-bracket text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            {{-- Desktop Top Bar (hidden lg:flex) --}}
            <header class="hidden lg:flex items-center justify-between px-8 py-3.5 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <h1 class="font-heading font-black text-lg text-slate-900 dark:text-white"
                        x-text="activeTab === 'beranda' ? 'Beranda & Presensi' : (activeTab === 'kehadiran' ? 'Rekap Kehadiran Siswa' : (activeTab === 'teman' ? 'Direktori Teman Sekelas' : 'Pengaturan Akun & Profil'))"></h1>
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-mono font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                        {{ $user->kelas->nama_kelas ?? 'Kelas Siswa' }}
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</p>
                        <p id="desktop-clock" class="text-[11px] font-mono text-slate-400 dark:text-slate-400">--:-- WIB</p>
                    </div>
                </div>
            </header>

            {{-- MAIN TABS CONTENT AREA --}}
            <main class="flex-1 max-w-2xl w-full mx-auto px-4 sm:px-6 py-5 pb-28 lg:pb-12 space-y-6">

                {{-- ══════════════════════════════════════════ --}}
                {{-- TAB 1: BERANDA (OVERVIEW & SCAN CEPAT)     --}}
                {{-- ══════════════════════════════════════════ --}}
                <div x-show="activeTab === 'beranda'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
                    {{-- KARTU PROFIL SISWA — Cover & Avatar Style --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden" data-aos="fade-down">
                        {{-- 1. Cover Banner Image --}}
                        <div class="relative h-28 sm:h-36 w-full bg-slate-800 overflow-hidden group">
                            <template x-if="profileBannerUrl">
                                <img :src="profileBannerUrl" 
                                     alt="Cover Profil" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            </template>
                            <template x-if="!profileBannerUrl">
                                <div class="w-full h-full bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 flex items-center justify-center relative overflow-hidden">
                                    <div class="absolute inset-0 bg-[radial-gradient(#ffffff15_1px,transparent_1px)] [background-size:16px_16px] opacity-40"></div>
                                </div>
                            </template>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-black/20 to-transparent"></div>

                            {{-- Status Bar on Banner --}}
                            <div class="absolute top-3 left-3 flex items-center gap-1.5 bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-full text-[10px] sm:text-xs text-white border border-white/20 shadow-sm">
                                @if($user->isFaceEnrolled())
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
                                    <span class="font-semibold text-white">Face ID Aktif</span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                    <span class="font-semibold text-amber-300">Belum Rekam Wajah</span>
                                @endif
                                <span class="text-white/40">&bull;</span>
                                <span id="siswa-clock" class="font-mono">--:--</span>
                            </div>

                            {{-- Tombol Edit Profile di Pojok Kanan Banner --}}
                            <button type="button" @click="openEditProfileModal()" 
                                    class="absolute top-3 right-3 bg-black/60 hover:bg-black/85 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-xl border border-white/25 transition-all shadow-md flex items-center gap-1.5 hover:scale-105 active:scale-95">
                                <i class="fas fa-pen-to-square text-xs"></i>
                                <span class="hidden sm:inline">Edit Profil</span>
                            </button>
                        </div>

                        {{-- 2. Floating Avatar & Profile Details --}}
                        <div class="px-4 sm:px-5 pb-5 pt-0 relative">
                            <div class="flex items-end justify-between -mt-10 mb-3 flex-wrap gap-2">
                                {{-- Avatar --}}
                                <div class="relative">
                                    <div class="w-20 h-20 sm:w-22 sm:h-22 rounded-full border-4 border-white dark:border-slate-900 overflow-hidden shadow-xl bg-blue-700 text-white shrink-0">
                                        <template x-if="profileAvatarUrl">
                                            <img :src="profileAvatarUrl" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!profileAvatarUrl">
                                            <div class="w-full h-full flex items-center justify-center font-heading font-extrabold text-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
                                                {{ strtoupper($initials) }}
                                            </div>
                                        </template>
                                    </div>
                                    @if($user->isFaceEnrolled())
                                        <span class="absolute bottom-0 right-0 w-6 h-6 bg-emerald-500 rounded-full border-2 border-white dark:border-slate-900 flex items-center justify-center shadow-sm" title="Biometrik Wajah Terdaftar">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    @else
                                        <span class="absolute bottom-0 right-0 w-6 h-6 bg-amber-500 rounded-full border-2 border-white dark:border-slate-900 flex items-center justify-center shadow-sm" title="Wajah Belum Terdaftar / Direset">
                                            <i class="fas fa-triangle-exclamation text-[10px] text-white"></i>
                                        </span>
                                    @endif
                                </div>

                                {{-- Persentase Kehadiran Badge (Klik untuk beralih ke Tab Kehadiran) --}}
                                <button type="button" @click="switchTab('kehadiran')" 
                                        class="text-right bg-slate-50 dark:bg-slate-800/80 hover:bg-blue-50 dark:hover:bg-blue-950/40 border border-slate-200 dark:border-slate-700 rounded-2xl px-3 py-2 transition-all shadow-xs group"
                                        title="Buka Rekap Kehadiran">
                                    <div class="flex items-center gap-1.5 justify-end">
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Kehadiran</span>
                                        <i class="fas fa-chart-pie text-[11px] text-emerald-500"></i>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <p class="text-xl font-black font-heading text-emerald-600 dark:text-emerald-400 leading-tight">
                                            {{ $persentaseKehadiran }}%
                                        </p>
                                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                                    </div>
                                </button>
                            </div>

                            {{-- Student Name & Class Info --}}
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h1 class="font-heading font-black text-xl text-slate-900 dark:text-white tracking-tight leading-snug">
                                        {{ $user->name }}
                                    </h1>
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-500 text-white text-[10px]" title="Siswa Terverifikasi">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 mt-1 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                                    <span class="font-bold text-slate-700 dark:text-slate-300 font-mono" x-text="'@' + profileUsername">
                                        {{ '@' . ($user->username ?? 'siswa') }}
                                    </span>
                                    <span>&bull;</span>
                                    <span>SMKN 1 Beringin</span>
                                </div>

                                {{-- Bio Siswa --}}
                                <div class="mt-3">
                                    <template x-if="profileBio">
                                        <p class="text-xs text-slate-600 dark:text-slate-300 italic leading-relaxed bg-slate-50 dark:bg-slate-800/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800"
                                           x-text="'“' + profileBio + '”'"></p>
                                    </template>
                                </div>

                                {{-- Website / Social Media Link --}}
                                <template x-if="profileWebsite">
                                    <div class="mt-2.5">
                                        <a :href="profileWebsite.startsWith('http') ? profileWebsite : 'https://' + profileWebsite"
                                           target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                            <i class="fas fa-link text-[10px]"></i>
                                            <span x-text="profileWebsite.replace(/^https?:\/\//, '')"></span>
                                        </a>
                                    </div>
                                </template>

                                {{-- Kelas & NISN Pills --}}
                                <div class="grid grid-cols-2 gap-2.5 mt-3.5">
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-2.5 border border-slate-100 dark:border-slate-800 flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                            <i class="fas fa-school text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400 dark:text-slate-500">Kelas</p>
                                            <p class="text-xs font-black text-slate-800 dark:text-slate-100 truncate font-heading">{{ $user->kelas->nama_kelas ?? 'X TJKT' }}</p>
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-2.5 border border-slate-100 dark:border-slate-800 flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                            <i class="fas fa-id-card text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400 dark:text-slate-500">NISN</p>
                                            <p class="text-xs font-black text-slate-800 dark:text-slate-100 truncate font-mono">{{ $user->nisn ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bottom Action/Notice Bar on Profile Card --}}
                        @if(!$user->isFaceEnrolled())
                            <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-orange-600 px-4 sm:px-5 py-2.5 sm:py-3 flex items-center justify-between gap-3 text-white border-t border-amber-400/30">
                                <div class="flex items-center gap-2.5 text-xs font-bold min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-xs flex items-center justify-center shrink-0">
                                        <i class="fas fa-triangle-exclamation text-sm text-white"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="leading-tight font-extrabold text-white truncate sm:overflow-visible">Wajah Belum Terdaftar / Direset</p>
                                        <p class="text-[10px] text-amber-100 font-medium hidden sm:block">Wajib rekam biometrik wajah untuk verifikasi presensi</p>
                                    </div>
                                </div>
                                <a href="{{ route('siswa.enroll') }}" class="shrink-0 bg-white hover:bg-amber-50 text-amber-800 hover:text-amber-900 text-xs font-black px-3.5 py-1.5 rounded-xl transition-all shadow-sm hover:shadow active:scale-95 flex items-center gap-1.5">
                                    <i class="fas fa-camera text-xs text-amber-600"></i>
                                    <span>Rekam Wajah</span>
                                </a>
                            </div>
                        @elseif(!$user->bio)
                            <div class="bg-blue-50/70 dark:bg-blue-950/40 border-t border-blue-100 dark:border-blue-900/50 px-4 sm:px-5 py-2.5 flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs text-blue-800 dark:text-blue-200">
                                    <i class="fas fa-sparkles text-blue-500"></i>
                                    <span>Lengkapi bio profil & link sosmed kamu!</span>
                                </div>
                                <button type="button" @click="openEditProfileModal()" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-bold">
                                    Atur &rarr;
                                </button>
                            </div>
                        @else
                            <div class="bg-blue-700 dark:bg-blue-950/80 px-4 sm:px-5 py-2 flex items-center gap-2 text-white text-xs font-semibold border-t border-blue-600/40">
                                <i class="fas fa-shield-check text-blue-200 text-xs"></i>
                                <span class="text-[11px] text-blue-100">Sistem biometrik Face ID aktif & terverifikasi</span>
                            </div>
                        @endif
                    </div>

                    {{-- SESI PRESENSI AKTIF CARD (PRIORITAS SCAN UTAMA) --}}
                    <div>
                        {{-- Loading State --}}
                        <div x-show="sesiLoading"
                            class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-8 flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 rounded-full border-4 border-blue-100 dark:border-slate-800 border-t-blue-600 animate-spin"></div>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Menghubungkan ke server presensi...</span>
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
                                <div class="bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 rounded-xl px-3.5 py-2.5 text-center shadow-sm shrink-0 border border-white/60 dark:border-slate-700">
                                    <i class="fas fa-circle-check text-xl text-emerald-600 dark:text-emerald-400"></i>
                                    <p class="text-[10px] font-extrabold mt-0.5 font-mono text-emerald-600 dark:text-emerald-400">HADIR</p>
                                </div>
                            </div>
                            <div class="bg-emerald-50 dark:bg-emerald-950/70 border-t border-emerald-100 dark:border-emerald-900/60 px-5 py-3 flex items-center gap-2.5 text-emerald-900 dark:text-emerald-200">
                                <i class="fas fa-shield-halved text-emerald-600 dark:text-emerald-400 text-base shrink-0"></i>
                                <span class="text-xs font-semibold">Kehadiran Anda telah terverifikasi biometrik. Selamat belajar!</span>
                            </div>
                        </div>

                        {{-- 2. Ada sesi aktif, BELUM HADIR --}}
                        <div x-show="!sesiLoading && sesiData && !sudahHadir" x-cloak
                            class="bg-blue-600 rounded-2xl shadow-sm border border-blue-700 overflow-hidden text-white">
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
                                <div class="bg-white dark:bg-slate-900 text-amber-600 dark:text-amber-400 rounded-xl px-3.5 py-2.5 text-center shadow-sm shrink-0 border border-white/60 dark:border-slate-700">
                                    <i class="far fa-clock text-xl text-amber-500 dark:text-amber-400"></i>
                                    <p class="text-[10px] font-extrabold mt-0.5 font-mono text-amber-600 dark:text-amber-400">BELUM</p>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 p-5 border-t border-blue-500/20 dark:border-slate-800">
                                {{-- WiFi Whitelist Radar --}}
                                <div x-show="ipWhitelistActive" class="mb-4 pb-3.5 border-b border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-slate-700 dark:text-slate-200 font-bold flex items-center gap-1.5">
                                            <i class="fas fa-wifi text-blue-600 dark:text-blue-400"></i> WiFi Sekolah:
                                        </span>
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded font-bold"
                                              :class="isIpAllowed ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800'">
                                            IP: <span x-text="clientIp"></span>
                                        </span>
                                    </div>
                                    <div x-show="isIpAllowed"
                                        class="bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-300 dark:border-emerald-800/80 rounded-xl px-3.5 py-2.5 text-xs text-emerald-800 dark:text-emerald-200 flex items-center gap-2">
                                        <i class="fas fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                                        <span>Terhubung ke WiFi Resmi SMKN 1 Beringin.</span>
                                    </div>
                                    <div x-show="!isIpAllowed"
                                        class="bg-rose-50 dark:bg-rose-950/50 border border-rose-300 dark:border-rose-800/80 rounded-xl px-3.5 py-2.5 text-xs text-rose-800 dark:text-rose-200 space-y-1">
                                        <div class="flex items-center gap-2 font-bold text-rose-900 dark:text-rose-100">
                                            <i class="fas fa-triangle-exclamation text-rose-500"></i>
                                            <span>Bukan Jaringan WiFi Sekolah</span>
                                        </div>
                                        <p class="text-[11px] text-rose-700 dark:text-rose-300 pl-6">Silakan sambungkan perangkat ke WiFi SMKN 1 Beringin untuk scan presensi.</p>
                                    </div>
                                </div>

                                {{-- Tombol Mulai Scan Wajah --}}
                                @if(!$user->isFaceEnrolled())
                                    <div class="space-y-3">
                                        <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800/80 rounded-xl p-3.5 text-xs text-amber-900 dark:text-amber-200 flex items-start gap-2.5">
                                            <i class="fas fa-triangle-exclamation text-amber-500 text-base mt-0.5 shrink-0"></i>
                                            <div>
                                                <p class="font-extrabold text-amber-900 dark:text-amber-100">Wajib Rekam Wajah Terlebih Dahulu</p>
                                                <p class="text-[11px] text-amber-800 dark:text-amber-300 mt-0.5 leading-relaxed">
                                                    Data biometrik wajah Anda belum terdaftar atau baru saja direset oleh pihak sekolah. Anda wajib merekam foto wajah biometrik terlebih dahulu sebelum dapat presensi.
                                                </p>
                                            </div>
                                        </div>

                                        <a href="{{ route('siswa.enroll') }}"
                                            class="w-full font-extrabold py-3.5 rounded-xl flex items-center justify-center gap-2.5 text-sm bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white shadow-sm hover:shadow transition-all active:scale-[0.99]">
                                            <i class="fas fa-camera text-base"></i>
                                            <span>Rekam Wajah Sekarang &rarr;</span>
                                        </a>
                                    </div>
                                @else
                                    <button @click="openFaceScanner()"
                                        :disabled="(geofencingActive && geoStatus === 'outside') || (ipWhitelistActive && !isIpAllowed)"
                                        :class="(geofencingActive && geoStatus === 'outside') || (ipWhitelistActive && !isIpAllowed) ? 'opacity-70 cursor-not-allowed bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-transparent dark:border-slate-700' : 'bg-blue-600 hover:bg-blue-700 text-white shadow-sm hover:shadow-md active:scale-[0.99]'"
                                        class="w-full font-extrabold py-3.5 rounded-xl flex items-center justify-center gap-2.5 text-sm transition-all">
                                        <i class="fas text-base" :class="(geofencingActive && geoStatus === 'outside') || (ipWhitelistActive && !isIpAllowed) ? 'fa-lock' : 'fa-camera'"></i>
                                        <span x-text="ipWhitelistActive && !isIpAllowed ? 'Terkunci: Harus Pakai WiFi Sekolah' : (geofencingActive && geoStatus === 'outside' ? 'Terkunci: Di Luar Sekolah' : 'Mulai Verifikasi Wajah')"></span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- 3. Tidak ada sesi aktif --}}
                        <div x-show="!sesiLoading && !sesiData" x-cloak
                            class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                            <div class="bg-slate-50 dark:bg-slate-800/60 px-6 py-8 text-center border-b border-slate-100 dark:border-slate-800">
                                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-950/60 text-blue-500 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/60 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-hourglass-half text-2xl"></i>
                                </div>
                                <h2 class="text-base font-heading font-extrabold text-slate-800 dark:text-white mb-1">Belum Ada Sesi Aktif</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-300 leading-relaxed max-w-xs mx-auto">Menunggu guru pengampu membuka sesi presensi kelas hari ini.</p>
                            </div>
                            <div class="border-t border-slate-100 dark:border-slate-800 px-6 py-3 flex items-center justify-center gap-2">
                                <i class="fas fa-arrows-rotate text-blue-400 text-[10px] fa-spin"></i>
                                <span class="text-[11px] text-slate-400 dark:text-slate-400 font-mono">Auto-sinkron setiap 5 detik</span>
                            </div>
                        </div>
                    </div>

                    {{-- TEMAN SEKELAS HARI INI (HORIZONTAL STORY ROW) --}}
                    @if($temanSekelas->isNotEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm" data-aos="fade-up">
                        <div class="flex items-center justify-between mb-3 px-1">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                                    <i class="fas fa-users text-xs"></i>
                                </div>
                                <h3 class="font-heading font-extrabold text-xs sm:text-sm text-slate-800 dark:text-white">
                                    Teman Sekelas <span class="text-slate-400 dark:text-slate-500 font-mono font-normal">({{ $temanSekelas->count() }})</span>
                                </h3>
                            </div>
                            <button type="button" @click="switchTab('teman')" class="text-[11px] text-blue-600 dark:text-blue-400 hover:underline font-bold">
                                Lihat Semua &rarr;
                            </button>
                        </div>

                        <div class="flex items-center gap-3.5 overflow-x-auto pb-2 pt-1 -mx-1 px-1">
                            <div @click="openEditProfileModal()" class="flex flex-col items-center gap-1.5 shrink-0 cursor-pointer group snap-start">
                                <div class="w-14 h-14 rounded-full border-2 border-dashed border-blue-400 dark:border-blue-500 flex items-center justify-center text-blue-600 dark:text-blue-400 hover:scale-105 transition-transform bg-blue-50/50 dark:bg-blue-950/40">
                                    <i class="fas fa-plus text-sm"></i>
                                </div>
                                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 truncate max-w-[64px]">Profilku</span>
                            </div>

                            @foreach($temanSekelas as $teman)
                            <div @click="openClassmateModal(@js($teman))"
                                 class="flex flex-col items-center gap-1.5 shrink-0 cursor-pointer group snap-start transition-transform hover:scale-105">
                                <div class="relative w-14 h-14 rounded-full p-0.5 border-2 {{ $teman->status_hari_ini === 'hadir' ? 'border-emerald-500 dark:border-emerald-400 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-700' }}">
                                    @if($teman->avatar_url)
                                        <img src="{{ $teman->avatar_url }}" alt="{{ $teman->name }}" class="w-full h-full rounded-full object-cover">
                                    @else
                                        <div class="w-full h-full rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-700 dark:text-slate-200 font-heading font-bold text-xs">
                                            {{ strtoupper(mb_substr($teman->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-slate-900 {{ $teman->status_hari_ini === 'hadir' ? 'bg-emerald-500' : ($teman->status_hari_ini === 'izin' ? 'bg-amber-500' : ($teman->status_hari_ini === 'sakit' ? 'bg-sky-500' : 'bg-slate-300 dark:bg-slate-600')) }}"></span>
                                </div>
                                <span class="text-[11px] font-medium text-slate-700 dark:text-slate-200 truncate max-w-[68px] text-center leading-tight">
                                    {{ explode(' ', trim($teman->name))[0] }}
                                </span>
                                <span class="text-[9px] font-mono px-1.5 py-0.2 rounded-full font-bold {{ $teman->status_hari_ini === 'hadir' ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60' : 'text-slate-400 dark:text-slate-400 bg-slate-100 dark:bg-slate-800' }}">
                                    {{ $teman->status_hari_ini === 'hadir' ? 'Hadir' : 'Belum' }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- PWA Install Banner --}}
                    <div x-show="showInstallPrompt" x-cloak
                        class="bg-gradient-to-r from-blue-50 to-emerald-50 dark:from-slate-800/90 dark:to-slate-900 rounded-2xl p-4 text-slate-800 dark:text-slate-100 shadow-sm flex items-center justify-between gap-3 border border-blue-200/80 dark:border-slate-700"
                        data-aos="fade-down">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20">
                                <i class="fas fa-mobile-screen-button text-lg"></i>
                            </div>
                            <div>
                                <p class="font-extrabold text-xs text-slate-900 dark:text-white font-heading">Pasang Aplikasi Presensi (PWA)</p>
                                <p class="text-[11px] text-slate-600 dark:text-slate-300">Akses instan dari layar utama HP Anda</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button @click="installApp()"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs transition">
                                Pasang
                            </button>
                            <button @click="showInstallPrompt = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-xs p-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════ --}}
                {{-- TAB 2: KEHADIRAN & RIWAYAT                 --}}
                {{-- ══════════════════════════════════════════ --}}
                <div x-show="activeTab === 'kehadiran'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
                    {{-- Header Kehadiran --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <span class="text-[10px] font-mono font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Rekapitulasi Presensi</span>
                            <h2 class="font-heading font-black text-xl text-slate-900 dark:text-white">Kehadiran & Riwayat</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Semester Berjalan &bull; {{ $user->kelas->nama_kelas ?? 'Kelas Siswa' }}</p>
                        </div>
                        <div class="text-right flex items-center gap-3 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-2xl px-4 py-2.5">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-emerald-700 dark:text-emerald-300">Persentase</p>
                                <p class="text-2xl font-black font-heading text-emerald-600 dark:text-emerald-400 leading-none">{{ $persentaseKehadiran }}%</p>
                            </div>
                            <i class="fas fa-circle-check text-2xl text-emerald-500"></i>
                        </div>
                    </div>

                    {{-- STAT CARDS — Colored Pills --}}
                    <div class="grid grid-cols-4 gap-2.5">
                        <div class="stat-pill hadir shadow-sm">
                            <div class="w-8 h-8 bg-emerald-600 text-white rounded-xl flex items-center justify-center mx-auto mb-1.5 shadow-md">
                                <i class="fas fa-user-check text-xs"></i>
                            </div>
                            <p class="text-xl font-heading font-extrabold text-emerald-800 dark:text-emerald-300 leading-none">{{ $stats['hadir'] }}</p>
                            <p class="text-[10px] text-emerald-700 dark:text-emerald-300 font-bold mt-1">Hadir</p>
                        </div>
                        <div class="stat-pill izin shadow-sm">
                            <div class="w-8 h-8 bg-amber-500 text-white rounded-xl flex items-center justify-center mx-auto mb-1.5 shadow-md">
                                <i class="fas fa-envelope-open-text text-xs"></i>
                            </div>
                            <p class="text-xl font-heading font-extrabold text-amber-800 dark:text-amber-300 leading-none">{{ $stats['izin'] }}</p>
                            <p class="text-[10px] text-amber-700 dark:text-amber-300 font-bold mt-1">Izin</p>
                        </div>
                        <div class="stat-pill sakit shadow-sm">
                            <div class="w-8 h-8 bg-sky-500 text-white rounded-xl flex items-center justify-center mx-auto mb-1.5 shadow-md">
                                <i class="fas fa-hospital-user text-xs"></i>
                            </div>
                            <p class="text-xl font-heading font-extrabold text-sky-800 dark:text-sky-300 leading-none">{{ $stats['sakit'] }}</p>
                            <p class="text-[10px] text-sky-700 dark:text-sky-300 font-bold mt-1">Sakit</p>
                        </div>
                        <div class="stat-pill alpa shadow-sm">
                            <div class="w-8 h-8 bg-rose-500 text-white rounded-xl flex items-center justify-center mx-auto mb-1.5 shadow-md">
                                <i class="fas fa-user-xmark text-xs"></i>
                            </div>
                            <p class="text-xl font-heading font-extrabold text-rose-800 dark:text-rose-300 leading-none">{{ $stats['alpa'] }}</p>
                            <p class="text-[10px] text-rose-700 dark:text-rose-300 font-bold mt-1">Alpa</p>
                        </div>
                    </div>

                    {{-- Search & Filter Controls --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" x-model="riwayatSearch"
                                   placeholder="Cari mata pelajaran, guru, atau tanggal..."
                                   class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        </div>

                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 -mx-1 px-1">
                            <button type="button" @click="riwayatFilter = 'semua'"
                                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shrink-0"
                                :class="riwayatFilter === 'semua' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'">
                                Semua (<span x-text="riwayatSemuaList.length"></span>)
                            </button>
                            <button type="button" @click="riwayatFilter = 'hadir'"
                                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shrink-0"
                                :class="riwayatFilter === 'hadir' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300'">
                                Hadir
                            </button>
                            <button type="button" @click="riwayatFilter = 'izin'"
                                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shrink-0"
                                :class="riwayatFilter === 'izin' ? 'bg-amber-600 text-white' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300'">
                                Izin
                            </button>
                            <button type="button" @click="riwayatFilter = 'sakit'"
                                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shrink-0"
                                :class="riwayatFilter === 'sakit' ? 'bg-sky-600 text-white' : 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300'">
                                Sakit
                            </button>
                            <button type="button" @click="riwayatFilter = 'alpa'"
                                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shrink-0"
                                :class="riwayatFilter === 'alpa' ? 'bg-rose-600 text-white' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300'">
                                Alpa
                            </button>
                        </div>
                    </div>

                    {{-- Riwayat Presensi List --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <h3 class="font-heading font-extrabold text-slate-800 dark:text-white text-sm flex items-center gap-2">
                                <i class="fas fa-list-check text-blue-600 dark:text-blue-400"></i>
                                <span>Daftar Riwayat Presensi</span>
                            </h3>
                            <span class="text-[10px] font-mono text-slate-400" x-text="filteredRiwayat().length + ' catatan'"></span>
                        </div>

                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            <template x-for="(item, idx) in filteredRiwayat()" :key="idx">
                                <div class="px-5 py-4 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 mt-0.5"
                                             :class="{
                                                 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400': item.status === 'hadir',
                                                 'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400': item.status === 'izin',
                                                 'bg-sky-100 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400': item.status === 'sakit',
                                                 'bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400': item.status === 'alpa',
                                             }">
                                            <i class="fas text-sm"
                                               :class="{
                                                   'fa-check': item.status === 'hadir',
                                                   'fa-envelope': item.status === 'izin',
                                                   'fa-hospital': item.status === 'sakit',
                                                   'fa-times': item.status === 'alpa',
                                               }"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-heading font-bold text-xs text-slate-900 dark:text-white truncate" x-text="item.mapel"></h4>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1">
                                                <i class="fas fa-chalkboard-user text-[10px] opacity-70"></i>
                                                <span x-text="item.guru"></span>
                                            </p>
                                            <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400 font-mono">
                                                <span x-text="item.tanggal"></span>
                                                <template x-if="item.waktu && item.waktu !== '-'">
                                                    <span>&bull; <span x-text="item.waktu"></span> WIB</span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span class="badge text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full inline-block"
                                              :class="{
                                                  'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800': item.status === 'hadir',
                                                  'bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-300 dark:border-amber-800': item.status === 'izin',
                                                  'bg-sky-100 text-sky-700 dark:bg-sky-950/80 dark:text-sky-300 border border-sky-300 dark:border-sky-800': item.status === 'sakit',
                                                  'bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-300 dark:border-rose-800': item.status === 'alpa',
                                              }"
                                              x-text="item.status.toUpperCase()"></span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="filteredRiwayat().length === 0">
                                <div class="py-12 text-center">
                                    <div class="w-14 h-14 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3 text-slate-400">
                                        <i class="fas fa-calendar-xmark text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Tidak ada riwayat presensi yang cocok</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci pencarian atau filter status.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════ --}}
                {{-- TAB 3: TEMAN SEKELAS (DIREKTORI SOSIAL)    --}}
                {{-- ══════════════════════════════════════════ --}}
                <div x-show="activeTab === 'teman'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
                    {{-- Header Teman --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <span class="text-[10px] font-mono font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Komunitas Kelas</span>
                            <h2 class="font-heading font-black text-xl text-slate-900 dark:text-white">Teman Sekelas</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar siswa resmi di kelas {{ $user->kelas->nama_kelas ?? 'Anda' }}</p>
                        </div>
                        <div class="bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 rounded-2xl px-4 py-2.5 text-center">
                            <p class="text-[10px] font-bold text-indigo-700 dark:text-indigo-300 uppercase">Total Siswa</p>
                            <p class="text-2xl font-black font-heading text-indigo-600 dark:text-indigo-400 leading-none">{{ $temanSekelas->count() }}</p>
                        </div>
                    </div>

                    {{-- Search & Classmate Filters --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" x-model="classmateSearch"
                                   placeholder="Cari nama, username @, atau bio teman..."
                                   class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="classmateFilter = 'semua'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition"
                                :class="classmateFilter === 'semua' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'">
                                Semua
                            </button>
                            <button type="button" @click="classmateFilter = 'hadir'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition"
                                :class="classmateFilter === 'hadir' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300'">
                                Hadir Hari Ini
                            </button>
                            <button type="button" @click="classmateFilter = 'belum'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition"
                                :class="classmateFilter === 'belum' ? 'bg-slate-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'">
                                Belum Hadir
                            </button>
                        </div>
                    </div>

                    {{-- Classmate Cards Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <template x-for="item in filteredClassmates()" :key="item.id">
                            <div @click="openClassmateModal(item)"
                                 class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-all hover:scale-[1.01] cursor-pointer group flex flex-col justify-between">
                                <div>
                                    {{-- Banner Mini --}}
                                    <div class="h-16 bg-slate-800 relative overflow-hidden">
                                        <template x-if="item.banner_url || item.banner">
                                            <img :src="item.banner_url || '/storage/' + item.banner" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!item.banner_url && !item.banner">
                                            <div class="w-full h-full bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 relative">
                                                <div class="absolute inset-0 bg-[radial-gradient(#ffffff15_1px,transparent_1px)] [background-size:12px_12px] opacity-40"></div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Body Card --}}
                                    <div class="px-4 pb-3 pt-0 relative">
                                        <div class="flex items-end justify-between -mt-7 mb-2">
                                            {{-- Avatar --}}
                                            <div class="relative w-14 h-14 rounded-full border-3 border-white dark:border-slate-900 overflow-hidden shadow-md bg-blue-600 text-white shrink-0">
                                                <template x-if="item.avatar_url || item.avatar">
                                                    <img :src="item.avatar_url || '/storage/' + item.avatar" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!item.avatar_url && !item.avatar">
                                                    <div class="w-full h-full flex items-center justify-center font-heading font-extrabold text-sm"
                                                         x-text="item.name.substring(0, 2).toUpperCase()"></div>
                                                </template>
                                            </div>

                                            {{-- Status Badge --}}
                                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full font-bold"
                                                  :class="item.status_hari_ini === 'hadir' ? 'bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'">
                                                <span x-text="item.status_hari_ini === 'hadir' ? 'Hadir' : 'Belum'"></span>
                                            </span>
                                        </div>

                                        <h4 class="font-heading font-extrabold text-sm text-slate-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition" x-text="item.name"></h4>
                                        <p class="text-[11px] font-mono text-slate-400" x-text="'@' + (item.username || 'siswa')"></p>

                                        <template x-if="item.bio">
                                            <p class="text-[11px] text-slate-600 dark:text-slate-300 italic mt-2 line-clamp-2" x-text="'“' + item.bio + '”'"></p>
                                        </template>
                                    </div>
                                </div>

                                <div class="px-4 py-2.5 bg-slate-50/70 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-blue-600 dark:text-blue-400 font-bold">
                                    <span>Lihat Profil Lengkap</span>
                                    <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════ --}}
                {{-- TAB 4: AKUN & PROFIL                       --}}
                {{-- ══════════════════════════════════════════ --}}
                <div x-show="activeTab === 'profil'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
                    {{-- Header Profil --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <span class="text-[10px] font-mono font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Identitas & Akun</span>
                            <h2 class="font-heading font-black text-xl text-slate-900 dark:text-white">Pengaturan Akun</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola foto profil, bio, dan status keamanan biometrik Anda</p>
                        </div>
                        <button type="button" @click="openEditProfileModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs px-4 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2">
                            <i class="fas fa-pen-to-square"></i>
                            <span>Edit Profil</span>
                        </button>
                    </div>

                    {{-- Detail Dapodik (Data Terkunci) --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs">
                                <i class="fas fa-shield-halved"></i>
                            </div>
                            <div>
                                <h3 class="font-heading font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white">Data Resmi Siswa (Dapodik)</h3>
                                <p class="text-[11px] text-slate-400">Data ini tersinkronisasi otomatis dengan server sekolah.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5 font-heading">{{ $user->name }}</p>
                                <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 mt-1"><i class="fas fa-lock text-[9px]"></i> Terkunci</span>
                            </div>

                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NISN</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5 font-mono">{{ $user->nisn ?? '-' }}</p>
                                <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 mt-1"><i class="fas fa-lock text-[9px]"></i> Terkunci</span>
                            </div>

                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas & Rombel</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5 font-heading">{{ $user->kelas->nama_kelas ?? 'Siswa' }}</p>
                                <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 mt-1"><i class="fas fa-lock text-[9px]"></i> Terkunci</span>
                            </div>

                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Username Akun</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5 font-mono" x-text="'@' + profileUsername"></p>
                                <span class="inline-flex items-center gap-1 text-[10px] text-blue-600 dark:text-blue-400 mt-1"><i class="fas fa-pen text-[9px]"></i> Dapat diubah di Edit Profil</span>
                            </div>
                        </div>

                        <div class="p-3.5 bg-blue-50/60 dark:bg-blue-950/40 rounded-2xl border border-blue-100 dark:border-blue-900/50 text-xs text-blue-800 dark:text-blue-200 flex items-start gap-2.5">
                            <i class="fas fa-circle-info text-blue-500 mt-0.5 shrink-0"></i>
                            <span>Untuk perubahan nama resmi, NISN, atau kelas, silakan menghubungi operator kurikulum sekolah.</span>
                        </div>
                    </div>

                    {{-- Biometrik Wajah Card --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                                    <i class="fas fa-face-viewfinder"></i>
                                </div>
                                <div>
                                    <h3 class="font-heading font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white">Biometrik Face ID</h3>
                                    <p class="text-[11px] text-slate-400">Perekaman wajah untuk presensi real-time</p>
                                </div>
                            </div>
                            @if($user->isFaceEnrolled())
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold font-mono uppercase bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 pulse-dot"></span>
                                    Face ID Aktif
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold font-mono uppercase bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    Belum Rekam Wajah
                                </span>
                            @endif
                        </div>

                        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center justify-between flex-wrap gap-3">
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Perekaman Ulang Wajah (Re-Enroll)</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Jika wajah Anda sering gagal terdeteksi atau berubah penampilan.</p>
                            </div>
                            <a href="{{ route('siswa.enroll') }}"
                               class="bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl transition shadow-2xs">
                                Rekam Ulang &rarr;
                            </a>
                        </div>
                    </div>
                </div>

            </main>

            {{-- ────────────────────────────────────────────── --}}
            {{-- 3. MOBILE FLOATING BOTTOM BAR (lg:hidden)       --}}
            {{-- ────────────────────────────────────────────── --}}
            <nav class="lg:hidden fixed bottom-3 left-3 right-3 max-w-md mx-auto z-40 select-none">
                <div class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl border border-slate-200/90 dark:border-slate-800/90 rounded-2xl shadow-2xl px-2 py-1.5 flex items-center justify-around">
                    {{-- 1. Beranda --}}
                    <button type="button" @click="switchTab('beranda')"
                        class="flex flex-col items-center justify-center py-1.5 px-3 rounded-xl transition-all"
                        :class="activeTab === 'beranda' ? 'text-blue-600 dark:text-blue-400 font-extrabold scale-105' : 'text-slate-400 dark:text-slate-500 font-medium hover:text-slate-700 dark:hover:text-slate-300'">
                        <i class="fas fa-house-chimney text-base"></i>
                        <span class="text-[10px] mt-1 tracking-tight">Beranda</span>
                    </button>

                    {{-- 2. Kehadiran --}}
                    <button type="button" @click="switchTab('kehadiran')"
                        class="flex flex-col items-center justify-center py-1.5 px-3 rounded-xl transition-all relative"
                        :class="activeTab === 'kehadiran' ? 'text-blue-600 dark:text-blue-400 font-extrabold scale-105' : 'text-slate-400 dark:text-slate-500 font-medium hover:text-slate-700 dark:hover:text-slate-300'">
                        <i class="fas fa-chart-pie text-base"></i>
                        <span class="text-[10px] mt-1 tracking-tight">Kehadiran</span>
                    </button>

                    {{-- 3. Teman --}}
                    <button type="button" @click="switchTab('teman')"
                        class="flex flex-col items-center justify-center py-1.5 px-3 rounded-xl transition-all relative"
                        :class="activeTab === 'teman' ? 'text-blue-600 dark:text-blue-400 font-extrabold scale-105' : 'text-slate-400 dark:text-slate-500 font-medium hover:text-slate-700 dark:hover:text-slate-300'">
                        <i class="fas fa-user-group text-base"></i>
                        <span class="text-[10px] mt-1 tracking-tight">Teman</span>
                    </button>

                    {{-- 4. Profil --}}
                    <button type="button" @click="switchTab('profil')"
                        class="flex flex-col items-center justify-center py-1.5 px-3 rounded-xl transition-all"
                        :class="activeTab === 'profil' ? 'text-blue-600 dark:text-blue-400 font-extrabold scale-105' : 'text-slate-400 dark:text-slate-500 font-medium hover:text-slate-700 dark:hover:text-slate-300'">
                        <i class="fas fa-id-card text-base"></i>
                        <span class="text-[10px] mt-1 tracking-tight">Profil</span>
                    </button>
                </div>
            </nav>

        </div>
    </div>

    {{-- ──────────────────────────────────────────── --}}
    {{-- FACE SCANNER MODAL (ROUNDED-3XL LIGHT THEME) --}}
    {{-- ──────────────────────────────────────────── --}}
    <div x-show="isScanning" x-cloak
        class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
        <div @click.away="closeFaceScanner()"
            class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-sm shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative">

            {{-- Header Modal --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-4 flex justify-between items-center border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div
                        class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-800/80">
                        <i class="fas fa-camera text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-sm text-slate-900 dark:text-white">Verifikasi Wajah</h3>
                        <p class="text-slate-500 dark:text-slate-300 text-[11px]">Posisikan wajah di dalam bingkai oval</p>
                    </div>
                </div>
                <button @click="closeFaceScanner()"
                    class="text-slate-400 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
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
            <div class="p-5 bg-white dark:bg-slate-900">
                <div x-show="scanMessage" class="text-xs font-bold p-3 rounded-xl mb-3 text-center transition-all"
                    :class="scanSuccess ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : (scanState === 'idle' ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800')">
                    <span x-text="scanMessage"></span>
                </div>

                {{-- Tombol Verifikasi Instan Dihilangkan agar siswa wajib melewati tantangan liveness --}}
                <div x-show="scanState === 'detecting' || scanState === 'idle'">
                    <p class="text-[11px] text-slate-500 dark:text-slate-300 text-center mt-2 font-medium bg-slate-50 dark:bg-slate-800/80 p-3 rounded-lg border border-slate-100 dark:border-slate-700">
                        <i class="fas fa-shield-halved text-emerald-600 dark:text-emerald-400 mr-1"></i> Sistem Keamanan Aktif. Silakan ikuti instruksi di layar untuk memverifikasi kehadiran.
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

    {{-- ──────────────────────────────────────────── --}}
    {{-- MODAL EDIT PROFILE (IMAGE 1 SHADCN / X STYLE) --}}
    {{-- ──────────────────────────────────────────── --}}
    <div x-show="showEditProfile" x-cloak
        class="fixed inset-0 z-[110] flex items-center justify-center bg-black/75 backdrop-blur-sm p-4 overflow-y-auto">
        <div @click.away="closeEditProfileModal()"
            class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative my-auto animate-in fade-in zoom-in-95">
            
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white">Edit Profile</h3>
                </div>
                <button type="button" @click="closeEditProfileModal()"
                    class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Body with scroll --}}
            <div class="max-h-[calc(85vh-120px)] overflow-y-auto">
                {{-- Banner Cover with Upload/Remove buttons --}}
                <div class="h-32 bg-slate-200 dark:bg-slate-800 relative group overflow-hidden">
                    <template x-if="editBannerPreview">
                        <img :src="editBannerPreview" class="w-full h-full object-cover" alt="Cover Banner">
                    </template>
                    <template x-if="!editBannerPreview">
                        <div class="w-full h-full bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 opacity-90"></div>
                    </template>
                    
                    {{-- Banner Action Buttons --}}
                    <div class="absolute inset-0 bg-black/30 flex items-center justify-center gap-3">
                        <button type="button" @click="triggerBannerUpload()"
                            class="w-10 h-10 rounded-full bg-black/60 hover:bg-black/80 text-white flex items-center justify-center transition shadow-md cursor-pointer"
                            title="Ubah Banner Cover">
                            <i class="fas fa-camera text-sm"></i>
                        </button>
                        <template x-if="editBannerPreview">
                            <button type="button" @click="doRemoveBanner()"
                                class="w-10 h-10 rounded-full bg-black/60 hover:bg-black/80 text-white flex items-center justify-center transition shadow-md cursor-pointer"
                                title="Hapus Banner">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </template>
                    </div>
                    <input type="file" x-ref="bannerInput" @change="handleBannerInput" accept="image/*" class="hidden">
                </div>

                {{-- Avatar Circle overlapping banner --}}
                <div class="-mt-10 px-6 flex items-end justify-between">
                    <div class="relative w-20 h-20 rounded-full border-4 border-white dark:border-slate-900 bg-slate-200 dark:bg-slate-800 shadow-md overflow-hidden group">
                        <template x-if="editAvatarPreview">
                            <img :src="editAvatarPreview" class="w-full h-full object-cover" alt="Avatar Preview">
                        </template>
                        <template x-if="!editAvatarPreview">
                            <div class="w-full h-full flex items-center justify-center bg-blue-600 text-white font-bold text-xl">
                                {{ strtoupper($initials) }}
                            </div>
                        </template>
                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-1.5 text-white transition">
                            <button type="button" @click="triggerAvatarUpload()"
                                class="w-7 h-7 rounded-full bg-black/60 hover:bg-black/90 flex items-center justify-center text-white transition cursor-pointer"
                                title="Ubah Foto Profil">
                                <i class="fas fa-camera text-xs"></i>
                            </button>
                            <template x-if="editAvatarPreview">
                                <button type="button" @click="doRemoveAvatar()"
                                    class="w-7 h-7 rounded-full bg-rose-600/80 hover:bg-rose-600 flex items-center justify-center text-white transition cursor-pointer"
                                    title="Hapus Foto Profil">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div class="pb-1">
                        <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">Maks 2MB</span>
                    </div>
                    <input type="file" x-ref="avatarInput" @change="handleAvatarInput" accept="image/*" class="hidden">
                </div>

                {{-- Form fields --}}
                <div class="px-6 pb-6 pt-4 space-y-4">
                    {{-- Alert message --}}
                    <div x-show="profileAlert" x-cloak
                        :class="profileAlert?.type === 'success' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-200 border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-800 dark:text-rose-200 border-rose-200 dark:border-rose-800'"
                        class="p-3 rounded-xl border text-xs font-semibold flex items-center gap-2">
                        <i :class="profileAlert?.type === 'success' ? 'fas fa-check-circle text-emerald-500' : 'fas fa-exclamation-circle text-rose-500'"></i>
                        <span x-text="profileAlert?.message"></span>
                    </div>

                    {{-- Nama Siswa (LOCKED DARI DAPODIK) --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                            <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800/80 px-2 py-0.5 rounded-md flex items-center gap-1">
                                <i class="fas fa-lock text-[9px]"></i> Data Resmi Terkunci
                            </span>
                        </div>
                        <input type="text" value="{{ $user->name }}" disabled
                            class="w-full bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-500 dark:text-slate-400 font-medium cursor-not-allowed">
                        <p class="text-[10px] text-slate-400 dark:text-slate-500">Nama siswa diverifikasi langsung sesuai data Dapodik sekolah.</p>
                    </div>

                    {{-- Locked NISN & Kelas --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">NISN</label>
                            <input type="text" value="{{ $user->nisn ?? '-' }}" disabled
                                class="w-full bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-500 dark:text-slate-400 font-mono cursor-not-allowed">
                        </div>
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Kelas</label>
                            <input type="text" value="{{ $user->kelas ? $user->kelas->nama_kelas : 'Reguler' }}" disabled
                                class="w-full bg-slate-100 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-500 dark:text-slate-400 font-medium cursor-not-allowed">
                        </div>
                    </div>

                    {{-- Username --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Username</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400 font-bold text-xs">@</span>
                            <input type="text" x-model="editUsername" placeholder="username_kamu"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-8 pr-10 py-2.5 text-xs text-slate-800 dark:text-white font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <span class="absolute right-3 text-emerald-500 text-xs">
                                <i class="fas fa-check"></i>
                            </span>
                        </div>
                    </div>

                    {{-- Website / Media Sosial --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Website / Instagram / Portfolio</label>
                        <div class="flex rounded-xl shadow-xs overflow-hidden border border-slate-200 dark:border-slate-700">
                            <span class="inline-flex items-center px-3 bg-slate-50 dark:bg-slate-800 text-slate-400 text-xs font-mono border-r border-slate-200 dark:border-slate-700">
                                https://
                            </span>
                            <input type="text" x-model="editWebsite" placeholder="instagram.com/akun atau portfolio.me"
                                class="w-full bg-white dark:bg-slate-800 px-3 py-2.5 text-xs text-slate-800 dark:text-white font-medium focus:outline-none focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- Bio with character count --}}
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300">Biografi</label>
                            <span class="text-[11px] font-mono text-slate-400 dark:text-slate-400">
                                <span class="tabular-nums font-bold" x-text="bioLimit - charCount"></span> karakter tersisa
                            </span>
                        </div>
                        <textarea x-model="editBio" @input="updateBioCount()" :maxlength="bioLimit" rows="3"
                            placeholder="Ceritakan sedikit tentang minat, hobi, atau kutipan favoritmu..."
                            class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 dark:text-white font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 bg-slate-50/50 dark:bg-slate-800/40">
                <button type="button" @click="closeEditProfileModal()"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-bold text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    Batal
                </button>
                <button type="button" @click="saveProfile()" :disabled="isSavingProfile"
                    class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-sm flex items-center gap-2 disabled:opacity-50">
                    <template x-if="isSavingProfile">
                        <i class="fas fa-spinner fa-spin text-xs"></i>
                    </template>
                    <span x-text="isSavingProfile ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ──────────────────────────────────────────── --}}
    {{-- MODAL DETAIL TEMAN SEKELAS (WHO'S IN CLASS)   --}}
    {{-- ──────────────────────────────────────────── --}}
    <div x-show="showClassmateModal" x-cloak
        class="fixed inset-0 z-[110] flex items-center justify-center bg-black/75 backdrop-blur-sm p-4">
        <div @click.away="closeClassmateModal()"
            class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-sm shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative animate-in fade-in zoom-in-95">
            
            {{-- Classmate Banner --}}
            <div class="h-28 bg-slate-200 dark:bg-slate-800 relative overflow-hidden">
                <template x-if="selectedClassmate?.banner_url || selectedClassmate?.banner">
                    <img :src="selectedClassmate?.banner_url || '/storage/' + selectedClassmate?.banner"
                         class="w-full h-full object-cover" alt="Classmate Banner">
                </template>
                <template x-if="!selectedClassmate?.banner_url && !selectedClassmate?.banner">
                    <div class="w-full h-full bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 flex items-center justify-center relative">
                        <div class="absolute inset-0 bg-[radial-gradient(#ffffff15_1px,transparent_1px)] [background-size:16px_16px] opacity-40"></div>
                    </div>
                </template>
                <button type="button" @click="closeClassmateModal()"
                    class="absolute top-3 right-3 w-7 h-7 rounded-full bg-black/60 hover:bg-black/80 text-white flex items-center justify-center text-xs transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Classmate Avatar --}}
            <div class="-mt-9 px-5 flex items-end justify-between">
                <div class="relative w-18 h-18 rounded-full border-4 border-white dark:border-slate-900 bg-slate-100 dark:bg-slate-800 shadow-md overflow-hidden">
                    <template x-if="selectedClassmate?.avatar_url || selectedClassmate?.avatar">
                        <img :src="selectedClassmate?.avatar_url || '/storage/' + selectedClassmate?.avatar" class="w-full h-full object-cover" :alt="selectedClassmate.name">
                    </template>
                    <template x-if="!selectedClassmate?.avatar_url && !selectedClassmate?.avatar">
                        <div class="w-full h-full flex items-center justify-center font-heading font-extrabold text-base bg-blue-600 text-white"
                             x-text="selectedClassmate ? selectedClassmate.name.substring(0, 2).toUpperCase() : ''"></div>
                    </template>
                </div>
                {{-- Status Badge Hari Ini --}}
                <div class="pb-1">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold"
                          :class="selectedClassmate?.status_hari_ini === 'hadir' ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800' : (selectedClassmate?.status_hari_ini === 'izin' ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-300 dark:border-amber-800' : (selectedClassmate?.status_hari_ini === 'sakit' ? 'bg-sky-100 dark:bg-sky-950/80 text-sky-700 dark:text-sky-300 border border-sky-300 dark:border-sky-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700'))">
                        <span class="w-2 h-2 rounded-full"
                              :class="selectedClassmate?.status_hari_ini === 'hadir' ? 'bg-emerald-500' : (selectedClassmate?.status_hari_ini === 'izin' ? 'bg-amber-500' : (selectedClassmate?.status_hari_ini === 'sakit' ? 'bg-sky-500' : 'bg-slate-400'))"></span>
                        <span x-text="selectedClassmate?.status_hari_ini === 'hadir' ? 'Hadir ' + (selectedClassmate?.jam_absen ? selectedClassmate.jam_absen + ' WIB' : 'Hari Ini') : (selectedClassmate?.status_hari_ini === 'izin' ? 'Izin' : (selectedClassmate?.status_hari_ini === 'sakit' ? 'Sakit' : 'Belum Presensi'))"></span>
                    </span>
                </div>
            </div>

            {{-- Info Siswa --}}
            <div class="p-5 pt-3">
                <h4 class="font-heading font-extrabold text-base text-slate-900 dark:text-white flex items-center gap-1.5">
                    <span x-text="selectedClassmate?.name"></span>
                    <i class="fas fa-circle-check text-blue-500 text-xs"></i>
                </h4>
                <div class="flex items-center gap-2 text-xs mt-1">
                    <span class="font-mono text-slate-500 dark:text-slate-400" x-text="'@' + (selectedClassmate?.username || 'siswa')"></span>
                    <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                    <span class="text-slate-600 dark:text-slate-300 font-semibold">{{ $user->kelas ? $user->kelas->nama_kelas : 'Siswa SMKN 1 Beringin' }}</span>
                </div>

                {{-- Bio --}}
                <div class="mt-3.5 p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                    <template x-if="selectedClassmate?.bio">
                        <p class="text-xs text-slate-700 dark:text-slate-200 italic leading-relaxed" x-text="'“' + selectedClassmate.bio + '”'"></p>
                    </template>
                    <template x-if="!selectedClassmate?.bio">
                        <p class="text-xs text-slate-400 dark:text-slate-500 italic">Teman ini belum menulis biografi profil.</p>
                    </template>
                </div>

                {{-- Website / Social --}}
                <template x-if="selectedClassmate?.website">
                    <div class="mt-3">
                        <a :href="selectedClassmate.website.startsWith('http') ? selectedClassmate.website : 'https://' + selectedClassmate.website" target="_blank"
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            <i class="fas fa-link text-[11px]"></i>
                            <span x-text="selectedClassmate.website.replace(/^https?:\/\//, '')"></span>
                        </a>
                    </div>
                </template>

                <div class="mt-5">
                    <button type="button" @click="closeClassmateModal()"
                        class="w-full py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────── --}}
    {{-- MODAL REKAP LENGKAP PRESENSI (CEK DATA KEHADIRAN SELAMA INI) --}}
    {{-- ──────────────────────────────────────────────────────────── --}}
    <div x-show="showRiwayatLengkap" x-cloak
        class="fixed inset-0 z-[110] flex items-center justify-center bg-black/75 backdrop-blur-sm p-3 sm:p-5 overflow-y-auto">
        <div @click.away="showRiwayatLengkap = false"
            class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-3xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative my-auto flex flex-col max-h-[92vh] animate-in fade-in zoom-in-95">
            
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-800/80">
                        <i class="fas fa-clipboard-check text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white">Rekap & Riwayat Kehadiran Siswa</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-xs">Arsip lengkap seluruh sesi presensi Anda di SMKN 1 Beringin</p>
                    </div>
                </div>
                <button type="button" @click="showRiwayatLengkap = false"
                    class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Summary Stats Bar --}}
            <div class="px-6 py-3.5 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 shrink-0">
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                    {{-- Persentase Kehadiran --}}
                    <div class="col-span-2 sm:col-span-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-2.5 flex items-center gap-2.5 shadow-xs">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <i class="fas fa-percent text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Tingkat Hadir</p>
                            <p class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ $persentaseKehadiran }}%</p>
                        </div>
                    </div>
                    {{-- Hadir --}}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-2.5 flex items-center gap-2.5 shadow-xs">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Hadir</p>
                            <p class="text-sm font-black text-slate-800 dark:text-white">{{ $stats['hadir'] }} <span class="text-[10px] font-normal text-slate-400">kali</span></p>
                        </div>
                    </div>
                    {{-- Izin --}}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-2.5 flex items-center gap-2.5 shadow-xs">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                            <i class="fas fa-envelope text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Izin</p>
                            <p class="text-sm font-black text-slate-800 dark:text-white">{{ $stats['izin'] }} <span class="text-[10px] font-normal text-slate-400">kali</span></p>
                        </div>
                    </div>
                    {{-- Sakit --}}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-2.5 flex items-center gap-2.5 shadow-xs">
                        <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center shrink-0">
                            <i class="fas fa-hospital text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Sakit</p>
                            <p class="text-sm font-black text-slate-800 dark:text-white">{{ $stats['sakit'] }} <span class="text-[10px] font-normal text-slate-400">kali</span></p>
                        </div>
                    </div>
                    {{-- Alpa --}}
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/80 rounded-2xl p-2.5 flex items-center gap-2.5 shadow-xs">
                        <div class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                            <i class="fas fa-times text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Alpa</p>
                            <p class="text-sm font-black text-slate-800 dark:text-white">{{ $stats['alpa'] }} <span class="text-[10px] font-normal text-slate-400">kali</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filter & Search Controls --}}
            <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shrink-0">
                {{-- Status Pills Filter --}}
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                    <button type="button" @click="riwayatFilter = 'semua'"
                        :class="riwayatFilter === 'semua' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
                        Semua (<span x-text="riwayatSemuaList.length"></span>)
                    </button>
                    <button type="button" @click="riwayatFilter = 'hadir'"
                        :class="riwayatFilter === 'hadir' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
                        Hadir ({{ $stats['hadir'] }})
                    </button>
                    <button type="button" @click="riwayatFilter = 'izin'"
                        :class="riwayatFilter === 'izin' ? 'bg-amber-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
                        Izin ({{ $stats['izin'] }})
                    </button>
                    <button type="button" @click="riwayatFilter = 'sakit'"
                        :class="riwayatFilter === 'sakit' ? 'bg-sky-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
                        Sakit ({{ $stats['sakit'] }})
                    </button>
                    <button type="button" @click="riwayatFilter = 'alpa'"
                        :class="riwayatFilter === 'alpa' ? 'bg-rose-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap">
                        Alpa ({{ $stats['alpa'] }})
                    </button>
                </div>

                {{-- Search Box --}}
                <div class="relative min-w-[200px]">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" x-model="riwayatSearch" placeholder="Cari mapel, guru, tanggal..."
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl pl-8 pr-3 py-1.5 text-xs text-slate-800 dark:text-white font-medium focus:ring-1 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            {{-- Table Container with scroll --}}
            <div class="flex-1 overflow-y-auto min-h-[250px] p-4 sm:p-6">
                {{-- Empty State --}}
                <div x-show="filteredRiwayat().length === 0" class="py-12 text-center">
                    <div class="w-14 h-14 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-500">
                        <i class="fas fa-calendar-xmark text-2xl"></i>
                    </div>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Tidak ada riwayat yang cocok</p>
                    <p class="text-xs text-slate-400 dark:text-slate-400 mt-1">Coba ubah kata kunci pencarian atau filter status Anda.</p>
                </div>

                {{-- Desktop Table View --}}
                <div x-show="filteredRiwayat().length > 0" class="hidden sm:block overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-400 uppercase font-bold border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="px-4 py-3">Tanggal & Waktu</th>
                                <th class="px-4 py-3">Mata Pelajaran & Guru</th>
                                <th class="px-4 py-3">Kelas</th>
                                <th class="px-4 py-3">Metode</th>
                                <th class="px-4 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                            <template x-for="item in filteredRiwayat()" :key="item.id">
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="px-4 py-3.5 whitespace-nowrap">
                                        <p class="font-bold text-slate-800 dark:text-white" x-text="item.tanggal"></p>
                                        <p class="text-[11px] font-mono text-slate-400 dark:text-slate-400 mt-0.5" x-text="item.jam"></p>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <p class="font-bold text-slate-800 dark:text-slate-100" x-text="item.mapel"></p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                            <i class="fas fa-chalkboard-user text-[10px]"></i>
                                            <span x-text="item.guru"></span>
                                        </p>
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-600 dark:text-slate-300 font-semibold" x-text="item.kelas"></td>
                                    <td class="px-4 py-3.5">
                                        <span class="inline-flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400">
                                            <i class="fas text-[10px]" :class="item.metode.includes('Wajah') ? 'fa-camera text-blue-500' : 'fa-clipboard-user text-slate-400'"></i>
                                            <span x-text="item.metode"></span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="badge text-[11px] font-bold"
                                            :class="item.status === 'hadir' ? 'badge-hadir' : (item.status === 'izin' ? 'badge-izin' : (item.status === 'sakit' ? 'badge-sakit' : 'badge-alpa'))"
                                            x-text="item.status.toUpperCase()"></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card List View --}}
                <div x-show="filteredRiwayat().length > 0" class="sm:hidden space-y-2.5">
                    <template x-for="item in filteredRiwayat()" :key="'mob-' + item.id">
                        <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-slate-900 dark:text-white truncate" x-text="item.mapel"></span>
                                    <span class="text-[10px] text-slate-400 font-mono" x-text="item.kelas"></span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                    <span x-text="item.tanggal"></span> &bull; <span x-text="item.jam"></span>
                                </p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5 flex items-center gap-1">
                                    <i class="fas fa-chalkboard-user text-[9px]"></i>
                                    <span x-text="item.guru"></span>
                                </p>
                            </div>
                            <span class="badge text-[10px] font-bold shrink-0"
                                :class="item.status === 'hadir' ? 'badge-hadir' : (item.status === 'izin' ? 'badge-izin' : (item.status === 'sakit' ? 'badge-sakit' : 'badge-alpa'))"
                                x-text="item.status.toUpperCase()"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-3.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/40 shrink-0">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                    Menampilkan <span class="font-bold text-slate-800 dark:text-white" x-text="filteredRiwayat().length"></span> dari <span class="font-bold text-slate-800 dark:text-white" x-text="riwayatSemuaList.length"></span> sesi
                </span>
                <button type="button" @click="showRiwayatLengkap = false"
                    class="px-5 py-2 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-xs hover:opacity-90 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true, duration: 400, offset: 20 });

        // Realtime clock di profile hero & desktop header
        function updateSiswaClock() {
            const timeStr = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }) + ' WIB';
            const el = document.getElementById('siswa-clock');
            if (el) el.textContent = timeStr;
            const elDesktop = document.getElementById('desktop-clock');
            if (elDesktop) elDesktop.textContent = timeStr;
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
                // ── App Shell Navigation State ──
                activeTab: (['beranda', 'kehadiran', 'teman', 'profil'].includes(window.location.hash.replace('#', '')) ? window.location.hash.replace('#', '') : 'beranda'),

                // ── Classmate Social Directory State ──
                classmateSearch: '',
                classmateFilter: 'semua',
                classmatesList: {!! json_encode($temanSekelas) !!},

                // ── Profile & Social State ──
                showEditProfile: false,
                showClassmateModal: false,
                selectedClassmate: null,
                showRiwayatLengkap: false,

                // Active Profile Display
                profileName: '{{ addslashes($user->name) }}',
                profileUsername: '{{ addslashes($user->username ?? "siswa") }}',
                profileBio: {!! json_encode($user->bio ?? '') !!},
                profileWebsite: {!! json_encode($user->website ?? '') !!},
                profileAvatarUrl: {!! json_encode($user->avatar_url) !!},
                profileBannerUrl: {!! json_encode($user->banner_url) !!},
                isFaceEnrolled: {{ $user->isFaceEnrolled() ? 'true' : 'false' }},

                // Edit Form State
                editUsername: '{{ addslashes($user->username ?? "siswa") }}',
                editBio: {!! json_encode($user->bio ?? '') !!},
                editWebsite: {!! json_encode($user->website ?? '') !!},
                editAvatarPreview: {!! json_encode($user->avatar_url) !!},
                editBannerPreview: {!! json_encode($user->banner_url) !!},
                avatarFile: null,
                bannerFile: null,
                removeAvatar: false,
                removeBanner: false,
                bioLimit: 180,
                charCount: {{ mb_strlen($user->bio ?? '') }},
                isSavingProfile: false,
                profileAlert: null,

                // Full Attendance History & Search
                riwayatFilter: 'semua',
                riwayatSearch: '',
                riwayatSemuaList: {!! json_encode($riwayatSemuaFormatted) !!},

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
                    const initialHash = window.location.hash.replace('#', '');
                    if (['beranda', 'kehadiran', 'teman', 'profil'].includes(initialHash)) {
                        this.activeTab = initialHash;
                    }
                    window.addEventListener('hashchange', () => {
                        const newHash = window.location.hash.replace('#', '');
                        if (['beranda', 'kehadiran', 'teman', 'profil'].includes(newHash)) {
                            this.activeTab = newHash;
                        }
                    });

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
                    if (!this.isFaceEnrolled) {
                        audioFx.playError();
                        alert('Biometrik wajah Anda belum terdaftar atau telah direset. Silakan rekam wajah terlebih dahulu.');
                        window.location.href = '{{ route('siswa.enroll') }}';
                        return;
                    }

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

                // ── Profile Methods ──
                openEditProfileModal() {
                    this.editUsername = this.profileUsername;
                    this.editBio = this.profileBio || '';
                    this.editWebsite = this.profileWebsite || '';
                    this.editAvatarPreview = this.profileAvatarUrl;
                    this.editBannerPreview = this.profileBannerUrl;
                    this.avatarFile = null;
                    this.bannerFile = null;
                    this.removeAvatar = false;
                    this.removeBanner = false;
                    this.charCount = (this.editBio || '').length;
                    this.profileAlert = null;
                    this.showEditProfile = true;
                },

                closeEditProfileModal() {
                    this.showEditProfile = false;
                    this.profileAlert = null;
                },

                triggerBannerUpload() {
                    this.$refs.bannerInput.click();
                },

                triggerAvatarUpload() {
                    this.$refs.avatarInput.click();
                },

                handleBannerInput(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    if (file.size > 4 * 1024 * 1024) {
                        this.profileAlert = { type: 'error', message: 'Ukuran banner maksimal 4MB.' };
                        return;
                    }
                    this.bannerFile = file;
                    this.removeBanner = false;
                    this.editBannerPreview = URL.createObjectURL(file);
                },

                handleAvatarInput(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    if (file.size > 2 * 1024 * 1024) {
                        this.profileAlert = { type: 'error', message: 'Ukuran foto profil maksimal 2MB.' };
                        return;
                    }
                    this.avatarFile = file;
                    this.removeAvatar = false;
                    this.editAvatarPreview = URL.createObjectURL(file);
                },

                doRemoveBanner() {
                    this.bannerFile = null;
                    this.removeBanner = true;
                    this.editBannerPreview = null;
                },

                doRemoveAvatar() {
                    this.avatarFile = null;
                    this.removeAvatar = true;
                    this.editAvatarPreview = null;
                },

                updateBioCount() {
                    if (this.editBio && this.editBio.length > this.bioLimit) {
                        this.editBio = this.editBio.substring(0, this.bioLimit);
                    }
                    this.charCount = (this.editBio || '').length;
                },

                async saveProfile() {
                    if (!this.editUsername || this.editUsername.trim() === '') {
                        this.profileAlert = { type: 'error', message: 'Username tidak boleh kosong.' };
                        return;
                    }

                    this.isSavingProfile = true;
                    this.profileAlert = null;

                    const formData = new FormData();
                    formData.append('username', this.editUsername.trim());
                    formData.append('bio', this.editBio || '');
                    formData.append('website', this.editWebsite || '');
                    if (this.avatarFile) formData.append('avatar', this.avatarFile);
                    if (this.bannerFile) formData.append('banner', this.bannerFile);
                    if (this.removeBanner) formData.append('remove_banner', '1');
                    if (this.removeAvatar) formData.append('remove_avatar', '1');

                    try {
                        const res = await fetch('{{ route("siswa.profile.update") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: formData
                        });
                        const data = await res.json();
                        this.isSavingProfile = false;

                        if (res.ok && data.success) {
                            this.profileAlert = { type: 'success', message: 'Profil berhasil diperbarui!' };
                            this.profileUsername = data.user.username;
                            this.profileBio = data.user.bio;
                            this.profileWebsite = data.user.website;
                            this.profileAvatarUrl = data.user.avatar_url;
                            this.profileBannerUrl = data.user.banner_url;

                            setTimeout(() => {
                                this.closeEditProfileModal();
                                window.location.reload();
                            }, 1000);
                        } else {
                            const errMsg = data.message || (data.errors ? Object.values(data.errors)[0][0] : 'Gagal memperbarui profil.');
                            this.profileAlert = { type: 'error', message: errMsg };
                        }
                    } catch (err) {
                        this.isSavingProfile = false;
                        this.profileAlert = { type: 'error', message: 'Terjadi kesalahan jaringan saat menyimpan.' };
                    }
                },

                // ── Classmate Modal Methods ──
                openClassmateModal(teman) {
                    if (!teman) return;
                    if (!teman.avatar_url && teman.avatar) {
                        teman.avatar_url = (teman.avatar.startsWith('http') ? teman.avatar : '/storage/' + teman.avatar);
                    }
                    if (!teman.banner_url && teman.banner) {
                        teman.banner_url = (teman.banner.startsWith('http') ? teman.banner : '/storage/' + teman.banner);
                    }
                    this.selectedClassmate = teman;
                    this.showClassmateModal = true;
                },

                closeClassmateModal() {
                    this.showClassmateModal = false;
                    this.selectedClassmate = null;
                },

                // ── Riwayat Filter Method ──
                filteredRiwayat() {
                    return this.riwayatSemuaList.filter(item => {
                        // Filter status
                        if (this.riwayatFilter !== 'semua' && item.status !== this.riwayatFilter) {
                            return false;
                        }
                        // Filter search text
                        if (this.riwayatSearch && this.riwayatSearch.trim() !== '') {
                            const q = this.riwayatSearch.toLowerCase().trim();
                            const matchMapel = (item.mapel || '').toLowerCase().includes(q);
                            const matchGuru = (item.guru || '').toLowerCase().includes(q);
                            const matchTanggal = (item.tanggal || '').toLowerCase().includes(q);
                            const matchKelas = (item.kelas || '').toLowerCase().includes(q);
                            return matchMapel || matchGuru || matchTanggal || matchKelas;
                        }
                        return true;
                    });
                },

                // ── App Shell Tab Switching & Classmate Filtering ──
                switchTab(tab) {
                    this.activeTab = tab;
                    window.location.hash = tab;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                filteredClassmates() {
                    return this.classmatesList.filter(item => {
                        if (this.classmateFilter === 'hadir' && item.status_hari_ini !== 'hadir') return false;
                        if (this.classmateFilter === 'belum' && item.status_hari_ini === 'hadir') return false;
                        if (this.classmateSearch && this.classmateSearch.trim() !== '') {
                            const q = this.classmateSearch.toLowerCase().trim();
                            const matchName = (item.name || '').toLowerCase().includes(q);
                            const matchUsername = (item.username || '').toLowerCase().includes(q);
                            const matchBio = (item.bio || '').toLowerCase().includes(q);
                            return matchName || matchUsername || matchBio;
                        }
                        return true;
                    });
                },
            };
        }
    </script>
</body>

</html>