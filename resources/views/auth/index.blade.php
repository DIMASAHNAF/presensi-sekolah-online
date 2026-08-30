<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Absensi Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-image:
                linear-gradient(rgba(6, 78, 59, 0.55), rgba(6, 95, 70, 0.55)),
                url('{{ asset('images/bg-sekolah.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .glass {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.35);
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(8px);
        }

        .glass-input:focus {
            background: rgba(255, 255, 255, 0.85);
        }

        [x-cloak] { display: none !important; }

        .fade-slide-enter {
            animation: fadeSlideIn .35s ease forwards;
        }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-10" x-data="{ tab: 'siswa' }">

    <div class="glass rounded-3xl shadow-2xl p-8 w-full max-w-md">

        <div class="text-center mb-6">
            <h1 class="text-2xl font-extrabold text-white drop-shadow-sm">ABSENSI SMKN 1 BERINGIN</h1>
            <p class="text-green-50/90 text-sm mt-1">Masuk untuk melanjutkan</p>
        </div>

        {{-- Tab Switch --}}
        <div class="relative flex bg-white/25 backdrop-blur rounded-2xl p-1 mb-6">
            <div
                class="absolute top-1 bottom-1 w-1/2 bg-white rounded-xl shadow transition-all duration-300 ease-out"
                :class="tab === 'siswa' ? 'left-1' : 'left-1/2'"
            ></div>

            <button
                type="button"
                @click="tab = 'siswa'"
                class="relative z-10 w-1/2 py-2.5 text-sm font-semibold rounded-xl transition-colors duration-300"
                :class="tab === 'siswa' ? 'text-green-700' : 'text-white/90'"
            >
                Siswa
            </button>
            <button
                type="button"
                @click="tab = 'guru'"
                class="relative z-10 w-1/2 py-2.5 text-sm font-semibold rounded-xl transition-colors duration-300"
                :class="tab === 'guru' ? 'text-green-700' : 'text-white/90'"
            >
                Guru
            </button>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/20 border border-red-300/50 text-red-50 text-sm rounded-xl p-3 mb-4 backdrop-blur">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- FORM SISWA --}}
        <div x-show="tab === 'siswa'" x-cloak x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0">
            <form method="POST" action="{{ route('login.siswa') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1">Username / Email / NISN</label>
                    <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                           class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 placeholder-gray-500 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                </div>

                <label class="flex items-center gap-2 text-sm text-white/90">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-400">
                    Ingat saya
                </label>

                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 active:scale-[0.98] text-white font-semibold py-3 rounded-xl transition-all duration-200 shadow-lg shadow-green-900/20">
                    Masuk sebagai Siswa
                </button>
            </form>

            <p class="text-center text-sm text-white/80 mt-5">
                Belum punya akun?
                <a href="{{ route('register.siswa') }}" class="text-white font-semibold hover:underline">Daftar di sini</a>
            </p>
        </div>

        {{-- FORM GURU --}}
        <div x-show="tab === 'guru'" x-cloak x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0">
            <form method="POST" action="{{ route('login.guru') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" required
                           class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 placeholder-gray-500 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-white/90 mb-1">Password</label>
                    <input type="password" name="password" required
                           class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                </div>

                <label class="flex items-center gap-2 text-sm text-white/90">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-400">
                    Ingat saya
                </label>

                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 active:scale-[0.98] text-white font-semibold py-3 rounded-xl transition-all duration-200 shadow-lg shadow-green-900/20">
                    Masuk sebagai Guru
                </button>
            </form>

            <p class="text-center text-sm text-white/70 mt-5">
                Akun guru dibuat oleh admin sekolah.
            </p>
        </div>

    </div>
</body>
</html>