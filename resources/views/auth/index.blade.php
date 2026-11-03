<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Presensi SMKN 1 Beringin</title>
    <meta name="description" content="Sistem Presensi Biometrik Siswa, Guru, dan Staf SMKN 1 Beringin. Portal presensi digital berbasis verifikasi wajah dan GPS.">
    <meta name="robots" content="index, follow">
    <meta name="author" content="SMKN 1 Beringin">
    <link rel="canonical" href="{{ url('/') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Masuk — Sistem Presensi SMKN 1 Beringin">
    <meta property="og:description" content="Portal presensi digital biometrik wajah dan geolokasi untuk siswa dan guru SMKN 1 Beringin.">
    <meta property="og:image" content="{{ asset('images/logo.webp') }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="Masuk — Sistem Presensi SMKN 1 Beringin">
    <meta name="twitter:description" content="Portal presensi digital biometrik wajah dan geolokasi SMKN 1 Beringin.">
    <meta name="twitter:image" content="{{ asset('images/logo.webp') }}">

    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">

    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0a0f1d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Presensi">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.webp') }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Tailwind & Alpine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #070b14;
        }

        .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        /* Glass Surface Core */
        .glass-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.07) 0%, rgba(255, 255, 255, 0.02) 100%), rgba(11, 17, 32, 0.75);
            backdrop-filter: blur(28px) saturate(190%);
            -webkit-backdrop-filter: blur(28px) saturate(190%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-top: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.7),
                        0 0 50px -10px rgba(37, 99, 235, 0.2),
                        inset 0 1px 0 rgba(255, 255, 255, 0.18);
        }

        .glass-input {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .glass-input:hover {
            background: rgba(15, 23, 42, 0.75);
            border-color: rgba(255, 255, 255, 0.22);
        }
        .glass-input:focus {
            background: rgba(15, 23, 42, 0.9);
            border-color: rgba(96, 165, 250, 0.85);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.22),
                        0 10px 25px -5px rgba(37, 99, 235, 0.2);
        }
        .glass-input::placeholder {
            color: #64748b;
        }

        .glass-pill {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        /* Ambient Lighting Animations */
        @keyframes orbFloat1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(30px, 40px) scale(1.1); }
        }
        @keyframes orbFloat2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-40px, -30px) scale(1.15); }
        }
        @keyframes orbFloat3 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(25px, -35px) scale(0.95); }
        }

        .animate-orb-1 {
            animation: orbFloat1 14s ease-in-out infinite;
        }
        .animate-orb-2 {
            animation: orbFloat2 16s ease-in-out infinite;
        }
        .animate-orb-3 {
            animation: orbFloat3 12s ease-in-out infinite;
        }

        /* Shimmer Button Sweep */
        .btn-shimmer {
            position: relative;
            overflow: hidden;
        }
        .btn-shimmer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -120%;
            width: 80%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35), transparent);
            transform: skewX(-25deg);
            transition: left 0.75s ease;
        }
        .btn-shimmer:hover::before {
            left: 140%;
        }

        /* Delicate Dot Grid Background */
        .grid-backdrop {
            background-image: radial-gradient(rgba(255, 255, 255, 0.07) 1px, transparent 1px);
            background-size: 28px 28px;
        }
    </style>
