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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        
        .split-bg {
            background-image: linear-gradient(135deg, rgba(6, 78, 59, 0.85) 0%, rgba(15, 23, 42, 0.9) 100%), url('{{ asset('images/bg-sekolah.jpg') }}');
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
<body class="antialiased overflow-hidden selection:bg-teal-500 selection:text-white" x-data="{ showPass: false, focused: '' }">

    <div class="min-h-screen flex">
        
        {{-- Left Side: Visual/Branding (Hidden on mobile) --}}
        <div class="hidden lg:flex lg:w-1/2 split-bg relative items-center justify-center p-12 overflow-hidden">
            <!-- Decorative Blobs -->
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-teal-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 0s;"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 2s;"></div>
            
            <div class="relative z-10 glass-panel p-10 rounded-[2rem] max-w-lg text-white shadow-2xl animate-float">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                    <i class="fas fa-school text-3xl text-teal-100"></i>
                </div>
                <h1 class="text-4xl font-extrabold mb-4 leading-tight">Portal Presensi <br> Masa Depan.</h1>
                <p class="text-teal-50 text-lg leading-relaxed opacity-90">
                    Akses dashboard siswa, kelola kehadiran, dan rasakan pengalaman presensi wajah berteknologi tinggi langsung dari genggamanmu.
                </p>
                
                <div class="mt-8 flex items-center gap-4">
                    <div class="flex -space-x-3">
                        <img class="w-10 h-10 rounded-full border-2 border-teal-800" src="https://ui-avatars.com/api/?name=Siswa+1&background=0D8ABC&color=fff" alt="User">
                        <img class="w-10 h-10 rounded-full border-2 border-teal-800" src="https://ui-avatars.com/api/?name=Siswa+2&background=10B981&color=fff" alt="User">
                        <img class="w-10 h-10 rounded-full border-2 border-teal-800" src="https://ui-avatars.com/api/?name=Siswa+3&background=F59E0B&color=fff" alt="User">
                    </div>
                    <p class="text-sm text-teal-100"><span class="font-bold">+1000</span> siswa telah bergabung</p>
                </div>
            </div>
        </div>

        {{-- Right Side: Form --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-white relative">
            <div class="w-full max-w-md animate-slide-in">
                
                {{-- Mobile Logo --}}
                <div class="lg:hidden text-center mb-8">
                    <div class="w-14 h-14 bg-teal-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-teal-500/30">
                        <i class="fas fa-school text-2xl text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-800">Presensi Sekolah</h1>
                </div>

                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang! 👋</h2>
                    <p class="text-slate-500 mt-2">Silakan masuk ke akun siswa kamu.</p>
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

                @if(session('success'))
                    <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 p-4 rounded-r-xl mb-6 flex items-start gap-3 animate-slide-in">
                        <i class="fas fa-check-circle mt-0.5"></i>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.siswa') }}" class="space-y-5">
                    @csrf

                    <div class="input-field group" :class="focused === 'identifier' ? 'ring-2 ring-teal-100 rounded-xl' : ''">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5 transition-colors group-focus-within:text-teal-600">Username / Email / NISN</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-slate-400 group-focus-within:text-teal-500 transition-colors"></i>
                            </div>
                            <input type="text" name="identifier" value="{{ old('identifier') }}" required autofocus
                                   @focus="focused = 'identifier'" @blur="focused = ''"
                                   class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-200" 
                                   placeholder="Masukkan username kamu">
                        </div>
                    </div>

                    <div class="input-field group" :class="focused === 'password' ? 'ring-2 ring-teal-100 rounded-xl' : ''">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-semibold text-slate-700 transition-colors group-focus-within:text-teal-600">Password</label>
                            <a href="#" class="text-xs font-medium text-teal-600 hover:text-teal-700 hover:underline">Lupa password?</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-400 group-focus-within:text-teal-500 transition-colors"></i>
                            </div>
                            <input :type="showPass ? 'text' : 'password'" name="password" required
                                   @focus="focused = 'password'" @blur="focused = ''"
                                   class="block w-full pl-11 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-200"
                                   placeholder="••••••••">
                            <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-teal-600 transition-colors focus:outline-none">
                                <i class="fas fa-fw" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="remember" type="checkbox" name="remember" class="w-4 h-4 text-teal-600 bg-slate-100 border-slate-300 rounded focus:ring-teal-500 focus:ring-2 cursor-pointer transition-colors">
                        <label for="remember" class="ml-2 text-sm font-medium text-slate-600 cursor-pointer select-none">Ingat sesi saya</label>
                    </div>

                    <button type="submit"
                            class="w-full bg-slate-900 hover:bg-teal-600 text-white font-semibold py-3.5 rounded-xl transition-all duration-300 transform active:scale-[0.98] shadow-lg hover:shadow-teal-500/25 flex items-center justify-center gap-2 group mt-2">
                        <span>Masuk ke Dashboard</span>
                        <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>

                <div class="mt-8 relative">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm font-medium leading-6">
                        <span class="bg-white px-6 text-slate-500">Siswa Baru?</span>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-3">
                    <a href="{{ route('register.siswa') }}"
                       class="w-full flex items-center justify-center gap-2 bg-white border-2 border-slate-200 hover:border-teal-500 hover:bg-teal-50 text-slate-700 hover:text-teal-700 font-semibold py-3 rounded-xl transition-all duration-200">
                        <i class="fas fa-user-plus"></i> Daftar Akun Siswa
                    </a>
                    
                    <a href="{{ route('choose-role') }}" class="text-center text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors mt-2">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Pilihan Login
                    </a>
                </div>
                
                <p class="text-center text-xs text-slate-400 mt-10">
                    &copy; {{ date('Y') }} Presensi Sekolah. All rights reserved.
                </p>

            </div>
        </div>
    </div>

</body>
</html>