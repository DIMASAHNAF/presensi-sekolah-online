<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Siswa — Portal Presensi SMKN 1 Beringin</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
    
    <!-- Fonts: Inter & JetBrains Mono (Strict PRD Compliance) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Tailwind Configuration (Linear / Vercel Aesthetic) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        };
    </script>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; font-variant-numeric: tabular-nums; }
        .tabular-nums { font-variant-numeric: tabular-nums; }

        /* Step Indicators */
        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            background: #d4d4d8;
            transition: all .2s ease;
        }
        .step-dot.active {
            background: #18181b;
            width: 24px;
        }
        html.dark .step-dot.active {
            background: #fafafa;
        }
        .step-dot.done {
            background: #10b981;
        }

        /* Face capture overlay */
        .face-ring {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -55%);
            width: 200px; height: 250px;
            border-radius: 50% / 45%;
            border: 2px solid rgba(255,255,255,0.4);
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);
            pointer-events: none;
            z-index: 10;
            transition: border-color .2s;
        }
        .face-ring.detected { border-color: #10b981; }
        .face-ring.capturing { border-color: #f59e0b; }

        #video-reg { width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
    </style>
</head>
<body class="antialiased min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 selection:bg-zinc-900 selection:text-zinc-50"
      x-data="registerApp()" x-init="checkInitialStep()">

    <div class="min-h-screen flex flex-col lg:flex-row">
        
        {{-- Left Side: Info & Guidelines (Desktop Only) --}}
        <div class="hidden lg:flex lg:w-5/12 bg-zinc-900 text-zinc-100 p-12 flex-col justify-between border-r border-zinc-800">
            <div>
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="h-9 w-auto object-contain">
                    <div class="w-px h-5 bg-zinc-700"></div>
                    <span class="text-xs font-mono uppercase tracking-wider text-zinc-400">SMKN 1 BERINGIN</span>
                </div>
                
                <div class="mt-16 max-w-sm">
                    <h1 class="text-2xl font-bold tracking-tight text-white leading-tight">
                        Pendaftaran Akun Presensi Siswa
                    </h1>
                    <p class="text-xs text-zinc-400 mt-2 leading-relaxed">
                        Lakukan registrasi akun dan rekam data biometrik wajah untuk autentikasi kehadiran otomatis di sekolah.
                    </p>

                    <div class="mt-10 space-y-4 text-xs text-zinc-300">
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-md bg-zinc-800 border border-zinc-700 flex items-center justify-center font-mono text-[11px] text-zinc-400 shrink-0">1</span>
                            <div>
                                <p class="font-medium text-white">Verifikasi Identitas</p>
                                <p class="text-zinc-400 text-[11px] mt-0.5">Lengkapi data NISN, Nama Lengkap, dan Kelas Anda.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-md bg-zinc-800 border border-zinc-700 flex items-center justify-center font-mono text-[11px] text-zinc-400 shrink-0">2</span>
                            <div>
                                <p class="font-medium text-white">Perekaman Sampel Wajah</p>
                                <p class="text-zinc-400 text-[11px] mt-0.5">Ambil 5 frame wajah dari berbagai sudut untuk pencocokan biometrik presensi.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 rounded-md bg-zinc-800 border border-zinc-700 flex items-center justify-center font-mono text-[11px] text-zinc-400 shrink-0">3</span>
                            <div>
                                <p class="font-medium text-white">Akun Langsung Aktif</p>
                                <p class="text-zinc-400 text-[11px] mt-0.5">Siswa dapat langsung melakukan presensi masuk di gerbang atau kelas.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-[11px] font-mono text-zinc-500">
                Sistem Presensi Biometrik &bull; 2026 SMKN 1 Beringin
            </div>
        </div>

        {{-- Right Side: Form & Camera --}}
        <div id="form-scroll-container" class="w-full lg:w-7/12 flex flex-col items-center justify-start p-6 sm:p-10 lg:p-12 overflow-y-auto min-h-screen">
            <div class="w-full max-w-md my-auto py-4">
                
                {{-- Mobile Brand Header --}}
                <div class="lg:hidden flex items-center justify-center gap-2 mb-6">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="h-8 w-auto">
                    <span class="text-xs font-mono uppercase tracking-wider text-zinc-500">SMKN 1 BERINGIN</span>
                </div>

                {{-- Step Indicator --}}
                <div class="flex items-center justify-center gap-2 mb-6">
                    <div class="step-dot" :class="step >= 1 ? (step > 1 ? 'done' : 'active') : ''"></div>
                    <div class="h-px w-8 bg-zinc-200 dark:bg-zinc-800"></div>
                    <div class="step-dot" :class="step >= 2 ? 'active' : ''"></div>
                </div>

                {{-- STEP 1: FORM DATA DIRI --}}
                <div x-show="step === 1" x-cloak class="w-full">
                    
                    <div class="mb-5">
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">Formulir Pendaftaran</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Langkah 1 dari 2 &mdash; Masukkan data akun dan identitas siswa.</p>
                    </div>

                    @if ($errors->any())
                        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300 p-3.5 rounded-lg mb-5 text-xs">
                            <p class="font-semibold mb-1">Periksa kembali data pendaftaran:</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="form-step1" method="POST" action="{{ route('register.siswa') }}" class="space-y-3.5">
                        @csrf

                        <div>
                            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                                   placeholder="Contoh: Budi Santoso"
                                   class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 text-xs sm:text-sm placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Username</label>
                            <input type="text" name="username" value="{{ old('username') }}" required
                                   placeholder="Contoh: budi_santoso"
                                   class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 text-xs sm:text-sm placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 outline-none transition font-mono">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">NISN (10 Digit)</label>
                            <input type="text" name="nisn" value="{{ old('nisn') }}" required maxlength="10"
                                   placeholder="10 digit angka NISN"
                                   class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 text-xs sm:text-sm placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 outline-none transition font-mono tabular-nums">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Kelas</label>
                            <select name="kelas_id" required
                                    class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 text-xs sm:text-sm focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 outline-none transition cursor-pointer">
                                <option value="" disabled {{ old('kelas_id') ? '' : 'selected' }}>-- Pilih Kelas --</option>
                                @foreach ($kelas as $item)
                                    <option value="{{ $item->id }}" {{ old('kelas_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_kelas }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Nama Orang Tua / Wali <span class="text-zinc-400 text-[11px]">(Opsional)</span></label>
                                <input type="text" name="nama_ortu" value="{{ old('nama_ortu') }}"
                                       placeholder="Nama orang tua"
                                       class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 text-xs sm:text-sm placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">No. WhatsApp Ortu <span class="text-zinc-400 text-[11px]">(Opsional)</span></label>
                                <input type="text" name="no_hp_ortu" value="{{ old('no_hp_ortu') }}"
                                       placeholder="08xxxxxxxxxx"
                                       class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 text-xs sm:text-sm placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 outline-none transition font-mono tabular-nums">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Password</label>
                            <input type="password" id="pass1" name="password" required
                                   placeholder="Minimal 6 karakter"
                                   class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 text-xs sm:text-sm placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 outline-none transition">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-zinc-700 dark:text-zinc-300 mb-1">Konfirmasi Password</label>
                            <input type="password" id="pass2" name="password_confirmation" required
                                   placeholder="Ulangi password"
                                   class="w-full px-3.5 py-2.5 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 rounded-lg text-zinc-900 dark:text-zinc-100 text-xs sm:text-sm placeholder:text-zinc-400 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-1 focus:ring-zinc-900 dark:focus:ring-zinc-100 outline-none transition">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-zinc-900 hover:bg-zinc-800 text-zinc-50 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 font-medium py-2.5 px-4 rounded-lg text-xs sm:text-sm transition shadow-xs flex items-center justify-center gap-2">
                                <span>Lanjut ke Perekaman Wajah</span>
                                <i class="fas fa-arrow-right text-[11px]"></i>
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 pt-4 border-t border-zinc-200 dark:border-zinc-800 text-center">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Sudah memiliki akun?
                            <a href="{{ route('choose-role') }}" class="font-medium text-zinc-900 dark:text-zinc-100 hover:underline ml-1">
                                Masuk di sini
                            </a>
                        </p>
                    </div>
                </div>

                {{-- STEP 2: SCAN WAJAH REAL-TIME --}}
                <div x-show="step === 2" x-cloak class="w-full">
                    
                    <div class="mb-4">
                        <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 text-[10px] font-mono mb-2">
                            <span>Langkah 2 / 2</span>
                        </div>
                        <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">Perekaman Biometrik Wajah</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Sistem akan mengambil 5 foto sampel untuk identifikasi presensi.</p>
                    </div>

                    @if(session('step') == 2 && $errors->has('face'))
                        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-700 dark:text-rose-300 p-3 rounded-lg mb-4 text-xs font-medium">
                            {{ $errors->first('face') }}
                        </div>
                    @endif

                    {{-- Panduan Aksi Saat Ini --}}
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-3 text-center mb-4 shadow-xs">
                        <p class="text-zinc-500 dark:text-zinc-400 text-[10px] font-mono uppercase tracking-wider mb-0.5">Instruksi Kamera</p>
                        <p x-text="currentInstruction" class="text-zinc-900 dark:text-zinc-100 font-semibold text-xs sm:text-sm"></p>
                    </div>

                    {{-- Progress Bar 5 Foto --}}
                    <div class="flex justify-center gap-1.5 mb-4 font-mono tabular-nums">
                        <template x-for="i in 5" :key="i">
                            <div class="h-1.5 rounded-full transition-all duration-200"
                                 :class="capturedImages.length >= i ? 'bg-zinc-900 dark:bg-zinc-100 w-8' : 'bg-zinc-200 dark:bg-zinc-800 w-5'"></div>
                        </template>
                    </div>

                    {{-- Camera Preview Frame --}}
                    <div class="relative bg-black rounded-lg overflow-hidden mb-4 border border-zinc-800" style="height: 280px;">
                        <video id="video-reg" autoplay playsinline muted class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                        <canvas id="canvas-reg" class="hidden"></canvas>

                        {{-- Face oval guide --}}
                        <div class="face-ring" :class="{ 'detected': isCameraReady, 'capturing': faceState === 'capturing' }"></div>

                        {{-- Status overlay di bawah --}}
                        <div class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                            <div x-show="!isCameraReady" class="bg-zinc-900/90 text-white text-[11px] px-3 py-1 rounded-md border border-zinc-700 flex items-center gap-1.5 font-mono">
                                <i class="fas fa-spinner fa-spin"></i> Menyiapkan kamera...
                            </div>
                            <div x-show="isCameraReady && capturedImages.length < 5" class="bg-zinc-900/90 text-white text-[11px] px-3 py-1 rounded-md border border-zinc-700 flex items-center gap-2 font-mono tabular-nums">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span x-text="'Foto ' + (capturedImages.length + 1) + ' dari 5'"></span>
                            </div>
                            <div x-show="capturedImages.length >= 5" class="bg-emerald-600 text-white text-[11px] px-3 py-1 rounded-md font-mono flex items-center gap-1.5">
                                <i class="fas fa-check"></i> 5 Foto Lengkap
                            </div>
                        </div>
                    </div>

                    {{-- Thumbnail preview 5 Slot --}}
                    <div class="flex gap-2 justify-center mb-4">
                        <template x-for="i in 5" :key="i">
                            <div class="w-10 h-10 rounded-md overflow-hidden bg-zinc-100 dark:bg-zinc-800 relative flex items-center justify-center border"
                                 :class="capturedImages.length >= i ? 'border-zinc-900 dark:border-zinc-100' : 'border-zinc-200 dark:border-zinc-700'">
                                <template x-if="capturedImages[i-1]">
                                    <img :src="capturedImages[i-1]" class="w-full h-full object-cover" style="transform: scaleX(-1);">
                                </template>
                                <template x-if="!capturedImages[i-1]">
                                    <span class="text-zinc-400 text-[10px] font-mono" x-text="i"></span>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Tombol Ambil Foto Manual --}}
                    <div x-show="capturedImages.length < 5" class="mb-3">
                        <button type="button" @click="manualCapture()"
                                :disabled="faceState === 'capturing' || !isCameraReady"
                                class="w-full bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100 font-medium py-2.5 px-4 rounded-lg text-xs transition flex items-center justify-center gap-2 shadow-xs disabled:opacity-50">
                            <i class="fas fa-camera text-xs"></i>
                            <span>Ambil Foto (<span class="font-mono tabular-nums" x-text="capturedImages.length + 1"></span>/5)</span>
                        </button>
                    </div>

                    {{-- Form Submit (Step 2) --}}
                    <form id="form-step2" method="POST" action="{{ route('register.siswa') }}">
                        @csrf
                        <input type="hidden" name="face_images" id="face-images-input">

                        <button type="button" @click="submitFace()"
                                :disabled="capturedImages.length < 5 || isSubmitting"
                                :class="capturedImages.length >= 5 && !isSubmitting ? 'bg-zinc-900 hover:bg-zinc-800 text-zinc-50 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 cursor-pointer' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-400 cursor-not-allowed'"
                                class="w-full font-medium py-2.5 rounded-lg transition text-xs sm:text-sm flex items-center justify-center gap-2">
                            <template x-if="!isSubmitting">
                                <span>Simpan &amp; Daftarkan Akun Siswa</span>
                            </template>
                            <template x-if="isSubmitting">
                                <span><i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan data...</span>
                            </template>
                        </button>
                    </form>

                    <button @click="step = 1; stopCamera()" class="w-full mt-4 text-zinc-500 text-xs hover:text-zinc-900 dark:hover:text-zinc-100 transition text-center font-medium flex items-center justify-center gap-1.5 py-1.5">
                        <i class="fas fa-arrow-left text-[10px]"></i> Kembali ke data identitas
                    </button>
                </div>

            </div>
        </div>
    </div>

<script>
    function registerApp() {
        return {
            step: 1,
            faceState: 'ready',
            capturedImages: [],
            isSubmitting: false,
            isCameraReady: false,
            videoStream: null,

            instructions: [
                'Posisikan wajah tepat di dalam bingkai oval',
                'Tolehkan wajah sedikit ke sisi kiri',
                'Tolehkan wajah sedikit ke sisi kanan',
                'Posisikan wajah santai menghadap kamera',
                'Posisikan wajah tegak lurus ke depan',
            ],

            get currentInstruction() {
                if (this.capturedImages.length >= 5) return '5 Foto selesai. Menyimpan ke sistem...';
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
                        throw new Error("Akses kamera diblokir atau peramban tidak mendukung.");
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
                    alert('Kamera tidak dapat diakses: ' + err.message);
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
                    setTimeout(() => this.submitFace(), 300);
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
</body>
</html>
