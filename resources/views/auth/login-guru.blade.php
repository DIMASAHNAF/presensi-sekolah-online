<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-green-50 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md border border-green-100">
        <h1 class="text-xl font-bold text-green-700 mb-1">Login Guru</h1>
        <p class="text-gray-500 text-sm mb-6">Gunakan NIK yang terdaftar</p>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg p-3 mb-4">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.guru') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                <input type="text" name="nik" value="{{ old('nik') }}" required autofocus
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                Ingat saya
            </label>

            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-400 mt-6">
            Akun guru dibuat oleh admin sekolah.
        </p>
        <p class="text-center text-sm mt-2">
            <a href="{{ route('choose-role') }}" class="text-gray-400 hover:underline">&larr; Kembali</a>
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInputs = document.querySelectorAll('input[type="password"]');
            passwordInputs.forEach(input => {
                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';
                input.parentNode.insertBefore(wrapper, input);
                wrapper.appendChild(input);

                const toggleBtn = document.createElement('span');
                toggleBtn.innerHTML = '<i class="far fa-eye text-gray-500"></i>';
                toggleBtn.style.position = 'absolute';
                toggleBtn.style.right = '12px';
                toggleBtn.style.top = '50%';
                toggleBtn.style.transform = 'translateY(-50%)';
                toggleBtn.style.cursor = 'pointer';
                wrapper.appendChild(toggleBtn);

                toggleBtn.addEventListener('click', function() {
                    if (input.type === 'password') {
                        input.type = 'text';
                        toggleBtn.innerHTML = '<i class="far fa-eye-slash text-gray-800"></i>';
                    } else {
                        input.type = 'password';
                        toggleBtn.innerHTML = '<i class="far fa-eye text-gray-500"></i>';
                    }
                });
            });
        });
    </script>
</body>
</html>