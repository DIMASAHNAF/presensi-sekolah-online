<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru — SMKN 1 Beringin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <link rel="stylesheet" href="{{ asset('css/theme-dark.css') }}">
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-green-50 dark:bg-slate-950 min-h-screen text-slate-800 dark:text-slate-100 transition-colors duration-200">
    <div class="max-w-3xl mx-auto py-10 px-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow p-6 border border-green-100 dark:border-slate-800 transition-colors duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1" class="w-10 h-10 object-contain">
                    <div>
                        <h1 class="text-xl font-bold text-green-700 dark:text-green-400">Halo, {{ auth()->user()->name }} 👋</h1>
                        <p class="text-gray-500 dark:text-slate-400 text-sm">Selamat datang di dashboard guru SMKN 1 Beringin.</p>
                    </div>
                </div>
                <x-sky-toggle size="8px" id="guru-sky-toggle" />
            </div>

            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                    Buka Panel Manajemen Presensi &rarr;
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">Logout</button>
                </form>
            </div>
        </div>
    </div>
    <x-page-loader />
</body>
</html>