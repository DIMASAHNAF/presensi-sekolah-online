<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan Wajah — Presensi Siswa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 10% 10%, rgba(37, 99, 235, 0.08) 0px, transparent 50%),
                radial-gradient(at 90% 90%, rgba(16, 185, 129, 0.08) 0px, transparent 50%);
        }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }

        .face-ring {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -55%);
            width: 200px; height: 250px;
            border-radius: 50% / 45%;
            border: 3px solid rgba(255,255,255,0.4);
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.55);
            pointer-events: none;
            z-index: 10;
            transition: border-color .3s, box-shadow .3s;
        }
        .face-ring.detected {
            border-color: #10b981;
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.55), 0 0 20px rgba(16, 185, 129, 0.6);
        }
        .face-ring.success-pulse {
            border-color: #2563eb;
            box-shadow: 0 0 0 9999px rgba(15, 23, 42, 0.55), 0 0 30px rgba(37, 99, 235, 0.8);
        }
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
        #video-enroll { width:100%; height:100%; object-fit:cover; transform:scaleX(-1); }

        @keyframes checkmark-pop {
            0%   { transform: scale(0); opacity: 0; }
            70%  { transform: scale(1.15); opacity: 1; }
            100% { transform: scale(1);   opacity: 1; }
        }
        .check-pop { animation: checkmark-pop .4s cubic-bezier(.17,.67,.4,1.2) forwards; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 py-8 text-slate-800"
      x-data="enrollApp()" x-init="startCamera()">

    <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-6 sm:p-8 w-full max-w-md relative overflow-hidden">
        
        {{-- Decorative top accent line --}}
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-600 via-teal-500 to-emerald-500"></div>

        {{-- Header --}}
        <div class="text-center mb-5">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-600 to-emerald-500 text-white rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-md shadow-blue-500/20">
                <i class="fas fa-face-smile text-2xl"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 font-heading">Daftarkan Wajah</h1>
            <p class="text-slate-500 text-xs mt-1">
                Halo, <strong class="text-slate-800">{{ $user->name }}</strong>! Posisikan wajah untuk perekaman biometrik.
            </p>
        </div>

        {{-- Status message toast --}}
        <div x-show="message" x-cloak class="rounded-xl px-4 py-2.5 text-xs font-semibold text-center mb-4 transition-all"
             :class="isSuccess ? 'bg-emerald-50 border border-emerald-300 text-emerald-800' : 'bg-blue-50 border border-blue-200 text-blue-800'">
            <i class="fas fa-info-circle mr-1"></i> <span x-text="message"></span>
        </div>

        {{-- Instruksi Dinamis --}}
        <div x-show="!isSuccess" class="bg-gradient-to-r from-blue-50/80 to-emerald-50/80 rounded-2xl px-4 py-3.5 text-center mb-4 border border-blue-100 shadow-2xs">
            <p class="text-blue-600 text-[10px] uppercase tracking-widest font-bold mb-0.5">Panduan Perekaman Wajah</p>
            <p x-text="currentInstruction" class="text-slate-800 font-extrabold text-sm sm:text-base"></p>
        </div>

        {{-- Progress Dots 5 Tahap --}}
        <div x-show="!isSuccess" class="flex justify-center gap-2 mb-4">
            <template x-for="i in 5" :key="i">
                <div class="h-2 rounded-full transition-all duration-300"
                     :class="capturedImages.length >= i ? 'bg-emerald-500 w-10 shadow-xs shadow-emerald-500/40' : 'bg-slate-200 w-7'"></div>
            </template>
        </div>

        {{-- Camera Viewport --}}
        <div x-show="!isSuccess" class="relative bg-slate-950 rounded-2xl overflow-hidden mb-4 shadow-md border-2 border-slate-200" style="height:280px;">
            <video id="video-enroll" autoplay playsinline muted></video>
            <canvas id="canvas-enroll" class="hidden"></canvas>
            <div class="face-ring" :class="{ 'detected': isCameraReady, 'success-pulse': showPulse }">
                <div class="scanner-laser" x-show="isCameraReady && !isProcessing && capturedImages.length < 5"></div>
            </div>

            {{-- Status Pill di Bawah Frame --}}
            <div class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                <div x-show="capturedImages.length < 5 && isCameraReady && !isProcessing" class="bg-black/75 backdrop-blur-md text-white text-xs px-3.5 py-1.5 rounded-full font-bold border border-white/10 flex items-center gap-2 shadow-lg">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span x-text="'Langkah ' + (capturedImages.length + 1) + ' / 5'"></span>
                </div>
                <div x-show="capturedImages.length >= 5 || isProcessing" class="bg-blue-600/90 backdrop-blur-md text-white text-xs px-4 py-1.5 rounded-full font-bold border border-blue-400/40 shadow-md">
                    <i class="fas fa-brain mr-1.5"></i> Memproses AI Server...
                </div>
            </div>
        </div>

        {{-- Thumbnails 5 Slot --}}
        <div x-show="!isSuccess" class="flex gap-2 justify-center mb-4">
            <template x-for="i in 5" :key="i">
                <div class="w-11 h-11 rounded-xl overflow-hidden border-2 transition-all bg-slate-50 relative shadow-2xs"
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

        {{-- Tombol Ambil Manual / Cadangan --}}
        <div x-show="!isSuccess && capturedImages.length < 5" class="mb-4">
            <button type="button" @click="manualCapture()"
                    :disabled="isProcessing || !isCameraReady"
                    class="w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-emerald-600 hover:from-blue-700 hover:to-emerald-700 text-white font-extrabold py-3 px-4 rounded-xl shadow-md transition flex items-center justify-center gap-2 text-xs cursor-pointer active:scale-[0.98]">
                <i class="fas fa-camera text-sm"></i>
                <span>Ambil Foto Sekarang (Langkah <span x-text="capturedImages.length + 1"></span> / 5)</span>
            </button>
        </div>

        {{-- Error Retry Button --}}
        <div x-show="!isSuccess && isFailed" class="mb-4">
            <button type="button" @click="restartEnroll()"
                    class="w-full bg-slate-800 hover:bg-slate-900 text-white font-extrabold py-3 px-4 rounded-xl shadow-sm transition flex items-center justify-center gap-2 text-xs cursor-pointer">
                <i class="fas fa-rotate-right"></i>
                <span>Ulangi Pendaftaran Wajah</span>
            </button>
        </div>

        {{-- Success State --}}
        <div x-show="isSuccess" x-cloak class="text-center py-6 check-pop">
            <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-emerald-400 shadow-lg shadow-emerald-500/10">
                <i class="fas fa-check text-4xl text-emerald-600"></i>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 mb-1 font-heading">Wajah Berhasil Didaftarkan! 🎉</h2>
            <p class="text-slate-500 text-xs">Profil biometrik Anda kini aktif untuk absensi mandiri di kelas.</p>
            <a href="{{ route('siswa.dashboard') }}"
               class="mt-6 flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-emerald-600 hover:from-blue-700 hover:to-emerald-700 text-white font-extrabold py-3.5 rounded-xl transition shadow-lg shadow-blue-500/20 text-sm">
                <span>Ke Dashboard Siswa</span> <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        {{-- Processing Banner --}}
        <div x-show="!isSuccess && (isProcessing || capturedImages.length >= 5)" class="mt-2">
            <button disabled
                    class="w-full text-white font-bold py-3.5 rounded-xl shadow-md flex items-center justify-center gap-2 bg-slate-700 cursor-not-allowed text-sm">
                <i class="fas fa-spinner fa-spin text-base text-blue-400"></i>
                <span>Menghitung vektor biometrik di AI Server...</span>
            </button>
        </div>

        <a href="{{ route('siswa.dashboard') }}" x-show="!isSuccess && !isProcessing" class="block text-center text-slate-400 hover:text-slate-700 text-xs mt-4 transition font-semibold">
            Kembali ke Dashboard &rarr;
        </a>
    </div>

<script>
    function enrollApp() {
        return {
            capturedImages: [],
            isProcessing: false,
            isSuccess: false,
            isFailed: false,
            isCameraReady: false,
            message: '',
            videoStream: null,
            showPulse: false,

            get currentInstruction() {
                if (this.capturedImages.length >= 5) return 'Memproses profil wajah di server AI...';
                
                const instructions = [
                    '1. Hadap tegak ke depan, wajah di dalam oval',
                    '2. Tolehkan kepala sedikit ke KIRI',
                    '3. Sekarang tolehkan sedikit ke KANAN',
                    '4. Tersenyumlah santai ke arah kamera',
                    '5. Hadap tegak ke depan (posisi stabil)'
                ];
                return instructions[this.capturedImages.length] || 'Posisikan wajah Anda';
            },

            async startCamera() {
                this.message = 'Membuka kamera...';
                try {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        throw new Error("Akses kamera diblokir browser. Pastikan menggunakan HTTPS.");
                    }
                    this.videoStream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                        audio: false
                    });
                    const video = document.getElementById('video-enroll');
                    video.srcObject = this.videoStream;
                    
                    video.onloadedmetadata = async () => {
                        video.width = video.videoWidth || 640;
                        video.height = video.videoHeight || 480;
                        try {
                            await video.play();
                        } catch (e) {}
                        this.isCameraReady = true;
                        this.message = '';
                    };
                } catch (err) {
                    this.isCameraReady = false;
                    this.message = 'Gagal mengakses kamera. Pastikan izin kamera telah diberikan.';
                }
            },

            manualCapture() {
                if (this.capturedImages.length >= 5 || this.isProcessing || !this.isCameraReady) return;
                this.takeSnapshot();
            },

            takeSnapshot() {
                this.triggerPulse();
                this.captureFrame();

                if (this.capturedImages.length >= 5) {
                    this.submitEnroll();
                }
            },

            triggerPulse() {
                this.showPulse = true;
                setTimeout(() => this.showPulse = false, 300);
            },

            captureFrame() {
                const video  = document.getElementById('video-enroll');
                const canvas = document.getElementById('canvas-enroll');
                if (!video || !canvas) return;

                canvas.width  = 640;
                canvas.height = 480;
                const ctx = canvas.getContext('2d');
                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                ctx.restore();

                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                this.capturedImages.push(dataUrl);
            },

            restartEnroll() {
                this.capturedImages = [];
                this.isFailed = false;
                this.isProcessing = false;
                this.message = '';
            },

            async submitEnroll() {
                if (this.isProcessing) return;
                this.isProcessing = true;
                this.isFailed = false;
                this.message = 'Menganalisis 5 sampel wajah di server AI...';

                try {
                    const resp = await fetch('{{ route('siswa.enroll.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ face_images: JSON.stringify(this.capturedImages) })
                    });
                    
                    let data;
                    try {
                        data = await resp.json();
                    } catch (parseErr) {
                        data = { success: false, message: 'Server merespons status ' + resp.status + '. Silakan coba lagi.' };
                    }

                    if (data.success) {
                        this.isSuccess = true;
                        this.message = '';
                        if (this.videoStream) {
                            this.videoStream.getTracks().forEach(t => t.stop());
                            this.videoStream = null;
                        }
                    } else {
                        this.message = data.message || 'Gagal memproses wajah. Pastikan cahaya terang dan wajah terlihat jelas.';
                        this.isProcessing = false;
                        this.isFailed = true;
                    }
                } catch (e) {
                    console.error('Enroll submission error:', e);
                    this.message = 'Koneksi error ke server. Pastikan jaringan internet stabil.';
                    this.isProcessing = false;
                    this.isFailed = true;
                }
            }
        };
    }
</script>
    <x-page-loader />
</body>
</html>
