<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Guru</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        
        .split-bg {
            background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 58, 138, 0.85) 100%), url('{{ asset('images/bg-sekolah.jpg') }}');
            background-size: cover;
            background-position: center;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus-within {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .animate-float { animation: float 6s ease-in-out infinite; }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-slide-in { animation: slideInRight 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>
<body class="antialiased overflow-hidden selection:bg-blue-500 selection:text-white" x-data="{ showPass: false, focused: '' }">

    <div class="min-h-screen flex">
        
        {{-- Left Side: Visual/Branding --}}
        <div class="hidden lg:flex lg:w-1/2 split-bg relative items-center justify-center p-12 overflow-hidden">
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 0s;"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 2s;"></div>
            
            <div class="relative z-10 glass-panel p-10 rounded-[2rem] max-w-lg text-white shadow-2xl animate-float">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                    <i class="fas fa-chalkboard-teacher text-3xl text-blue-100"></i>
                </div>
                <h1 class="text-4xl font-extrabold mb-4 leading-tight">Portal Guru <br> Profesional.</h1>
                <p class="text-blue-50 text-lg leading-relaxed opacity-90">
                    Kelola sesi kelas, pantau kehadiran siswa secara real-time, dan ekspor laporan absensi dengan mudah.
                </p>
                <div class="mt-8 border-t border-white/20 pt-6">
                    <p class="text-sm text-blue-100 font-medium"><i class="fas fa-shield-alt mr-2"></i>Akses Terbatas untuk Staf Pendidik</p>
                </div>
            </div>
        </div>

        {{-- Right Side: Form --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-white relative">
            <div class="w-full max-w-md animate-slide-in">
                
                {{-- Mobile Logo --}}
                <div class="lg:hidden text-center mb-8">
                    <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-500/30">
                        <i class="fas fa-chalkboard-teacher text-2xl text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800">Portal Guru</h1>
                </div>

                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang 👋</h2>
                    <p class="text-slate-500 mt-2">Silakan masuk menggunakan NIK Anda.</p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl mb-6 flex items-start gap-3 animate-slide-in">
                        <i class="fas fa-exclamation-circle mt-0.5"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p class="text-sm font-medium">{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.guru') }}" class="space-y-5">
                    @csrf

                    <div class="input-field group" :class="focused === 'nik' ? 'ring-2 ring-blue-100 rounded-xl' : ''">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5 transition-colors group-focus-within:text-blue-600">Nomor Induk Kependudukan (NIK)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                            <input type="text" name="nik" value="{{ old('nik') }}" required autofocus
                                   @focus="focused = 'nik'" @blur="focused = ''"
                                   class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all duration-200" 
                                   placeholder="Masukkan NIK terdaftar">
                        </div>
                    </div>

                    <div class="input-field group" :class="focused === 'password' ? 'ring-2 ring-blue-100 rounded-xl' : ''">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-slate-700 transition-colors group-focus-within:text-blue-600">Password</label>
                            <a href="#" class="text-xs font-medium text-blue-600 hover:text-blue-700 hover:underline">Butuh bantuan?</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                            <input :type="showPass ? 'text' : 'password'" name="password" required
                                   @focus="focused = 'password'" @blur="focused = ''"
                                   class="block w-full pl-11 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all duration-200"
                                   placeholder="••••••••">
                            <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-blue-600 transition-colors focus:outline-none">
                                <i class="fas fa-fw" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="remember" type="checkbox" name="remember" class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer transition-colors">
                        <label for="remember" class="ml-2 text-sm font-medium text-slate-600 cursor-pointer select-none">Ingat saya di perangkat ini</label>
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3.5 rounded-xl transition-all duration-300 transform active:scale-[0.98] shadow-lg hover:shadow-blue-500/25 flex items-center justify-center gap-2 group mt-2">
                        <span>Masuk ke Sistem</span>
                        <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>
                
                <div class="mt-8 text-center text-sm font-medium text-slate-500 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <i class="fas fa-info-circle text-blue-500 mr-1"></i> Akun Guru baru hanya dapat dibuat oleh Admin.
                </div>

                <div class="mt-6 flex justify-center">
                    <a href="{{ route('choose-role') }}" class="text-center text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Halaman Utama
                    </a>
                </div>
                
                <p class="text-center text-xs text-slate-400 mt-10">
                    &copy; {{ date('Y') }} Presensi Sekolah. All rights reserved.
                </p>

            </div>
        </div>
    </div>

    <x-page-loader />
</body>
</html>