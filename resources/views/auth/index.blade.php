<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Presensi SMKN 1 Beringin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">

    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#e0e5ec">
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
            background-color: #e0e5ec;
            color: #4a5568;
        }
        [x-cloak] { display: none !important; }

        /* Neumorphism Utilities */
        .neu-flat {
            background-color: #e0e5ec;
            box-shadow: 9px 9px 16px rgb(163,177,198,0.6), -9px -9px 16px rgba(255,255,255, 0.5);
        }
        .neu-pressed {
            background-color: #e0e5ec;
            box-shadow: inset 6px 6px 10px 0 rgba(163, 177, 198, 0.7), inset -6px -6px 10px 0 rgba(255, 255, 255, 0.8);
        }
        .neu-button {
            background-color: #e0e5ec;
            box-shadow: 6px 6px 10px rgb(163,177,198,0.6), -6px -6px 10px rgba(255,255,255, 0.5);
            transition: all 0.2s ease-in-out;
        }
        .neu-button:hover {
            box-shadow: 8px 8px 14px rgb(163,177,198,0.6), -8px -8px 14px rgba(255,255,255, 0.5);
            transform: translateY(-1px);
        }
        .neu-button:active, .neu-button.active {
            box-shadow: inset 4px 4px 8px 0 rgba(163, 177, 198, 0.7), inset -4px -4px 8px 0 rgba(255, 255, 255, 0.8);
            transform: translateY(1px);
        }
        .neu-input {
            background-color: #e0e5ec;
            box-shadow: inset 5px 5px 10px 0 rgba(163, 177, 198, 0.7), inset -5px -5px 10px 0 rgba(255, 255, 255, 0.8);
            transition: all 0.2s ease-in-out;
        }
        .neu-input:focus {
            box-shadow: inset 7px 7px 12px 0 rgba(163, 177, 198, 0.8), inset -7px -7px 12px 0 rgba(255, 255, 255, 0.9);
            outline: none;
        }
        .neu-checkbox {
            appearance: none;
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 0.375rem;
            background-color: #e0e5ec;
            box-shadow: inset 3px 3px 6px 0 rgba(163, 177, 198, 0.7), inset -3px -3px 6px 0 rgba(255, 255, 255, 0.8);
            position: relative;
            cursor: pointer;
        }
        .neu-checkbox:checked::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #3182ce;
            font-size: 0.7rem;
        }
        .neu-checkbox:checked {
             box-shadow: inset 2px 2px 4px 0 rgba(163, 177, 198, 0.7), inset -2px -2px 4px 0 rgba(255, 255, 255, 0.8);
        }
    </style>
