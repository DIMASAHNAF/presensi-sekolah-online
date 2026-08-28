<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 min-h-screen">
    <div class="max-w-3xl mx-auto py-10 px-4">
        <div class="bg-white rounded-2xl shadow p-6 border border-green-100">
            <h1 class="text-xl font-bold text-green-700">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="text-gray-500 mt-1">Selamat datang di dashboard guru.</p>

            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">Logout</button>
            </form>
        </div>
    </div>
</body>
</html>