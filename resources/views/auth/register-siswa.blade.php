<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa — Sistem Absensi Sekolah</title>
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

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeSlideIn .4s ease forwards;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 py-10">

    <div class="glass rounded-3xl shadow-2xl p-8 w-full max-w-md fade-in">

        {{-- Header --}}
        <div class="text-center mb-6">
            <h1 class="text-2xl font-extrabold text-white drop-shadow-sm">Daftar Akun Siswa</h1>
            <p class="text-green-50/90 text-sm mt-1">Lengkapi data di bawah ini untuk mendaftar</p>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="bg-red-500/20 border border-red-300/50 text-red-50 text-sm rounded-xl p-3 mb-4 backdrop-blur">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('register.siswa') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 placeholder-gray-500 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required
                       placeholder="cth: budi_santoso"
                       class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 placeholder-gray-500 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 placeholder-gray-500 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">NISN</label>
                <input type="text" name="nisn" value="{{ old('nisn') }}" required maxlength="10"
                       placeholder="10 digit angka"
                       class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 placeholder-gray-500 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">Password</label>
                <input type="password" name="password" required
                       class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                <p class="text-xs text-white/60 mt-1">Min. 8 karakter, kombinasi huruf besar/kecil, angka &amp; simbol.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                       class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
            </div>

            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 active:scale-[0.98] text-white font-semibold py-3 rounded-xl transition-all duration-200 shadow-lg shadow-green-900/20">
                Daftar Sekarang
            </button>
        </form>

        <p class="text-center text-sm text-white/80 mt-5">
            Sudah punya akun?
            <a href="{{ route('choose-role') }}" class="text-white font-semibold hover:underline">Masuk di sini</a>
        </p>

    </div>
</body>
</html>