</head>
<body class="min-h-screen w-screen flex flex-col items-center justify-center p-4 selection:bg-blue-300 selection:text-blue-900"
      x-data="loginCardApp()">

    <x-page-loader />

    {{-- Main Neumorphic Card --}}
    <div class="w-full max-w-md relative z-10 py-6">
        <div class="neu-flat rounded-[2rem] p-8 sm:p-10">

            <div class="text-center space-y-4 mb-8">
                <div class="flex items-center justify-center gap-4">
                    <div class="neu-flat p-3 rounded-full flex items-center justify-center">
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo SMKN 1 Beringin" class="h-10 w-auto object-contain">
                    </div>
                    <div class="neu-flat p-3 rounded-full flex items-center justify-center">
                        <img src="{{ asset('images/logo-kolaborasi.webp') }}" alt="Logo Kolaborasi" class="h-10 w-auto object-contain">
                    </div>
                </div>

                <div>
                    <h1 class="text-2xl font-bold text-gray-700 tracking-tight mt-4">
                        Portal Presensi
                    </h1>
                    <p class="text-gray-500 text-xs font-semibold uppercase tracking-widest mt-1">
                        SMKN 1 Beringin
                    </p>
                </div>
            </div>

            {{-- Role Tab Switcher (Siswa / Guru) --}}
            <div class="flex gap-4 mb-8">
                <button type="button"
                        @click="tab = 'siswa'"
                        class="w-1/2 py-3 rounded-xl font-semibold flex items-center justify-center gap-2 text-sm transition-all"
                        :class="tab === 'siswa' ? 'neu-pressed text-blue-600' : 'neu-button text-gray-600'">
                    <i class="fas fa-user-graduate"></i>
                    <span>Siswa</span>
                </button>
                <button type="button"
                        @click="tab = 'guru'"
                        class="w-1/2 py-3 rounded-xl font-semibold flex items-center justify-center gap-2 text-sm transition-all"
                        :class="tab === 'guru' ? 'neu-pressed text-blue-600' : 'neu-button text-gray-600'">
                    <i class="fas fa-chalkboard-user"></i>
                    <span>Guru / Staf</span>
                </button>
            </div>

            {{-- Error Alert --}}
            @if ($errors->any())
                <div class="neu-pressed rounded-xl p-4 mb-6 flex items-start gap-3 text-red-500 text-sm">
                    <i class="fas fa-circle-exclamation mt-0.5"></i>
                    <div class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- FORM SISWA --}}
            <div x-show="tab === 'siswa'" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <form method="POST" action="{{ route('login.siswa') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide px-1">Username / Email / NISN</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-gray-400">
                                <i class="fas fa-user text-sm"></i>
                            </span>
                            <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                                   placeholder="Ketik identitas Anda"
                                   class="neu-input w-full rounded-xl pl-11 pr-4 py-3.5 text-sm text-gray-700 placeholder:text-gray-400 border-none">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide px-1">Password</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-gray-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input :type="showPasswordSiswa ? 'text' : 'password'"
                                   name="password" required
                                   placeholder="••••••••"
                                   class="neu-input w-full rounded-xl pl-11 pr-11 py-3.5 text-sm text-gray-700 placeholder:text-gray-400 border-none">
                            <button type="button"
                                    @click="showPasswordSiswa = !showPasswordSiswa"
                                    class="absolute right-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas text-sm" :class="showPasswordSiswa ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-3 text-sm text-gray-600 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="neu-checkbox">
                            <span class="font-medium">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="neu-button w-full text-blue-600 font-bold py-4 rounded-xl mt-6 flex items-center justify-center gap-2 uppercase tracking-wide text-sm">
                        <span>Masuk</span>
                        <i class="fas fa-sign-in-alt"></i>
                    </button>
                </form>

                <p class="text-center text-sm text-gray-500 mt-6 font-medium">
                    Belum punya akun?
                    <a href="{{ route('register.siswa') }}" class="text-blue-600 font-bold hover:underline ml-1">
                        Daftar
                    </a>
                </p>
            </div>

            {{-- FORM GURU --}}
            <div x-show="tab === 'guru'" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0">
                <form method="POST" action="{{ route('login.guru') }}" class="space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide px-1">NIK atau Username</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-gray-400">
                                <i class="fas fa-user-shield text-sm"></i>
                            </span>
                            <input type="text" name="nik" value="{{ old('nik') }}" required
                                   placeholder="16 digit NIK atau username"
                                   class="neu-input w-full rounded-xl pl-11 pr-4 py-3.5 text-sm text-gray-700 placeholder:text-gray-400 border-none">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide px-1">Password</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 text-gray-400">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <input :type="showPasswordGuru ? 'text' : 'password'"
                                   name="password" required
                                   placeholder="••••••••"
                                   class="neu-input w-full rounded-xl pl-11 pr-11 py-3.5 text-sm text-gray-700 placeholder:text-gray-400 border-none">
                            <button type="button"
                                    @click="showPasswordGuru = !showPasswordGuru"
                                    class="absolute right-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <i class="fas text-sm" :class="showPasswordGuru ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-3 text-sm text-gray-600 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="neu-checkbox">
                            <span class="font-medium">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="neu-button w-full text-blue-600 font-bold py-4 rounded-xl mt-6 flex items-center justify-center gap-2 uppercase tracking-wide text-sm">
                        <span>Masuk Guru / Staf</span>
                        <i class="fas fa-sign-in-alt"></i>
                    </button>
                </form>

                <div class="mt-6 p-4 rounded-xl neu-pressed text-center text-xs text-gray-500 font-medium">
                    <i class="fas fa-info-circle mr-1 text-blue-500"></i> Akun dewan guru & staf dibuat oleh Admin Sekolah.
                </div>
            </div>

        </div>
    </div>

    {{-- Footer --}}
    <footer class="mt-6 text-center text-xs text-gray-400 font-medium tracking-wide">
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