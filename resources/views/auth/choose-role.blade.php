<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi Sekolah - Masuk</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md border border-green-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-green-700">Sistem Absensi Sekolah</h1>
            <p class="text-gray-500 mt-1">Pilih peran kamu untuk melanjutkan</p>
        </div>

        <div class="space-y-4">
            <a href="{{ route('login.siswa') }}"
               class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition">
                Masuk sebagai Siswa
            </a>
            <a href="{{ route('login.guru') }}"
               class="block w-full text-center bg-white border-2 border-green-600 hover:bg-green-50 text-green-700 font-semibold py-3 rounded-xl transition">
                Masuk sebagai Guru
            </a>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun siswa?
            <a href="{{ route('register.siswa') }}" class="text-green-600 font-medium hover:underline">Daftar di sini</a>
        </p>
    </div>
</body>
</html>