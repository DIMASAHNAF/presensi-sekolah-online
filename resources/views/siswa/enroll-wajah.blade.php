<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan Wajah — Presensi Siswa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/dist/face-api.min.js"></script>
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
            <p class="text-blue-600 text-[10px] uppercase tracking-widest font-bold mb-0.5" x-text="isLoadingModels ? 'Mempersiapkan AI...' : 'Tugas Perekaman'"></p>
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
            <div class="face-ring" :class="{ 'detected': isFaceCentered, 'success-pulse': showPulse }"></div>

            {{-- Loading overlay --}}
            <div class="absolute inset-0 flex items-center justify-center bg-slate-900/80 backdrop-blur-xs z-30" x-show="isLoadingModels">
                <div class="text-center text-white">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-blue-400 mb-2"></i>
                    <p class="text-xs font-semibold text-slate-200">Memuat AI Biometrik...</p>
                </div>
            </div>

            {{-- Status Pill di Bawah Frame --}}
            <div class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                <div x-show="capturedImages.length < 5 && !isLoadingModels" class="bg-black/70 backdrop-blur-sm text-white text-xs px-3.5 py-1.5 rounded-full font-bold border border-white/10 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full" :class="isFaceCentered ? 'bg-emerald-400 animate-pulse' : 'bg-amber-400'"></span>
                    <span x-text="'Langkah ' + (capturedImages.length + 1) + ' / 5'"></span>
                </div>
                <div x-show="capturedImages.length >= 5" class="bg-emerald-600 backdrop-blur-sm text-white text-xs px-4 py-1.5 rounded-full font-bold border border-emerald-400/50 shadow-md">
                    <i class="fas fa-check mr-1"></i> Semua selesai!
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

        {{-- Tombol Cadangan Manual jika Diperlukan --}}
        <div x-show="!isSuccess && capturedImages.length < 5" class="mb-4">
            <button type="button" @click="manualCapture()"
                    :disabled="isProcessing || isLoadingModels"
                    class="w-full bg-white hover:bg-slate-50 text-slate-700 font-semibold py-2.5 px-4 rounded-xl border border-slate-300 shadow-2xs transition flex items-center justify-center gap-2 text-xs">
                <i class="fas fa-camera text-blue-600"></i>
                <span>Ambil Manual Sekarang (Foto <span x-text="capturedImages.length + 1"></span> / 5)</span>
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

        {{-- Buttons (Loading/Processing fallback) --}}
        <div x-show="!isSuccess && (isProcessing || capturedImages.length >= 5)" class="mt-2">
            <button disabled
                    class="w-full text-white font-bold py-3.5 rounded-xl shadow-md flex items-center justify-center gap-2 bg-slate-400 cursor-not-allowed text-sm">
                <i class="fas fa-spinner fa-spin text-base"></i>
                <span>Menyimpan data biometrik wajah...</span>
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
            isLoadingModels: true,
            message: '',
            videoStream: null,
            detectionInterval: null,
            
            // UI States
            isFaceCentered: false,
            showPulse: false,
            
            // Liveness States
            blinkState: 'open',
            turnedDirection: null,
            lastAutoCapture: 0,

            get currentInstruction() {
                if (this.isLoadingModels) return 'Mohon tunggu mempersiapkan AI...';
                if (this.capturedImages.length === 5) return 'Memproses Profil Wajah...';
                
                const instructions = [
                    'Hadap ke depan, wajah di dalam oval',
                    'Bagus! Kedipkan mata Anda',
                    'Tolehkan kepala sedikit ke KIRI',
                    'Sekarang tolehkan sedikit ke KANAN',
                    'Tersenyumlah (atau hadap tegak)'
                ];
                return instructions[this.capturedImages.length];
            },

            async startCamera() {
                this.message = 'Meminta akses kamera...';
                try {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        throw new Error("Akses kamera diblokir browser. Pastikan Anda menggunakan HTTPS atau Localhost.");
                    }
                    this.videoStream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                        audio: false
                    });
                    const video = document.getElementById('video-enroll');
                    video.srcObject = this.videoStream;
                    
                    this.message = 'Mengunduh modul AI wajah...';
                    const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/model/';
                    if (typeof faceapi !== 'undefined') {
                        await Promise.all([
                            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL)
                        ]);
                    }
                    
                    this.isLoadingModels = false;
                    this.message = '';
                    
                    video.onloadedmetadata = () => {
                        video.width = video.videoWidth;
                        video.height = video.videoHeight;
                    };
                    
                    await video.play();
                    this.startDetectionLoop(video);
                } catch (err) {
                    this.isLoadingModels = false;
                    this.message = 'Gagal mengakses kamera. Pastikan izin kamera telah diberikan.';
                }
            },
            
            startDetectionLoop(video) {
                this.detectionInterval = setInterval(async () => {
                    if (this.isProcessing || this.isSuccess || this.isLoadingModels) return;
                    if (this.capturedImages.length >= 5) {
                        clearInterval(this.detectionInterval);
                        this.submitEnroll();
                        return;
                    }
                    
                    if (typeof faceapi === 'undefined') return;

                    const detections = await faceapi.detectSingleFace(
                        video, 
                        new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.3, inputSize: 224 })
                    ).withFaceLandmarks();
                    
                    if (detections) {
                        this.isFaceCentered = true;
                        const landmarks = detections.landmarks;
                        const step = this.capturedImages.length;
                        let passed = false;
                        
                        if (step === 0) {
                            // Step 0: Center face
                            const box = detections.detection.box;
                            if (box.width > 75) passed = true;
                        } 
                        else if (step === 1) {
                            // Step 1: Blink
                            const leftEye = landmarks.getLeftEye();
                            const rightEye = landmarks.getRightEye();
                            const earL = this.getEAR(leftEye);
                            const earR = this.getEAR(rightEye);
                            const avgEAR = (earL + earR) / 2;
                            
                            // Toleransi kedip diperbesar dari 0.27 menjadi 0.29 agar lebih mudah
                            if (avgEAR < 0.29) {
                                this.blinkState = 'closed';
                            } else if (avgEAR > 0.29 && this.blinkState === 'closed') {
                                this.blinkState = 'open';
                                passed = true;
                            }
                            // Auto-pass setelah 3.5 detik agar tidak macet
                            else if (Date.now() - this.lastAutoCapture > 3500) {
                                passed = true;
                            }
                        }
                        else if (step === 2) {
                            // Step 2: Turn Left
                            const nose = landmarks.getNose()[3];
                            const leftJaw = landmarks.getJawOutline()[0];
                            const rightJaw = landmarks.getJawOutline()[16];
                            const distLeft = Math.abs(nose.x - leftJaw.x);
                            const distRight = Math.abs(nose.x - rightJaw.x);
                            
                            // Toleransi toleh diperkecil dari 1.3 ke 1.15
                            if (distLeft > distRight * 1.15 || distRight > distLeft * 1.15) {
                                this.turnedDirection = distLeft > distRight ? 'left' : 'right';
                                passed = true;
                            }
                            // Auto-pass setelah 3.5 detik
                            else if (Date.now() - this.lastAutoCapture > 3500) {
                                this.turnedDirection = 'none';
                                passed = true;
                            }
                        }
                        else if (step === 3) {
                            // Step 3: Turn Opposite or keep stable
                            const nose = landmarks.getNose()[3];
                            const leftJaw = landmarks.getJawOutline()[0];
                            const rightJaw = landmarks.getJawOutline()[16];
                            const distLeft = Math.abs(nose.x - leftJaw.x);
                            const distRight = Math.abs(nose.x - rightJaw.x);
                            
                            if (this.turnedDirection === 'left' && distRight > distLeft * 1.15) {
                                passed = true;
                            } else if (this.turnedDirection === 'right' && distLeft > distRight * 1.15) {
                                passed = true;
                            } else if (Date.now() - this.lastAutoCapture > 2500) {
                                // Graceful auto-progression
                                passed = true;
                            }
                        }
                        else if (step === 4) {
                            // Step 4: Smile / Final frame
                            const mouth = landmarks.getMouth();
                            const width = Math.hypot(mouth[0].x - mouth[6].x, mouth[0].y - mouth[6].y);
                            const jawWidth = Math.abs(landmarks.getJawOutline()[0].x - landmarks.getJawOutline()[16].x);
                            
                            if (width / jawWidth > 0.36 || Date.now() - this.lastAutoCapture > 1200) {
                                passed = true;
                            }
                        }
                        
                        if (passed) {
                            this.lastAutoCapture = Date.now();
                            this.triggerPulse();
                            this.captureFrame();
                            
                            if (this.capturedImages.length === 5) {
                                clearInterval(this.detectionInterval);
                                setTimeout(() => this.submitEnroll(), 500);
                            }
                        }
                    } else {
                        this.isFaceCentered = false;
                    }
                }, 150);
            },
            
            getEAR(eye) {
                const width = Math.hypot(eye[0].x - eye[3].x, eye[0].y - eye[3].y);
                const h1 = Math.hypot(eye[1].x - eye[5].x, eye[1].y - eye[5].y);
                const h2 = Math.hypot(eye[2].x - eye[4].x, eye[2].y - eye[4].y);
                return (h1 + h2) / (2.0 * width);
            },
            
            triggerPulse() {
                this.showPulse = true;
                setTimeout(() => this.showPulse = false, 300);
            },

            manualCapture() {
                if (this.capturedImages.length >= 5 || this.isProcessing) return;
                this.triggerPulse();
                this.captureFrame();
                if (this.capturedImages.length === 5) {
                    if (this.detectionInterval) clearInterval(this.detectionInterval);
                    setTimeout(() => this.submitEnroll(), 400);
                }
            },

            captureFrame() {
                const video  = document.getElementById('video-enroll');
                const canvas = document.getElementById('canvas-enroll');
                if (!video || !canvas) return;

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

            async submitEnroll() {
                if (this.isProcessing) return;
                this.isProcessing = true;
                this.message = 'Menyimpan profil biometrik wajah ke server...';

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
                        this.message = '';
                        if (this.videoStream) {
                            this.videoStream.getTracks().forEach(t => t.stop());
                        }
                    } else {
                        this.message = data.message || 'Terjadi kesalahan. Coba muat ulang halaman.';
                        this.isProcessing = false;
                    }
                } catch (e) {
                    this.message = 'Koneksi error. Gagal menyimpan wajah.';
                    this.isProcessing = false;
                }
            }
        };
    }
</script>
    <x-page-loader />
</body>
</html>
