<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Presensi SMKN 1 Beringin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffffff">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Presensi">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Tailwind & Alpine --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body, button, input { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }

        /* Modern Subtle Dot Grid Pattern */
        .dot-grid {
            background-image: radial-gradient(rgba(148, 163, 184, 0.25) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        /* Inner Grid Pattern on White Card */
        .inner-grid {
            background-image: linear-gradient(135deg, rgba(226, 232, 240, 0.4) 1px, transparent 1px),
                              linear-gradient(45deg, rgba(226, 232, 240, 0.4) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* Traveling Light Beam Animations (Border Laser - Sleek Silver / Monochrome) */
        @keyframes beamTop {
            0%   { left: -50%; opacity: 0.2; }
            50%  { opacity: 0.9; }
            100% { left: 100%; opacity: 0.2; }
        }
        @keyframes beamRight {
            0%   { top: -50%; opacity: 0.2; }
            50%  { opacity: 0.9; }
            100% { top: 100%; opacity: 0.2; }
        }
        @keyframes beamBottom {
            0%   { right: -50%; opacity: 0.2; }
            50%  { opacity: 0.9; }
            100% { right: 100%; opacity: 0.2; }
        }
        @keyframes beamLeft {
            0%   { bottom: -50%; opacity: 0.2; }
            50%  { opacity: 0.9; }
            100% { bottom: 100%; opacity: 0.2; }
        }

        .animate-beam-top {
            animation: beamTop 2.6s ease-in-out infinite;
        }
        .animate-beam-right {
            animation: beamRight 2.6s ease-in-out 0.65s infinite;
        }
        .animate-beam-bottom {
            animation: beamBottom 2.6s ease-in-out 1.3s infinite;
        }
        .animate-beam-left {
            animation: beamLeft 2.6s ease-in-out 1.95s infinite;
        }

        /* Ambient Glow Pulse */
        @keyframes ambientPulse {
            0%, 100% { transform: scale(1) translate(-50%, 0); opacity: 0.35; }
            50%      { transform: scale(1.05) translate(-50%, 0); opacity: 0.6; }
        }
        .animate-glow-top {
            animation: ambientPulse 8s ease-in-out infinite;
        }

        /* Button Shimmer */
        @keyframes shimmer {
            0%   { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</head>
<body class="min-h-screen w-screen bg-[#F8FAFC] text-slate-800 relative overflow-x-hidden flex flex-col items-center justify-center p-4 selection:bg-blue-900 selection:text-white"
      x-data="loginCardApp()">

    <x-page-loader />

    {{-- Clean Ambient Background Layers --}}
    <div class="absolute inset-0 bg-gradient-to-b from-white via-slate-50 to-slate-100 pointer-events-none"></div>
    <div class="absolute inset-0 dot-grid opacity-60 pointer-events-none"></div>

    {{-- Top & Ambient Soft Spotlight Glows --}}
    <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-[100vw] h-[45vh] rounded-b-[50%] bg-gradient-to-b from-blue-100/40 to-transparent blur-[90px] pointer-events-none animate-glow-top"></div>
    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-[85vw] h-[35vh] rounded-t-full bg-slate-200/30 blur-[100px] pointer-events-none"></div>

    {{-- Main 3D Card Container --}}
    <div class="w-full max-w-md relative z-10 py-6"
         style="perspective: 1500px;">

        <div x-ref="card"
             @mousemove="handleMouseMove($event)"
             @mouseleave="handleMouseLeave()"
             :style="'transform: perspective(1500px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg); transition: transform ' + (rotateX === 0 ? '0.4s ease-out' : '0.08s ease-out') + ';'"
             class="relative group transform-gpu">

            {{-- Outer Card Soft Glow Effect on Hover --}}
            <div class="absolute -inset-[1px] rounded-xl opacity-40 group-hover:opacity-80 transition-opacity duration-500 pointer-events-none blur-xs bg-gradient-to-r from-blue-200 via-slate-300 to-blue-200"></div>

            {{-- Dominant Pure White Card Body --}}
            <div class="relative bg-white/95 backdrop-blur-2xl rounded-xl p-6 sm:p-8 border border-slate-200 shadow-[0_12px_36px_-10px_rgba(15,23,42,0.08),0_1px_2px_0_rgba(15,23,42,0.04)] overflow-hidden">

                {{-- Inner Geometric Pattern --}}
                <div class="absolute inset-0 inner-grid opacity-40 pointer-events-none"></div>

                {{-- Header & Logo --}}
                <div class="text-center space-y-2 mb-6 relative z-10">
                    <div class="mx-auto w-14 h-14 rounded-xl border border-slate-200 bg-white p-2 flex items-center justify-center relative shadow-xs">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1 Beringin" class="w-full h-full object-contain">
                    </div>

                    <div>
                        <h1 class="text-xl sm:text-2xl font-heading font-extrabold text-slate-900 tracking-tight">
                            PORTAL PRESENSI
                        </h1>
                        <p class="text-slate-500 text-[11px] font-bold uppercase tracking-widest mt-0.5">
                            SMKN 1 BERINGIN
                        </p>
                    </div>
                </div>

                {{-- Role Tab Switcher (Siswa / Guru) --}}
                <div class="relative flex bg-slate-100 border border-slate-200 rounded-lg p-1 mb-5 z-10">
                    <div class="absolute top-1 bottom-1 w-[calc(50%-4px)] bg-white rounded-md shadow-xs border border-slate-200/80 transition-all duration-300 ease-out"
                         :class="tab === 'siswa' ? 'left-1' : 'left-[calc(50%+2px)]'"></div>

                    <button type="button"
                            @click="tab = 'siswa'"
                            class="relative z-10 w-1/2 py-2 text-xs font-semibold rounded-md transition-colors duration-200 flex items-center justify-center gap-1.5"
                            :class="tab === 'siswa' ? 'text-blue-900 font-bold' : 'text-slate-500 hover:text-slate-800'">
                        <i class="fas fa-user-graduate text-xs"></i>
                        <span>Siswa</span>
                    </button>
                    <button type="button"
                            @click="tab = 'guru'"
                            class="relative z-10 w-1/2 py-2 text-xs font-semibold rounded-md transition-colors duration-200 flex items-center justify-center gap-1.5"
                            :class="tab === 'guru' ? 'text-blue-900 font-bold' : 'text-slate-500 hover:text-slate-800'">
                        <i class="fas fa-chalkboard-user text-xs"></i>
                        <span>Guru / Staf</span>
                    </button>
                </div>

                {{-- Error Alert --}}
                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-2xl p-3.5 mb-5 flex items-start gap-2.5 relative z-10 animate-shake">
                        <i class="fas fa-circle-exclamation text-rose-500 mt-0.5 text-sm shrink-0"></i>
                        <div class="space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- FORM SISWA --}}
                <div x-show="tab === 'siswa'" x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-x-3"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="relative z-10">
                    <form method="POST" action="{{ route('login.siswa') }}" class="space-y-4">
                        @csrf

                        {{-- Identifier Input --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700">Username / Email / NISN</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-sm transition-colors duration-200"
                                      :class="focusedInput === 'siswa-id' ? 'text-slate-900' : 'text-slate-400'">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                                       placeholder="Ketik username, email, atau NISN"
                                       @focus="focusedInput = 'siswa-id'"
                                       @blur="focusedInput = null"
                                       class="w-full bg-slate-50/70 border border-slate-200/90 focus:border-slate-900 focus:bg-white text-slate-900 placeholder:text-slate-400 text-sm rounded-xl pl-10 pr-4 py-3 outline-none focus:ring-2 focus:ring-slate-900/10 transition-all duration-200">
                            </div>
                        </div>

                        {{-- Password Input --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700">Password</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-sm transition-colors duration-200"
                                      :class="focusedInput === 'siswa-pass' ? 'text-slate-900' : 'text-slate-400'">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input :type="showPasswordSiswa ? 'text' : 'password'"
                                       name="password" required
                                       placeholder="••••••••"
                                       @focus="focusedInput = 'siswa-pass'"
                                       @blur="focusedInput = null"
                                       class="w-full bg-slate-50/70 border border-slate-200/90 focus:border-slate-900 focus:bg-white text-slate-900 placeholder:text-slate-400 text-sm rounded-xl pl-10 pr-11 py-3 outline-none focus:ring-2 focus:ring-slate-900/10 transition-all duration-200">
                                <button type="button"
                                        @click="showPasswordSiswa = !showPasswordSiswa"
                                        class="absolute right-3.5 text-slate-400 hover:text-slate-700 transition-colors text-sm p-1">
                                    <i class="fas" :class="showPasswordSiswa ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 text-xs text-slate-600 hover:text-slate-900 cursor-pointer select-none">
                                <input type="checkbox" name="remember"
                                       class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20 focus:ring-offset-0 transition">
                                <span>Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                                class="w-full relative group/btn overflow-hidden bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-lg shadow-sm active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-wider mt-5">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:animate-[shimmer_1.2s_infinite] pointer-events-none"></div>
                            <span>Masuk sebagai Siswa</span>
                            <i class="fas fa-arrow-right text-[10px] transition-transform duration-300 group-hover/btn:translate-x-1"></i>
                        </button>
                    </form>

                    {{-- Register Link --}}
                    <p class="text-center text-xs text-slate-500 mt-4">
                        Belum punya akun siswa?
                        <a href="{{ route('register.siswa') }}"
                           class="text-blue-700 hover:text-blue-800 font-bold underline underline-offset-4 transition-colors">
                            Daftar mandiri di sini
                        </a>
                    </p>
                </div>

                {{-- FORM GURU --}}
                <div x-show="tab === 'guru'" x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-3"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="relative z-10">
                    <form method="POST" action="{{ route('login.guru') }}" class="space-y-4">
                        @csrf

                        {{-- NIK Input --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700">Nomor Induk Kependudukan (NIK)</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-sm transition-colors duration-200"
                                      :class="focusedInput === 'guru-nik' ? 'text-blue-700' : 'text-slate-400'">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <input type="text" name="nik" value="{{ old('nik') }}" required
                                       placeholder="16 digit NIK"
                                       @focus="focusedInput = 'guru-nik'"
                                       @blur="focusedInput = null"
                                       class="w-full bg-slate-50/70 border border-slate-200 focus:border-blue-700 focus:bg-white text-slate-900 placeholder:text-slate-400 text-sm rounded-lg pl-10 pr-4 py-2.5 outline-none focus:ring-2 focus:ring-blue-700/15 transition-all duration-200">
                            </div>
                        </div>

                        {{-- Password Input --}}
                        <div class="space-y-1.5">
                            <label class="block text-xs font-semibold text-slate-700">Password</label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-sm transition-colors duration-200"
                                      :class="focusedInput === 'guru-pass' ? 'text-blue-700' : 'text-slate-400'">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input :type="showPasswordGuru ? 'text' : 'password'"
                                       name="password" required
                                       placeholder="••••••••"
                                       @focus="focusedInput = 'guru-pass'"
                                       @blur="focusedInput = null"
                                       class="w-full bg-slate-50/70 border border-slate-200 focus:border-blue-700 focus:bg-white text-slate-900 placeholder:text-slate-400 text-sm rounded-lg pl-10 pr-11 py-2.5 outline-none focus:ring-2 focus:ring-blue-700/15 transition-all duration-200">
                                <button type="button"
                                        @click="showPasswordGuru = !showPasswordGuru"
                                        class="absolute right-3.5 text-slate-400 hover:text-slate-700 transition-colors text-sm p-1">
                                    <i class="fas" :class="showPasswordGuru ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 text-xs text-slate-600 hover:text-slate-900 cursor-pointer select-none">
                                <input type="checkbox" name="remember"
                                       class="w-4 h-4 rounded border-slate-300 text-blue-700 focus:ring-blue-700/20 focus:ring-offset-0 transition">
                                <span>Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit"
                                class="w-full relative group/btn overflow-hidden bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-lg shadow-sm active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-xs uppercase tracking-wider mt-5">
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:animate-[shimmer_1.2s_infinite] pointer-events-none"></div>
                            <span>Masuk sebagai Guru / Staf</span>
                            <i class="fas fa-arrow-right text-[10px] transition-transform duration-300 group-hover/btn:translate-x-1"></i>
                        </button>
                    </form>

                    <div class="mt-4 p-3 rounded-lg bg-slate-50 border border-slate-200 text-center text-xs text-slate-500">
                        <i class="fas fa-info-circle mr-1 text-blue-600"></i> Akun dewan guru & staf dibuat oleh Admin Sekolah.
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="mt-4 text-center text-xs text-slate-400 font-mono relative z-10">
        Sistem Presensi Biometrik &copy; 2026 SMKN 1 BERINGIN
    </footer>

    <script>
        function loginCardApp() {
            return {
                tab: '{{ request("tab", $initialTab ?? (request()->is("*guru*") ? "guru" : "siswa")) }}',
                showPasswordSiswa: false,
                showPasswordGuru: false,
                focusedInput: null,
                rotateX: 0,
                rotateY: 0,

                handleMouseMove(e) {
                    // Only apply 3D tilt on devices with mouse pointer (non-touch)
                    if (window.innerWidth < 768) return;

                    const rect = this.$refs.card.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;

                    // Calculate tilt angles (limits: -8 to +8 deg)
                    this.rotateX = (y / (rect.height / 2)) * -8;
                    this.rotateY = (x / (rect.width / 2)) * 8;
                },

                handleMouseLeave() {
                    this.rotateX = 0;
                    this.rotateY = 0;
                }
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