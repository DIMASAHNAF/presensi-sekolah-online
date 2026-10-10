<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa — Sistem Presensi Sekolah</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        
        .split-bg {
            background-image: linear-gradient(135deg, rgba(6, 78, 59, 0.85) 0%, rgba(15, 23, 42, 0.9) 100%), url('{{ asset('images/bg-sekolah.webp') }}');
            background-size: cover;
            background-position: center;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
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

        [x-cloak] { display: none !important; }

        /* Neumorphism Inputs from uiverse.io */
        .neu-input {
            border: none;
            padding: 1rem 1.25rem;
            border-radius: 1rem;
            background: #e8e8e8;
            box-shadow: inset 6px 6px 12px #c5c5c5, inset -6px -6px 12px #ffffff;
            transition: 0.3s;
            color: #333;
            width: 100%;
        }
        .neu-input:focus {
            outline: none;
            box-shadow: inset 8px 8px 16px #c5c5c5, inset -8px -8px 16px #ffffff;
        }

        .neu-btn {
            border: none;
            padding: 1rem;
            border-radius: 1rem;
            background: #e8e8e8;
            box-shadow: 6px 6px 12px #c5c5c5, -6px -6px 12px #ffffff;
            transition: 0.3s;
            color: #0f766e;
            font-weight: 800;
            width: 100%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .neu-btn:hover:not(:disabled) {
            box-shadow: 8px 8px 16px #c5c5c5, -8px -8px 16px #ffffff;
            transform: translateY(-1px);
        }
        .neu-btn:active:not(:disabled) {
            box-shadow: inset 6px 6px 12px #c5c5c5, inset -6px -6px 12px #ffffff;
            transform: translateY(1px);
        }

        .neu-card {
            background: #e8e8e8;
            box-shadow: 9px 9px 18px #c5c5c5, -9px -9px 18px #ffffff;
            border-radius: 1.5rem;
        }

        /* Face capture overlay */
        .face-ring {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -55%);
            width: 210px; height: 260px;
            border-radius: 50% / 45%;
            border: 3px solid rgba(255,255,255,0.4);
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.45);
            pointer-events: none;
            z-index: 10;
            transition: border-color .3s;
        }
        .face-ring.detected { border-color: #4ade80; box-shadow: 0 0 0 9999px rgba(0,0,0,0.45), 0 0 20px rgba(74,222,128,0.5); }
        .face-ring.capturing { border-color: #facc15; box-shadow: 0 0 0 9999px rgba(0,0,0,0.45), 0 0 24px rgba(250,204,21,0.7); }
        .face-ring .scanner-laser {
            position: absolute;
            left: 5%;
            right: 5%;
            height: 2.5px;
            background: linear-gradient(90deg, transparent, #38bdf8, #60a5fa, #38bdf8, transparent);
            box-shadow: 0 0 12px #38bdf8, 0 0 22px #60a5fa;
            border-radius: 50%;
            animation: laser-scan 1.8s ease-in-out infinite alternate;
        }
        @keyframes laser-scan {
            0% { top: 12%; opacity: 0.25; }
            50% { opacity: 1; }
            100% { top: 88%; opacity: 0.25; }
        }

        .step-dot { width:12px; height:12px; border-radius:50%; background:#e8e8e8; box-shadow: inset 2px 2px 5px #c5c5c5, inset -2px -2px 5px #ffffff; transition: all .3s; }
        .step-dot.active { background:#e8e8e8; box-shadow: 3px 3px 6px #c5c5c5, -3px -3px 6px #ffffff; width:32px; border-radius:8px; }
        .step-dot.done   { background:#0f766e; box-shadow: none; }

        #video-reg { width:100%; height:100%; object-fit:cover; transform: scaleX(-1); }

    </style>
</head>
<body class="antialiased min-h-screen bg-slate-50 selection:bg-teal-500 selection:text-white"
      x-data="registerApp()" x-init="checkInitialStep()">

    <div class="min-h-screen flex flex-col lg:flex-row">
        
        {{-- Left Side: Visual/Branding (Hidden on mobile) --}}
        <div class="hidden lg:flex lg:w-1/2 split-bg relative items-center justify-center p-12 overflow-hidden">
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-teal-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 0s;"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-emerald-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-float" style="animation-delay: 2s;"></div>
            
            <div class="relative z-10 glass-panel p-10 rounded-[2rem] max-w-lg text-white shadow-2xl animate-float">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                    <i class="fas fa-user-plus text-3xl text-teal-100"></i>
                </div>
                <h1 class="text-4xl font-extrabold mb-4 leading-tight">Bergabung <br> Sekarang.</h1>
                <p class="text-teal-50 text-lg leading-relaxed opacity-90">
                    Daftarkan akunmu dan integrasikan wajahmu dengan sistem presensi berteknologi tinggi kami.
                </p>
                <div class="mt-8">
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-check-circle text-teal-300"></i>
                        <span>Proses pendaftaran cepat</span>
                    </div>
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-check-circle text-teal-300"></i>
                        <span>Keamanan data wajah terenkripsi</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-teal-300"></i>
                        <span>Terintegrasi dengan seluruh kelas</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Side: Form --}}
        <div id="form-scroll-container" class="w-full lg:w-1/2 flex flex-col items-center justify-start p-6 sm:p-10 lg:p-12 bg-[#e8e8e8] relative overflow-y-auto min-h-screen lg:h-screen">
            <div class="w-full max-w-md animate-slide-in my-auto py-6 sm:py-8">
                
                {{-- Mobile Logo --}}
                <div class="lg:hidden text-center mb-6">
                    <div class="w-14 h-14 bg-[#e8e8e8] shadow-[5px_5px_10px_#c5c5c5,-5px_-5px_10px_#ffffff] rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-user-plus text-2xl text-teal-600"></i>
                    </div>
                </div>

                {{-- STEP INDICATOR --}}
                <div class="flex items-center justify-center gap-3 mb-8">
                    <div class="step-dot" :class="step >= 1 ? (step > 1 ? 'done' : 'active') : ''"></div>
                    <div class="h-0.5 w-10 bg-slate-300 rounded"></div>
                    <div class="step-dot" :class="step >= 2 ? 'active' : ''"></div>
                </div>

                {{-- STEP 1: FORM DATA --}}
                <div x-show="step === 1" x-cloak class="w-full">
                    
                    <div class="text-center lg:text-left mb-6">
                        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">Daftar Akun Baru</h2>
                        <p class="text-slate-500 mt-1 text-sm font-medium">Langkah 1 dari 2 &mdash; Isi data dirimu dengan benar.</p>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-xl mb-6 shadow-xs">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-circle-exclamation text-red-500 mt-0.5 text-base shrink-0"></i>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-red-800 mb-1">Periksa Kembali Data Pendaftaran</h4>
                                    <ul class="list-disc list-inside space-y-1 text-xs font-medium text-red-700">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form id="form-step1" method="POST" action="{{ route('register.siswa') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5 ml-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                   class="neu-input placeholder-slate-400">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5 ml-1">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" required
                                   placeholder="cth: budi_santoso"
                                   class="neu-input placeholder-slate-400">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5 ml-1">Email <span class="opacity-60 text-xs">(Opsional)</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="neu-input placeholder-slate-400">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5 ml-1">NISN</label>
                            <input type="text" name="nisn" value="{{ old('nisn') }}" required maxlength="10"
                                   placeholder="10 digit angka"
                                   class="neu-input placeholder-slate-400">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5 ml-1">Kelas</label>
                            <select name="kelas_id" required
                                    class="neu-input cursor-pointer appearance-none">
                                <option value="" disabled {{ old('kelas_id') ? '' : 'selected' }}>-- Pilih Kelas --</option>
                                @foreach ($kelas as $item)
                                    <option value="{{ $item->id }}" {{ old('kelas_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5 ml-1">Password</label>
                            <div class="relative">
                                <input type="password" id="pass1" name="password" required
                                       class="neu-input pr-12">
                                <button type="button" onclick="togglePass('pass1', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-teal-600 focus:outline-none">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-600 mb-1.5 ml-1">Konfirmasi Password</label>
                            <div class="relative">
                                <input type="password" id="pass2" name="password_confirmation" required
                                       class="neu-input pr-12">
                                <button type="button" onclick="togglePass('pass2', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-teal-600 focus:outline-none">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="neu-btn mt-4 group">
                                <span>Lanjut Scan Wajah</span>
                                <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </div>
                    </form>

                    <div class="mt-10 relative">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-slate-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm font-medium leading-6">
                            <span class="bg-[#e8e8e8] px-4 text-slate-500">Sudah punya akun?</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('choose-role') }}"
                           class="neu-btn text-slate-600 hover:text-teal-700 text-sm">
                            Masuk di sini
                        </a>
                    </div>
                </div>

                {{-- STEP 2: SCAN WAJAH REAL-TIME --}}
                <div x-show="step === 2" x-cloak class="w-full">
                    
                    <div class="text-center lg:text-left mb-5">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-semibold mb-2 shadow-[inset_2px_2px_4px_#c5c5c5,inset_-2px_-2px_4px_#ffffff]">
                            <i class="fas fa-shield-halved"></i> Verifikasi Biometrik AI
                        </div>
                        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">Perekaman Wajah Real-Time</h2>
                        <p class="text-slate-500 text-sm mt-1">AI akan mendeteksi dan mengambil 5 foto secara otomatis.</p>
                    </div>

                    @if(session('step') == 2 && $errors->has('face'))
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl mb-5 shadow-xs">
                            <i class="fas fa-exclamation-circle mr-1"></i> <span class="font-medium text-sm">{{ $errors->first('face') }}</span>
                        </div>
                    @endif

                    {{-- Instruksi Dinamis --}}
                    <div class="neu-card px-4 py-4 text-center mb-5">
                        <p class="text-teal-600 text-[11px] uppercase tracking-widest font-bold mb-0.5">Panduan Perekaman Wajah</p>
                        <p x-text="currentInstruction" class="text-slate-800 font-extrabold text-base"></p>
                    </div>

                    {{-- Progress Bar 5 Foto --}}
                    <div class="flex justify-center gap-2 mb-5">
                        <template x-for="i in 5" :key="i">
                            <div class="h-2.5 rounded-full transition-all duration-300"
                                 :class="capturedImages.length >= i ? 'bg-teal-500 w-10 shadow-[inset_1px_1px_3px_rgba(0,0,0,0.2)]' : 'bg-[#d1d5db] shadow-[inset_2px_2px_4px_#b3b3b3,inset_-2px_-2px_4px_#ffffff] w-7'"></div>
                        </template>
                    </div>

                    {{-- Camera Preview Real-Time --}}
                    <div class="relative bg-slate-950 rounded-2xl overflow-hidden mb-5 shadow-[8px_8px_16px_#c5c5c5,-8px_-8px_16px_#ffffff]" style="height: 300px;">
                        <video id="video-reg" autoplay playsinline muted class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                        <canvas id="canvas-reg" class="hidden"></canvas>

                        {{-- Face oval HUD --}}
                        <div class="face-ring" :class="{ 'detected': isCameraReady, 'capturing': faceState === 'capturing' }">
                            <div class="scanner-laser" x-show="isCameraReady && capturedImages.length < 5"></div>
                        </div>

                        {{-- Status overlay di bawah --}}
                        <div class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                            <div x-show="!isCameraReady" class="bg-black/70 text-white text-xs px-4 py-1.5 rounded-full backdrop-blur-sm flex items-center gap-1.5 border border-white/10">
                                <i class="fas fa-circle-notch fa-spin text-blue-400"></i> Membuka kamera...
                            </div>
                            <div x-show="isCameraReady && capturedImages.length < 5" class="bg-black/75 backdrop-blur-md text-white text-xs px-3.5 py-1.5 rounded-full font-bold border border-white/10 flex items-center gap-2 shadow-lg">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span x-text="'Langkah ' + (capturedImages.length + 1) + ' / 5'"></span>
                            </div>
                            <div x-show="capturedImages.length >= 5" class="bg-emerald-600 text-white text-xs px-4 py-1.5 rounded-full font-bold backdrop-blur-sm flex items-center gap-1.5 shadow-md">
                                <i class="fas fa-check-double text-white"></i> 5 Foto Lengkap!
                            </div>
                        </div>
                    </div>

                    {{-- Thumbnail preview 5 Slot --}}
                    <div class="flex gap-3 justify-center mb-6">
                        <template x-for="i in 5" :key="i">
                            <div class="w-12 h-12 rounded-xl overflow-hidden transition-all bg-[#e8e8e8] relative flex items-center justify-center"
                                 :class="capturedImages.length >= i ? 'shadow-[4px_4px_8px_#c5c5c5,-4px_-4px_8px_#ffffff] border-2 border-teal-400' : 'shadow-[inset_4px_4px_8px_#c5c5c5,inset_-4px_-4px_8px_#ffffff]'">
                                <template x-if="capturedImages[i-1]">
                                    <div class="relative w-full h-full">
                                        <img :src="capturedImages[i-1]" class="w-full h-full object-cover" style="transform: scaleX(-1);">
                                        <div class="absolute inset-0 bg-emerald-600/30 flex items-center justify-center text-white text-xs backdrop-blur-[1px]">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!capturedImages[i-1]">
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 text-[10px] font-mono font-bold">
                                        <i class="fas fa-user mb-0.5 text-xs"></i>
                                        <span x-text="i"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Tombol Ambil Manual / Cadangan --}}
                    <div x-show="capturedImages.length < 5" class="mb-4">
                        <button type="button" @click="manualCapture()"
                                :disabled="faceState === 'capturing' || !isCameraReady"
                                class="neu-btn text-sm py-3 px-4 disabled:opacity-50">
                            <i class="fas fa-camera"></i>
                            <span>Ambil Foto Sekarang (<span x-text="capturedImages.length + 1"></span>/5)</span>
                        </button>
                    </div>

                    {{-- Form Submit (Step 2) --}}
                    <form id="form-step2" method="POST" action="{{ route('register.siswa') }}">
                        @csrf
                        <input type="hidden" name="face_images" id="face-images-input">

                        <button type="button" @click="submitFace()"
                                :disabled="capturedImages.length < 5 || isSubmitting"
                                :class="capturedImages.length >= 5 && !isSubmitting ? 'neu-btn text-teal-700' : 'neu-input text-slate-400 cursor-not-allowed text-center font-bold justify-center flex py-4'"
                                class="w-full font-extrabold py-4 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                            <template x-if="!isSubmitting">
                                <span><i class="fas fa-user-check mr-2"></i>Daftar & Simpan Akun Siswa</span>
                            </template>
                            <template x-if="isSubmitting">
                                <span><i class="fas fa-spinner fa-spin mr-2"></i>Mendaftarkan Wajah...</span>
                            </template>
                        </button>
                    </form>

                    <button @click="step = 1; stopCamera()" class="w-full mt-6 text-slate-500 text-xs hover:text-slate-800 transition text-center font-semibold flex items-center justify-center gap-1.5 py-2 hover:bg-slate-200/50 rounded-lg">
                        <i class="fas fa-arrow-left text-[11px]"></i> Kembali ubah data identitas
                    </button>
                </div>
                
                <div class="mt-8 mb-6 flex justify-center w-full">
                    <img src="{{ asset('images/logo-kolaborasi.webp') }}" alt="Logo Kolaborasi" class="h-10 sm:h-12 w-auto object-contain mix-blend-multiply opacity-80">
                </div>

                <p class="text-center text-xs text-slate-400 mt-8">
                    Sistem Presensi Online &copy; 2026 SMKN 1 BERINGIN
                </p>

            </div>
        </div>
    </div>

<script>
    // Toggle password visibility
    function togglePass(id, btn) {
        const inp = document.getElementById(id);
        if (inp.type === 'password') {
            inp.type = 'text';
            btn.innerHTML = '<i class="far fa-eye-slash"></i>';
        } else {
            inp.type = 'password';
            btn.innerHTML = '<i class="far fa-eye"></i>';
        }
    }

    // ─── Alpine.js App dengan Real-Time Face Detection & Auto-Capture ───
    function registerApp() {
        return {
            step: 1,
            faceState: 'ready',   // ready | capturing | done
            capturedImages: [],
            isSubmitting: false,
            isCameraReady: false,
            videoStream: null,

            instructions: [
                '1. Posisikan wajah tepat di tengah oval',
                '2. Bagus! Tolehkan wajah sedikit ke KIRI',
                '3. Hebat! Sekarang tolehkan sedikit ke KANAN',
                '4. Tersenyumlah santai ke arah kamera',
                '5. Satu lagi! Hadap tegak lurus ke depan (stabil)',
            ],

            get currentInstruction() {
                if (this.capturedImages.length >= 5) return 'Semua foto berhasil diambil! Mendaftarkan ke server AI...';
                const idx = Math.min(this.capturedImages.length, 4);
                return this.instructions[idx];
            },

            checkInitialStep() {
                const serverStep = {{ session('step') ?? 1 }};
                if (serverStep === 2) {
                    this.$nextTick(() => {
                        this.step = 2;
                        this.startCamera();
                    });
                }
                @if ($errors->any())
                this.$nextTick(() => {
                    const scrollContainer = document.getElementById('form-scroll-container');
                    if (scrollContainer) scrollContainer.scrollTop = 0;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
                @endif
            },

            async startCamera() {
                this.faceState = 'ready';
                this.isCameraReady = false;

                try {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        throw new Error("Akses kamera diblokir browser. Pastikan Anda menggunakan HTTPS atau Localhost.");
                    }
                    this.videoStream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                        audio: false
                    });
                    const video = document.getElementById('video-reg');
                    video.srcObject = this.videoStream;

                    video.onloadedmetadata = async () => {
                        video.width = video.videoWidth || 640;
                        video.height = video.videoHeight || 480;
                        try {
                            await video.play();
                        } catch (e) {}
                        this.isCameraReady = true;
                    };

                } catch (err) {
                    this.isCameraReady = false;
                    alert('Kamera tidak dapat diakses atau perizinan ditolak: ' + err.message);
                }
            },

            manualCapture() {
                if (this.capturedImages.length >= 5 || this.faceState === 'capturing' || !this.isCameraReady) return;
                this.takeSnapshot();
            },

            takeSnapshot() {
                this.captureFrame();
                if (this.capturedImages.length >= 5) {
                    this.faceState = 'done';
                    setTimeout(() => this.submitFace(), 400);
                }
            },

            stopCamera() {
                if (this.videoStream) {
                    this.videoStream.getTracks().forEach(t => t.stop());
                    this.videoStream = null;
                }
            },

            captureFrame() {
                const video  = document.getElementById('video-reg');
                const canvas = document.getElementById('canvas-reg');
                if (!video || !canvas) return;

                canvas.width  = 640;
                canvas.height = 480;
                const ctx = canvas.getContext('2d');

                // Mirror flip konsisten
                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                ctx.restore();

                this.faceState = 'capturing';

                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                this.capturedImages.push(dataUrl);

                setTimeout(() => {
                    if (this.capturedImages.length < 5) {
                        this.faceState = 'ready';
                    }
                }, 300);
            },

            submitFace() {
                if (this.capturedImages.length < 5 || this.isSubmitting) return;
                this.isSubmitting = true;

                document.getElementById('face-images-input').value = JSON.stringify(this.capturedImages);
                this.stopCamera();
                document.getElementById('form-step2').submit();
            },
        };
    }
</script>
</body>
</html>