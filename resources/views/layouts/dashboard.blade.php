<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Presensi SMKN 1 Beringin</title>
    <meta name="description" content="Sistem Presensi Sekolah SMKN 1 Beringin — Panel Manajemen">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1d4ed8">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Presensi SMKN 1">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.webp') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    
    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- AOS --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    {{-- Dark Theme Styles --}}
    <link rel="stylesheet" href="{{ asset('css/theme-dark.css') }}">
    {{-- Anti-flicker Theme Init --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* ─── Base & Background (Aurora/Mesh Gradient) ─── */
        body {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, hsla(210, 100%, 95%, 1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, hsla(160, 100%, 95%, 1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(280, 100%, 95%, 1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(320, 100%, 95%, 1) 0px, transparent 50%);
            background-attachment: fixed;
            color: #1e293b;
        }
        html.dark body {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, hsla(210, 100%, 15%, 1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, hsla(160, 100%, 12%, 1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(280, 100%, 15%, 1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(320, 100%, 12%, 1) 0px, transparent 50%);
            color: #f8fafc;
        }

        /* ─── Glassmorphism Utilities ─── */
        .glass-panel {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.05);
        }
        html.dark .glass-panel {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.3);
        }

        /* ─── Sidebar (Glass Bento Style) ─── */
        .sidebar-bg {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(24px);
            border-right: 1px solid rgba(255,255,255,0.8);
            box-shadow: 4px 0 24px rgba(0,0,0,0.02);
        }
        html.dark .sidebar-bg {
            background: rgba(15, 23, 42, 0.75);
            border-right: 1px solid rgba(255,255,255,0.05);
            box-shadow: 4px 0 24px rgba(0,0,0,0.2);
        }

        .sidebar-logo-area {
            background: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        html.dark .sidebar-logo-area { border-bottom: 1px solid rgba(255,255,255,0.05); }

        .sidebar-category {
            font-size: 0.65rem; font-weight: 800; letter-spacing: 0.1em;
            text-transform: uppercase; color: #64748b;
            padding: 1.2rem 1rem 0.4rem;
        }
        html.dark .sidebar-category { color: #94a3b8; }

        .nav-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.75rem 1rem; margin: 0.2rem 0.6rem;
            border-radius: 0.75rem;
            font-size: 0.85rem; font-weight: 600; color: #475569;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid transparent;
        }
        html.dark .nav-link { color: #cbd5e1; }
        
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.9);
            color: #0f172a;
            transform: translateX(4px);
            border-color: rgba(255,255,255,0.5);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        html.dark .nav-link:hover {
            background: rgba(30, 41, 59, 0.8);
            color: #ffffff;
            border-color: rgba(255,255,255,0.1);
        }

        .nav-link.active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .nav-link .icon {
            width: 1.2rem; text-align: center; color: #94a3b8; transition: color 0.2s;
        }
        .nav-link.active .icon { color: #ffffff; }
        .nav-link:hover:not(.active) .icon { color: #3b82f6; }

        /* ─── Stat Cards (Bento Grid Style) ─── */
        .stat-card {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(16px);
            border-radius: 1.5rem;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.04);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        html.dark .stat-card {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }
        .stat-card:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
        }
        html.dark .stat-card:hover {
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }
        
        /* Bento inner glow */
        .stat-card::after {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(circle at top right, var(--card-accent, rgba(255,255,255,0.2)) 0%, transparent 60%);
            opacity: 0.15; pointer-events: none;
        }

        .stat-card.accent-blue   { --card-accent: #3b82f6; }
        .stat-card.accent-indigo { --card-accent: #6366f1; }
        .stat-card.accent-slate  { --card-accent: #64748b; }
        .stat-card.accent-green  { --card-accent: #22c55e; }
        .stat-card.accent-amber  { --card-accent: #f59e0b; }
        .stat-card.accent-red    { --card-accent: #ef4444; }

        /* ─── Buttons ─── */
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #ffffff;
            border-radius: 0.75rem;
            padding: 0.6rem 1.25rem;
            font-size: 0.85rem; font-weight: 700;
            display: inline-flex; align-items: center; gap: 0.5rem;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.4);
        }
        .btn-primary:active { transform: scale(0.95); }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            color: #334155;
            border-radius: 0.75rem;
            padding: 0.6rem 1.25rem;
            font-size: 0.85rem; font-weight: 700;
            display: inline-flex; align-items: center; gap: 0.5rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.2s ease;
        }
        html.dark .btn-secondary {
            background: rgba(30, 41, 59, 0.6);
            color: #f1f5f9; border-color: rgba(255, 255, 255, 0.1);
        }
        .btn-secondary:hover {
            background: #ffffff; transform: translateY(-1px);
        }
        html.dark .btn-secondary:hover { background: rgba(51, 65, 85, 0.8); }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0.75rem;
            padding: 0.6rem 1.25rem; font-size: 0.85rem; font-weight: 700;
            display: inline-flex; align-items: center; gap: 0.5rem;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
        }

        /* ─── Status Badges ─── */
        .badge-hadir  { background: rgba(34, 197, 94, 0.15); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.3); }
        .badge-izin   { background: rgba(234, 179, 8, 0.15); color: #ca8a04; border: 1px solid rgba(234, 179, 8, 0.3); }
        .badge-sakit  { background: rgba(249, 115, 22, 0.15); color: #ea580c; border: 1px solid rgba(249, 115, 22, 0.3); }
        .badge-alpa   { background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.3); }
        html.dark .badge-hadir { color: #4ade80; }
        html.dark .badge-izin  { color: #facc15; }
        html.dark .badge-sakit { color: #fb923c; }
        html.dark .badge-alpa  { color: #f87171; }
        
        .badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.25rem 0.75rem; border-radius: 9999px;
            font-size: 0.75rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase;
            backdrop-filter: blur(4px);
        }

        /* ─── Header ─── */
        .main-header {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255,255,255,0.5);
        }
        html.dark .main-header {
            background: rgba(15, 23, 42, 0.5);
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        /* ─── Card wrapper (Data Tables, dll) ─── */
        .content-card {
            background: rgba(255, 255, 255, 0.65);
            backdrop-filter: blur(16px);
            border-radius: 1.5rem;
            border: 1px solid rgba(255,255,255,0.8);
            box-shadow: 0 4px 24px -4px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        html.dark .content-card {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 4px 24px -4px rgba(0,0,0,0.3);
        }
        
        .table-row:hover td { background: rgba(241, 245, 249, 0.5); }
        html.dark .table-row:hover td { background: rgba(30, 41, 59, 0.5); }

        /* ─── Misc ─── */
        [x-cloak] { display: none !important; }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.3); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(148, 163, 184, 0.6); }

        /* Realtime pulse dot */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            50% { opacity: 0.8; transform: scale(0.9); box-shadow: 0 0 0 4px rgba(34, 197, 94, 0); }
        }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
    </style>

    @stack('styles')
</head>

<body class="text-slate-800 antialiased" 
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }" 
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

<x-page-loader />
<x-toast />
<x-alert-dialog />

{{-- ===== SIDEBAR ===== --}}
<aside class="sidebar-bg fixed top-0 left-0 h-full z-40 text-white flex flex-col
              transition-all duration-300 shadow-2xl"
       :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-64 -translate-x-full lg:w-0 lg:translate-x-0 overflow-hidden'"
       x-cloak>

    {{-- School Header (Glass Bento Top) --}}
    <div class="sidebar-logo-area flex items-center gap-3 px-5 py-5 shrink-0">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shrink-0 border border-white/20 shadow-[0_4px_12px_rgba(59,130,246,0.3)] p-1">
            <img src="{{ asset('images/logo.webp') }}" alt="Logo SMKN 1" class="w-full h-full object-contain filter drop-shadow-md">
        </div>
        <div class="min-w-0">
            <p class="font-heading font-extrabold text-[15px] text-slate-800 dark:text-white tracking-tight truncate leading-tight">SMKN 1 BERINGIN</p>
            <p class="text-blue-600 dark:text-blue-400 text-[10px] font-extrabold tracking-widest mt-0.5 uppercase">Sistem Presensi</p>
        </div>
    </div>

    {{-- Academic Tag --}}
    <div class="px-5 py-2.5 flex items-center justify-between text-[11px] bg-slate-50/50 dark:bg-slate-900/50 backdrop-blur-md border-y border-slate-200/50 dark:border-slate-700/50">
        <span class="text-slate-600 dark:text-slate-300 font-bold"><i class="fas fa-calendar-check text-blue-500 dark:text-blue-400 mr-1.5"></i>T.A. 2026/2027</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 shadow-sm">GANJIL</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">

        <div class="sidebar-category">Menu Utama</div>

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-gauge-high icon"></i>
            <span>Overview</span>
        </a>

        <a href="{{ route('dashboard.presensi') }}"
           class="nav-link {{ request()->routeIs('dashboard.presensi*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-user icon"></i>
            <span>@if(auth()->user()->isAdmin()) Semua Presensi @else Kelola Presensi @endif</span>
        </a>

        <a href="{{ route('dashboard.nilai') }}"
           class="nav-link {{ request()->routeIs('dashboard.nilai*') ? 'active' : '' }}">
            <i class="fas fa-graduation-cap icon"></i>
            <span>Rekap Nilai Siswa</span>
        </a>

        @if(auth()->user()->isAdmin())
            <div class="sidebar-category mt-2">Data Akademik</div>

            <a href="{{ route('dashboard.siswa') }}"
               class="nav-link {{ request()->routeIs('dashboard.siswa') ? 'active' : '' }}">
                <i class="fas fa-user-graduate icon"></i>
                <span>Data Siswa</span>
            </a>

            <a href="{{ route('dashboard.guru') }}"
               class="nav-link {{ request()->routeIs('dashboard.guru') ? 'active' : '' }}">
                <i class="fas fa-chalkboard-user icon"></i>
                <span>Data Guru</span>
            </a>

            <a href="{{ route('dashboard.kelas') }}"
               class="nav-link {{ request()->routeIs('dashboard.kelas') ? 'active' : '' }}">
                <i class="fas fa-door-open icon"></i>
                <span>Data Rombel &amp; Kelas</span>
            </a>

            <div class="sidebar-category mt-2">Sistem &amp; Keamanan</div>

            <a href="{{ route('dashboard.log') }}"
               class="nav-link {{ request()->routeIs('dashboard.log') ? 'active' : '' }}">
                <i class="fas fa-clock-rotate-left icon"></i>
                <span>Audit Log Presensi</span>
            </a>

            <a href="{{ route('dashboard.lokasi') }}"
               class="nav-link {{ request()->routeIs('dashboard.lokasi') ? 'active' : '' }}">
                <i class="fas fa-map-location-dot icon"></i>
                <span>Radius Geofencing</span>
            </a>

            <a href="{{ route('dashboard.storage') }}"
               class="nav-link {{ request()->routeIs('dashboard.storage*') ? 'active' : '' }}">
                <i class="fas fa-hard-drive icon"></i>
                <span>Manajemen Storage</span>
            </a>
        @endif
    </nav>

    {{-- User Footer (Bento Box inside Sidebar) --}}
    <div class="p-4 shrink-0 mt-auto mb-2">
        <div class="glass-panel rounded-2xl p-3 flex items-center gap-3 shadow-sm border border-slate-200/60 dark:border-slate-700/60 bg-white/60 dark:bg-slate-800/60">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-blue-600 text-white rounded-xl flex items-center justify-center text-sm font-extrabold shrink-0 shadow-[0_2px_8px_rgba(79,70,229,0.3)] border border-white/20">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-extrabold text-slate-800 dark:text-white truncate leading-tight">{{ auth()->user()->name }}</p>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 pulse-dot block"></span>
                    <span class="text-slate-500 dark:text-slate-400 text-[9px] font-extrabold uppercase tracking-widest">
                        {{ auth()->user()->isAdmin() ? 'Administrator' : 'Guru / Wali' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</aside>

{{-- ===== MAIN ===== --}}
<div class="flex flex-col min-h-screen transition-all duration-300"
     :class="sidebarOpen ? 'lg:ml-64 ml-0' : 'ml-0'">

    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen && window.innerWidth < 1024" 
         @click="sidebarOpen = false" 
         x-cloak 
         class="fixed inset-0 bg-slate-950/50 z-30 lg:hidden backdrop-blur-sm"></div>

    {{-- Header --}}
    <header class="main-header px-4 sm:px-6 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm gap-2">
        <div class="flex items-center gap-3 min-w-0 shrink-0">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/50 dark:bg-slate-800/50 text-slate-600 dark:text-slate-300 hover:text-blue-600 hover:bg-white dark:hover:bg-slate-700 transition border border-slate-200/60 dark:border-slate-700/60 shadow-sm shrink-0">
                <i class="fas fa-bars text-[15px]"></i>
            </button>
            <div class="min-w-0">
                <div class="hidden sm:flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider mb-0.5">
                    <span>SMKN 1 Beringin</span>
                    <i class="fas fa-chevron-right text-[9px] text-slate-300 dark:text-slate-600"></i>
                    <span class="text-blue-600 dark:text-blue-400">@yield('page-title', 'Dashboard')</span>
                </div>
                <h1 class="font-heading text-lg sm:text-xl font-extrabold text-slate-800 dark:text-white leading-tight truncate">@yield('page-title', 'Dashboard')</h1>
            </div>
        </div>

        <div class="hidden md:flex flex-1 justify-center items-center">
            <img src="{{ asset('images/logo-kolaborasi.webp') }}" class="h-8 lg:h-9 object-contain opacity-90 drop-shadow-sm" alt="Logo Kolaborasi">
        </div>

        <div class="flex items-center gap-3 shrink-0">
            {{-- Mobile Collaboration Logo --}}
            <img src="{{ asset('images/logo-kolaborasi.webp') }}" class="h-6 w-auto object-contain mr-1 md:hidden" alt="Logo Kolaborasi">

            {{-- Realtime Clock (Glass Bento) --}}
            <div class="hidden md:flex items-center gap-2 bg-white/60 dark:bg-slate-800/60 border border-slate-200/60 dark:border-slate-700/60 px-3.5 py-2 rounded-xl text-xs shadow-sm backdrop-blur-md">
                <i class="far fa-calendar text-blue-500 dark:text-blue-400 text-sm"></i>
                <span class="text-slate-700 dark:text-slate-200 font-bold tracking-wide">{{ now()->locale('id')->isoFormat('dddd, D MMM Y') }}</span>
                <span class="w-1 h-1 bg-slate-300 dark:bg-slate-600 rounded-full mx-1"></span>
                <span class="font-mono font-extrabold text-blue-700 dark:text-blue-300" id="realtimeClock">--:--:--</span>
            </div>

            {{-- Theme Switcher (Sky Toggle) --}}
            <div class="flex items-center bg-white/60 dark:bg-slate-800/60 p-1.5 rounded-xl border border-slate-200/60 dark:border-slate-700/60 shadow-sm backdrop-blur-md">
                <x-sky-toggle size="8px" id="admin-sky-toggle" />
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 text-xs font-extrabold text-rose-600 dark:text-rose-400 bg-white/60 dark:bg-slate-800/60 hover:bg-rose-50 dark:hover:bg-rose-950/40 border border-rose-200/60 dark:border-rose-800/60 hover:border-rose-300 dark:hover:border-rose-700 p-2 sm:px-4 sm:py-2.5 rounded-xl transition shadow-sm backdrop-blur-md"
                        title="Keluar">
                    <i class="fas fa-power-off text-sm"></i>
                    <span class="hidden sm:inline tracking-wide uppercase">Keluar</span>
                </button>
            </form>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mx-6 mt-4 bg-emerald-50 dark:bg-emerald-950/70 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 rounded-xl px-4 py-3 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                {{ session('success') }}
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 dark:text-emerald-400 ml-4">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:leave="transition ease-in duration-200"
             class="mx-6 mt-4 bg-blue-50 dark:bg-blue-950/70 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-200 rounded-xl px-4 py-3 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-circle-info text-blue-600 dark:text-blue-400 text-base"></i>
                {{ session('info') }}
            </div>
            <button @click="show = false" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 ml-4">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mx-6 mt-4 bg-rose-50 dark:bg-rose-950/70 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 rounded-xl px-4 py-3 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-circle-exclamation text-rose-600 dark:text-rose-400 text-base"></i>
                {{ session('error') }}
            </div>
            <button @click="show = false" class="text-rose-500 hover:text-rose-700 dark:text-rose-400 ml-4">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mx-6 mt-4 bg-rose-50 dark:bg-rose-950/70 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 rounded-xl px-4 py-3 shadow-xs">
            <div class="flex items-center gap-2 text-sm font-bold mb-1">
                <i class="fas fa-triangle-exclamation text-rose-600 dark:text-rose-400"></i> Perhatian:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700 dark:text-rose-300">
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
        <footer class="mt-10 pt-4 border-t border-slate-200 dark:border-slate-800 text-center text-xs text-slate-400 dark:text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>Sistem Presensi Biometrik &amp; Geofencing &bull; <strong class="text-slate-500 dark:text-slate-300">SMK Negeri 1 Beringin</strong></span>
            <span>Versi 2.0 &bull; Tahun Ajaran 2026/2027</span>
        </footer>
    </main>
</div>

{{-- AOS --}}
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 280, offset: 20 });
    
    // Realtime Clock
    function updateClock() {
        const clockElem = document.getElementById('realtimeClock');
        if (clockElem) {
            clockElem.textContent = new Date().toLocaleTimeString('id-ID', { hour12: false }) + ' WIB';
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
</body>
</html>
