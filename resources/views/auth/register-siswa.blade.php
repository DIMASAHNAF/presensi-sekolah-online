<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa — Sistem Presensi Sekolah</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

                {{-- STEP 2: SCAN WAJAH --}}
                <div x-show="step === 2" x-cloak class="w-full">
                    
                    <div class="text-center lg:text-left mb-6">
                        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftarkan Wajah Kamu</h2>
                        <p class="text-slate-500 mt-1">Langkah 2 dari 2 &mdash; Digunakan untuk verifikasi absensi.</p>
                    </div>

                    @if(session('step') == 2 && $errors->has('face'))
                        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-xl mb-6">
                            <i class="fas fa-exclamation-circle mr-1"></i> <span class="font-medium text-sm">{{ $errors->first('face') }}</span>
                        </div>
                    @endif

                    {{-- Instruksi --}}
                    <div class="bg-slate-100 rounded-2xl px-4 py-3 text-center mb-6">
                        <p class="text-slate-500 text-xs uppercase tracking-wider font-semibold mb-1">Instruksi</p>
                        <p x-text="currentInstruction" class="text-slate-800 font-bold text-lg"></p>
                    </div>

                    {{-- Progress dots --}}
                    <div class="flex justify-center gap-2 mb-6">
                        <template x-for="i in 5" :key="i">
                            <div class="w-8 h-2 rounded-full transition-all duration-300"
                                 :class="capturedImages.length >= i ? 'bg-teal-500' : 'bg-slate-200'"></div>
                        </template>
                    </div>

                    {{-- Camera Preview --}}
                    <div class="relative bg-slate-900 rounded-3xl overflow-hidden mb-6 shadow-inner" style="height: 320px;">
                        <video id="video-reg" autoplay playsinline muted class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                        <canvas id="canvas-reg" class="hidden"></canvas>

                        {{-- Face oval guide --}}
                        <div class="face-ring" :class="faceState === 'detected' ? 'detected' : (faceState === 'capturing' ? 'capturing' : '')"></div>

                        {{-- Status overlay --}}
                        <div class="absolute bottom-4 left-0 right-0 flex justify-center z-20">
                            <div x-show="faceState === 'idle'" class="bg-black/60 text-white/90 text-xs px-4 py-2 rounded-full backdrop-blur-sm">
                                <i class="fas fa-circle-notch fa-spin mr-1"></i> Mendeteksi wajah...
                            </div>
                            <div x-show="faceState === 'detected'" class="bg-green-500/90 text-white text-xs px-4 py-2 rounded-full font-semibold pulse-ring backdrop-blur-sm shadow-lg shadow-green-500/20">
                                <i class="fas fa-check mr-1"></i> Wajah terdeteksi!
                            </div>
                            <div x-show="faceState === 'capturing'" class="bg-yellow-400/90 text-black text-xs px-4 py-2 rounded-full font-semibold backdrop-blur-sm">
                                <i class="fas fa-camera mr-1"></i> Mengambil foto...
                            </div>
                            <div x-show="capturedImages.length >= 5 && faceState !== 'capturing'" class="bg-teal-600/90 text-white text-xs px-4 py-2 rounded-full font-semibold backdrop-blur-sm">
                                <i class="fas fa-check-double mr-1"></i> Selesai!
                            </div>
                        </div>
                    </div>

                    {{-- Thumbnail preview --}}
                    <div class="flex gap-2 justify-center mb-6">
                        <template x-for="i in 5" :key="i">
                            <div class="w-12 h-12 rounded-xl overflow-hidden border-2 transition-all bg-slate-50"
                                 :class="capturedImages.length >= i ? 'border-teal-500' : 'border-slate-200'">
                                <template x-if="capturedImages[i-1]">
                                    <div class="relative w-full h-full">
                                        <img :src="capturedImages[i-1]" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-teal-500/60 flex items-center justify-center text-white text-sm">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!capturedImages[i-1]">
                                    <div class="w-full h-full flex items-center justify-center text-slate-300 text-xs">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Manual capture button (backup) --}}
                    <button @click="manualCapture()"
                            x-show="capturedImages.length < 5"
                            :disabled="faceState === 'capturing'"
                            class="w-full mb-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-3.5 rounded-xl transition border border-slate-200 flex items-center justify-center gap-2">
                        <i class="fas fa-camera"></i>
                        <span x-text="'Ambil Foto ' + (capturedImages.length + 1) + ' / 5'"></span>
                    </button>

                    {{-- Submit form (step 2) --}}
                    <form id="form-step2" method="POST" action="{{ route('register.siswa') }}">
                        @csrf
                        <input type="hidden" name="face_images" id="face-images-input">

                        <button type="button" @click="submitFace()"
                                :disabled="capturedImages.length < 5 || isSubmitting"
                                :class="capturedImages.length >= 5 && !isSubmitting ? 'bg-teal-600 hover:bg-teal-700 cursor-pointer shadow-lg hover:shadow-teal-500/25' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                class="w-full text-white font-bold py-3.5 rounded-xl transition-all duration-200 flex items-center justify-center gap-2">
                            <template x-if="!isSubmitting">
                                <span><i class="fas fa-user-check mr-2"></i>Daftar & Simpan Wajah</span>
                            </template>
                            <template x-if="isSubmitting">
                                <span><i class="fas fa-spinner fa-spin mr-2"></i>Memproses...</span>
                            </template>
                        </button>
                    </form>

                    <button @click="step = 1; stopCamera()" class="w-full mt-4 text-slate-500 text-sm hover:text-slate-800 transition text-center font-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ubah data
                    </button>
                </div>
                
                <p class="text-center text-xs text-slate-400 mt-10">
                    &copy; {{ date('Y') }} Presensi Sekolah. All rights reserved.
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

    // ─── Alpine.js App ────────────────────────────────────────────
    function registerApp() {
        return {
            step: 1,
            faceState: 'idle',   // idle | detected | capturing
            capturedImages: [],
            isSubmitting: false,
            videoStream: null,
            autoDetectInterval: null,
            faceDetectionActive: false,

            instructions: [
                'Hadap ke depan',
                'Miringkan kepala ke KIRI',
                'Miringkan kepala ke KANAN',
                'Tengadah sedikit ke ATAS',
                'Tundukkan sedikit ke BAWAH',
            ],

            get currentInstruction() {
                const idx = Math.min(this.capturedImages.length, 4);
                return this.instructions[idx];
            },

            checkInitialStep() {
                // Jika server mengirim session step=2 setelah redirect, langsung ke step 2
                const serverStep = {{ session('step') ?? 1 }};
                if (serverStep === 2) {
                    this.$nextTick(() => {
                        this.step = 2;
                        this.startCamera();
                    });
                }
            },

            async startCamera() {
                try {
                    this.videoStream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                        audio: false
                    });
                    const video = document.getElementById('video-reg');
                    video.srcObject = this.videoStream;
                    await video.play();
                    this.startAutoDetect();
                } catch (err) {
                    alert('Kamera tidak dapat diakses: ' + err.message + '\nPastikan halaman dibuka via HTTPS.');
                }
            },

            stopCamera() {
                if (this.videoStream) {
                    this.videoStream.getTracks().forEach(t => t.stop());
                    this.videoStream = null;
                }
                if (this.autoDetectInterval) {
                    clearInterval(this.autoDetectInterval);
                    this.autoDetectInterval = null;
                }
            },

            startAutoDetect() {
                // Auto-capture setiap 2.5 detik jika belum 5 foto
                // Dalam skenario nyata ini langsung capture (deteksi face ada di backend Python)
                // Frontend hanya pastikan kamera aktif
                this.autoDetectInterval = setInterval(() => {
                    if (this.capturedImages.length >= 5 || this.faceState === 'capturing') return;
                    this.faceState = 'detected';
                }, 800);
            },

            manualCapture() {
                if (this.capturedImages.length >= 5 || this.faceState === 'capturing') return;
                this.captureFrame();
            },

            captureFrame() {
                const video  = document.getElementById('video-reg');
                const canvas = document.getElementById('canvas-reg');
                canvas.width  = video.videoWidth  || 640;
                canvas.height = video.videoHeight || 480;
                const ctx = canvas.getContext('2d');
                // Mirror flip untuk konsistensi dengan preview
                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                ctx.restore();

                this.faceState = 'capturing';

                setTimeout(() => {
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                    this.capturedImages.push(dataUrl);

                    if (this.capturedImages.length >= 5) {
                        this.faceState = 'idle';
                        if (this.autoDetectInterval) {
                            clearInterval(this.autoDetectInterval);
                        }
                    } else {
                        this.faceState = 'detected';
                    }
                }, 400);
            },

            submitFace() {
                if (this.capturedImages.length < 5 || this.isSubmitting) return;
                this.isSubmitting = true;

                // Masukkan array base64 ke hidden input
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