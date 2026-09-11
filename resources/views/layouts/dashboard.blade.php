<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Presensi SMKN 1 Beringin</title>
    <meta name="description" content="Sistem Presensi Sekolah SMKN 1 Beringin — Panel Manajemen">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1d4ed8">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Presensi SMKN 1">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">

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
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Outfit', 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* ─── Base ─────────────────────────────────────────────── */
        body {
            background-color: #f0f4f8;
            color: #1e293b;
        }

        /* ─── Sidebar (Light / Materially style) ───────────────── */
        .sidebar-bg {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            box-shadow: 2px 0 12px rgba(15, 23, 42, 0.04);
        }

        .sidebar-logo-area {
            background: #1d4ed8;
        }

        .sidebar-category {
            font-size: 0.67rem;
            font-weight: 700;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            color: #94a3b8;
            padding: 0.9rem 0.875rem 0.3rem;
        }

        .nav-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.6rem 0.875rem; border-radius: 0.5rem;
            font-size: 0.825rem; font-weight: 500;
            color: #475569;
            transition: all 0.15s ease-in-out;
        }
        .nav-link:hover {
            background: #f1f5f9;
            color: #1e293b;
        }
        .nav-link.active {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 700;
            border-left: 3px solid #1d4ed8;
            padding-left: calc(0.875rem - 3px);
        }
        .nav-link .icon {
            width: 1.2rem; text-align: center;
            color: #94a3b8;
            transition: color 0.15s;
        }
        .nav-link.active .icon { color: #1d4ed8; }
        .nav-link:hover .icon { color: #475569; }

        /* ─── Stat Cards (Materially style) ─────────────────────── */
        .stat-card {
            background: #ffffff;
            border-radius: 0.75rem;
            padding: 1.25rem 1.5rem 1rem;
            border: none;
            box-shadow: 0 1px 4px rgba(15,23,42,0.06), 0 4px 16px rgba(15,23,42,0.04);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: var(--card-accent, #e2e8f0);
            border-radius: 0 0 0.75rem 0.75rem;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(15,23,42,0.10);
        }
        .stat-card.accent-blue   { --card-accent: #1d4ed8; }
        .stat-card.accent-indigo { --card-accent: #4f46e5; }
        .stat-card.accent-slate  { --card-accent: #475569; }
        .stat-card.accent-green  { --card-accent: #16a34a; }
        .stat-card.accent-amber  { --card-accent: #d97706; }
        .stat-card.accent-red    { --card-accent: #dc2626; }

        /* ─── Buttons ─────────────────────────────────────────────── */
        .btn-primary {
            background: #1d4ed8;
            color: #ffffff;
            border-radius: 0.5rem;
            padding: 0.55rem 1.2rem;
            font-size: 0.825rem;
            font-weight: 600;
            display: inline-flex; align-items: center; gap: 0.5rem;
            transition: all 0.15s ease;
            box-shadow: 0 1px 3px rgba(29,78,216,0.25);
        }
        .btn-primary:hover {
            background: #1e40af;
            box-shadow: 0 4px 12px rgba(29,78,216,0.35);
        }
        .btn-primary:active { transform: scale(0.98); }

        .btn-secondary {
            background: #ffffff;
            color: #334155;
            border-radius: 0.5rem;
            padding: 0.5rem 0.95rem;
            font-size: 0.8125rem;
            font-weight: 600;
            display: inline-flex; align-items: center; gap: 0.4rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            transition: all 0.15s ease;
        }
        .btn-secondary:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .btn-danger {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
            border-radius: 0.5rem;
            padding: 0.5rem 0.875rem;
            font-size: 0.8125rem;
            font-weight: 600;
            display: inline-flex; align-items: center; gap: 0.4rem;
            transition: all 0.15s ease;
        }
        .btn-danger:hover {
            background: #ffe4e6;
            color: #9f1239;
        }

        /* ─── Status Badges ───────────────────────────────────────── */
        .badge-hadir  { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-izin   { background: #fefce8; color: #a16207; border: 1px solid #fef08a; }
        .badge-sakit  { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
        .badge-alpa   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.22rem 0.65rem; border-radius: 0.375rem;
            font-size: 0.72rem; font-weight: 700; letter-spacing: 0.02em;
        }

        /* ─── Table ───────────────────────────────────────────────── */
        .table-row:hover td { background: #f8fafc; }

        /* ─── Header ──────────────────────────────────────────────── */
        .main-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
        }

        /* ─── Card wrapper ────────────────────────────────────────── */
        .content-card {
            background: #ffffff;
            border-radius: 0.75rem;
            border: 1px solid #e8ecf0;
            box-shadow: 0 1px 3px rgba(15,23,42,0.05);
        }

        /* ─── Misc ────────────────────────────────────────────────── */
        [x-cloak] { display: none !important; }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Realtime pulse dot */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }
        .pulse-dot { animation: pulse-dot 1.8s ease-in-out infinite; }
    </style>

    @stack('styles')
</head>

<body class="text-slate-800 antialiased" 
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }" 
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

<x-page-loader />
<x-toast />

{{-- ===== SIDEBAR ===== --}}
<aside class="sidebar-bg fixed top-0 left-0 h-full z-40 text-white flex flex-col
              transition-all duration-300 shadow-2xl"
       :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-64 -translate-x-full lg:w-0 lg:translate-x-0 overflow-hidden'"
       x-cloak>

    {{-- School Header (blue accent top bar) --}}
    <div class="sidebar-logo-area flex items-center gap-3 px-5 py-4 shrink-0">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0 border border-white/30 overflow-hidden p-1">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1" class="w-full h-full object-contain">
        </div>
        <div class="min-w-0">
            <p class="font-heading font-extrabold text-sm text-white tracking-tight truncate leading-tight">SMKN 1 BERINGIN</p>
            <p class="text-blue-200 text-[10px] font-semibold tracking-widest mt-0.5 uppercase">Sistem Presensi</p>
        </div>
    </div>

    {{-- Academic Tag --}}
    <div class="px-5 py-2 border-b border-slate-100 flex items-center justify-between text-[11px] bg-slate-50">
        <span class="text-slate-500 font-medium"><i class="fas fa-calendar-check text-blue-500 mr-1.5"></i>T.A. 2026/2027</span>
        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">GANJIL</span>
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
        @endif
    </nav>

    {{-- User Footer --}}
    <div class="p-3 border-t border-slate-200 shrink-0 bg-slate-50">
        <div class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-white border border-slate-200 shadow-xs">
            <div class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center text-xs font-bold shrink-0 shadow-sm">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-slate-800 truncate leading-tight">{{ auth()->user()->name }}</p>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 pulse-dot"></span>
                    <span class="text-slate-400 text-[10px] font-semibold uppercase tracking-wider">
                        {{ auth()->user()->isAdmin() ? 'Administrator' : 'Guru / Wali Kelas' }}
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
    <header class="main-header px-6 py-3 flex items-center justify-between sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-3.5">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-500 hover:text-blue-700 hover:bg-blue-50 transition border border-slate-200">
                <i class="fas fa-bars text-sm"></i>
            </button>
            <div>
                <div class="flex items-center gap-1.5 text-xs text-slate-400 font-medium">
                    <span>SMKN 1 Beringin</span>
                    <i class="fas fa-chevron-right text-[9px] text-slate-300"></i>
                    <span class="text-blue-600 font-semibold">@yield('page-title', 'Dashboard')</span>
                </div>
                <h1 class="font-heading text-sm font-bold text-slate-800 leading-tight">@yield('page-title', 'Dashboard')</h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Realtime Clock --}}
            <div class="hidden md:flex items-center gap-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-lg text-xs">
                <i class="far fa-calendar text-blue-500 text-xs"></i>
                <span class="text-slate-600 font-medium">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                <span class="font-mono font-bold text-slate-700" id="realtimeClock">--:--:--</span>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-rose-600 bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 px-3 py-2 rounded-lg transition shadow-xs">
                    <i class="fas fa-arrow-right-from-bracket text-xs"></i>
                    <span class="hidden sm:inline">Keluar</span>
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
             class="mx-6 mt-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                {{ session('success') }}
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 ml-4">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:leave="transition ease-in duration-200"
             class="mx-6 mt-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-xl px-4 py-3 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-circle-info text-blue-600 text-base"></i>
                {{ session('info') }}
            </div>
            <button @click="show = false" class="text-blue-500 hover:text-blue-700 ml-4">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mx-6 mt-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2 text-sm font-medium">
                <i class="fas fa-circle-exclamation text-rose-600 text-base"></i>
                {{ session('error') }}
            </div>
            <button @click="show = false" class="text-rose-500 hover:text-rose-700 ml-4">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mx-6 mt-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl px-4 py-3 shadow-xs">
            <div class="flex items-center gap-2 text-sm font-bold mb-1">
                <i class="fas fa-triangle-exclamation text-rose-600"></i> Perhatian:
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
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
        <footer class="mt-10 pt-4 border-t border-slate-200 text-center text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
            <span>Sistem Presensi Biometrik &amp; Geofencing &bull; <strong class="text-slate-500">SMK Negeri 1 Beringin</strong></span>
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
