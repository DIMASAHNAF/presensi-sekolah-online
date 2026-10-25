<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Presensi SMKN 1 Beringin</title>
    <meta name="description" content="Sistem Presensi Biometrik Siswa, Guru, dan Staf SMKN 1 Beringin. Portal presensi digital berbasis verifikasi wajah dan GPS.">
    <meta name="robots" content="index, follow">
    <meta name="author" content="SMKN 1 Beringin">
    <link rel="canonical" href="https://presensi.tiksmkn1beringin.my.id/">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://presensi.tiksmkn1beringin.my.id/">
    <meta property="og:title" content="Masuk — Sistem Presensi SMKN 1 Beringin">
    <meta property="og:description" content="Portal presensi digital biometrik wajah dan geolokasi untuk siswa dan guru SMKN 1 Beringin.">
    <meta property="og:image" content="{{ asset('images/logo.webp') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://presensi.tiksmkn1beringin.my.id/">
    <meta name="twitter:title" content="Masuk — Sistem Presensi SMKN 1 Beringin">
    <meta name="twitter:description" content="Portal presensi digital biometrik wajah dan geolokasi SMKN 1 Beringin.">
    <meta name="twitter:image" content="{{ asset('images/logo.webp') }}">

    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">

    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Presensi">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.webp') }}">

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Tailwind & Alpine --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4 bg-gradient-to-br from-slate-50 via-gray-50 to-blue-50/40 text-slate-800 selection:bg-blue-600 selection:text-white"
      x-data="loginCardApp()">

    <x-page-loader />

    {{-- Main Login Card --}}
    <div class="w-full max-w-md relative z-10 py-6">
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-200/80 p-7 sm:p-9">

            {{-- Header & Logos --}}
            <div class="text-center space-y-3 mb-7">
                <div class="flex items-center justify-center gap-3">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo SMKN 1 Beringin" class="h-11 w-auto object-contain">
                    <img src="{{ asset('images/logo-kolaborasi.webp') }}" alt="Logo Kolaborasi" class="h-9 w-auto object-contain">
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-3">
                        Portal Presensi
                    </h1>
                    <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider mt-1">
                        SMKN 1 BERINGIN
                    </p>
                </div>
            </div>

            {{-- Role Tab Switcher (Siswa / Guru) --}}
            <div class="p-1 bg-slate-100 rounded-xl flex gap-1 mb-6 border border-slate-200/60">
                <button type="button"
                        @click="tab = 'siswa'"
                        class="w-1/2 py-2.5 rounded-lg font-semibold flex items-center justify-center gap-2 text-sm transition-all duration-150"
                        :class="tab === 'siswa' ? 'bg-white text-blue-600 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900'">
                    <i class="fas fa-user-graduate"></i>
                    <span>Siswa</span>
                </button>
                <button type="button"
                        @click="tab = 'guru'"
                        class="w-1/2 py-2.5 rounded-lg font-semibold flex items-center justify-center gap-2 text-sm transition-all duration-150"
                        :class="tab === 'guru' ? 'bg-white text-blue-600 shadow-sm font-bold' : 'text-slate-600 hover:text-slate-900'">
                    <i class="fas fa-chalkboard-user"></i>
                    <span>Guru / Staf</span>
                </button>
            </div>

            {{-- Error Alert --}}
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-start gap-3 text-red-600 text-sm">
                    <i class="fas fa-circle-exclamation mt-0.5 text-red-500 shrink-0"></i>
                    <div class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- FORM SISWA --}}
            <div x-show="tab === 'siswa'" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <form method="POST" action="{{ route('login.siswa') }}" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wide">NISN / Username Siswa</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <i class="fas fa-id-card text-sm"></i>
                            </span>
                            <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                                   placeholder="Ketik 10 digit NISN Anda"
                                   class="w-full rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 bg-white border border-slate-300 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all outline-none shadow-xs">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wide">Password</label>
                            <span class="text-[11px] text-blue-600 font-semibold">(Kosongkan jika login awal)</span>
                        </div>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input :type="showPasswordSiswa ? 'text' : 'password'"
                                   name="password"
                                   placeholder="Masukkan password Anda"
                                   class="w-full rounded-xl pl-10 pr-11 py-3 text-sm text-slate-800 placeholder:text-slate-400 bg-white border border-slate-300 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all outline-none shadow-xs">
                            <button type="button"
                                     @click="showPasswordSiswa = !showPasswordSiswa"
                                     class="absolute right-3.5 text-slate-400 hover:text-slate-600 focus:outline-none p-1">
                                <i class="fas text-sm" :class="showPasswordSiswa ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i> Login pertama kali? Cukup ketik <strong>NISN</strong>, kolom password dapat dikosongkan.
                        </p>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2.5 text-sm text-slate-600 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition cursor-pointer">
                            <span class="font-medium text-slate-700">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold py-3.5 rounded-xl mt-6 flex items-center justify-center gap-2 text-sm shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 transition-all">
                        <span>Masuk</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <p class="text-center text-sm text-slate-500 mt-6 font-medium">
                    Belum punya akun?
                    <a href="{{ route('register.siswa') }}" class="text-blue-600 font-bold hover:underline ml-1">
                        Daftar
                    </a>
                </p>
            </div>

            {{-- FORM GURU --}}
            <div x-show="tab === 'guru'" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <form method="POST" action="{{ route('login.guru') }}" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wide">NIK atau Username</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <i class="fas fa-user-shield text-sm"></i>
                            </span>
                            <input type="text" name="nik" value="{{ old('nik') }}" required
                                   placeholder="16 digit NIK atau username"
                                   class="w-full rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 bg-white border border-slate-300 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all outline-none shadow-xs">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wide">Password</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input :type="showPasswordGuru ? 'text' : 'password'"
                                   name="password" required
                                   placeholder="••••••••"
                                   class="w-full rounded-xl pl-10 pr-11 py-3 text-sm text-slate-800 placeholder:text-slate-400 bg-white border border-slate-300 focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition-all outline-none shadow-xs">
                            <button type="button"
                                    @click="showPasswordGuru = !showPasswordGuru"
                                    class="absolute right-3.5 text-slate-400 hover:text-slate-600 focus:outline-none p-1">
                                <i class="fas text-sm" :class="showPasswordGuru ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2.5 text-sm text-slate-600 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition cursor-pointer">
                            <span class="font-medium text-slate-700">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold py-3.5 rounded-xl mt-6 flex items-center justify-center gap-2 text-sm shadow-md shadow-blue-500/20 hover:shadow-lg hover:shadow-blue-500/30 transition-all">
                        <span>Masuk Guru / Staf</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <div class="mt-6 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-center text-xs text-slate-500 font-medium">
                    <i class="fas fa-info-circle mr-1 text-blue-500"></i> Akun dewan guru & staf dibuat oleh Admin Sekolah.
                </div>
            </div>

        </div>
    </div>



    {{-- Footer --}}
    <footer class="mt-4 text-center text-xs text-slate-400 font-medium tracking-wide">
        Sistem Presensi Biometrik &copy; 2026 SMKN 1 BERINGIN
    </footer>

    <script>
        function loginCardApp() {
            return {
                tab: '{{ request("tab", $initialTab ?? (request()->is("*guru*") ? "guru" : "siswa")) }}',
                showPasswordSiswa: false,
                showPasswordGuru: false
            };
        }

        // Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch((err) => {
                    console.log('SW registration failed: ', err);
                });
            });
        }
    </script>
</body>
</html>