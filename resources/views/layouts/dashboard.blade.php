<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Absensi Sekolah</title>
    <meta name="description" content="Sistem Absensi Sekolah — Panel Manajemen">

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
            background: linear-gradient(175deg, #1e3a8a 0%, #1e40af 60%, #2563eb 100%);
        }

        .nav-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.625rem 1rem; border-radius: 0.75rem;
            font-size: 0.875rem; font-weight: 500;
            color: rgba(255,255,255,0.8);
            transition: all 0.2s;
        }
        .nav-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .nav-link.active { background: rgba(255,255,255,0.18); color: #fff; }
        .nav-link .icon { width: 1.25rem; text-align: center; color: rgba(147,197,253,0.9); }

        .stat-card {
            background: #fff;
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37,99,235,0.1); }

        .btn-primary {
            background: #2563eb; color: #fff; border-radius: 0.75rem;
            padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600;
            display: inline-flex; align-items: center; gap: 0.5rem;
            transition: background 0.2s, transform 0.1s;
        }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-primary:active { transform: scale(0.97); }

        .btn-danger {
            background: #fee2e2; color: #dc2626; border-radius: 0.75rem;
            padding: 0.5rem 0.875rem; font-size: 0.8125rem; font-weight: 600;
            display: inline-flex; align-items: center; gap: 0.4rem;
            transition: background 0.2s;
        }
        .btn-danger:hover { background: #fecaca; }

        .btn-secondary {
            background: #eff6ff; color: #2563eb; border-radius: 0.75rem;
            padding: 0.5rem 0.875rem; font-size: 0.8125rem; font-weight: 600;
            display: inline-flex; align-items: center; gap: 0.4rem;
            transition: background 0.2s;
        }
        .btn-secondary:hover { background: #dbeafe; }

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
    <div class="flex items-center gap-3 px-5 py-[1.125rem] border-b border-white/10 shrink-0">
        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shrink-0 overflow-hidden">
            <img src="{{ asset('images/logo.png') }}" alt="Logo SMK" class="w-full h-full object-contain p-1">
        </div>
        <div>
            <p class="font-bold text-sm leading-tight">Absensi</p>
            <p class="text-blue-200 text-xs">Sekolah</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie icon"></i> Overview
        </a>

        <a href="{{ route('dashboard.absensi') }}"
           class="nav-link {{ request()->routeIs('dashboard.absensi*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list icon"></i>
            @if(auth()->user()->isAdmin()) Semua Absensi @else Kelola Absensi @endif
        </a>

        @if(auth()->user()->isAdmin())
            <div class="pt-4 pb-1 px-1">
                <p class="text-xs text-blue-300 font-semibold uppercase tracking-widest">Manajemen</p>
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
    <div class="p-3 border-t border-white/10 shrink-0">
        <div class="flex items-center gap-3 px-2 py-2 rounded-xl hover:bg-white/10 transition">
            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-xs font-bold shrink-0">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
                <p class="text-blue-300 text-xs capitalize">{{ auth()->user()->role }}</p>
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
    <header class="bg-white border-b border-slate-200 px-6 py-3.5 flex items-center justify-between sticky top-0 z-30 shadow-sm">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h1 class="text-sm font-semibold text-slate-800">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-slate-400">@yield('page-subtitle', '')</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <span class="text-xs text-slate-500 hidden md:flex items-center gap-3 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                <span><i class="far fa-calendar-alt text-slate-400 mr-1.5"></i>{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                <span class="font-bold text-slate-700" id="realtimeClock">--:--:--</span>
            </span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="flex items-center gap-2 text-sm text-slate-500 hover:text-red-600 px-3 py-2 rounded-lg hover:bg-red-50 transition">
                    <i class="fas fa-right-from-bracket"></i>
                    <span class="hidden sm:inline">Logout</span>
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
    <main class="flex-1 p-6">
        @yield('content')
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
