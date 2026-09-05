<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan Wajah — Presensi Sekolah</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tambahkan face-api.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/dist/face-api.min.js"></script>
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
            transition: border-color .3s, box-shadow .3s;
        }
        .face-ring.detected {
            border-color: #4ade80;
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.45), 0 0 20px rgba(74,222,128,0.5);
        }
        .face-ring.success-pulse {
            border-color: #3b82f6;
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.45), 0 0 30px rgba(59,130,246,0.8);
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
                Halo, <strong class="text-white">{{ $user->name }}</strong>! Ikuti instruksi liveness di bawah.
            </p>
        </div>

        {{-- Status message --}}
        <div x-show="message" x-cloak class="rounded-xl px-4 py-3 text-sm font-semibold text-center mb-4 transition-all"
             :class="isSuccess ? 'bg-emerald-500/20 border border-emerald-400/50 text-emerald-100' : 'bg-blue-500/20 border border-blue-400/50 text-blue-100'">
            <i class="fas fa-info-circle mr-1"></i> <span x-text="message"></span>
        </div>

        {{-- Instruksi --}}
        <div x-show="!isSuccess" class="bg-white/10 rounded-2xl px-4 py-3 text-center mb-4 border border-white/20 shadow-inner">
            <p class="text-white/60 text-[0.65rem] uppercase tracking-widest font-bold mb-1" x-text="isLoadingModels ? 'Mempersiapkan AI...' : 'Tugas Liveness'"></p>
            <p x-text="currentInstruction" class="text-white font-extrabold text-base"></p>
        </div>

        {{-- Progress --}}
        <div x-show="!isSuccess" class="flex justify-center gap-2 mb-4">
            <template x-for="i in 5" :key="i">
                <div class="h-2.5 rounded-full transition-all duration-500"
                     :class="capturedImages.length >= i ? 'bg-green-400 w-8 shadow-[0_0_8px_rgba(74,222,128,0.6)]' : 'bg-white/20 w-8'"></div>
            </template>
        </div>

        {{-- Camera --}}
        <div x-show="!isSuccess" class="relative bg-black rounded-2xl overflow-hidden mb-4 shadow-lg border border-white/10" style="height:260px;">
            <video id="video-enroll" autoplay playsinline muted></video>
            <canvas id="canvas-enroll" class="hidden"></canvas>
            <div class="face-ring" :class="{ 'detected': isFaceCentered, 'success-pulse': showPulse }"></div>

            <div class="absolute inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm" x-show="isLoadingModels">
                <div class="text-center">
                    <i class="fas fa-circle-notch fa-spin text-3xl text-blue-400 mb-2"></i>
                    <p class="text-white text-xs font-semibold mt-2">Memuat Model AI...</p>
                </div>
            </div>

            <div class="absolute bottom-3 left-0 right-0 flex justify-center">
                <div x-show="capturedImages.length < 5 && !isLoadingModels" class="bg-black/60 backdrop-blur-md text-white text-xs px-4 py-1.5 rounded-full font-bold border border-white/10">
                    <span x-text="'Langkah ' + (capturedImages.length + 1) + ' / 5'"></span>
                </div>
                <div x-show="capturedImages.length >= 5" class="bg-green-500/80 backdrop-blur-md text-white text-xs px-4 py-1.5 rounded-full font-bold border border-green-400/50">
                    <i class="fas fa-check mr-1"></i> Semua selesai!
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
                            <img :src="capturedImages[i-1]" class="w-full h-full object-cover" style="transform: scaleX(-1);">
                            <div class="absolute inset-0 bg-green-400/30 flex items-center justify-center text-white text-xs backdrop-blur-[1px]">
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
            <h2 class="text-2xl font-extrabold text-white mb-2">Wajah Terdaftar! 🎉</h2>
            <p class="text-green-100/70 text-sm">Kamu sekarang bisa absen menggunakan fitur Face ID.</p>
            <a href="{{ route('siswa.dashboard') }}"
               class="mt-6 flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 rounded-xl transition shadow-[0_0_15px_rgba(22,163,74,0.4)]">
                Ke Dashboard <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        {{-- Buttons (Loading/Processing fallback) --}}
        <div x-show="!isSuccess && (isProcessing || capturedImages.length >= 5)" class="mt-4">
            <button disabled
                    class="w-full text-white font-bold py-3.5 rounded-xl shadow-lg flex items-center justify-center gap-2 bg-white/20 cursor-not-allowed backdrop-blur-sm border border-white/20">
                <i class="fas fa-spinner fa-spin text-lg"></i>
                <span>Menyimpan data wajah...</span>
            </button>
        </div>

        <a href="{{ route('siswa.dashboard') }}" x-show="!isSuccess && !isProcessing" class="block text-center text-white/50 text-xs hover:text-white mt-5 transition font-medium">
            Lewati pendaftaran &rarr;
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

            get currentInstruction() {
                if (this.isLoadingModels) return 'Mohon tunggu...';
                if (this.capturedImages.length === 5) return 'Memproses Wajah...';
                
                const instructions = [
                    'Hadap ke depan, wajah di tengah',
                    'Kedipkan mata Anda',
                    'Tengok ke KIRI atau KANAN',
                    'Sekarang tengok ke arah SEBALIKNYA',
                    'Tersenyumlah (Buka mulut sedikit)'
                ];
                return instructions[this.capturedImages.length];
            },

            async startCamera() {
                this.message = 'Meminta akses kamera...';
                try {
                    this.videoStream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                        audio: false
                    });
                    const video = document.getElementById('video-enroll');
                    video.srcObject = this.videoStream;
                    
                    this.message = 'Mengunduh AI Model (2MB)...';
                    // Load face-api models from CDN
                    const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/model/';
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL)
                    ]);
                    
                    this.isLoadingModels = false;
                    this.message = '';
                    
                    video.onloadedmetadata = () => {
                        video.width = video.videoWidth;
                        video.height = video.videoHeight;
                    };
                    
                    await video.play();
                    this.startDetectionLoop(video);
                } catch (err) {
                    this.message = 'Gagal mengakses kamera. Izinkan akses kamera di browser.';
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
                    
                    // Detect face and landmarks
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
                            // Ensure face is reasonably large (width > 80px)
                            if (box.width > 80) {
                                passed = true;
                            }
                        } 
                        else if (step === 1) {
                            // Step 1: Blink
                            const leftEye = landmarks.getLeftEye();
                            const rightEye = landmarks.getRightEye();
                            const earL = this.getEAR(leftEye);
                            const earR = this.getEAR(rightEye);
                            const avgEAR = (earL + earR) / 2;
                            
                            if (avgEAR < 0.25) {
                                this.blinkState = 'closed';
                            } else if (avgEAR > 0.28 && this.blinkState === 'closed') {
                                this.blinkState = 'open';
                                passed = true;
                            }
                        }
                        else if (step === 2) {
                            // Step 2: Turn Left or Right
                            const nose = landmarks.getNose()[3];
                            const leftJaw = landmarks.getJawOutline()[0];
                            const rightJaw = landmarks.getJawOutline()[16];
                            const distLeft = Math.abs(nose.x - leftJaw.x);
                            const distRight = Math.abs(nose.x - rightJaw.x);
                            
                            if (distLeft > distRight * 1.4) {
                                this.turnedDirection = 'left';
                                passed = true;
                            } else if (distRight > distLeft * 1.4) {
                                this.turnedDirection = 'right';
                                passed = true;
                            }
                        }
                        else if (step === 3) {
                            // Step 3: Turn Opposite
                            const nose = landmarks.getNose()[3];
                            const leftJaw = landmarks.getJawOutline()[0];
                            const rightJaw = landmarks.getJawOutline()[16];
                            const distLeft = Math.abs(nose.x - leftJaw.x);
                            const distRight = Math.abs(nose.x - rightJaw.x);
                            
                            if (this.turnedDirection === 'left' && distRight > distLeft * 1.4) {
                                passed = true;
                            } else if (this.turnedDirection === 'right' && distLeft > distRight * 1.4) {
                                passed = true;
                            }
                        }
                        else if (step === 4) {
                            // Step 4: Smile (Mouth Aspect Ratio)
                            const mouth = landmarks.getMouth();
                            const width = Math.hypot(mouth[0].x - mouth[6].x, mouth[0].y - mouth[6].y);
                            const jawWidth = Math.abs(landmarks.getJawOutline()[0].x - landmarks.getJawOutline()[16].x);
                            
                            // Smile widens the mouth relative to jaw
                            if (width / jawWidth > 0.38) {
                                passed = true;
                            }
                        }
                        
                        if (passed) {
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
                // Calculate Eye Aspect Ratio
                const width = Math.hypot(eye[0].x - eye[3].x, eye[0].y - eye[3].y);
                const h1 = Math.hypot(eye[1].x - eye[5].x, eye[1].y - eye[5].y);
                const h2 = Math.hypot(eye[2].x - eye[4].x, eye[2].y - eye[4].y);
                return (h1 + h2) / (2.0 * width);
            },
            
            triggerPulse() {
                this.showPulse = true;
                setTimeout(() => this.showPulse = false, 300);
            },

            captureFrame() {
                const video  = document.getElementById('video-enroll');
                const canvas = document.getElementById('canvas-enroll');
                canvas.width  = video.videoWidth  || 640;
                canvas.height = video.videoHeight || 480;
                const ctx = canvas.getContext('2d');
                ctx.save();
                ctx.scale(-1, 1); // mirror for backend processing/saving
                ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                ctx.restore();

                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                this.capturedImages.push(dataUrl);
            },

            async submitEnroll() {
                if (this.isProcessing) return;
                this.isProcessing = true;
                this.message = 'Menyimpan profil wajah...';

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
</body>
</html>