</head>
<body class="min-h-screen relative flex flex-col items-center justify-center p-4 text-slate-100 selection:bg-blue-600 selection:text-white overflow-x-hidden"
      x-data="loginCardApp()">

    <x-page-loader />

    <!-- School Background with Vignette Overlay -->
    <div class="fixed inset-0 z-0 bg-cover bg-center pointer-events-none opacity-25 scale-105 filter blur-xs"
         style="background-image: url('{{ asset('images/bg-sekolah.webp') }}');"></div>

    <!-- Deep Ambient Vignette Gradient -->
    <div class="fixed inset-0 z-0 pointer-events-none bg-gradient-to-b from-[#070b14]/90 via-[#0a0f1d]/85 to-[#070b14]/95"></div>

    <!-- Grid Texture -->
    <div class="fixed inset-0 z-0 pointer-events-none grid-backdrop opacity-60"></div>

    <!-- Glowing Ambient Orbs for Refraction -->
    <div class="fixed top-[-10%] left-[-10%] w-[480px] h-[480px] rounded-full pointer-events-none z-0 bg-blue-600/25 blur-[120px] animate-orb-1"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[520px] h-[520px] rounded-full pointer-events-none z-0 bg-violet-600/20 blur-[130px] animate-orb-2"></div>
    <div class="fixed top-[40%] right-[15%] w-[360px] h-[360px] rounded-full pointer-events-none z-0 bg-cyan-500/15 blur-[100px] animate-orb-3"></div>

    <!-- Main Container -->
    <div class="w-full max-w-md relative z-10 py-6 sm:py-8">

        <!-- Glassmorphism Login Card -->
        <div class="glass-card rounded-[2rem] p-7 sm:p-9 relative overflow-hidden transition-all duration-300">

            <!-- Subtle Top Glass Specular Line -->
            <div class="absolute top-0 left-10 right-10 h-[1px] bg-gradient-to-r from-transparent via-white/40 to-transparent pointer-events-none"></div>

            <!-- Header & Branding -->
            <div class="text-center space-y-3 mb-7 relative">
                <!-- Logos with Glass Pill -->
                <div class="inline-flex items-center justify-center gap-3.5 px-4 py-2 rounded-2xl glass-pill shadow-inner">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo SMKN 1 Beringin" class="h-10 w-auto object-contain drop-shadow-md">
                    <div class="w-[1px] h-6 bg-white/15"></div>
                    <img src="{{ asset('images/logo-kolaborasi.webp') }}" alt="Logo Kolaborasi" class="h-8 w-auto object-contain drop-shadow-md">
                </div>

                <div class="pt-2">
                    <h1 class="text-2xl sm:text-[1.75rem] font-extrabold tracking-tight font-heading bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">
                        Portal Presensi
                    </h1>
                    <div class="inline-flex items-center gap-2 mt-1.5 px-3 py-0.5 rounded-full bg-blue-500/10 border border-blue-400/20 text-blue-400 text-[11px] font-bold tracking-wider uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>SMKN 1 BERINGIN</span>
                    </div>
                </div>
            </div>

            <!-- Role Tab Switcher (Siswa / Guru) -->
            <div class="p-1.5 bg-black/40 rounded-2xl flex gap-1.5 mb-6 border border-white/10 backdrop-blur-md shadow-inner">
                <button type="button"
                        @click="tab = 'siswa'"
                        class="w-1/2 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 text-xs sm:text-sm transition-all duration-200"
                        :class="tab === 'siswa'
                            ? 'bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/35 border border-white/20'
                            : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.03]'">
                    <i class="fas fa-user-graduate text-xs" :class="tab === 'siswa' ? 'text-blue-100' : 'text-slate-500'"></i>
                    <span>Siswa</span>
                </button>
                <button type="button"
                        @click="tab = 'guru'"
                        class="w-1/2 py-2.5 rounded-xl font-bold flex items-center justify-center gap-2 text-xs sm:text-sm transition-all duration-200"
                        :class="tab === 'guru'
                            ? 'bg-gradient-to-r from-indigo-600 via-indigo-500 to-purple-600 text-white shadow-lg shadow-indigo-500/35 border border-white/20'
                            : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.03]'">
                    <i class="fas fa-chalkboard-user text-xs" :class="tab === 'guru' ? 'text-indigo-100' : 'text-slate-500'"></i>
                    <span>Guru / Staf</span>
                </button>
            </div>

            <!-- Error Alert in Frosted Glass -->
            @if ($errors->any())
                <div class="bg-rose-500/15 border border-rose-500/30 backdrop-blur-md rounded-2xl p-4 mb-6 flex items-start gap-3 text-rose-200 text-xs sm:text-sm shadow-lg shadow-rose-950/40">
                    <div class="w-6 h-6 rounded-lg bg-rose-500/20 flex items-center justify-center shrink-0 mt-0.5 border border-rose-500/40">
                        <i class="fas fa-circle-exclamation text-rose-400 text-xs"></i>
                    </div>
                    <div class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <p class="leading-relaxed">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- FORM SISWA -->
            <div x-show="tab === 'siswa'" x-cloak
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-[0.99]"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                <form method="POST" action="{{ route('login.siswa') }}" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Nama Lengkap / Username / NISN</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <i class="fas fa-user text-sm"></i>
                            </span>
                            <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                                   placeholder="Ketik Nama, Username, atau NISN"
                                   class="glass-input w-full rounded-2xl pl-10 pr-4 py-3.5 text-sm outline-none">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Password</label>
                            <span class="text-[11px] text-emerald-400 font-medium bg-emerald-500/10 border border-emerald-400/20 px-2 py-0.5 rounded-full">
                                Login awal? Kosongkan
                            </span>
                        </div>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input :type="showPasswordSiswa ? 'text' : 'password'"
                                   name="password"
                                   placeholder="Ketik password akun Anda"
                                   class="glass-input w-full rounded-2xl pl-10 pr-11 py-3.5 text-sm outline-none">
                            <button type="button"
                                    @click="showPasswordSiswa = !showPasswordSiswa"
                                    class="absolute right-3.5 text-slate-400 hover:text-slate-200 focus:outline-none p-1 transition">
                                <i class="fas text-sm" :class="showPasswordSiswa ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <div class="glass-pill rounded-xl p-2.5 text-[11px] text-slate-400 leading-relaxed mt-1 flex items-center gap-2">
                            <i class="fas fa-circle-info text-blue-400 shrink-0"></i>
                            <span>Bisa login pakai <strong>Nama Lengkap</strong>, <strong>Username</strong>, atau <strong>NISN</strong>.</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2.5 text-xs sm:text-sm text-slate-300 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/20 bg-slate-900 text-blue-600 focus:ring-blue-500/30 transition cursor-pointer">
                            <span class="font-medium">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="btn-shimmer w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500 hover:from-blue-500 hover:to-indigo-500 active:scale-[0.98] text-white font-bold py-3.5 rounded-2xl mt-6 flex items-center justify-center gap-2 text-sm shadow-xl shadow-blue-600/30 border border-white/20 transition-all duration-200">
                        <span>Masuk Siswa</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <p class="text-center text-xs sm:text-sm text-slate-400 mt-6 font-medium">
                    Belum punya akun?
                    <a href="{{ route('register.siswa') }}" class="text-blue-400 hover:text-blue-300 font-bold underline underline-offset-4 ml-1 transition">
                        Daftar Baru
                    </a>
                </p>
            </div>

            <!-- FORM GURU -->
            <div x-show="tab === 'guru'" x-cloak
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-y-2 scale-[0.99]"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                <form method="POST" action="{{ route('login.guru') }}" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">NIP / NUPTK / Username / Nama Guru</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <i class="fas fa-user-shield text-sm"></i>
                            </span>
                            <input type="text" name="identifier" value="{{ old('identifier', old('nik')) }}" required
                                   placeholder="NIP (18 digit), NUPTK, Username, atau Nama"
                                   class="glass-input w-full rounded-2xl pl-10 pr-4 py-3.5 text-sm outline-none">
                        </div>
                        <div class="glass-pill rounded-xl p-2.5 text-[11px] text-slate-400 leading-relaxed mt-1 flex items-start gap-2">
                            <i class="fas fa-circle-info text-indigo-400 shrink-0 mt-0.5"></i>
                            <span>Guru ASN dapat menggunakan <strong>NIP</strong>. Guru honorer dapat memakai <strong>NUPTK</strong>, <strong>Username</strong>, atau <strong>Nama Lengkap</strong>.</span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Password</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-3.5 text-slate-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input :type="showPasswordGuru ? 'text' : 'password'"
                                   name="password" required
                                   placeholder="••••••••"
                                   class="glass-input w-full rounded-2xl pl-10 pr-11 py-3.5 text-sm outline-none">
                            <button type="button"
                                    @click="showPasswordGuru = !showPasswordGuru"
                                    class="absolute right-3.5 text-slate-400 hover:text-slate-200 focus:outline-none p-1 transition">
                                <i class="fas text-sm" :class="showPasswordGuru ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2.5 text-xs sm:text-sm text-slate-300 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/20 bg-slate-900 text-indigo-600 focus:ring-indigo-500/30 transition cursor-pointer">
                            <span class="font-medium">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="btn-shimmer w-full bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-500 hover:from-indigo-500 hover:to-purple-500 active:scale-[0.98] text-white font-bold py-3.5 rounded-2xl mt-6 flex items-center justify-center gap-2 text-sm shadow-xl shadow-indigo-600/30 border border-white/20 transition-all duration-200">
                        <span>Masuk Guru / Staf</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <div class="mt-6 p-3 rounded-xl bg-white/[0.03] border border-white/10 text-center text-xs text-slate-400 font-medium">
                    <i class="fas fa-shield-halved mr-1 text-indigo-400"></i> Akun dewan guru &amp; staf dikelola oleh Admin Sekolah.
                </div>
            </div>

        </div>

        <!-- PWA Install Option Glass Banner -->
        <div class="mt-4 p-4 rounded-2xl glass-card border border-white/15 text-white flex items-center justify-between gap-3 transition-all hover:border-blue-400/40"
             x-data="{ isStandalone: window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true }"
             x-show="!isStandalone">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500/30 to-indigo-500/20 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/20 shadow-inner">
                    <i class="fas fa-mobile-screen-button text-lg text-amber-300"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="font-extrabold text-xs tracking-tight text-white">Pasang Aplikasi Presensi</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-emerald-400 text-slate-950 uppercase tracking-wider">APK / PWA</span>
                    </div>
                    <p class="text-[11px] text-slate-300 truncate mt-0.5">Akses langsung dari layar HP tanpa buka browser</p>
                </div>
            </div>
            <button type="button" @click="window.openPwaInstallPrompt ? window.openPwaInstallPrompt() : $dispatch('open-pwa-install')"
                    class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 active:scale-95 text-white font-bold text-xs shadow-md border border-white/20 transition shrink-0 flex items-center gap-1.5">
                <i class="fas fa-download text-[11px]"></i>
                <span>Pasang</span>
            </button>
        </div>

        <!-- Help & Guide Quick Links in Glass Pills -->
        <div class="mt-4 flex items-center justify-center gap-3 text-xs font-semibold text-slate-300 flex-wrap">
            <a href="{{ route('panduan') }}" class="glass-pill px-3 py-1.5 rounded-full hover:bg-white/10 hover:text-white flex items-center gap-1.5 transition">
                <i class="fas fa-book-open text-blue-400"></i>
                <span>Buku Panduan Guru &amp; Siswa</span>
            </a>
            <button type="button" @click="$dispatch('open-pwa-install')" class="glass-pill px-3 py-1.5 rounded-full hover:bg-white/10 hover:text-white flex items-center gap-1.5 transition">
                <i class="fas fa-circle-question text-indigo-400"></i>
                <span>Cara Pasang APK</span>
            </button>
        </div>

        <!-- Footer -->
        <footer class="mt-6 text-center text-xs text-slate-400 font-medium tracking-wide">
            Sistem Presensi Biometrik &copy; 2026 SMKN 1 BERINGIN
        </footer>
    </div>

    <!-- Universal PWA Modal -->
    <x-pwa-modal />

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
