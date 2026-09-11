<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa — Sistem Presensi Sekolah</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/dist/face-api.min.js"></script>
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

        [x-cloak] { display: none !important; }

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

        .step-dot { width:10px; height:10px; border-radius:50%; background:#e2e8f0; transition: all .3s; }
        .step-dot.active { background:#0f766e; width:28px; border-radius:6px; }
        .step-dot.done   { background:#0f766e; }

        #video-reg { width:100%; height:100%; object-fit:cover; transform: scaleX(-1); }

        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(74,222,128,0.6); }
            70%  { box-shadow: 0 0 0 12px rgba(74,222,128,0); }
            100% { box-shadow: 0 0 0 0 rgba(74,222,128,0); }
        }
        .pulse-ring { animation: pulse-ring 1s ease infinite; }
    </style>
</head>
<body class="antialiased overflow-hidden selection:bg-teal-500 selection:text-white"
      x-data="registerApp()" x-init="checkInitialStep()">

    <div class="min-h-screen flex">
        
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
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-white relative overflow-y-auto max-h-screen">
            <div class="w-full max-w-md animate-slide-in py-8">
                
                {{-- Mobile Logo --}}
                <div class="lg:hidden text-center mb-6">
                    <div class="w-14 h-14 bg-teal-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-teal-500/30">
                        <i class="fas fa-user-plus text-2xl text-white"></i>
                    </div>
                </div>

                {{-- STEP INDICATOR --}}
                <div class="flex items-center justify-center gap-2 mb-8">
                    <div class="step-dot" :class="step >= 1 ? (step > 1 ? 'done' : 'active') : ''"></div>
                    <div class="h-0.5 w-10 bg-slate-200"></div>
                    <div class="step-dot" :class="step >= 2 ? 'active' : ''"></div>
                </div>

                {{-- STEP 1: FORM DATA --}}
                <div x-show="step === 1" x-cloak class="w-full">
                    
                    <div class="text-center lg:text-left mb-8">
                        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Baru</h2>
                        <p class="text-slate-500 mt-2">Langkah 1 dari 2 &mdash; Isi data dirimu dengan benar.</p>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl mb-6">
                            <ul class="list-disc list-inside space-y-1 text-sm font-medium">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="form-step1" method="POST" action="{{ route('register.siswa') }}" class="space-y-4">
                        @csrf

                        <div class="input-field group">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5 transition-colors group-focus-within:text-teal-600">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                   class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-200">
                        </div>

                        <div class="input-field group">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5 transition-colors group-focus-within:text-teal-600">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" required
                                   placeholder="cth: budi_santoso"
                                   class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-200">
                        </div>

                        <div class="input-field group">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5 transition-colors group-focus-within:text-teal-600">Email <span class="opacity-60">(Opsional)</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-200">
                        </div>

                        <div class="input-field group">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5 transition-colors group-focus-within:text-teal-600">NISN</label>
                            <input type="text" name="nisn" value="{{ old('nisn') }}" required maxlength="10"
                                   placeholder="10 digit angka"
                                   class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-200">
                        </div>

                        <div class="input-field group">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5 transition-colors group-focus-within:text-teal-600">Kelas</label>
                            <select name="kelas_id" required
                                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-200 cursor-pointer">
                                <option value="" disabled {{ old('kelas_id') ? '' : 'selected' }}>-- Pilih Kelas --</option>
                                @foreach ($kelas as $item)
                                    <option value="{{ $item->id }}" {{ old('kelas_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-field group">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5 transition-colors group-focus-within:text-teal-600">Password</label>
                            <div class="relative">
                                <input type="password" id="pass1" name="password" required
                                       class="block w-full px-4 py-3 pr-11 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-200">
                                <button type="button" onclick="togglePass('pass1', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-teal-600 focus:outline-none">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="input-field group">
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5 transition-colors group-focus-within:text-teal-600">Konfirmasi Password</label>
                            <div class="relative">
                                <input type="password" id="pass2" name="password_confirmation" required
                                       class="block w-full px-4 py-3 pr-11 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 focus:bg-white transition-all duration-200">
                                <button type="button" onclick="togglePass('pass2', this)" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-teal-600 focus:outline-none">
                                    <i class="far fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full bg-slate-900 hover:bg-teal-600 text-white font-semibold py-3.5 rounded-xl transition-all duration-300 transform active:scale-[0.98] shadow-lg hover:shadow-teal-500/25 flex items-center justify-center gap-2 group mt-6">
                            <span>Lanjut Scan Wajah</span>
                            <i class="fas fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>

                    <div class="mt-8 relative">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm font-medium leading-6">
                            <span class="bg-white px-6 text-slate-500">Sudah punya akun?</span>
                        </div>
                    </div>

                    <a href="{{ route('choose-role') }}"
                       class="mt-6 w-full flex items-center justify-center gap-2 bg-white border-2 border-slate-200 hover:border-teal-500 hover:bg-teal-50 text-slate-700 hover:text-teal-700 font-semibold py-3 rounded-xl transition-all duration-200">
                        Masuk di sini
                    </a>
                </div>

                {{-- STEP 2: SCAN WAJAH REAL-TIME --}}
                <div x-show="step === 2" x-cloak class="w-full">
                    
                    <div class="text-center lg:text-left mb-5">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold mb-2 border border-blue-200">
                            <i class="fas fa-shield-halved text-blue-600"></i> Verifikasi Biometrik AI
                        </div>
                        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Perekaman Wajah Real-Time</h2>
                        <p class="text-slate-500 text-sm mt-1">AI akan mendeteksi dan mengambil 5 foto secara otomatis.</p>
                    </div>

                    @if(session('step') == 2 && $errors->has('face'))
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl mb-5 shadow-xs">
                            <i class="fas fa-exclamation-circle mr-1"></i> <span class="font-medium text-sm">{{ $errors->first('face') }}</span>
                        </div>
                    @endif

                    {{-- Instruksi Dinamis --}}
                    <div class="bg-gradient-to-r from-blue-50 to-emerald-50 rounded-2xl px-4 py-3.5 text-center mb-4 border border-blue-100 shadow-xs">
                        <p class="text-blue-600 text-[11px] uppercase tracking-widest font-bold mb-0.5" x-text="isLoadingModels ? 'Mempersiapkan AI...' : 'Panduan Posisi'"></p>
                        <p x-text="currentInstruction" class="text-slate-800 font-extrabold text-base"></p>
                    </div>

                    {{-- Progress Bar 5 Foto --}}
                    <div class="flex justify-center gap-2 mb-4">
                        <template x-for="i in 5" :key="i">
                            <div class="h-2 rounded-full transition-all duration-300"
                                 :class="capturedImages.length >= i ? 'bg-emerald-500 w-10 shadow-xs shadow-emerald-500/50' : 'bg-slate-200 w-7'"></div>
                        </template>
                    </div>

                    {{-- Camera Preview Real-Time --}}
                    <div class="relative bg-slate-950 rounded-3xl overflow-hidden mb-4 shadow-lg border-2 border-slate-200" style="height: 300px;">
                        <video id="video-reg" autoplay playsinline muted class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                        <canvas id="canvas-reg" class="hidden"></canvas>

                        {{-- Face oval HUD --}}
                        <div class="face-ring" :class="{ 'detected': faceState === 'detected', 'capturing': faceState === 'capturing' }"></div>

                        {{-- Loading AI overlay --}}
                        <div x-show="isLoadingModels" class="absolute inset-0 bg-slate-900/80 backdrop-blur-xs flex flex-col items-center justify-center text-white z-30">
                            <i class="fas fa-circle-notch fa-spin text-3xl text-blue-400 mb-2"></i>
                            <span class="text-xs font-semibold text-slate-200">Memuat Model AI Wajah (Real-time)...</span>
                        </div>

                        {{-- Status overlay di bawah --}}
                        <div class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                            <div x-show="!isLoadingModels && faceState === 'searching'" class="bg-black/70 text-white text-xs px-4 py-1.5 rounded-full backdrop-blur-sm flex items-center gap-1.5 border border-white/10">
                                <i class="fas fa-expand text-blue-400"></i> Posisikan wajah di dalam oval
                            </div>
                            <div x-show="!isLoadingModels && faceState === 'detected'" class="bg-emerald-600/90 text-white text-xs px-4 py-1.5 rounded-full font-bold backdrop-blur-sm flex items-center gap-1.5 shadow-md border border-emerald-400/50">
                                <i class="fas fa-check-circle text-white"></i> Wajah Terdeteksi — Menangkap Otomatis...
                            </div>
                            <div x-show="!isLoadingModels && faceState === 'capturing'" class="bg-blue-600/90 text-white text-xs px-4 py-1.5 rounded-full font-bold backdrop-blur-sm flex items-center gap-1.5 shadow-md">
                                <i class="fas fa-camera text-yellow-300 fa-pulse"></i> Memproses Frame...
                            </div>
                            <div x-show="capturedImages.length >= 5" class="bg-emerald-600 text-white text-xs px-4 py-1.5 rounded-full font-bold backdrop-blur-sm flex items-center gap-1.5 shadow-md">
                                <i class="fas fa-check-double text-white"></i> 5 Foto Lengkap!
                            </div>
                        </div>
                    </div>

                    {{-- Thumbnail preview 5 Slot --}}
                    <div class="flex gap-2 justify-center mb-4">
                        <template x-for="i in 5" :key="i">
                            <div class="w-12 h-12 rounded-xl overflow-hidden border-2 transition-all bg-slate-50 relative shadow-2xs"
                                 :class="capturedImages.length >= i ? 'border-emerald-500 ring-2 ring-emerald-200' : 'border-slate-200'">
                                <template x-if="capturedImages[i-1]">
                                    <div class="relative w-full h-full">
                                        <img :src="capturedImages[i-1]" class="w-full h-full object-cover" style="transform: scaleX(-1);">
                                        <div class="absolute inset-0 bg-emerald-600/30 flex items-center justify-center text-white text-xs backdrop-blur-[1px]">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!capturedImages[i-1]">
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-300 text-[10px] font-mono font-bold">
                                        <i class="fas fa-user mb-0.5 text-xs"></i>
                                        <span x-text="i"></span>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Tombol Cadangan Manual (Hanya jika ingin ambil langsung atau ruangan gelap) --}}
                    <div x-show="capturedImages.length < 5" class="mb-4">
                        <button type="button" @click="manualCapture()"
                                :disabled="faceState === 'capturing' || isLoadingModels"
                                class="w-full bg-white hover:bg-slate-50 text-slate-700 font-semibold py-2.5 px-4 rounded-xl border border-slate-300 shadow-2xs transition flex items-center justify-center gap-2 text-xs">
                            <i class="fas fa-camera text-blue-600"></i>
                            <span>Ambil Manual Sekarang (Foto <span x-text="capturedImages.length + 1"></span> / 5)</span>
                        </button>
                    </div>

                    {{-- Form Submit (Step 2) --}}
                    <form id="form-step2" method="POST" action="{{ route('register.siswa') }}">
                        @csrf
                        <input type="hidden" name="face_images" id="face-images-input">

                        <button type="button" @click="submitFace()"
                                :disabled="capturedImages.length < 5 || isSubmitting"
                                :class="capturedImages.length >= 5 && !isSubmitting ? 'bg-gradient-to-r from-blue-600 to-emerald-600 hover:from-blue-700 hover:to-emerald-700 cursor-pointer shadow-lg shadow-blue-500/25' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                class="w-full text-white font-extrabold py-3.5 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                            <template x-if="!isSubmitting">
                                <span><i class="fas fa-user-check mr-2"></i>Daftar & Simpan Akun Siswa</span>
                            </template>
                            <template x-if="isSubmitting">
                                <span><i class="fas fa-spinner fa-spin mr-2"></i>Mendaftarkan Wajah ke Server...</span>
                            </template>
                        </button>
                    </form>

                    <button @click="step = 1; stopCamera()" class="w-full mt-4 text-slate-500 text-xs hover:text-slate-800 transition text-center font-semibold flex items-center justify-center gap-1.5 py-1">
                        <i class="fas fa-arrow-left text-[11px]"></i> Kembali ubah data identitas
                    </button>
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
            faceState: 'searching',   // searching | detected | capturing | done
            capturedImages: [],
            isSubmitting: false,
            isLoadingModels: true,
            videoStream: null,
            detectionInterval: null,
            lastCaptureTime: 0,

            instructions: [
                'Posisikan wajah tepat di tengah oval',
                'Bagus! Tolehkan wajah sedikit ke KIRI',
                'Hebat! Sekarang tolehkan sedikit ke KANAN',
                'Tersenyumlah sedikit (buka mulut perlahan)',
                'Satu lagi! Hadap tegak lurus ke depan',
            ],

            get currentInstruction() {
                if (this.isLoadingModels) return 'Sedang mengunduh modul AI...';
                if (this.capturedImages.length >= 5) return 'Semua foto berhasil diambil! Mendaftarkan...';
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
            },

            async startCamera() {
                this.isLoadingModels = true;
                this.faceState = 'searching';

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
                    
                    // Muat model face-api ringan
                    const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/model/';
                    if (typeof faceapi !== 'undefined') {
                        await Promise.all([
                            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL)
                        ]);
                    }
                    
                    this.isLoadingModels = false;

                    video.onloadedmetadata = () => {
                        video.width = video.videoWidth;
                        video.height = video.videoHeight;
                    };

                    await video.play();
                    this.startRealtimeDetection(video);

                } catch (err) {
                    this.isLoadingModels = false;
                    alert('Kamera tidak dapat diakses atau perizinan ditolak: ' + err.message);
                }
            },

            stopCamera() {
                if (this.detectionInterval) {
                    clearInterval(this.detectionInterval);
                    this.detectionInterval = null;
                }
                if (this.videoStream) {
                    this.videoStream.getTracks().forEach(t => t.stop());
                    this.videoStream = null;
                }
            },

            startRealtimeDetection(video) {
                this.detectionInterval = setInterval(async () => {
                    if (this.capturedImages.length >= 5) {
                        clearInterval(this.detectionInterval);
                        this.faceState = 'done';
                        setTimeout(() => this.submitFace(), 600);
                        return;
                    }

                    if (this.faceState === 'capturing' || this.isLoadingModels) return;

                    try {
                        if (typeof faceapi !== 'undefined') {
                            const detection = await faceapi.detectSingleFace(
                                video,
                                new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.3, inputSize: 224 })
                            );

                            if (detection && detection.box && detection.box.width > 70) {
                                this.faceState = 'detected';
                                
                                // Auto capture jika sudah berlalu minimal 850ms sejak foto terakhir
                                const now = Date.now();
                                if (now - this.lastCaptureTime > 850) {
                                    this.lastCaptureTime = now;
                                    this.captureFrame();
                                }
                            } else {
                                this.faceState = 'searching';
                            }
                        } else {
                            // Fallback jika CDN faceapi lambat
                            this.faceState = 'detected';
                        }
                    } catch (e) {
                        this.faceState = 'searching';
                    }
                }, 180);
            },

            manualCapture() {
                if (this.capturedImages.length >= 5 || this.faceState === 'capturing') return;
                this.captureFrame();
            },

            captureFrame() {
                const video  = document.getElementById('video-reg');
                const canvas = document.getElementById('canvas-reg');
                if (!video || !canvas) return;

                canvas.width  = video.videoWidth  || 640;
                canvas.height = video.videoHeight || 480;
                const ctx = canvas.getContext('2d');

                // Mirror flip konsisten
                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                ctx.restore();

                this.faceState = 'capturing';

                setTimeout(() => {
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                    this.capturedImages.push(dataUrl);

                    if (this.capturedImages.length >= 5) {
                        this.faceState = 'done';
                        if (this.detectionInterval) clearInterval(this.detectionInterval);
                        setTimeout(() => this.submitFace(), 500);
                    } else {
                        setTimeout(() => {
                            if (this.capturedImages.length < 5) {
                                this.faceState = 'detected';
                            }
                        }, 300);
                    }
                }, 250);
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
    <x-page-loader />
</body>
</html>