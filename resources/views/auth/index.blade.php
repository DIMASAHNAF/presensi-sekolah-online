<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Portal Presensi SMKN 1 Beringin</title>
    <meta name="description" content="Portal Presensi Digital Siswa dan Dewan Guru SMKN 1 Beringin berbasis biometrik dan geofencing.">
    <meta name="robots" content="index, follow">
    <meta name="author" content="SMKN 1 Beringin">
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph / Meta -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Masuk — Portal Presensi SMKN 1 Beringin">
    <meta property="og:description" content="Portal Presensi Digital Siswa dan Dewan Guru SMKN 1 Beringin.">
    <meta property="og:image" content="{{ asset('images/logo.webp') }}">

    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">

    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#18181b">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Presensi">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.webp') }}">

    <!-- Typography: Inter & JetBrains Mono (Strict PRD Compliance) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Tailwind Configuration (Linear / Vercel Aesthetic) -->
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
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; font-variant-numeric: tabular-nums; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased"
      x-data="loginCardApp()">

    <x-page-loader />

    {{-- Main Container --}}
    <div class="w-full max-w-sm sm:max-w-md py-6 sm:py-8">

        {{-- Card: Border-First, Zero Gradients, Zero Heavy Shadows --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 sm:p-8 shadow-xs">

            {{-- Header & Logos --}}
            <div class="text-center space-y-3 mb-6">
                <div class="flex items-center justify-center gap-3">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo SMKN 1 Beringin" class="h-10 w-auto object-contain">
                    <div class="w-px h-6 bg-zinc-200 dark:bg-zinc-800"></div>
                    <img src="{{ asset('images/logo-kolaborasi.webp') }}" alt="Logo Kolaborasi" class="h-8 w-auto object-contain">
                </div>

                <div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-100">
                        Portal Presensi
                    </h1>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 font-mono uppercase tracking-wider mt-1">
                        SMKN 1 BERINGIN
                    </p>
                </div>
            </div>

            {{-- Role Tab Switcher (Segmented Zinc Pill) --}}
            <div class="p-1 bg-zinc-100 dark:bg-zinc-800/80 rounded-lg flex gap-1 mb-6 border border-zinc-200/80 dark:border-zinc-700/80">
                <button type="button"
                        @click="tab = 'siswa'"
                        class="w-1/2 py-2 rounded-md font-medium flex items-center justify-center gap-2 text-xs sm:text-sm transition-all"
                        :class="tab === 'siswa'
                            ? 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs border border-zinc-200/60 dark:border-zinc-700 font-semibold'
                            : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'">
                    <i class="fas fa-user-graduate text-xs"></i>
                    <span>Siswa</span>
                </button>
                <button type="button"
                        @click="tab = 'guru'"
                        class="w-1/2 py-2 rounded-md font-medium flex items-center justify-center gap-2 text-xs sm:text-sm transition-all"
                        :class="tab === 'guru'
                            ? 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs border border-zinc-200/60 dark:border-zinc-700 font-semibold'
                            : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-200'">
                    <i class="fas fa-chalkboard-user text-xs"></i>
                    <span>Guru / Staf</span>
                </button>
            </div>

            {{-- Error Alert (Semantic Rose, Border-First) --}}
            @if ($errors->any())
                <div class="bg-rose-500/10 border border-rose-500/20 rounded-lg p-3.5 mb-5 flex items-start gap-2.5 text-rose-700 dark:text-rose-300 text-xs">
                    <svg class="w-4 h-4 text-rose-600 dark:text-rose-400 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <div class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <p class="leading-normal">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- FORM SISWA --}}
            <div x-show="tab === 'siswa'" x-cloak>
                <form method="POST" action="{{ route('login.siswa') }}" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">Nama Lengkap / Username / NISN</label>
                        <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                               placeholder="Ketik nama, username, atau NISN"
                               class="w-full rounded-lg px-3.5 py-2.5 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 transition outline-none placeholder:text-zinc-400">
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">Password</label>
                            <span class="text-[11px] text-zinc-500 dark:text-zinc-400 font-mono">Kosongkan jika login awal</span>
                        </div>
                        <div class="relative flex items-center">
                            <input :type="showPasswordSiswa ? 'text' : 'password'"
                                   name="password"
                                   placeholder="Masukkan password"
                                   class="w-full rounded-lg pl-3.5 pr-10 py-2.5 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 transition outline-none placeholder:text-zinc-400">
                            <button type="button"
                                    @click="showPasswordSiswa = !showPasswordSiswa"
                                    class="absolute right-3 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 focus:outline-none p-0.5"
                                    aria-label="Tampilkan password">
                                <i class="fas text-xs" :class="showPasswordSiswa ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-0.5">
                        <label class="flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-400 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-zinc-500">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    {{-- Primary Action: Solid High-Contrast (PRD 1.2) --}}
                    <button type="submit"
                            class="w-full bg-zinc-900 hover:bg-zinc-800 text-zinc-50 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 font-medium py-2.5 rounded-lg text-xs sm:text-sm transition shadow-xs flex items-center justify-center gap-2">
                        <span>Masuk Siswa</span>
                        <i class="fas fa-arrow-right text-[11px]"></i>
                    </button>
                </form>

                <p class="text-center text-xs text-zinc-500 dark:text-zinc-400 mt-5">
                    Belum punya akun?
                    <a href="{{ route('register.siswa') }}" class="font-medium text-zinc-900 dark:text-zinc-100 hover:underline ml-1">
                        Daftar Baru
                    </a>
                </p>
            </div>

            {{-- FORM GURU --}}
            <div x-show="tab === 'guru'" x-cloak>
                <form method="POST" action="{{ route('login.guru') }}" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">NIP / NUPTK / Username / Nama</label>
                        <input type="text" name="identifier" value="{{ old('identifier', old('nik')) }}" required
                               placeholder="Ketik NIP, NUPTK, Username, atau Nama"
                               class="w-full rounded-lg px-3.5 py-2.5 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 transition outline-none placeholder:text-zinc-400">
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-normal">
                            Guru ASN dapat menggunakan NIP. Guru honorer dapat memakai NUPTK, Username, atau Nama.
                        </p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300">Password</label>
                        <div class="relative flex items-center">
                            <input :type="showPasswordGuru ? 'text' : 'password'"
                                   name="password" required
                                   placeholder="••••••••"
                                   class="w-full rounded-lg pl-3.5 pr-10 py-2.5 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 transition outline-none placeholder:text-zinc-400">
                            <button type="button"
                                    @click="showPasswordGuru = !showPasswordGuru"
                                    class="absolute right-3 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 focus:outline-none p-0.5"
                                    aria-label="Tampilkan password">
                                <i class="fas text-xs" :class="showPasswordGuru ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-0.5">
                        <label class="flex items-center gap-2 text-xs text-zinc-600 dark:text-zinc-400 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 focus:ring-zinc-500">
                            <span>Ingat saya</span>
                        </label>
                    </div>

                    {{-- Primary Action: Solid High-Contrast --}}
                    <button type="submit"
                            class="w-full bg-zinc-900 hover:bg-zinc-800 text-zinc-50 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 font-medium py-2.5 rounded-lg text-xs sm:text-sm transition shadow-xs flex items-center justify-center gap-2">
                        <span>Masuk Guru / Staf</span>
                        <i class="fas fa-arrow-right text-[11px]"></i>
                    </button>
                </form>

                <p class="text-center text-[11px] text-zinc-500 dark:text-zinc-400 mt-5">
                    Akun dewan guru &amp; staf dikelola oleh Administrator Sekolah.
                </p>
            </div>

        </div>

        {{-- PWA Install Option (Border-First, No Gradient) --}}
        <div class="mt-3.5 p-3.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 flex items-center justify-between gap-3 shadow-xs"
             x-data="{ isStandalone: window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true }"
             x-show="!isStandalone">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0 border border-zinc-200 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300">
                    <i class="fas fa-mobile-screen text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-medium text-xs text-zinc-900 dark:text-zinc-100 truncate">Pasang Aplikasi Presensi</p>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 truncate">Akses cepat langsung dari layar utama</p>
                </div>
            </div>
            <button type="button" @click="window.openPwaInstallPrompt ? window.openPwaInstallPrompt() : $dispatch('open-pwa-install')"
                    class="px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 text-xs font-medium transition shrink-0">
                Pasang
            </button>
        </div>

        {{-- Help Link --}}
        <div class="mt-4 text-center">
            <a href="{{ route('panduan') }}" class="text-xs text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 transition inline-flex items-center gap-1.5 font-medium">
                <i class="fas fa-book-open text-[11px]"></i>
                <span>Buku Panduan Penggunaan</span>
            </a>
        </div>

        {{-- Footer --}}
        <footer class="mt-6 text-center text-[11px] text-zinc-400 dark:text-zinc-400 font-mono tabular-nums">
            Sistem Presensi Biometrik &copy; 2026 SMKN 1 BERINGIN
        </footer>
    </div>

    {{-- Universal PWA Modal --}}
    <x-pwa-modal />

    <script>
        function loginCardApp() {
            return {
                tab: '{{ request("tab", $initialTab ?? (request()->is("*guru*") ? "guru" : "siswa")) }}',
                showPasswordSiswa: false,
                showPasswordGuru: false
            };
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js?v=16').catch(() => {});
            });
        }
    </script>
</body>
</html>
