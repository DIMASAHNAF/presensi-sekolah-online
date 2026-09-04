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
        body {
            background-image:
                linear-gradient(rgba(6, 78, 59, 0.55), rgba(6, 95, 70, 0.55)),
                url('{{ asset('images/bg-sekolah.jpg') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Inter', sans-serif;
        }
        .glass {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.35);
        }
        .glass-input {
            background: rgba(255, 255, 255, 0.55);
            backdrop-filter: blur(8px);
        }
        .glass-input:focus { background: rgba(255, 255, 255, 0.85); }
        [x-cloak] { display: none !important; }

        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in { animation: fadeSlideIn .4s ease forwards; }

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

        .step-dot { width:10px; height:10px; border-radius:50%; background:rgba(255,255,255,0.3); transition: all .3s; }
        .step-dot.active { background:#4ade80; width:28px; border-radius:6px; }
        .step-dot.done   { background:#4ade80; }

        .thumb-wrap { position:relative; width:60px; height:60px; border-radius:12px; overflow:hidden; }
        .thumb-wrap img { width:100%; height:100%; object-fit:cover; }
        .thumb-check {
            position:absolute; inset:0; background:rgba(74,222,128,0.75);
            display:flex; align-items:center; justify-content:center;
            font-size:20px; color:white;
        }

        #video-reg { width:100%; height:100%; object-fit:cover; transform: scaleX(-1); }

        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(74,222,128,0.6); }
            70%  { box-shadow: 0 0 0 12px rgba(74,222,128,0); }
            100% { box-shadow: 0 0 0 0 rgba(74,222,128,0); }
        }
        .pulse-ring { animation: pulse-ring 1s ease infinite; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 py-10"
      x-data="registerApp()" x-init="checkInitialStep()">

    {{-- STEP INDICATOR --}}
    <div class="flex items-center gap-2 mb-6 fade-in">
        <div class="step-dot" :class="step >= 1 ? (step > 1 ? 'done' : 'active') : ''"></div>
        <div class="h-px w-8 bg-white/30"></div>
        <div class="step-dot" :class="step >= 2 ? 'active' : ''"></div>
    </div>

    {{-- ─────────────────────────────────────────────────────────────── --}}
    {{-- STEP 1: FORM DATA --}}
    {{-- ─────────────────────────────────────────────────────────────── --}}
    <div x-show="step === 1" x-cloak class="glass rounded-3xl shadow-2xl p-8 w-full max-w-md fade-in mb-8">

        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-user-plus text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white drop-shadow-sm">Daftar Akun Siswa</h1>
            <p class="text-green-50/90 text-sm mt-1">Langkah 1 dari 2 — Isi data dirimu</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-500/20 border border-red-300/50 text-red-50 text-sm rounded-xl p-3 mb-4 backdrop-blur">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="form-step1" method="POST" action="{{ route('register.siswa') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 placeholder-gray-500 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required
                       placeholder="cth: budi_santoso"
                       class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 placeholder-gray-500 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">Email <span class="opacity-60">(Opsional)</span></label>
                <input type="email" name="email" value="{{ old('email') }}"
                       class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 placeholder-gray-500 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">NISN</label>
                <input type="text" name="nisn" value="{{ old('nisn') }}" required maxlength="10"
                       placeholder="10 digit angka"
                       class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 placeholder-gray-500 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-white/90 mb-1">Kelas</label>
                <select name="kelas_id" required
                        class="glass-input w-full rounded-xl px-4 py-2.5 text-gray-800 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition cursor-pointer">
                    <option value="" disabled {{ old('kelas_id') ? '' : 'selected' }}>-- Pilih Kelas --</option>
                    @foreach ($kelas as $item)
                        <option value="{{ $item->id }}" {{ old('kelas_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="relative">
                <label class="block text-sm font-medium text-white/90 mb-1">Password</label>
                <input type="password" id="pass1" name="password" required
                       class="glass-input w-full rounded-xl px-4 py-2.5 pr-11 text-gray-800 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                <button type="button" onclick="togglePass('pass1', this)" class="absolute right-3 top-9 text-gray-500 hover:text-gray-800">
                    <i class="far fa-eye"></i>
                </button>
            </div>

            <div class="relative">
                <label class="block text-sm font-medium text-white/90 mb-1">Konfirmasi Password</label>
                <input type="password" id="pass2" name="password_confirmation" required
                       class="glass-input w-full rounded-xl px-4 py-2.5 pr-11 text-gray-800 border border-white/40 focus:outline-none focus:ring-2 focus:ring-green-400 transition">
                <button type="button" onclick="togglePass('pass2', this)" class="absolute right-3 top-9 text-gray-500 hover:text-gray-800">
                    <i class="far fa-eye"></i>
                </button>
            </div>

            <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 active:scale-[0.98] text-white font-semibold py-3 rounded-xl transition-all duration-200 shadow-lg shadow-green-900/20 flex items-center justify-center gap-2">
                Lanjut Scan Wajah <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <p class="text-center text-sm text-white/80 mt-5">
            Sudah punya akun?
            <a href="{{ route('choose-role') }}" class="text-white font-semibold hover:underline">Masuk di sini</a>
        </p>
    </div>

    {{-- ─────────────────────────────────────────────────────────────── --}}
    {{-- STEP 2: SCAN WAJAH --}}
    {{-- ─────────────────────────────────────────────────────────────── --}}
    <div x-show="step === 2" x-cloak class="glass rounded-3xl shadow-2xl p-6 w-full max-w-md fade-in mb-8">

        {{-- Error wajah dari server --}}
        @if(session('step') == 2 && $errors->has('face'))
            <div class="bg-red-500/20 border border-red-300/50 text-red-50 text-sm rounded-xl p-3 mb-4">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ $errors->first('face') }}
            </div>
        @endif

        <div class="text-center mb-5">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-face-smile text-white text-2xl"></i>
            </div>
            <h2 class="text-xl font-extrabold text-white">Daftarkan Wajah Kamu</h2>
            <p class="text-green-50/80 text-xs mt-1">Langkah 2 dari 2 — Digunakan untuk verifikasi absensi</p>
        </div>

        {{-- Instruksi saat ini --}}
        <div class="bg-white/10 rounded-2xl px-4 py-2.5 text-center mb-4">
            <p class="text-white/60 text-xs uppercase tracking-wider font-semibold">Instruksi</p>
            <p x-text="currentInstruction" class="text-white font-bold text-base mt-0.5"></p>
        </div>

        {{-- Progress dots --}}
        <div class="flex justify-center gap-2 mb-4">
            <template x-for="i in 5" :key="i">
                <div class="w-8 h-2 rounded-full transition-all duration-300"
                     :class="capturedImages.length >= i ? 'bg-green-400' : 'bg-white/20'"></div>
            </template>
        </div>

        {{-- Camera Preview --}}
        <div class="relative bg-black rounded-2xl overflow-hidden mb-4" style="height: 280px;">
            <video id="video-reg" autoplay playsinline muted class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
            <canvas id="canvas-reg" class="hidden"></canvas>

            {{-- Face oval guide --}}
            <div class="face-ring" :class="faceState === 'detected' ? 'detected' : (faceState === 'capturing' ? 'capturing' : '')"></div>

            {{-- Status overlay --}}
            <div class="absolute bottom-3 left-0 right-0 flex justify-center">
                <div x-show="faceState === 'idle'" class="bg-black/50 text-white/80 text-xs px-3 py-1 rounded-full">
                    <i class="fas fa-circle-notch fa-spin mr-1"></i> Mendeteksi wajah...
                </div>
                <div x-show="faceState === 'detected'" class="bg-green-500/80 text-white text-xs px-3 py-1.5 rounded-full font-semibold pulse-ring">
                    <i class="fas fa-check mr-1"></i> Wajah terdeteksi!
                </div>
                <div x-show="faceState === 'capturing'" class="bg-yellow-400/90 text-black text-xs px-3 py-1.5 rounded-full font-semibold">
                    <i class="fas fa-camera mr-1"></i> Mengambil foto...
                </div>
                <div x-show="capturedImages.length >= 5 && faceState !== 'capturing'" class="bg-green-600/90 text-white text-xs px-3 py-1.5 rounded-full font-semibold">
                    <i class="fas fa-check-double mr-1"></i> Semua foto berhasil!
                </div>
            </div>
        </div>

        {{-- Thumbnail preview --}}
        <div class="flex gap-2 justify-center mb-5">
            <template x-for="i in 5" :key="i">
                <div class="w-12 h-12 rounded-xl overflow-hidden border-2 transition-all"
                     :class="capturedImages.length >= i ? 'border-green-400' : 'border-white/20 bg-white/10'">
                    <template x-if="capturedImages[i-1]">
                        <div class="relative w-full h-full">
                            <img :src="capturedImages[i-1]" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-green-400/60 flex items-center justify-center text-white text-sm">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </template>
                    <template x-if="!capturedImages[i-1]">
                        <div class="w-full h-full flex items-center justify-center text-white/30 text-xs">
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
                class="w-full mb-3 bg-white/20 hover:bg-white/30 text-white font-semibold py-3 rounded-xl transition border border-white/30 flex items-center justify-center gap-2">
            <i class="fas fa-camera"></i>
            <span x-text="'Ambil Foto ' + (capturedImages.length + 1) + ' / 5'"></span>
        </button>

        {{-- Submit form (step 2) --}}
        <form id="form-step2" method="POST" action="{{ route('register.siswa') }}">
            @csrf
            <input type="hidden" name="face_images" id="face-images-input">

            <button type="button" @click="submitFace()"
                    :disabled="capturedImages.length < 5 || isSubmitting"
                    :class="capturedImages.length >= 5 && !isSubmitting ? 'bg-green-600 hover:bg-green-700 cursor-pointer' : 'bg-white/20 cursor-not-allowed'"
                    class="w-full text-white font-bold py-3.5 rounded-xl transition-all duration-200 shadow-lg flex items-center justify-center gap-2">
                <template x-if="!isSubmitting">
                    <span><i class="fas fa-user-check mr-2"></i>Daftar & Simpan Wajah</span>
                </template>
                <template x-if="isSubmitting">
                    <span><i class="fas fa-spinner fa-spin mr-2"></i>Memproses...</span>
                </template>
            </button>
        </form>

        <button @click="step = 1; stopCamera()" class="w-full mt-3 text-white/60 text-sm hover:text-white transition text-center">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ubah data
        </button>
    </div>

    <footer class="mt-auto pt-4 text-center text-xs text-white/80">
        &copy; {{ date('Y') }} Presensi Sekolah. Developed by <span class="font-semibold text-white">Dimas A.F</span>.
    </footer>

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
</body>
</html>