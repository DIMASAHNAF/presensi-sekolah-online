<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Presensi Sekolah</title>
    <meta name="description" content="Sistem Presensi Sekolah — Panel Manajemen">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Font Awesome 6 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- AOS --}}
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    {{-- Tailwind --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * { font-family: 'Inter', sans-serif; }

        .sidebar-bg {
            background: linear-gradient(175deg, #0f172a 0%, #1e293b 60%, #0f172a 100%);
        }

        .nav-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.75rem 1rem; border-radius: 0.75rem;
            font-size: 0.875rem; font-weight: 500;
            color: rgba(255,255,255,0.7);
            transition: all 0.2s ease-in-out;
        }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; transform: translateX(4px); }
        .nav-link.active { background: rgba(20, 184, 166, 0.15); color: #2dd4bf; font-weight: 600; border-right: 3px solid #2dd4bf; border-radius: 0.75rem 0 0 0.75rem; }
        .nav-link .icon { width: 1.25rem; text-align: center; color: rgba(255,255,255,0.5); transition: color 0.2s; }
        .nav-link.active .icon { color: #2dd4bf; }
        .nav-link:hover .icon { color: #fff; }

        .stat-card {
            background: #fff;
            border-radius: 1.25rem;
            padding: 1.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05); }

        .btn-primary {
            background: #0f766e; color: #fff; border-radius: 0.75rem;
            padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600;
            display: inline-flex; align-items: center; gap: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(13, 148, 136, 0.2);
        }
        .btn-primary:hover { background: #0f172a; box-shadow: 0 6px 12px -2px rgba(15, 23, 42, 0.3); }
        .btn-primary:active { transform: scale(0.98); }

        .btn-danger {
            background: #fef2f2; color: #ef4444; border-radius: 0.75rem;
            padding: 0.5rem 0.875rem; font-size: 0.8125rem; font-weight: 600;
            display: inline-flex; align-items: center; gap: 0.4rem;
            transition: all 0.2s ease;
        }
        .btn-danger:hover { background: #fee2e2; color: #b91c1c; }

        .btn-secondary {
            background: #f8fafc; color: #475569; border-radius: 0.75rem;
            padding: 0.5rem 0.875rem; font-size: 0.8125rem; font-weight: 600;
            display: inline-flex; align-items: center; gap: 0.4rem;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .btn-secondary:hover { background: #f1f5f9; color: #0f172a; border-color: #cbd5e1; }

        .badge-hadir  { background:#dcfce7; color:#16a34a; }
        .badge-izin   { background:#fef9c3; color:#ca8a04; }
        .badge-sakit  { background:#ffedd5; color:#ea580c; }
        .badge-alpa   { background:#fee2e2; color:#dc2626; }
        .badge {
            display: inline-block; padding: 0.2rem 0.625rem;
            border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
        }

        .table-row:hover td { background: #eff6ff; }

        [x-cloak] { display: none !important; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    </style>

    @stack('styles')
</head>

<body class="bg-slate-100 text-slate-800" 
      x-data="{ sidebarOpen: window.innerWidth >= 1024 }" 
      @resize.window="sidebarOpen = window.innerWidth >= 1024">

{{-- ===== SIDEBAR ===== --}}
<aside class="sidebar-bg fixed top-0 left-0 h-full z-40 text-white flex flex-col
              transition-all duration-300 shadow-xl shadow-blue-900/20"
       :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-64 -translate-x-full lg:w-0 lg:translate-x-0 overflow-hidden'"
       x-cloak>

    {{-- Logo --}}
    <div class="flex items-center gap-3 px-5 py-[1.125rem] border-b border-white/5 shrink-0">
        <div class="w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center shrink-0 shadow-lg shadow-teal-500/30">
            <i class="fas fa-school text-white text-lg"></i>
        </div>
        <div>
            <p class="font-bold text-sm text-white tracking-wide">Presensi</p>
            <p class="text-teal-400 text-xs font-medium uppercase tracking-widest">Sekolah</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie icon"></i> Overview
        </a>

        <a href="{{ route('dashboard.presensi') }}"
           class="nav-link {{ request()->routeIs('dashboard.presensi*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list icon"></i>
            @if(auth()->user()->isAdmin()) Semua Presensi @else Kelola Presensi @endif
        </a>

        @if(auth()->user()->isAdmin())
            <div class="pt-6 pb-2 px-3">
                <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-widest">Manajemen Master</p>
            </div>

            <a href="{{ route('dashboard.siswa') }}"
               class="nav-link {{ request()->routeIs('dashboard.siswa') ? 'active' : '' }}">
                <i class="fas fa-users icon"></i> Kelola Siswa
            </a>

            <a href="{{ route('dashboard.guru') }}"
               class="nav-link {{ request()->routeIs('dashboard.guru') ? 'active' : '' }}">
                <i class="fas fa-chalkboard-user icon"></i> Kelola Guru
            </a>

            <a href="{{ route('dashboard.kelas') }}"
               class="nav-link {{ request()->routeIs('dashboard.kelas') ? 'active' : '' }}">
                <i class="fas fa-door-open icon"></i> Kelola Kelas
            </a>

            <a href="{{ route('dashboard.log') }}"
               class="nav-link {{ request()->routeIs('dashboard.log') ? 'active' : '' }}">
                <i class="fas fa-history icon"></i> Log Perubahan
            </a>
        @endif
    </nav>

    {{-- User Info --}}
    <div class="p-4 border-t border-white/5 shrink-0 bg-slate-900/50">
        <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-white/5 transition border border-transparent hover:border-white/10 cursor-pointer">
            <div class="w-9 h-9 bg-teal-500/20 text-teal-400 border border-teal-500/30 rounded-xl flex items-center justify-center text-sm font-bold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-slate-400 text-[0.65rem] uppercase tracking-wider mt-0.5">{{ auth()->user()->role }}</p>
            </div>
        </div>
    </div>
</aside>

{{-- ===== MAIN ===== --}}
<div class="flex flex-col min-h-screen transition-all duration-300"
     :class="sidebarOpen ? 'lg:ml-64 ml-0' : 'ml-0'">

    {{-- Overlay for mobile when sidebar is open --}}
    <div x-show="sidebarOpen && window.innerWidth < 1024" 
         @click="sidebarOpen = false" 
         x-cloak 
         class="fixed inset-0 bg-slate-900/50 z-30 lg:hidden backdrop-blur-sm"></div>

    {{-- Header --}}
    <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 px-6 py-4 flex items-center justify-between sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="w-10 h-10 flex items-center justify-center rounded-xl text-slate-500 hover:text-teal-600 hover:bg-teal-50 transition border border-transparent hover:border-teal-100">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h1 class="text-sm font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-slate-400">@yield('page-subtitle', '')</p>
            </div>
        </div>

        <div class="flex items-center gap-5">
            <span class="text-xs text-slate-500 hidden md:flex items-center gap-3 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm">
                <span><i class="far fa-calendar-alt text-teal-500 mr-2"></i>{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                <span class="font-bold text-slate-700 font-mono tracking-tight" id="realtimeClock">--:--:--</span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-red-600 bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 px-4 py-2 rounded-xl transition shadow-sm">
                    <i class="fas fa-right-from-bracket"></i>
                    <span class="hidden sm:inline">Keluar</span>
                </button>
            </form>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mx-6 mt-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl px-4 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2 text-sm">
                <i class="fas fa-circle-check text-emerald-500"></i>
                {{ session('success') }}
            </div>
            <button @click="show = false" class="text-emerald-400 hover:text-emerald-600 ml-4">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2 text-sm">
                <i class="fas fa-circle-exclamation text-red-500"></i>
                {{ session('error') }}
            </div>
            <button @click="show = false" class="text-red-400 hover:text-red-600 ml-4">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 shadow-sm">
            <div class="flex items-center gap-2 text-sm font-semibold mb-1">
                <i class="fas fa-triangle-exclamation"></i> Terdapat kesalahan:
            </div>
            <ul class="list-disc list-inside text-sm space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Content --}}
    <main class="flex-1 p-6 flex flex-col">
        <div class="flex-1">
            @yield('content')
        </div>
        
        {{-- Footer --}}
        <footer class="mt-8 pt-4 border-t border-slate-200 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Presensi Sekolah. Developed by <span class="font-semibold text-slate-700">Dimas A.F</span>.
        </footer>
    </main>
</div>

{{-- AOS --}}
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 350, offset: 30 });
    
    // Realtime Clock
    function updateClock() {
        const now = new Date();
        const clockElem = document.getElementById('realtimeClock');
        if (clockElem) {
            clockElem.textContent = now.toLocaleTimeString('id-ID', { hour12: false });
        }
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@stack('scripts')
</body>
</html>
