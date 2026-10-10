<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Presensi">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.webp') }}">
    <title>Portal Presensi Siswa — SMKN 1 Beringin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
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

        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        
        .magic-btn {
            position: relative;
            overflow: hidden;
        }
        
        .magic-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.4), transparent);
            transform: skewX(-20deg);
            animation: shimmer 3s infinite linear;
        }

        @keyframes float-icon {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-2.5px) scale(1.08); }
        }

        @keyframes pulse-ring-amber {
            0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
            70% { box-shadow: 0 0 0 9px rgba(245, 158, 11, 0); }
            100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
        }

        @keyframes pulse-ring-sky {
            0% { box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.7); }
            70% { box-shadow: 0 0 0 9px rgba(56, 189, 248, 0); }
            100% { box-shadow: 0 0 0 0 rgba(56, 189, 248, 0); }
        }

        @keyframes wiggle-smile {
            0%, 100% { transform: rotate(-7deg) scale(1.15); }
            50% { transform: rotate(7deg) scale(1.22); }
        }

        .anim-float {
            animation: float-icon 2s ease-in-out infinite;
        }

        .anim-ring-amber {
            animation: pulse-ring-amber 1.4s cubic-bezier(0.24, 0, 0.38, 1) infinite;
        }

        .anim-ring-sky {
            animation: pulse-ring-sky 1.4s cubic-bezier(0.24, 0, 0.38, 1) infinite;
        }

        .anim-wiggle {
            animation: wiggle-smile 0.35s ease-in-out infinite;
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

        /* ── Face Oval HUD (Clean Apple FaceID / Fintech Style - No AI Slop) ── */
        .face-oval {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -55%);
            width: 195px;
            height: 250px;
            border-radius: 50% / 46%;
            border: 2px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.65);
            pointer-events: none;
            z-index: 5;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .face-oval.aligned {
            border-color: #10b981;
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.65), 0 0 0 3px rgba(16, 185, 129, 0.25);
        }

        .face-oval.action_success,
        .face-oval.detected {
            border-color: #10b981;
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.65), 0 0 0 6px rgba(16, 185, 129, 0.4);
            transform: translate(-50%, -55%) scale(1.02);
        }

        .face-oval.success {
            border-color: #10b981;
            box-shadow: 0 0 0 9999px rgba(6, 78, 59, 0.7), 0 0 0 8px rgba(16, 185, 129, 0.5);
            transform: translate(-50%, -55%) scale(1.03);
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
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo SMKN 1 Beringin" class="w-full h-full object-contain">
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
                            <img src="{{ asset('images/logo.webp') }}" alt="Logo SMKN 1 Beringin" class="w-full h-full object-contain">
                        </div>
                        <div class="min-w-0">
                            <span class="font-heading font-extrabold text-[11px] uppercase tracking-wider block text-slate-900 dark:text-white truncate">SMKN 1 BERINGIN</span>
                            <span class="text-[9px] text-blue-600 dark:text-blue-400 font-extrabold block truncate">Portal Siswa</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <img src="{{ asset('images/logo-kolaborasi.webp') }}" class="h-6 w-auto object-contain mr-1" alt="Logo Kolaborasi">
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
                    <div class="hidden md:block w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
                    <img src="{{ asset('images/logo-kolaborasi.webp') }}" class="h-8 object-contain opacity-90" alt="Logo Kolaborasi">
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
                @include('siswa.partials.beranda')
                @include('siswa.partials.kehadiran')
                @include('siswa.partials.teman')
                @include('siswa.partials.profil')
                    {{-- 1. Beranda --}}
                    <button type="button" x-ref="beranda_btn" @click="switchTab('beranda')"
                        class="relative flex flex-col items-center justify-center py-2 px-4 rounded-[20px] transition-all duration-300 z-10"
                        :class="activeTab === 'beranda' ? 'text-white scale-105' : 'text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'">
                        <i class="fas fa-house-chimney text-base"></i>
                        <span x-show="activeTab === 'beranda'" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" class="text-[9px] mt-1 tracking-wider font-bold uppercase">Beranda</span>
                    </button>

                    {{-- 2. Kehadiran --}}
                    <button type="button" x-ref="kehadiran_btn" @click="switchTab('kehadiran')"
                        class="relative flex flex-col items-center justify-center py-2 px-4 rounded-[20px] transition-all duration-300 z-10"
                        :class="activeTab === 'kehadiran' ? 'text-white scale-105' : 'text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'">
                        <i class="fas fa-chart-pie text-base"></i>
                        <span x-show="activeTab === 'kehadiran'" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" class="text-[9px] mt-1 tracking-wider font-bold uppercase">Hadir</span>
                    </button>

                    {{-- 3. Teman --}}
                    <button type="button" x-ref="teman_btn" @click="switchTab('teman')"
                        class="relative flex flex-col items-center justify-center py-2 px-4 rounded-[20px] transition-all duration-300 z-10"
                        :class="activeTab === 'teman' ? 'text-white scale-105' : 'text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'">
                        <i class="fas fa-user-group text-base"></i>
                        <span x-show="activeTab === 'teman'" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" class="text-[9px] mt-1 tracking-wider font-bold uppercase">Teman</span>
                    </button>

                    {{-- 4. Profil --}}
                    <button type="button" x-ref="profil_btn" @click="switchTab('profil')"
                        class="relative flex flex-col items-center justify-center py-2 px-4 rounded-[20px] transition-all duration-300 z-10"
                        :class="activeTab === 'profil' ? 'text-white scale-105' : 'text-slate-400 dark:text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'">
                        <i class="fas fa-id-card text-base"></i>
                        <span x-show="activeTab === 'profil'" x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" class="text-[9px] mt-1 tracking-wider font-bold uppercase">Profil</span>
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
            <div class="relative bg-slate-950 overflow-hidden select-none" style="height: 325px;">
                <video id="video-scan" autoplay playsinline muted class="w-full h-full object-cover"
                    style="transform:scaleX(-1)"></video>
                <canvas id="canvas-scan" class="hidden"></canvas>

                {{-- Shutter Flash Effect --}}
                <div x-show="showShutterFlash" x-transition.opacity.duration.120ms class="absolute inset-0 bg-white z-40 pointer-events-none"></div>

                {{-- Face Oval HUD (Clean Apple FaceID Style - No AI Laser) --}}
                <div class="face-oval"
                    :class="{
                        'aligned': livenessState === 'face_aligned',
                        'action_success': livenessState === 'action_success',
                        'detected': scanState === 'processing',
                        'success': scanState === 'success'
                    }">
                </div>

                {{-- Clean Top Instruction Pill (Single sleek frosted badge) --}}
                <div x-show="scanState === 'ready'" class="absolute top-3 left-0 right-0 flex justify-center items-center z-20 px-3 pointer-events-none">
                    <div class="backdrop-blur-md text-white text-xs px-4 py-1.5 rounded-full border shadow-lg flex items-center gap-2.5 transition-all duration-200 select-none pointer-events-auto"
                        :class="{
                            'bg-slate-900/80 border-white/20': livenessState === 'waiting_face',
                            'bg-slate-900/90 border-emerald-400/60 shadow-emerald-500/20 ring-1 ring-emerald-400/30': livenessState === 'face_aligned',
                            'bg-emerald-600/95 border-emerald-300 shadow-emerald-500/40 font-bold': livenessState === 'action_success'
                        }">
                        <span class="w-2 h-2 rounded-full transition-colors"
                            :class="{
                                'bg-amber-400 animate-pulse': livenessState === 'waiting_face',
                                'bg-emerald-400': livenessState === 'face_aligned',
                                'bg-white': livenessState === 'action_success'
                            }"></span>
                        <span class="font-medium tracking-wide" x-text="livenessPrompt"></span>

                        {{-- Tombol ganti tantangan cepat --}}
                        <button type="button" @click.stop="switchChallenge()" title="Ganti tantangan"
                            class="ml-1 text-slate-400 hover:text-white transition p-1 rounded-full hover:bg-white/10 active:scale-90 cursor-pointer">
                            <i class="fas fa-arrows-rotate text-[10px]"></i>
                        </button>
                    </div>
                </div>

                {{-- Clean Live Progress Meter with Animated Action Icon --}}
                <div x-show="scanState === 'ready'" class="absolute bottom-3 left-4 right-4 flex justify-center z-20 pointer-events-none">
                    <div class="w-full max-w-[295px] bg-slate-900/95 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-white/20 shadow-2xl flex items-center gap-2.5 pointer-events-auto">
                        
                        {{-- Animated Action Icon Container --}}
                        <div class="relative flex items-center justify-center w-7 h-7 rounded-full shrink-0 transition-all duration-300"
                             :class="{
                                 'anim-ring-amber bg-amber-500/25 ring-2 ring-amber-400': currentChallenge && currentChallenge.type === 'smile' && actionProgress > 15,
                                 'anim-ring-sky bg-sky-500/25 ring-2 ring-sky-400': currentChallenge && currentChallenge.type === 'blink' && actionProgress > 15,
                                 'bg-white/10': actionProgress <= 15
                             }">
                            <template x-if="currentChallenge && currentChallenge.type === 'smile'">
                                <span class="select-none transition-transform duration-200 inline-block"
                                      :class="{
                                          'text-xs anim-float': actionProgress === 0,
                                          'text-sm scale-110': actionProgress > 0 && actionProgress < 40,
                                          'text-base anim-wiggle': actionProgress >= 40 && actionProgress < 85,
                                          'text-lg animate-bounce drop-shadow-[0_0_8px_rgba(251,191,36,0.8)]': actionProgress >= 85
                                      }">
                                    <span x-show="actionProgress < 70">😊</span>
                                    <span x-show="actionProgress >= 70">😁</span>
                                </span>
                            </template>
                            <template x-if="currentChallenge && currentChallenge.type === 'blink'">
                                <span class="select-none transition-transform duration-200 inline-block"
                                      :class="{
                                          'text-xs anim-float': actionProgress === 0,
                                          'text-sm scale-110 animate-pulse': actionProgress > 0 && actionProgress < 50,
                                          'text-base scale-125 animate-ping': actionProgress >= 50 && actionProgress < 85,
                                          'text-lg animate-bounce drop-shadow-[0_0_8px_rgba(56,189,248,0.8)]': actionProgress >= 85
                                      }">
                                    😉
                                </span>
                            </template>
                            <template x-if="!currentChallenge">
                                <span class="text-xs animate-spin select-none">🎯</span>
                            </template>
                        </div>

                        {{-- Progress Track --}}
                        <div class="flex-1 bg-white/15 rounded-full h-2.5 overflow-hidden relative">
                            <div class="h-full rounded-full transition-all duration-100 ease-out relative"
                                :class="actionProgress >= 80 ? 'bg-gradient-to-r from-emerald-400 to-teal-300 shadow-sm shadow-emerald-400/50' : 'bg-gradient-to-r from-blue-400 via-sky-400 to-amber-400'"
                                :style="'width: ' + actionProgress + '%'">
                                <div x-show="actionProgress > 5" class="absolute right-0 top-0 bottom-0 w-2 bg-white/80 rounded-full blur-[1px] animate-pulse"></div>
                            </div>
                        </div>

                        {{-- Live Percentage & Micro-Animated Status --}}
                        <div class="flex items-center gap-1.5 shrink-0 min-w-[40px] justify-end">
                            <span class="font-mono text-xs font-bold transition-all duration-200"
                                :class="actionProgress >= 80 ? 'text-emerald-400 font-extrabold text-sm' : 'text-slate-300'"
                                x-text="actionProgress + '%'"></span>
                            <template x-if="actionProgress > 0 && actionProgress < 88">
                                <i class="fas fa-circle-notch fa-spin text-[9px] text-sky-400"></i>
                            </template>
                            <template x-if="actionProgress >= 88">
                                <i class="fas fa-check text-xs text-emerald-400 animate-bounce"></i>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Overlays Status (Clean Minimalist Fintech) --}}
                <div x-show="scanState === 'initializing' || scanState === 'idle'" class="absolute inset-0 bg-slate-950/85 flex flex-col items-center justify-center text-white z-30">
                    <i class="fas fa-circle-notch fa-spin text-2xl text-blue-400 mb-2"></i>
                    <p class="text-xs font-medium text-slate-300">Menyiapkan kamera...</p>
                </div>

                <div x-show="scanState === 'processing'"
                    class="absolute inset-0 bg-slate-950/90 flex flex-col items-center justify-center backdrop-blur-sm z-30 text-center px-4">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-emerald-400 mb-3"></i>
                    <p class="font-heading font-bold text-white text-sm">Memverifikasi Kehadiran...</p>
                    <p class="text-slate-400 text-xs mt-1">Mencocokkan biometrik wajah Anda</p>
                </div>

                <div x-show="scanState === 'success'"
                    class="absolute inset-0 bg-emerald-950/95 flex items-center justify-center z-30">
                    <div class="text-center text-white checkmark-pop">
                        <div class="w-14 h-14 rounded-full bg-emerald-500/20 border-2 border-emerald-400 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-check text-2xl text-emerald-400"></i>
                        </div>
                        <p class="font-heading font-extrabold text-base text-white">Presensi Berhasil ✓</p>
                        <p class="text-xs text-emerald-300/80 mt-0.5">Kehadiran telah tercatat</p>
                    </div>
                </div>

                <div x-show="scanState === 'failed'"
                    class="absolute inset-0 bg-rose-950/95 flex items-center justify-center z-30">
                    <div class="text-center text-white shake px-4">
                        <div class="w-14 h-14 rounded-full bg-rose-500/20 border-2 border-rose-400 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-times text-2xl text-rose-400"></i>
                        </div>
                        <p class="font-heading font-bold text-sm text-white">Verifikasi Gagal</p>
                        <p class="text-xs text-rose-300/80 mt-1" x-text="scanMessage"></p>
                    </div>
                </div>
            </div>

            {{-- Bottom Action & Controls (Clean & Functional) --}}
            <div class="p-4 sm:p-5 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800">
                <div x-show="scanMessage && scanState !== 'failed'" class="text-xs font-semibold p-2.5 rounded-xl mb-3 text-center transition-all"
                    :class="scanSuccess ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800'">
                    <span x-text="scanMessage"></span>
                </div>

                <div x-show="scanState === 'ready'">
                    <button type="button" @click="captureAndSend()"
                        class="w-full bg-slate-900 hover:bg-slate-800 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-2xl transition flex items-center justify-center gap-2 text-sm shadow-sm active:scale-[0.98] cursor-pointer">
                        <i class="fas fa-camera text-sm"></i>
                        <span>Ambil Foto Manual</span>
                    </button>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 text-center mt-2 font-medium">
                        Otomatis terpotret saat gerakan terdeteksi, atau tekan tombol manual di atas.
                    </p>
                </div>

                <div x-show="scanState === 'failed'">
                    <button type="button" @click="retryScan()"
                        class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-4 rounded-2xl transition flex items-center justify-center gap-2 text-sm shadow-sm active:scale-[0.98] cursor-pointer">
                        <i class="fas fa-rotate-right text-sm"></i> Coba Pindai Ulang
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

    <script src="{{ asset('vendor/face-api/face-api.min.js') }}"></script>
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

                // ── Face scanner state (Server-side AI) ──
                isScanning: false,
                scanState: 'idle', // 'idle' | 'initializing' | 'ready' | 'processing' | 'success' | 'failed'
                scanMessage: '',
                scanSuccess: false,
                videoStream: null,

                // E-Wallet Randomized Challenge States
                challenges: [
                    {
                        type: 'blink',
                        label: 'Kedipkan Mata Anda 😉',
                        actionText: 'Kedipkan mata perlahan 😉',
                        shortLabel: 'Kedip',
                        emoji: '😉',
                        hint: 'Kedipkan mata Anda ke arah kamera untuk verifikasi otomatis'
                    },
                    {
                        type: 'smile',
                        label: 'Silakan Tersenyum 😊',
                        actionText: 'Tersenyum ke kamera 😊',
                        shortLabel: 'Senyum',
                        emoji: '😊',
                        hint: 'Tersenyumlah ke arah kamera untuk verifikasi otomatis'
                    }
                ],
                currentChallenge: null,
                actionProgress: 0,
                livenessState: 'waiting_face', // 'waiting_face' | 'face_aligned' | 'action_success'
                livenessPrompt: 'Posisikan wajah di dalam oval',
                livenessSubPrompt: 'Posisikan seluruh wajah di dalam bingkai oval',
                isAiLoaded: false,
                isLoadingAi: false,
                showShutterFlash: false,
                aiLoopTimer: null,

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

                    if (window.faceapi) {
                        this.preloadFaceApi();
                    } else {
                        window.addEventListener('load', () => this.preloadFaceApi());
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
                    this.scanState = 'initializing';
                    this.scanMessage = 'Membuka kamera...';
                    this.scanSuccess = false;

                    try {
                        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                            throw new Error("Akses kamera diblokir browser. Pastikan Anda menggunakan HTTPS.");
                        }
                        this.videoStream = await navigator.mediaDevices.getUserMedia({
                            video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                            audio: false
                        });
                        const video = document.getElementById('video-scan');
                        video.srcObject = this.videoStream;

                        video.onloadedmetadata = async () => {
                            video.width = video.videoWidth || 640;
                            video.height = video.videoHeight || 480;
                            try {
                                await video.play();
                            } catch (e) {}
                            this.armScanner();
                        };

                    } catch (err) {
                        audioFx.playError();
                        this.scanMessage = 'Gagal mengakses kamera: ' + err.message;
                        this.scanState = 'failed';
                    }
                },

                pickRandomChallenge() {
                    const list = this.challenges;
                    const next = list[Math.floor(Math.random() * list.length)];
                    this.currentChallenge = next;
                    this.actionProgress = 0;
                    this.updateLivenessPrompt();
                },

                switchChallenge() {
                    const list = this.challenges;
                    let next = list[Math.floor(Math.random() * list.length)];
                    if (this.currentChallenge && list.length > 1) {
                        while (next.type === this.currentChallenge.type) {
                            next = list[Math.floor(Math.random() * list.length)];
                        }
                    }
                    this.currentChallenge = next;
                    this.actionProgress = 0;
                    audioFx.playBlink();
                    this.updateLivenessPrompt();
                },

                updateLivenessPrompt() {
                    const ch = this.currentChallenge || this.challenges[0];
                    if (this.livenessState === 'action_success') {
                        this.livenessPrompt = 'Gerakan terverifikasi! ✓';
                    } else if (this.livenessState === 'face_aligned') {
                        this.livenessPrompt = ch.actionText;
                    } else {
                        this.livenessPrompt = 'Posisikan wajah di dalam oval';
                    }
                },

                armScanner() {
                    this.scanState = 'ready';
                    this.scanMessage = '';
                    this.livenessState = 'waiting_face';
                    this.actionProgress = 0;
                    this.pickRandomChallenge();
                    this.startLivenessDetection();
                },

                triggerShutterFlash() {
                    this.showShutterFlash = true;
                    setTimeout(() => {
                        this.showShutterFlash = false;
                    }, 140);
                },

                startLivenessDetection() {
                    if (this.aiLoopTimer) {
                        cancelAnimationFrame(this.aiLoopTimer);
                        clearTimeout(this.aiLoopTimer);
                        this.aiLoopTimer = null;
                    }

                    this.actionProgress = 0;
                    this.livenessState = 'waiting_face';
                    this.updateLivenessPrompt();

                    // Canvas analisa offscreen berkecepatan tinggi (96x128)
                    const offCanvas = document.createElement('canvas');
                    offCanvas.width = 96;
                    offCanvas.height = 128;
                    const offCtx = offCanvas.getContext('2d', { willReadFrequently: true });

                    // Baseline akumulator saat wajah stabil
                    let baselineSmileRatio = null;
                    let baselineCornerLift = null;
                    let baselineInnerRatio = null;
                    let baselineEyeContrast = null;

                    let prevCheekY = null;
                    let stableFrames = 0;
                    const WARMUP_FRAMES = 25; // ~0.8 detik wajah harus diam sebelum aksi diaktifkan

                    let smileHoldCount = 0;
                    let blinkPhase = 'idle'; // 'idle' | 'closed'
                    let blinkClosedStart = 0;
                    let currentProgressVal = 0;
                    let isTriggered = false;

                    const processFrame = () => {
                        if (this.scanState !== 'ready' || !this.isScanning || isTriggered) {
                            return;
                        }

                        const video = document.getElementById('video-scan');
                        if (!video || video.paused || video.ended || video.readyState < 2) {
                            this.aiLoopTimer = requestAnimationFrame(processFrame);
                            return;
                        }

                        try {
                            const vw = video.videoWidth || 640;
                            const vh = video.videoHeight || 480;

                            // Area crop oval tengah di video (proporsi tepat dengan .face-oval)
                            const cropW = vw * 0.58;
                            const cropH = vh * 0.74;
                            const cropX = (vw - cropW) / 2;
                            const cropY = (vh - cropH) * 0.42;

                            offCtx.drawImage(video, cropX, cropY, cropW, cropH, 0, 0, 96, 128);
                            const imgData = offCtx.getImageData(0, 0, 96, 128);
                            const data = imgData.data;

                            // ── 1. Analisis Fotometrik & Struktur Anatomi Wajah Penuh ──
                            let totalOvalPix = 0;
                            let skinPix = 0;

                            let foreheadSkinPix = 0;
                            let leftEyeYSum = 0, leftEyeCount = 0;
                            let rightEyeYSum = 0, rightEyeCount = 0;
                            let noseBridgeYSum = 0, noseBridgeCount = 0;
                            
                            // Pipi (kiri & kanan) sebagai referensi warna kulit dasar & lebar wajah
                            let cheekYSum = 0, cheekCount = 0;
                            let cheekRSum = 0, cheekGSum = 0, cheekBSum = 0;
                            let cheekCrSum = 0, cheekCbSum = 0;
                            let leftCheekXSum = 0, leftCheekCount = 0;
                            let rightCheekXSum = 0, rightCheekCount = 0;
                            
                            // Mulut & Bibir
                            let lipPixelCount = 0;
                            const colLips = new Uint8Array(96);
                            const colLipYSum = new Float32Array(96);
                            let innerMouthYSum = 0, innerMouthCount = 0;

                            // Dagu
                            let chinSkinPix = 0;

                            // Gradien vertikal mata
                            let eyeVerticalGrad = 0;

                            for (let y = 0; y < 128; y++) {
                                for (let x = 0; x < 96; x++) {
                                    const idx = (y * 96 + x) * 4;
                                    const r = data[idx];
                                    const g = data[idx + 1];
                                    const b = data[idx + 2];

                                    // Luminance (Y) & Chrominance (Cb, Cr)
                                    const Y = 0.299 * r + 0.587 * g + 0.114 * b;
                                    const Cb = 128 - 0.1687 * r - 0.3313 * g + 0.5 * b;
                                    const Cr = 128 + 0.5 * r - 0.4187 * g - 0.0813 * b;

                                    // Titik di dalam elips oval tengah
                                    const dx = (x - 48) / 38;
                                    const dy = (y - 60) / 50;
                                    const inOval = (dx * dx + dy * dy) <= 1.0;

                                    const isSkin = (Y >= 35 && Y <= 245 && Cr >= 126 && Cr <= 178 && Cb >= 72 && Cb <= 135 && (Cr - Cb) >= 2);

                                    if (inOval) {
                                        totalOvalPix++;
                                        if (isSkin) skinPix++;
                                    }

                                    // Zona 1: Dahi (y: 16..30, x: 28..68)
                                    if (y >= 16 && y <= 30 && x >= 28 && x <= 68) {
                                        if (isSkin) foreheadSkinPix++;
                                    }

                                    // Zona 2: Mata Kiri (x: 22..40, y: 38..50), Hidung Tengah (x: 44..52, y: 38..50), Mata Kanan (x: 56..74, y: 38..50)
                                    if (y >= 38 && y <= 50) {
                                        if (x >= 22 && x <= 40) {
                                            leftEyeYSum += Y;
                                            leftEyeCount++;
                                            if (y > 38) {
                                                const prevIdx = ((y - 1) * 96 + x) * 4;
                                                const prevY = 0.299 * data[prevIdx] + 0.587 * data[prevIdx + 1] + 0.114 * data[prevIdx + 2];
                                                eyeVerticalGrad += Math.abs(Y - prevY);
                                            }
                                        } else if (x >= 44 && x <= 52) {
                                            noseBridgeYSum += Y;
                                            noseBridgeCount++;
                                        } else if (x >= 56 && x <= 74) {
                                            rightEyeYSum += Y;
                                            rightEyeCount++;
                                            if (y > 38) {
                                                const prevIdx = ((y - 1) * 96 + x) * 4;
                                                const prevY = 0.299 * data[prevIdx] + 0.587 * data[prevIdx + 1] + 0.114 * data[prevIdx + 2];
                                                eyeVerticalGrad += Math.abs(Y - prevY);
                                            }
                                        }
                                    }

                                    // Zona 3: Pipi (y: 54..72, x: 18..36 & x: 60..78)
                                    if (y >= 54 && y <= 72) {
                                        if (x >= 18 && x <= 36) {
                                            cheekYSum += Y;
                                            cheekRSum += r; cheekGSum += g; cheekBSum += b;
                                            cheekCrSum += Cr; cheekCbSum += Cb;
                                            leftCheekXSum += x; leftCheekCount++;
                                            cheekCount++;
                                        } else if (x >= 60 && x <= 78) {
                                            cheekYSum += Y;
                                            cheekRSum += r; cheekGSum += g; cheekBSum += b;
                                            cheekCrSum += Cr; cheekCbSum += Cb;
                                            rightCheekXSum += x; rightCheekCount++;
                                            cheekCount++;
                                        }
                                    }

                                    // Zona 4: Mulut & Bibir (y: 82..98, x: 26..70)
                                    // PENTING: Deteksi warna bibir relatif terhadap warna kulit pipi pengguna
                                    if (y >= 82 && y <= 98 && x >= 26 && x <= 70) {
                                        const avgCheekR = cheekCount > 0 ? (cheekRSum / cheekCount) : 160;
                                        const avgCheekG = cheekCount > 0 ? (cheekGSum / cheekCount) : 120;
                                        const avgCheekCr = cheekCount > 0 ? (cheekCrSum / cheekCount) : 148;
                                        const avgCheekCb = cheekCount > 0 ? (cheekCbSum / cheekCount) : 108;
                                        const cheekRedRatio = avgCheekG > 0 ? (avgCheekR / avgCheekG) : 1.25;
                                        const cheekCrCbDiff = avgCheekCr - avgCheekCb;

                                        // Bibir manusia jauh lebih merah/pekat dibanding kulit pipinya sendiri
                                        const isLipColor = (
                                            ((r / (g + 1)) >= (cheekRedRatio + 0.11) && (r - g) >= (avgCheekR - avgCheekG + 7) && (Cr - Cb) >= (cheekCrCbDiff + 6) && r >= 50 && Y <= 220) ||
                                            ((Cr - Cb) >= (cheekCrCbDiff + 12) && r > g * 1.25 && r >= 50 && Y <= 220)
                                        );

                                        if (isLipColor) {
                                            lipPixelCount++;
                                            colLips[x]++;
                                            colLipYSum[x] += y;
                                        }

                                        // Area bukaan tengah mulut (gigi / ekspresi: x: 42..54, y: 86..94)
                                        if (y >= 86 && y <= 94 && x >= 42 && x <= 54) {
                                            innerMouthYSum += Y;
                                            innerMouthCount++;
                                        }
                                    }

                                    // Zona 5: Dagu (y: 104..118, x: 32..64)
                                    if (y >= 104 && y <= 118 && x >= 32 && x <= 64) {
                                        if (isSkin) chinSkinPix++;
                                    }
                                }
                            }

                            // ── 2. EKSTRAKSI LEBAR BIBIR KONTINU (KEBAL NOISE PIKSEL TEPI) ──
                            let centerLipCol = 48;
                            let maxCenterLipPix = 0;
                            for (let cx = 40; cx <= 56; cx++) {
                                if (colLips[cx] > maxCenterLipPix) {
                                    maxCenterLipPix = colLips[cx];
                                    centerLipCol = cx;
                                }
                            }

                            let denseLipLeft = centerLipCol;
                            let denseLipRight = centerLipCol;
                            let mouthWidth = 0;
                            let cornerLift = 0;

                            if (maxCenterLipPix >= 2) {
                                let gap = 0;
                                for (let cx = centerLipCol - 1; cx >= 24; cx--) {
                                    if (colLips[cx] >= 1) {
                                        denseLipLeft = cx;
                                        gap = 0;
                                    } else {
                                        gap++;
                                        if (gap >= 2) break;
                                    }
                                }

                                gap = 0;
                                for (let cx = centerLipCol + 1; cx <= 72; cx++) {
                                    if (colLips[cx] >= 1) {
                                        denseLipRight = cx;
                                        gap = 0;
                                    } else {
                                        gap++;
                                        if (gap >= 2) break;
                                    }
                                }

                                mouthWidth = denseLipRight - denseLipLeft + 1;

                                const leftCornerY = colLips[denseLipLeft] > 0 ? (colLipYSum[denseLipLeft] / colLips[denseLipLeft]) : 90;
                                const rightCornerY = colLips[denseLipRight] > 0 ? (colLipYSum[denseLipRight] / colLips[denseLipRight]) : 90;
                                const centerLipY = colLips[centerLipCol] > 0 ? (colLipYSum[centerLipCol] / colLips[centerLipCol]) : 90;
                                const avgCornerY = (leftCornerY + rightCornerY) / 2;
                                cornerLift = centerLipY - avgCornerY;
                            }

                            // ── 3. VALIDASI ANATOMI WAJAH UTUH (ANTI-JIDAT & ANTI-SEBAGIAN WAJAH) ──
                            const skinRatio = totalOvalPix > 0 ? (skinPix / totalOvalPix) : 0;
                            const leftEyeAvgY = leftEyeCount > 0 ? (leftEyeYSum / leftEyeCount) : 0;
                            const rightEyeAvgY = rightEyeCount > 0 ? (rightEyeYSum / rightEyeCount) : 0;
                            const eyeAvgY = (leftEyeAvgY + rightEyeAvgY) / 2;
                            const noseBridgeAvgY = noseBridgeCount > 0 ? (noseBridgeYSum / noseBridgeCount) : 0;
                            const cheekAvgY = cheekCount > 0 ? (cheekYSum / cheekCount) : 0;
                            const innerMouthAvgY = innerMouthCount > 0 ? (innerMouthYSum / innerMouthCount) : 0;

                            const leftCheekCenterX = leftCheekCount > 0 ? (leftCheekXSum / leftCheekCount) : 27;
                            const rightCheekCenterX = rightCheekCount > 0 ? (rightCheekXSum / rightCheekCount) : 69;
                            const faceSpan = Math.max(30, rightCheekCenterX - leftCheekCenterX);

                            // Rasio ternormalisasi geometris: kebal jarak dan kebal pencahayaan
                            const smileRatio = faceSpan > 20 ? (mouthWidth / faceSpan) : 0;
                            const normalizedInnerY = cheekAvgY > 15 ? (innerMouthAvgY / cheekAvgY) : 0;

                            // Syarat kelengkapan fitur wajah utuh:
                            const hasDualEyes = (leftEyeCount > 0 && rightEyeCount > 0 && 
                                                 noseBridgeAvgY > leftEyeAvgY + 1.5 && 
                                                 noseBridgeAvgY > rightEyeAvgY + 1.5 && 
                                                 cheekAvgY > eyeAvgY + 2.0);
                            const hasForehead = foreheadSkinPix >= 35;
                            const hasRealMouth = lipPixelCount >= 14 && mouthWidth >= 16;
                            const hasChin = chinSkinPix >= 20;

                            const isFullFace = (
                                skinRatio >= 0.22 &&
                                hasForehead &&
                                hasDualEyes &&
                                hasRealMouth &&
                                hasChin &&
                                Math.abs(leftEyeAvgY - rightEyeAvgY) <= 24
                            );

                            if (!isFullFace) {
                                stableFrames = Math.max(0, stableFrames - 2);
                                if (stableFrames === 0) {
                                    if (this.livenessState !== 'waiting_face') {
                                        this.livenessState = 'waiting_face';
                                        this.updateLivenessPrompt();
                                    }
                                    currentProgressVal = Math.max(0, currentProgressVal - 5);
                                    this.actionProgress = Math.round(currentProgressVal);
                                    baselineSmileRatio = null;
                                    baselineCornerLift = null;
                                    baselineInnerRatio = null;
                                    baselineEyeContrast = null;
                                    smileHoldCount = 0;
                                    blinkPhase = 'idle';
                                }
                                this.aiLoopTimer = requestAnimationFrame(processFrame);
                                return;
                            }

                            // Posisi wajah utuh terdeteksi!
                            stableFrames++;

                            // ── FASE WARM-UP (~0.8 detik tahan posisi stabil dulu) ──
                            if (stableFrames < WARMUP_FRAMES) {
                                this.livenessState = 'face_aligned';
                                this.livenessPrompt = 'Tahan posisi wajah...';
                                this.actionProgress = 0;
                                currentProgressVal = 0;

                                // Rekam baseline netral saat wajah diam
                                if (baselineSmileRatio === null) {
                                    baselineSmileRatio = smileRatio;
                                    baselineCornerLift = cornerLift;
                                    baselineInnerRatio = normalizedInnerY;
                                    baselineEyeContrast = eyeVerticalGrad;
                                } else {
                                    baselineSmileRatio = baselineSmileRatio * 0.85 + smileRatio * 0.15;
                                    baselineCornerLift = baselineCornerLift * 0.85 + cornerLift * 0.15;
                                    baselineInnerRatio = baselineInnerRatio * 0.85 + normalizedInnerY * 0.15;
                                    baselineEyeContrast = baselineEyeContrast * 0.85 + eyeVerticalGrad * 0.15;
                                }

                                prevCheekY = cheekAvgY;
                                this.aiLoopTimer = requestAnimationFrame(processFrame);
                                return;
                            }

                            // Wajah sudah stabil! Tampilkan instruksi aksi
                            const challengeType = this.currentChallenge ? this.currentChallenge.type : 'blink';
                            const ch = this.currentChallenge || this.challenges[0];
                            if (this.livenessPrompt === 'Tahan posisi wajah...') {
                                this.livenessPrompt = ch.actionText;
                            }

                            // ── 3. DETEKSI GERAKAN DENGAN METER REAL-TIME ──

                            // A. TANTANGAN SENYUM (😊)
                            if (challengeType === 'smile') {
                                const deltaRatio = baselineSmileRatio > 0.1 ? ((smileRatio - baselineSmileRatio) / baselineSmileRatio) : 0;
                                const deltaCorner = cornerLift - baselineCornerLift;
                                const deltaInner = Math.max(0, normalizedInnerY - baselineInnerRatio);

                                // Jika wajah sedang santai/netral, adaptasi baseline perlahan
                                if (deltaRatio < 0.07 && deltaCorner < 1.0) {
                                    baselineSmileRatio = baselineSmileRatio * 0.96 + smileRatio * 0.04;
                                    baselineCornerLift = baselineCornerLift * 0.96 + cornerLift * 0.04;
                                    baselineInnerRatio = baselineInnerRatio * 0.96 + normalizedInnerY * 0.04;
                                }

                                // Skor senyum: wajib ada pelebaran bibir nyata (>= 6%) atau angkatan sudut bibir
                                let smileScore = 0;
                                if (deltaRatio >= 0.06 || deltaCorner >= 1.2 || deltaInner >= 0.12) {
                                    smileScore = (Math.max(0, deltaRatio - 0.05) * 350.0) + 
                                                 (Math.max(0, deltaCorner - 0.5) * 18.0) + 
                                                 (Math.max(0, deltaInner - 0.08) * 40.0);
                                }

                                let targetProgress = 0;
                                // Ambang batas: wajah diam/netral = 0%
                                if (smileScore >= 10.0) {
                                    targetProgress = Math.min(100, Math.round(((smileScore - 10.0) / 40.0) * 100));
                                }

                                // Lerp responsif & halus
                                currentProgressVal = (currentProgressVal * 0.78) + (targetProgress * 0.22);
                                this.actionProgress = Math.round(currentProgressVal);

                                if (this.actionProgress >= 88) {
                                    smileHoldCount++;
                                    // Tahan senyum minimal 10 frame (~330ms)
                                    if (smileHoldCount >= 10) {
                                        this.actionProgress = 100;
                                        isTriggered = true;
                                        this.livenessState = 'action_success';
                                        this.updateLivenessPrompt();
                                        audioFx.playBlink();
                                        this.triggerShutterFlash();
                                        setTimeout(() => { this.captureAndSend(); }, 200);
                                        return;
                                    }
                                } else {
                                    smileHoldCount = 0;
                                }
                            }

                            // B. TANTANGAN KEDIP (😉)
                            else if (challengeType === 'blink') {
                                const cheekDelta = prevCheekY !== null ? Math.abs(cheekAvgY - prevCheekY) : 0;
                                prevCheekY = cheekAvgY;

                                const currentG = eyeVerticalGrad;
                                if (currentG >= (baselineEyeContrast * 0.85) && cheekDelta <= 3.0) {
                                    baselineEyeContrast = baselineEyeContrast * 0.96 + currentG * 0.04;
                                }

                                const contrastDrop = baselineEyeContrast - currentG;
                                const dropPercent = baselineEyeContrast > 100 ? (contrastDrop / baselineEyeContrast) : 0;

                                // Syarat mata terpejam:
                                // 1. Penurunan gradien vertikal mata >= 30%
                                // 2. Pipi diam / stabil (cheekDelta <= 3.0) agar bukan karena goyangan kamera
                                const isEyesClosed = (dropPercent >= 0.30 && cheekDelta <= 3.0);
                                const now = Date.now();

                                if (blinkPhase === 'idle') {
                                    if (isEyesClosed) {
                                        blinkPhase = 'closed';
                                        blinkClosedStart = now;
                                        currentProgressVal = 50;
                                        this.actionProgress = 50;
                                    } else {
                                        currentProgressVal = Math.max(0, currentProgressVal - 4);
                                        this.actionProgress = Math.round(currentProgressVal);
                                    }
                                } else if (blinkPhase === 'closed') {
                                    const closedDuration = now - blinkClosedStart;

                                    // Mata terbuka kembali (dropPercent < 0.15) dalam rentang kedipan manusia normal (140ms - 650ms)
                                    if (!isEyesClosed && closedDuration >= 140 && closedDuration <= 650) {
                                        this.actionProgress = 100;
                                        isTriggered = true;
                                        this.livenessState = 'action_success';
                                        this.updateLivenessPrompt();
                                        audioFx.playBlink();
                                        this.triggerShutterFlash();
                                        setTimeout(() => { this.captureAndSend(); }, 200);
                                        return;
                                    } else if (closedDuration > 700) {
                                        // Terpejam terlalu lama -> reset
                                        blinkPhase = 'idle';
                                        currentProgressVal = 0;
                                        this.actionProgress = 0;
                                    }
                                }
                            }

                        } catch (err) {
                            console.warn('Liveness frame error:', err);
                        }

                        this.aiLoopTimer = requestAnimationFrame(processFrame);
                    };

                    this.aiLoopTimer = requestAnimationFrame(processFrame);
                },

                captureAndSend() {
                    if (this.aiLoopTimer) {
                        cancelAnimationFrame(this.aiLoopTimer);
                        clearTimeout(this.aiLoopTimer);
                        this.aiLoopTimer = null;
                    }
                    if (this.scanState === 'processing' || this.scanState === 'success') return;

                    const video = document.getElementById('video-scan');
                    const canvas = document.getElementById('canvas-scan');
                    if (!video || !canvas) return;

                    canvas.width = 640;
                    canvas.height = 480;
                    const ctx = canvas.getContext('2d');
                    ctx.save();
                    ctx.scale(-1, 1);
                    ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                    ctx.restore();

                    const imageB64 = canvas.toDataURL('image/jpeg', 0.85);

                    this.scanState = 'processing';
                    this.scanMessage = 'Memverifikasi wajah di server AI...';

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
                    const video = document.getElementById('video-scan');
                    if (video && this.videoStream) {
                        this.armScanner();
                    } else {
                        this.openFaceScanner(this.currentSesiId);
                    }
                },

                closeFaceScanner() {
                    this.isScanning = false;
                    this.stopCamera();
                    if (this.scanSuccess) window.location.reload();
                },

                stopCamera() {
                    if (this.aiLoopTimer) {
                        cancelAnimationFrame(this.aiLoopTimer);
                        clearTimeout(this.aiLoopTimer);
                        this.aiLoopTimer = null;
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

                // ── Sliding Pill Logic ──
                pillLeft: 0,
                pillWidth: 0,
                pillTop: 0,
                pillHeight: 0,

                initPill() {
                    // Update initial position after elements are rendered
                    this.$nextTick(() => {
                        this.updatePillPosition();
                        // re-update slightly later for safety on mobile rendering
                        setTimeout(() => this.updatePillPosition(), 300);
                    });
                    
                    window.addEventListener('resize', () => this.updatePillPosition());
                },

                updatePillPosition() {
                    const activeBtn = this.$refs[this.activeTab + '_btn'];
                    if (activeBtn) {
                        this.pillLeft = activeBtn.offsetLeft;
                        this.pillTop = activeBtn.offsetTop;
                        this.pillWidth = activeBtn.offsetWidth;
                        this.pillHeight = activeBtn.offsetHeight;
                    }
                },

                // ── App Shell Tab Switching & Classmate Filtering ──
                switchTab(tab) {
                    this.activeTab = tab;
                    window.location.hash = tab;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    this.$nextTick(() => {
                        this.updatePillPosition();
                    });
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
    <script>
        document.addEventListener('alpine:initialized', () => {
            // Animate Profile Card and other cards using GSAP
            gsap.fromTo(".gsap-stagger-item", 
                { y: 30, opacity: 0 }, 
                { y: 0, opacity: 1, duration: 0.8, ease: "power3.out", stagger: 0.15 }
            );

            // Animate Bottom Nav
            gsap.fromTo("nav.lg\\:hidden", 
                { y: 100, opacity: 0 }, 
                { y: 0, opacity: 1, duration: 1, delay: 0.3, ease: "elastic.out(1, 0.5)" }
            );
            
            // Animate Tab Buttons
            gsap.fromTo("nav.lg\\:hidden button",
                { scale: 0, opacity: 0 },
                { scale: 1, opacity: 1, duration: 0.5, stagger: 0.1, delay: 0.6, ease: "back.out(1.7)" }
            );
        });
    </script>
</body>

</html>