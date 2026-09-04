<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa — Presensi Sekolah</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-image:
                linear-gradient(rgba(6, 78, 59, 0.55), rgba(6, 95, 70, 0.55)),
                url('{{ asset('images/bg-sekolah.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Inter', sans-serif;
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
        .glass-input:focus { background: rgba(255, 255, 255, 0.85); }
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeSlideIn .4s ease forwards; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 py-10">

    <div class="glass rounded-3xl shadow-2xl p-8 w-full max-w-md fade-in mb-8">

        {{-- Header --}}
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-10 h-10 object-contain">
            </div>
            <h1 class="text-2xl font-extrabold text-white drop-shadow-sm">Login Siswa</h1>
            <p class="text-green-50/80 text-sm mt-1">Masuk dengan Username, Email, atau NISN</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/20 border border-red-300/50 text-red-50 text-sm rounded-xl p-3 mb-4">
                @foreach ($errors->all() as $error)
                    <p><i class="fas fa-exclamation-circle mr-1"></i>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="bg-emerald-500/20 border border-emerald-300/50 text-emerald-50 text-sm rounded-xl p-3 mb-4">
                <i class="fas fa-check-circle mr-1"></i>{{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.siswa') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">Username / Email / NISN</label>
                <div class="relative">
                    <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                           class="glass-input w-full rounded-xl px-4 py-2.5 pl-11 text-gray-800 placeholder-gray-500 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                    <i class="fas fa-user absolute left-3.5 top-3 text-gray-500"></i>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           class="glass-input w-full rounded-xl px-4 py-2.5 pl-11 pr-11 text-gray-800 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                    <i class="fas fa-lock absolute left-3.5 top-3 text-gray-500"></i>
                    <button type="button" onclick="togglePass()" class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-800 transition p-0.5">
                        <i id="eye-icon" class="far fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-white/80 cursor-pointer select-none">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                    Ingat saya
                </label>
            </div>

            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 active:scale-[0.98] text-white font-semibold py-3 rounded-xl transition-all duration-200 shadow-lg shadow-green-900/20 flex items-center justify-center gap-2">
                <i class="fas fa-right-to-bracket"></i> Masuk
            </button>
        </form>

        <div class="flex items-center my-5">
            <div class="flex-1 h-px bg-white/20"></div>
            <span class="px-3 text-white/40 text-xs">atau</span>
            <div class="flex-1 h-px bg-white/20"></div>
        </div>

        <a href="{{ route('register.siswa') }}"
           class="w-full block text-center border border-white/30 text-white/80 hover:bg-white/10 font-medium py-2.5 rounded-xl transition text-sm">
            <i class="fas fa-user-plus mr-2"></i>Belum punya akun? Daftar
        </a>

        <p class="text-center text-sm mt-4">
            <a href="{{ route('choose-role') }}" class="text-white/50 hover:text-white/80 transition">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </p>
    </div>

    <footer class="mt-auto pt-4 text-center text-xs text-white/60">
        &copy; {{ date('Y') }} Presensi Sekolah &mdash; <span class="font-semibold text-white/80">Dimas A.F</span>
    </footer>

    <script>
        function togglePass() {
            const inp = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.className = 'far fa-eye-slash';
            } else {
                inp.type = 'password';
                icon.className = 'far fa-eye';
            }
        }
    </script>
</body>
</html>