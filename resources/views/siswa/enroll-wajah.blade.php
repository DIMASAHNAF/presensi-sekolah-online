<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan Wajah — Presensi Sekolah</title>
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
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.35);
        }
        [x-cloak] { display: none !important; }

        .face-ring {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -55%);
            width: 200px; height: 250px;
            border-radius: 50% / 45%;
            border: 3px solid rgba(255,255,255,0.4);
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.45);
            pointer-events: none;
            z-index: 10;
            transition: border-color .3s;
        }
        .face-ring.detected {
            border-color: #4ade80;
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.45), 0 0 20px rgba(74,222,128,0.5);
        }
        #video-enroll { width:100%; height:100%; object-fit:cover; transform:scaleX(-1); }

        @keyframes checkmark-pop {
            0%   { transform: scale(0); opacity: 0; }
            70%  { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1);   opacity: 1; }
        }
        .check-pop { animation: checkmark-pop .4s cubic-bezier(.17,.67,.4,1.2) forwards; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 py-10"
      x-data="enrollApp()" x-init="startCamera()">

    <div class="glass rounded-3xl shadow-2xl p-6 w-full max-w-md mb-8">

        {{-- Header --}}
        <div class="text-center mb-5">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-face-smile text-white text-2xl"></i>
            </div>
            <h1 class="text-xl font-extrabold text-white">Daftarkan Wajah</h1>
            <p class="text-green-50/70 text-xs mt-1">
                Halo, <strong class="text-white">{{ $user->name }}</strong>! Daftarkan wajah kamu agar bisa absen.
            </p>
        </div>

        {{-- Status message --}}
        <div x-show="message" x-cloak class="rounded-xl px-4 py-3 text-sm font-semibold text-center mb-4 transition-all"
             :class="isSuccess ? 'bg-emerald-500/20 border border-emerald-400/50 text-emerald-100' : 'bg-red-500/20 border border-red-400/50 text-red-100'">
            <span x-text="message"></span>
        </div>

        {{-- Instruksi --}}
        <div x-show="!isSuccess" class="bg-white/10 rounded-2xl px-4 py-2.5 text-center mb-4">
            <p class="text-white/60 text-xs uppercase tracking-wider font-semibold">Instruksi</p>
            <p x-text="currentInstruction" class="text-white font-bold text-base mt-0.5"></p>
        </div>

        {{-- Progress --}}
        <div x-show="!isSuccess" class="flex justify-center gap-2 mb-4">
            <template x-for="i in 5" :key="i">
                <div class="h-2 rounded-full transition-all duration-300"
                     :class="capturedImages.length >= i ? 'bg-green-400 w-8' : 'bg-white/20 w-8'"></div>
            </template>
        </div>

        {{-- Camera --}}
        <div x-show="!isSuccess" class="relative bg-black rounded-2xl overflow-hidden mb-4" style="height:260px;">
            <video id="video-enroll" autoplay playsinline muted></video>
            <canvas id="canvas-enroll" class="hidden"></canvas>
            <div class="face-ring" :class="capturedImages.length < 5 ? 'detected' : ''"></div>

            <div class="absolute bottom-3 left-0 right-0 flex justify-center">
                <div x-show="capturedImages.length < 5" class="bg-green-500/80 text-white text-xs px-3 py-1.5 rounded-full font-semibold">
                    <i class="fas fa-camera mr-1"></i>
                    <span x-text="'Foto ' + (capturedImages.length + 1) + ' / 5'"></span>
                </div>
                <div x-show="capturedImages.length >= 5" class="bg-blue-500/80 text-white text-xs px-3 py-1.5 rounded-full font-semibold">
                    <i class="fas fa-check mr-1"></i> Semua foto berhasil!
                </div>
            </div>
        </div>

        {{-- Thumbnails --}}
        <div x-show="!isSuccess" class="flex gap-2 justify-center mb-4">
            <template x-for="i in 5" :key="i">
                <div class="w-11 h-11 rounded-xl overflow-hidden border-2 transition-all"
                     :class="capturedImages.length >= i ? 'border-green-400' : 'border-white/20 bg-white/10'">
                    <template x-if="capturedImages[i-1]">
                        <div class="relative w-full h-full">
                            <img :src="capturedImages[i-1]" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-green-400/50 flex items-center justify-center text-white text-xs">
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

        {{-- Success state --}}
        <div x-show="isSuccess" x-cloak class="text-center py-8 check-pop">
            <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-green-400">
                <i class="fas fa-check text-4xl text-green-400"></i>
            </div>
            <h2 class="text-xl font-extrabold text-white mb-2">Wajah Terdaftar! 🎉</h2>
            <p class="text-green-100/70 text-sm">Kamu sekarang bisa absen dengan scan wajah dari dashboard.</p>
            <a href="{{ route('siswa.dashboard') }}"
               class="mt-6 inline-block bg-green-600 hover:bg-green-700 text-white font-bold px-8 py-3 rounded-xl transition">
                <i class="fas fa-arrow-right mr-2"></i>Ke Dashboard
            </a>
        </div>

        {{-- Buttons --}}
        <div x-show="!isSuccess">
            {{-- Ambil foto manual --}}
            <button @click="captureFrame()"
                    x-show="capturedImages.length < 5"
                    :disabled="isProcessing"
                    class="w-full mb-3 bg-white/20 hover:bg-white/30 text-white font-semibold py-3 rounded-xl transition border border-white/30 flex items-center justify-center gap-2">
                <i class="fas fa-camera"></i>
                <span x-text="'Ambil Foto ' + (capturedImages.length + 1) + ' / 5'"></span>
            </button>

            {{-- Simpan wajah --}}
            <button @click="submitEnroll()"
                    x-show="capturedImages.length >= 5"
                    :disabled="isProcessing"
                    :class="isProcessing ? 'bg-white/20 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 cursor-pointer'"
                    class="w-full text-white font-bold py-3.5 rounded-xl transition-all duration-200 shadow-lg flex items-center justify-center gap-2">
                <template x-if="!isProcessing">
                    <span><i class="fas fa-user-check mr-2"></i>Simpan & Daftarkan Wajah</span>
                </template>
                <template x-if="isProcessing">
                    <span><i class="fas fa-spinner fa-spin mr-2"></i>Memproses wajah...</span>
                </template>
            </button>

            {{-- Ulangi --}}
            <button @click="resetCapture()"
                    x-show="capturedImages.length > 0 && capturedImages.length < 5"
                    class="w-full mt-3 text-white/50 text-sm hover:text-white/80 transition text-center">
                <i class="fas fa-rotate-right mr-1"></i> Ulangi dari awal
            </button>
        </div>

        <a href="{{ route('siswa.dashboard') }}" class="block text-center text-white/40 text-xs hover:text-white/70 mt-4 transition">
            Lewati dulu &rarr;
        </a>
    </div>

<script>
    function enrollApp() {
        return {
            capturedImages: [],
            isProcessing: false,
            isSuccess: false,
            message: '',
            videoStream: null,

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

            async startCamera() {
                try {
                    this.videoStream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                        audio: false
                    });
                    const video = document.getElementById('video-enroll');
                    video.srcObject = this.videoStream;
                    await video.play();
                } catch (err) {
                    this.message = 'Kamera tidak dapat diakses: ' + err.message;
                }
            },

            captureFrame() {
                if (this.capturedImages.length >= 5 || this.isProcessing) return;

                const video  = document.getElementById('video-enroll');
                const canvas = document.getElementById('canvas-enroll');
                canvas.width  = video.videoWidth  || 640;
                canvas.height = video.videoHeight || 480;
                const ctx = canvas.getContext('2d');
                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                ctx.restore();

                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                this.capturedImages.push(dataUrl);
            },

            resetCapture() {
                this.capturedImages = [];
                this.message = '';
            },

            async submitEnroll() {
                if (this.capturedImages.length < 5 || this.isProcessing) return;
                this.isProcessing = true;
                this.message = '';

                try {
                    const resp = await fetch('{{ route('siswa.enroll.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ face_images: JSON.stringify(this.capturedImages) })
                    });
                    const data = await resp.json();

                    if (data.success) {
                        this.isSuccess = true;
                        this.message = data.message;
                        if (this.videoStream) {
                            this.videoStream.getTracks().forEach(t => t.stop());
                        }
                    } else {
                        this.message = data.message || 'Terjadi kesalahan. Coba lagi.';
                        this.isProcessing = false;
                        this.capturedImages = []; // Reset untuk foto ulang
                    }
                } catch (e) {
                    this.message = 'Koneksi error. Coba lagi.';
                    this.isProcessing = false;
                }
            }
        };
    }
</script>
</body>
</html>
