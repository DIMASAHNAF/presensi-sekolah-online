<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Presensi SMKN 1 Beringin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistem Presensi Sekolah SMKN 1 Beringin — Panel Manajemen">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#18181b">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Presensi SMKN 1">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.webp') }}">

    {{-- Fonts: Inter & JetBrains Mono (Strict PRD Compliance) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    {{-- Font Awesome 6 Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- AOS --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    
    {{-- Tailwind Configuration (Linear / Vercel Aesthetic) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        };
    </script>
    {{-- Dark Theme Styles --}}
    <link rel="stylesheet" href="{{ asset('css/theme-dark.css') }}">
    {{-- Anti-flicker Theme Init & Global Toggle Function --}}
    <script>
        (function() {
            const saved = localStorage.getItem('theme');
            if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        window.toggleAppTheme = function(forceDark) {
            let isDark;
            if (typeof forceDark === 'boolean') {
                isDark = forceDark;
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } else {
                isDark = document.documentElement.classList.toggle('dark');
            }
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: isDark ? 'dark' : 'light' } }));
            return isDark;
        };
    </script>
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; font-variant-numeric: tabular-nums; }
        .tabular-nums { font-variant-numeric: tabular-nums; }

        body {
            background-color: #fafafa;
            color: #18181b;
        }
        html.dark body {
            background-color: #09090b;
            color: #fafafa;
        }

        /* ─── Sidebar (Border-First) ─── */
        .sidebar-bg {
            background-color: #ffffff;
            border-right: 1px solid #e4e4e7;
        }
        html.dark .sidebar-bg {
            background-color: #18181b;
            border-right: 1px solid #27272a;
        }

        .sidebar-category {
            font-size: 0.65rem; font-weight: 700; letter-spacing: 0.08em;
            text-transform: uppercase; color: #71717a;
            padding: 0.65rem 0.75rem 0.25rem;
        }
        html.dark .sidebar-category { color: #a1a1aa; }

        .nav-link {
            display: flex; align-items: center; gap: 0.625rem;
            padding: 0.5rem 0.75rem; margin: 0.1rem 0;
            border-radius: 0.5rem;
            font-size: 0.875rem; font-weight: 500; color: #52525b;
            transition: all 0.15s ease;
            border: 1px solid transparent;
        }
        html.dark .nav-link { color: #a1a1aa; }
        
        .nav-link:hover {
            background-color: #f4f4f5;
            color: #18181b;
        }
        html.dark .nav-link:hover {
            background-color: #27272a;
            color: #fafafa;
        }

        .nav-link.active {
            background-color: #18181b;
            color: #fafafa;
            font-weight: 600;
        }
        html.dark .nav-link.active {
            background-color: #f4f4f5;
            color: #18181b;
        }
        .nav-link .icon {
            width: 1.1rem; text-align: center; color: #71717a; transition: color 0.15s;
        }
        .nav-link.active .icon { color: inherit; }

        /* ─── Cards (Border-First, Zero Gradients, Zero Heavy Shadows) ─── */
        .stat-card {
            background-color: #ffffff;
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
            padding: 1.25rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: border-color 0.15s ease;
        }
        html.dark .stat-card {
            background-color: #18181b;
            border-color: #27272a;
        }
        .stat-card:hover {
            border-color: #a1a1aa;
        }
        html.dark .stat-card:hover {
            border-color: #3f3f46;
        }

        /* ─── High-Contrast Action Buttons (PRD 1.2) ─── */
        .btn-primary {
            background-color: #18181b;
            color: #fafafa;
            border-radius: 0.5rem;
            padding: 0.5rem 0.9rem;
            font-size: 0.8125rem; font-weight: 500;
            display: inline-flex; align-items: center; gap: 0.45rem;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid transparent;
        }
        .btn-primary:hover {
            background-color: #27272a;
        }
        html.dark .btn-primary {
            background-color: #f4f4f5;
            color: #18181b;
        }
        html.dark .btn-primary:hover {
            background-color: #e4e4e7;
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #27272a;
            border-radius: 0.5rem;
            padding: 0.5rem 0.9rem;
            font-size: 0.8125rem; font-weight: 500;
            display: inline-flex; align-items: center; gap: 0.45rem;
            border: 1px solid #e4e4e7;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.15s ease;
        }
        html.dark .btn-secondary {
            background-color: #18181b;
            color: #f4f4f5;
            border-color: #27272a;
        }
        .btn-secondary:hover {
            background-color: #f4f4f5;
        }
        html.dark .btn-secondary:hover {
            background-color: #27272a;
        }

        .btn-danger {
            background-color: #e11d48;
            color: #ffffff;
            border-radius: 0.5rem;
            padding: 0.5rem 0.9rem;
            font-size: 0.8125rem; font-weight: 500;
            display: inline-flex; align-items: center; gap: 0.45rem;
            transition: all 0.15s ease;
        }
        .btn-danger:hover {
            background-color: #be123c;
        }

        /* ─── Semantic Status Badges (Strict PRD 1.2) ─── */
        .badge-hadir {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        html.dark .badge-hadir {
            color: #34d399;
        }

        .badge-terlambat {
            background-color: rgba(245, 158, 11, 0.1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        html.dark .badge-terlambat {
            color: #fbbf24;
        }

        .badge-izin, .badge-sakit, .badge-dispensasi {
            background-color: rgba(14, 165, 233, 0.1);
            color: #0284c7;
            border: 1px solid rgba(14, 165, 233, 0.2);
        }
        html.dark .badge-izin, html.dark .badge-sakit, html.dark .badge-dispensasi {
            color: #38bdf8;
        }

        .badge-alpa, .badge-alfa {
            background-color: rgba(244, 63, 94, 0.1);
            color: #e11d48;
            border: 1px solid rgba(244, 63, 94, 0.2);
        }
        html.dark .badge-alpa, html.dark .badge-alfa {
            color: #fb7185;
        }

        .badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.15rem 0.5rem; border-radius: 0.375rem;
            font-size: 0.75rem; font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
            font-variant-numeric: tabular-nums;
        }

        /* ─── Main Header ─── */
        .main-header {
            background-color: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid #e4e4e7;
        }
        html.dark .main-header {
            background-color: rgba(24, 24, 27, 0.92);
            border-bottom: 1px solid #27272a;
        }

        /* ─── Content Card ─── */
        .content-card {
            background-color: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e4e4e7;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        html.dark .content-card {
            background-color: #18181b;
            border-color: #27272a;
        }

        .table-row:hover td { background-color: #f4f4f5; }
        html.dark .table-row:hover td { background-color: #27272a; }

        [x-cloak] { display: none !important; }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d4d4d8; border-radius: 4px; }
        html.dark ::-webkit-scrollbar-thumb { background: #3f3f46; }
    </style>

    @stack('styles')
</head>

<body class="text-zinc-900 dark:text-zinc-100 antialiased font-sans" 
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }" 
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

<x-page-loader />
<x-toast />
<x-alert-dialog />

{{-- ===== SIDEBAR ===== --}}
<aside class="sidebar-bg fixed top-0 left-0 h-screen max-h-screen z-40 flex flex-col
              transition-all duration-200"
       :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-64 -translate-x-full lg:w-0 lg:translate-x-0 overflow-hidden'"
       x-cloak>

    {{-- School Header & Info (shrink-0) --}}
    <div class="shrink-0">
        <div class="flex items-center gap-3 px-4 py-3.5 border-b border-zinc-200 dark:border-zinc-800">
            <div class="w-8 h-8 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center shrink-0 border border-zinc-200 dark:border-zinc-700 p-1">
                <img src="{{ asset('images/logo.webp') }}" alt="Logo SMKN 1" class="w-full h-full object-contain">
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-xs text-zinc-900 dark:text-zinc-100 tracking-tight truncate leading-tight">SMKN 1 BERINGIN</p>
                <p class="text-zinc-500 dark:text-zinc-400 text-[10px] font-sans font-medium tracking-wider uppercase mt-0.5">Sistem Presensi</p>
            </div>
        </div>

        {{-- Academic Tag --}}
        <div class="px-4 py-1.5 flex items-center justify-between text-[11px] bg-zinc-50 dark:bg-zinc-800/40 border-b border-zinc-200 dark:border-zinc-800 font-sans font-medium tabular-nums">
            <span class="text-zinc-600 dark:text-zinc-400">T.A. 2026/2027</span>
            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200">GANJIL</span>
        </div>
    </div>

    {{-- Navigation (flex-1 overflow-y-auto pt-2 pb-8) --}}
    <nav class="flex-1 min-h-0 overflow-y-auto pt-2 pb-8 px-2.5 space-y-0.5">

        <div class="sidebar-category">Menu Utama</div>

        <a href="{{ route('dashboard') }}"
           class="nav-link py-2 px-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-gauge-high icon"></i>
            <span>Ringkasan</span>
        </a>

        <a href="{{ route('dashboard.presensi') }}"
           class="nav-link py-2 px-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard.presensi*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-user icon"></i>
            <span>@if(auth()->user()->isAdmin()) Semua Presensi @else Kelola Presensi @endif</span>
        </a>

        <a href="{{ route('dashboard.izin') }}"
           class="nav-link py-2 px-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard.izin*') ? 'active' : '' }}">
            <i class="fas fa-envelope-open-text icon"></i>
            <span>Buku Izin &amp; Sakit</span>
        </a>

        <a href="{{ route('kiosk.gerbang') }}" target="_blank"
           class="nav-link py-2 px-3 text-sm font-medium rounded-lg">
            <i class="fas fa-tablet-screen-button icon"></i>
            <span class="flex items-center justify-between flex-1">
                <span>Kiosk Gerbang</span>
                <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-sans font-semibold tracking-wider">LIVE</span>
            </span>
        </a>

        <a href="{{ route('dashboard.nilai') }}"
           class="nav-link py-2 px-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard.nilai*') ? 'active' : '' }}">
            <i class="fas fa-graduation-cap icon"></i>
            <span>Rekap Nilai Siswa</span>
        </a>

        @if(auth()->user()->isAdmin())
            <div class="sidebar-category mt-2">Data Akademik</div>

            <a href="{{ route('dashboard.siswa') }}"
               class="nav-link py-2 px-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard.siswa') ? 'active' : '' }}">
                <i class="fas fa-user-graduate icon"></i>
                <span>Data Siswa</span>
            </a>

            <a href="{{ route('dashboard.guru') }}"
               class="nav-link py-2 px-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard.guru') ? 'active' : '' }}">
                <i class="fas fa-chalkboard-user icon"></i>
                <span>Data Guru</span>
            </a>

            <a href="{{ route('dashboard.kelas') }}"
               class="nav-link py-2 px-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard.kelas') ? 'active' : '' }}">
                <i class="fas fa-door-open icon"></i>
                <span>Data Rombel &amp; Kelas</span>
            </a>

            <div class="sidebar-category mt-2">Sistem &amp; Pengaturan</div>

            <a href="{{ route('dashboard.log') }}"
               class="nav-link py-2 px-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard.log') ? 'active' : '' }}">
                <i class="fas fa-clock-rotate-left icon"></i>
                <span>Audit Log Presensi</span>
            </a>

            <a href="{{ route('dashboard.lokasi') }}"
               class="nav-link py-2 px-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard.lokasi') ? 'active' : '' }}">
                <i class="fas fa-map-location-dot icon"></i>
                <span>Radius Geofencing</span>
            </a>

            <a href="{{ route('dashboard.storage') }}"
               class="nav-link py-2 px-3 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard.storage*') ? 'active' : '' }}">
                <i class="fas fa-hard-drive icon"></i>
                <span>Manajemen Storage</span>
            </a>
        @endif

        <div class="sidebar-category mt-2">Bantuan</div>

        <a href="{{ route('panduan') }}" target="_blank"
           class="nav-link py-2 px-3 text-sm font-medium rounded-lg">
            <i class="fas fa-book-open icon"></i>
            <span>Buku Panduan</span>
        </a>

        <button type="button" @click="window.openPwaInstallPrompt ? window.openPwaInstallPrompt() : $dispatch('open-pwa-install')"
           class="w-full text-left nav-link py-2 px-3 text-sm font-medium rounded-lg cursor-pointer">
            <i class="fas fa-download icon"></i>
            <span>Pasang Aplikasi</span>
        </button>
    </nav>

    {{-- Footer Profil Administrator (shrink-0 border-t) --}}
    <div class="p-3 shrink-0 border-t border-zinc-200 dark:border-zinc-800">
        <div class="rounded-lg p-2.5 flex items-center gap-2.5 bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-700/60">
            <div class="w-8 h-8 bg-zinc-900 text-zinc-50 dark:bg-zinc-100 dark:text-zinc-900 rounded-md flex items-center justify-center text-xs font-semibold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 truncate leading-tight">{{ auth()->user()->name }}</p>
                <p class="text-zinc-500 dark:text-zinc-400 text-[10px] font-sans font-medium mt-0.5 truncate flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <span>{{ auth()->user()->isAdmin() ? 'Akses Penuh • Aktif' : (auth()->user()->isWaliKelas() ? 'Wali Kelas • Aktif' : 'Tenaga Pendidik • Aktif') }}</span>
                </p>
            </div>
        </div>
    </div>
</aside>

{{-- ===== MAIN ===== --}}
<div class="flex flex-col min-h-screen transition-all duration-200"
     :class="sidebarOpen ? 'lg:ml-64 ml-0' : 'ml-0'">

    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen && window.innerWidth < 1024" 
         @click="sidebarOpen = false" 
         x-cloak 
         class="fixed inset-0 bg-black/60 z-30 lg:hidden backdrop-blur-xs"></div>

    {{-- Header --}}
    <header class="main-header px-4 sm:px-6 py-2.5 flex items-center justify-between sticky top-0 z-30 gap-2">
        <div class="flex items-center gap-3 min-w-0 shrink-0">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition border border-zinc-200 dark:border-zinc-700 shrink-0"
                    aria-label="Toggle Sidebar">
                <i class="fas fa-bars text-xs"></i>
            </button>
            <div class="min-w-0">
                <div class="hidden sm:flex items-center gap-1.5 text-[11px] text-zinc-500 dark:text-zinc-400 font-sans font-medium uppercase tracking-wider">
                    <span>SMKN 1 Beringin</span>
                    <span>/</span>
                    <span class="text-zinc-900 dark:text-zinc-200">@yield('page-title', 'Dashboard')</span>
                </div>
                <h1 class="text-sm sm:text-base font-semibold text-zinc-900 dark:text-zinc-100 leading-tight truncate tracking-tight">@yield('page-title', 'Dashboard')</h1>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            {{-- Realtime Clock (Strict tabular-nums per PRD 1.2) --}}
            <div class="hidden md:flex items-center gap-2 bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700/80 px-2.5 py-1.5 rounded-lg text-xs font-sans tabular-nums">
                <span class="text-zinc-600 dark:text-zinc-400 font-medium">{{ now()->locale('id')->isoFormat('D MMM Y') }}</span>
                <span class="text-zinc-300 dark:text-zinc-600">&bull;</span>
                <span class="font-semibold text-zinc-900 dark:text-zinc-100 font-sans tabular-nums text-xs" id="realtimeClock">{{ now()->setTimezone('Asia/Jakarta')->format('H:i:s') }} WIB</span>
            </div>

            {{-- Minimalist Theme Toggle Button --}}
            <button type="button" 
                    onclick="toggleAppTheme()"
                    class="h-8 w-8 rounded-lg border border-zinc-200 dark:border-zinc-800 flex items-center justify-center text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                    title="Ganti Tema (Terang / Gelap)"
                    aria-label="Toggle Tema">
                {{-- Sun icon (visible in dark mode) --}}
                <svg class="w-4 h-4 hidden dark:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2"></path>
                    <path d="M12 20v2"></path>
                    <path d="m4.93 4.93 1.41 1.41"></path>
                    <path d="m17.66 17.66 1.41 1.41"></path>
                    <path d="M2 12h2"></path>
                    <path d="M20 12h2"></path>
                    <path d="m6.34 17.66-1.41 1.41"></path>
                    <path d="m19.07 4.93-1.41 1.41"></path>
                </svg>
                {{-- Moon icon (visible in light mode) --}}
                <svg class="w-4 h-4 block dark:hidden" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
                </svg>
            </button>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-1.5 text-xs font-medium text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-zinc-200 dark:border-zinc-800 px-2.5 py-1.5 rounded-lg transition"
                        title="Keluar">
                    <i class="fas fa-arrow-right-from-bracket text-xs"></i>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </header>

    {{-- Flash Messages (Border-First, No AI-Slop) --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mx-6 mt-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-300 rounded-lg px-4 py-2.5 flex items-center justify-between text-xs font-medium">
            <div class="flex items-center gap-2">
                <i class="fas fa-check text-emerald-600 dark:text-emerald-400"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-600 dark:text-emerald-400 hover:opacity-75">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             class="mx-6 mt-4 bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300 rounded-lg px-4 py-2.5 flex items-center justify-between text-xs font-medium">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-exclamation text-rose-600 dark:text-rose-400"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-rose-600 dark:text-rose-400 hover:opacity-75">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mx-6 mt-4 bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300 rounded-lg px-4 py-2.5 text-xs">
            <p class="font-semibold mb-1">Perhatian:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Content --}}
    <main class="flex-1 p-5 sm:p-6 flex flex-col">
        <div class="flex-1">
            @yield('content')
        </div>
        
        {{-- Footer --}}
        <footer class="mt-10 pt-4 border-t border-zinc-200 dark:border-zinc-800 text-xs text-zinc-500 dark:text-zinc-400 flex flex-col sm:flex-row items-center justify-between gap-2 font-sans tabular-nums">
            <span>Sistem Presensi Biometrik &amp; Geofencing &bull; SMKN 1 Beringin</span>
            <span>Versi 2.0 &bull; 2026</span>
        </footer>
    </main>
</div>

{{-- AOS --}}
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 200, offset: 10 });
    
    // Realtime Clock (Strict WIB tabular-nums, HH:mm:ss WIB format)
    function updateClock() {
        const clockElem = document.getElementById('realtimeClock');
        if (clockElem) {
            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            const mm = String(now.getMinutes()).padStart(2, '0');
            const ss = String(now.getSeconds()).padStart(2, '0');
            clockElem.textContent = `${hh}:${mm}:${ss} WIB`;
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        });
    }
</script>
@stack('scripts')
{{-- Universal PWA Modal --}}
<x-pwa-modal />
</body>
</html>
