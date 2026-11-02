<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Face ID Setup — {{ $user->name }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
    
    <!-- Tailwind CSS, AlpineJS, Face-API & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('vendor/face-api/face-api.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Outfit", "Plus Jakarta Sans", sans-serif;
            background-color: #000000;
            color: #f5f5f7;
            overflow-x: hidden;
            user-select: none;
        }
        h1, h2, h3, .font-apple {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Outfit", sans-serif;
            letter-spacing: -0.025em;
        }
        [x-cloak] { display: none !important; }

        /* Apple Face ID Stage (340px) */
        .faceid-stage {
            position: relative;
            width: 340px;
            height: 340px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Central Circular Camera Viewport (240px) */
        .faceid-viewport {
            position: relative;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            overflow: hidden;
            background: #000;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.12), 0 20px 40px rgba(0, 0, 0, 0.85);
            z-index: 10;
        }

        #video-enroll {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        /* Canvas Radial Ticks Ring (Overlays Stage) */
        #faceid-ring-canvas {
            position: absolute;
            inset: 0;
            width: 340px;
            height: 340px;
            pointer-events: none;
            z-index: 20;
        }

        /* Apple Shutter Vignette Flash */
        .shutter-flash {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.4) 70%, transparent 100%);
            z-index: 30;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.08s ease-in;
        }
        .shutter-flash.active {
            opacity: 1;
            transition: none;
        }

        /* Continuous Smooth Success Ring */
        .success-solid-ring {
            position: absolute;
            width: 248px;
            height: 248px;
            border-radius: 50%;
            border: 4px solid #30D158;
            box-shadow: 0 0 25px rgba(48, 209, 88, 0.65), inset 0 0 15px rgba(48, 209, 88, 0.35);
            pointer-events: none;
            z-index: 15;
            animation: ring-pulse 2s infinite ease-in-out;
        }

        @keyframes ring-pulse {
            0%, 100% { transform: scale(1); opacity: 0.95; }
            50% { transform: scale(1.02); opacity: 1; }
        }

        /* Apple Draw Checkmark Animation */
        .checkmark-circle {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 3.5;
            stroke-miterlimit: 10;
            stroke: #30D158;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        .checkmark-check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            stroke-width: 4;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke: #30D158;
            fill: none;
            animation: stroke 0.4s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards;
        }

        @keyframes stroke {
            100% { stroke-dashoffset: 0; }
        }

        /* Apple Glass Pill */
        .apple-pill {
            background: rgba(30, 30, 32, 0.85);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 0.5px solid rgba(255, 255, 255, 0.16);
        }

        /* Apple Button */
        .apple-button {
            background-color: #0071e3;
            color: #ffffff;
            font-weight: 600;
            border-radius: 980px;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .apple-button:active {
            transform: scale(0.97);
            background-color: #0077ed;
        }

        /* Pulse guide animation */
        @keyframes subtle-bounce-x {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(-6px); }
        }
        .bounce-left { animation: subtle-bounce-x 1.2s infinite ease-in-out; }

        @keyframes subtle-bounce-right {
            0%, 100% { transform: translateX(0); }
            50% { transform: translateX(6px); }
        }
        .bounce-right { animation: subtle-bounce-right 1.2s infinite ease-in-out; }
    </style>
</head>
<body class="min-h-screen w-screen flex flex-col justify-between items-center px-4 py-6 bg-black select-none"
      x-data="faceIdAppleApp()" x-init="init()">

    <!-- TOP: APPLE HEADER & ICON -->
    <header class="w-full max-w-md text-center pt-1 sm:pt-3">
        <!-- Iconic Apple Face ID Icon (Square with bracket corners & eyes/smile) -->
        <div class="inline-flex items-center justify-center w-11 h-11 rounded-2xl bg-zinc-900/90 border border-white/10 mb-2 shadow-lg shadow-black">
            <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 8V6a2 2 0 0 1 2-2h2" />
                <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                <path d="M20 16v2a2 2 0 0 1-2 2h-2" />
                <path d="M8 20H6a2 2 0 0 1-2-2v-2" />
                <line x1="9" y1="10" x2="9.01" y2="10" stroke-width="2.6" />
                <line x1="15" y1="10" x2="15.01" y2="10" stroke-width="2.6" />
                <path d="M12 11v3" />
                <path d="M9 16c1 1 5 1 6 0" />
            </svg>
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold text-white font-apple tracking-tight"
            x-text="isSuccess ? 'Face ID Telah Diatur' : 'Pengaturan Face ID'">
            Pengaturan Face ID
        </h1>
        
        <p class="text-zinc-400 text-xs sm:text-sm mt-1 px-4 font-normal leading-relaxed"
           x-text="isSuccess ? 'Wajah Anda berhasil diverifikasi & didaftarkan untuk presensi sekolah.' : currentStepInfo.appleInstruction">
            Posisikan wajah Anda di dalam lingkaran, lalu ikuti gerakan kepala sesuai panduan.
        </p>
    </header>

    <!-- MIDDLE: APPLE RADIAL TICK CIRCLE STAGE -->
    <main class="w-full flex flex-col items-center justify-center my-auto py-2 relative">
        
        <div class="faceid-stage">
            <!-- Central Circular Camera Viewport (240px) -->
            <div class="faceid-viewport">
                <video id="video-enroll" autoplay playsinline muted></video>
                <canvas id="canvas-enroll" class="hidden"></canvas>

                <!-- Shutter Vignette Flash -->
                <div class="shutter-flash" :class="{ 'active': showFlash }"></div>

                <!-- Success Overlay with Apple Checkmark -->
                <div x-show="isSuccess" x-cloak class="absolute inset-0 bg-black/75 backdrop-blur-md flex flex-col items-center justify-center z-40">
                    <svg class="w-20 h-20" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="24" />
                        <path class="checkmark-check" d="M14 27l8 8 16-16" />
                    </svg>
                </div>
            </div>

            <!-- 60 Radial Ticks Canvas (340px) Overlay — 100% Guaranteed Render in All Browsers -->
            <canvas id="faceid-ring-canvas" width="340" height="340"></canvas>

            <!-- Success Solid Ring when Complete -->
            <div x-show="isSuccess" x-cloak class="success-solid-ring"></div>
        </div>

        <!-- Dynamic Directional Hint Pill (Toleh Kiri / Kanan / Lurus) -->
        <div x-show="!isSuccess && !isProcessing" class="mt-4 flex flex-col items-center gap-1.5">
            <div class="apple-pill px-4 py-2 rounded-full flex items-center gap-2.5 shadow-xl transition-all duration-300">
                <span class="w-2.5 h-2.5 rounded-full"
                      :class="{
                          'bg-emerald-400 animate-ping': isPoseMatching,
                          'bg-amber-400': isFaceDetected && !isPoseMatching,
                          'bg-zinc-500': !isFaceDetected
                      }"></span>
                
                <span class="text-xs sm:text-sm font-semibold tracking-wide"
                      :class="{
                          'text-emerald-300 font-bold': isPoseMatching,
                          'text-amber-300': isFaceDetected && !isPoseMatching,
                          'text-zinc-400': !isFaceDetected
                      }"
                      x-text="currentStatusLabel">
                    Posisikan wajah di dalam lingkaran
                </span>
            </div>

            <!-- Interactive Arrow Helper -->
            <div x-show="isFaceDetected && !isPoseMatching" class="text-xs text-amber-300/90 font-medium flex items-center gap-1.5">
                <template x-if="capturedImages.length === 1">
                    <span class="inline-flex items-center gap-1 bounce-left">
                        <i class="fas fa-arrow-left text-xs"></i> Tolehkan sedikit ke kiri
                    </span>
                </template>
                <template x-if="capturedImages.length === 2">
                    <span class="inline-flex items-center gap-1 bounce-right">
                        Tolehkan sedikit ke kanan <i class="fas fa-arrow-right text-xs"></i>
                    </span>
                </template>
                <template x-if="capturedImages.length === 3">
                    <span class="inline-flex items-center gap-1">
                        😊 Tersenyumlah santai ke kamera
                    </span>
                </template>
            </div>
        </div>

        <!-- Processing State Pill -->
        <div x-show="isProcessing" x-cloak class="mt-4 flex justify-center">
            <div class="apple-pill px-4 py-2 rounded-full flex items-center gap-2 text-xs text-emerald-300 font-semibold shadow-xl">
                <i class="fas fa-circle-notch fa-spin text-emerald-400"></i>
                <span>Menyimpan Biometrik 512-D ke Server...</span>
            </div>
        </div>

        <!-- 5 Steps Minimal Capsules (Apple Segmented Indicator) -->
        <div x-show="!isSuccess" class="flex items-center gap-2 mt-4">
            <template x-for="i in 5" :key="i">
                <div class="h-1.5 rounded-full transition-all duration-300"
                     :class="capturedImages.length >= i ? 'w-8 bg-[#30D158] shadow-[0_0_8px_rgba(48,209,88,0.7)]' : (capturedImages.length === i - 1 ? 'w-6 bg-white/40' : 'w-3 bg-white/10')">
                </div>
            </template>
        </div>

        <!-- Fallback Manual Snap Button (Jika Sensor HP Pelajar Bermasalah) -->
        <div x-show="!isSuccess && capturedImages.length < 5" class="mt-3.5 text-center">
            <button type="button" @click="manualCapture()"
                    :disabled="isProcessing || !isCameraReady"
                    class="text-[11px] text-zinc-500 hover:text-zinc-300 transition py-1 flex items-center justify-center gap-1.5 mx-auto cursor-pointer">
                <i class="fas fa-camera text-[10px]"></i>
                <span>Gerakan sulit terbaca? Klik untuk foto langkah ini secara manual</span>
            </button>
        </div>

        <!-- Error Alert & Retry Button -->
        <div x-show="!isSuccess && isFailed" x-cloak class="mt-3 w-full max-w-sm px-4">
            <div class="apple-pill border-rose-500/40 text-rose-300 rounded-2xl p-3 text-xs flex items-start gap-2 shadow-lg">
                <i class="fas fa-triangle-exclamation mt-0.5 text-rose-400 shrink-0"></i>
                <span x-text="errorMessage || 'Gagal merekam sampel wajah. Pastikan pencahayaan cukup dan wajah terlihat jelas.'"></span>
            </div>
            <button type="button" @click="restartEnroll()"
                    class="mt-2 w-full apple-pill hover:bg-white/10 text-white font-semibold py-2 px-4 rounded-xl transition text-xs flex items-center justify-center gap-2 cursor-pointer">
                <i class="fas fa-rotate-right text-xs"></i>
                <span>Ulangi Perekaman</span>
            </button>
        </div>

    </main>

    <!-- BOTTOM: ACTIONS & DONE BUTTON -->
    <footer class="w-full max-w-md pb-2 pt-1 text-center">
        <!-- Success "Selesai" Apple Button -->
        <div x-show="isSuccess" x-cloak class="px-4">
            <a href="{{ route('siswa.dashboard') }}"
               class="apple-button w-full py-3.5 text-base shadow-xl flex items-center justify-center gap-2">
                <span>Selesai</span>
            </a>
        </div>

        <!-- Cancel Link when in Progress -->
        <div x-show="!isSuccess && !isProcessing">
            <a href="{{ route('siswa.dashboard') }}" class="text-xs text-zinc-500 hover:text-zinc-300 transition font-medium">
                Batal
            </a>
        </div>
    </footer>

<script>
    // ── Apple Sound & Haptic FX Engine (Web Audio API & Vibration API) ──
    const appleFx = {
        ctx: null,
        getCtx() {
            if (!this.ctx) {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (AudioCtx) this.ctx = new AudioCtx();
            }
            if (this.ctx && this.ctx.state === 'suspended') {
                this.ctx.resume();
            }
            return this.ctx;
        },

        playTick() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(1750, now);
                gain.gain.setValueAtTime(0.05, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.02);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.02);
            } catch (e) {}
            if (navigator.vibrate) navigator.vibrate(8);
        },

        playStepSnap() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                [783.99, 1046.5].forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.05);
                    gain.gain.setValueAtTime(0.12, now + idx * 0.05);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.05 + 0.14);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + idx * 0.05);
                    osc.stop(now + idx * 0.05 + 0.14);
                });
            } catch (e) {}
            if (navigator.vibrate) navigator.vibrate(25);
        },

        playVictory() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                const chord = [659.25, 830.61, 987.77, 1318.51]; // E5, G#5, B5, E6
                chord.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.06);
                    gain.gain.setValueAtTime(0.18, now + idx * 0.06);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.06 + 0.55);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + idx * 0.06);
                    osc.stop(now + idx * 0.06 + 0.55);
                });
            } catch (e) {}
            if (navigator.vibrate) navigator.vibrate([30, 70, 45]);
        }
    };

    function faceIdAppleApp() {
        return {
            capturedImages: [],
            isProcessing: false,
            isSuccess: false,
            isFailed: false,
            errorMessage: '',
            isCameraReady: false,
            videoStream: null,
            showFlash: false,

            // Canvas & Animation State
            displayFilledTicks: 0,
            targetFilledTicks: 0,
            radarAngle: 0,
            canvasCtx: null,

            // Tracking & Pose States
            isFaceDetected: false,
            isPoseMatching: false,
            currentYaw: 1.0,
            currentSmile: 0.0,
            holdProgress: 0,
            holdDurationMs: 420, // Snappy & cepat! Cukup tahan 0.42 detik
            cooldownUntil: 0,
            animLoopId: null,

            // Detection Loop throttling
            isDetecting: false,
            lastDetectTime: 0,
            lastDetectionSuccess: 0,

            steps: [
                {
                    instruction: 'Hadap lurus ke depan',
                    appleInstruction: 'Langkah 1: Posisikan wajah Anda di dalam lingkaran, hadap lurus ke depan.'
                },
                {
                    instruction: 'Tolehkan kepala sedikit ke KIRI 👈',
                    appleInstruction: 'Langkah 2: Tolehkan kepala Anda perlahan sedikit ke KIRI.'
                },
                {
                    instruction: 'Tolehkan kepala sedikit ke KANAN 👉',
                    appleInstruction: 'Langkah 3: Tolehkan kepala Anda perlahan sedikit ke KANAN.'
                },
                {
                    instruction: 'Tersenyumlah santai 😊',
                    appleInstruction: 'Langkah 4: Tersenyumlah santai menghadap kamera.'
                },
                {
                    instruction: 'Hadap lurus kembali 🔒',
                    appleInstruction: 'Langkah 5: Hadap lurus ke depan sekali lagi untuk menyelesaikan pemindaian.'
                }
            ],

            get currentStepInfo() {
                const idx = Math.min(this.capturedImages.length, 4);
                return this.steps[idx];
            },

            get currentStatusLabel() {
                if (this.isPoseMatching) {
                    return Math.round(this.holdProgress) + '% Tahan Posisi...';
                }
                if (this.isFaceDetected) {
                    return this.currentStepInfo.instruction;
                }
                return 'Posisikan wajah di dalam lingkaran';
            },

            init() {
                this.initCanvas();
                this.loadFaceApi();
                this.startCamera();
            },

            initCanvas() {
                const canvas = document.getElementById('faceid-ring-canvas');
                if (canvas) {
                    this.canvasCtx = canvas.getContext('2d');
                }
            },

            async loadFaceApi() {
                if (window.faceapi) {
                    try {
                        await Promise.all([
                            faceapi.nets.tinyFaceDetector.loadFromUri('/vendor/face-api/models'),
                            faceapi.nets.faceLandmark68TinyNet.loadFromUri('/vendor/face-api/models'),
                            faceapi.nets.faceExpressionNet.loadFromUri('/vendor/face-api/models')
                        ]);
                    } catch (e) {
                        console.warn('face-api load:', e);
                    }
                }
            },

            async startCamera() {
                try {
                    if (!navigator.mediaDevices?.getUserMedia) {
                        throw new Error("Akses kamera tidak didukung.");
                    }

                    this.videoStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            width: { ideal: 1280, min: 640 },
                            height: { ideal: 720, min: 480 },
                            facingMode: 'user'
                        },
                        audio: false
                    });

                    const video = document.getElementById('video-enroll');
                    video.srcObject = this.videoStream;

                    video.onloadedmetadata = async () => {
                        try {
                            await video.play();
                        } catch (e) {}
                        this.isCameraReady = true;
                        this.startAnimationLoop();
                    };
                } catch (err) {
                    this.isCameraReady = false;
                    this.isFailed = true;
                    this.errorMessage = 'Gagal mengakses kamera: ' + err.message;
                }
            },

            startAnimationLoop() {
                if (this.animLoopId) {
                    cancelAnimationFrame(this.animLoopId);
                }

                let lastFrame = performance.now();

                const loop = (now) => {
                    const dt = Math.min(now - lastFrame, 80);
                    lastFrame = now;

                    // 1. Run Face API Detection every ~90ms asynchronously
                    if (!this.isDetecting && (now - this.lastDetectTime > 90) && this.isCameraReady && !this.isSuccess && this.capturedImages.length < 5) {
                        this.lastDetectTime = now;
                        this.runFaceDetection();
                    }

                    // 2. Evaluate Pose Matching & Hold Progress
                    if (now >= this.cooldownUntil && !this.isProcessing && this.capturedImages.length < 5) {
                        this.evaluatePoseState(dt);
                    }

                    // 3. Render 60 Radial Ticks Canvas at silky 60 FPS
                    this.renderAppleRingCanvas();

                    this.animLoopId = requestAnimationFrame(loop);
                };

                this.animLoopId = requestAnimationFrame(loop);
            },

            async runFaceDetection() {
                this.isDetecting = true;
                const video = document.getElementById('video-enroll');
                if (!video || video.paused || video.ended || video.readyState < 2) {
                    this.isDetecting = false;
                    return;
                }

                try {
                    if (window.faceapi && faceapi.nets.tinyFaceDetector.params) {
                        const detection = await faceapi.detectSingleFace(
                            video,
                            new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.30 })
                        ).withFaceLandmarks(true).withFaceExpressions();

                        if (detection && detection.landmarks) {
                            this.lastDetectionSuccess = performance.now();
                            this.isFaceDetected = true;

                            const pts = detection.landmarks.positions;
                            const nose = pts[30];
                            const leftJaw = pts[0];
                            const rightJaw = pts[16];
                            const leftEye = pts[36];
                            const rightEye = pts[45];

                            // Calculate Yaw from Jawline and Eye distance
                            const distJawL = Math.hypot(nose.x - leftJaw.x, nose.y - leftJaw.y);
                            const distJawR = Math.hypot(rightJaw.x - nose.x, rightJaw.y - nose.y);
                            const yawJaw = distJawL / Math.max(1, distJawR);

                            const distEyeL = Math.hypot(nose.x - leftEye.x, nose.y - leftEye.y);
                            const distEyeR = Math.hypot(rightEye.x - nose.x, rightEye.y - nose.y);
                            const yawEye = distEyeL / Math.max(1, distEyeR);

                            const rawYaw = (yawJaw * 0.5) + (yawEye * 0.5);
                            this.currentYaw = (this.currentYaw * 0.6) + (rawYaw * 0.4);

                            if (detection.expressions?.happy !== undefined) {
                                this.currentSmile = (this.currentSmile * 0.6) + (detection.expressions.happy * 0.4);
                            }
                        } else {
                            if (performance.now() - this.lastDetectionSuccess > 380) {
                                this.isFaceDetected = false;
                            }
                        }
                    } else {
                        // Fallback detection via video brightness if faceapi is loading
                        this.isFaceDetected = true;
                    }
                } catch (e) {
                    // Silent failover
                } finally {
                    this.isDetecting = false;
                }
            },

            evaluatePoseState(dt) {
                const step = this.capturedImages.length;
                let matches = false;

                if (this.isFaceDetected) {
                    switch (step) {
                        case 0: // 1. Lurus depan
                            matches = (this.currentYaw >= 0.76 && this.currentYaw <= 1.28);
                            break;
                        case 1: // 2. Toleh KIRI (Yaw > 1.12)
                            matches = (this.currentYaw > 1.12);
                            break;
                        case 2: // 3. Toleh KANAN (Yaw < 0.88)
                            matches = (this.currentYaw < 0.88);
                            break;
                        case 3: // 4. Senyum santai
                            matches = (this.currentSmile > 0.28) || (this.currentYaw >= 0.78 && this.currentYaw <= 1.25);
                            break;
                        case 4: // 5. Kunci Face ID (Lurus kembali)
                            matches = (this.currentYaw >= 0.76 && this.currentYaw <= 1.26);
                            break;
                    }
                }

                this.isPoseMatching = matches;

                if (matches) {
                    this.holdProgress += (dt / this.holdDurationMs) * 100;
                    if (this.holdProgress >= 100) {
                        this.holdProgress = 0;
                        this.autoSnapStep();
                    }
                } else {
                    this.holdProgress = Math.max(0, this.holdProgress - (dt / 100) * 100);
                }
            },

            autoSnapStep() {
                this.triggerFlash();
                this.captureFrame();
                appleFx.playStepSnap();

                this.targetFilledTicks = this.capturedImages.length * 12;

                if (this.capturedImages.length >= 5) {
                    this.submitEnroll();
                } else {
                    this.cooldownUntil = performance.now() + 600;
                    this.isPoseMatching = false;
                    this.holdProgress = 0;
                }
            },

            manualCapture() {
                if (this.capturedImages.length >= 5 || this.isProcessing || !this.isCameraReady) return;
                this.autoSnapStep();
            },

            triggerFlash() {
                this.showFlash = true;
                setTimeout(() => {
                    this.showFlash = false;
                }, 130);
            },

            captureFrame() {
                const video  = document.getElementById('video-enroll');
                const canvas = document.getElementById('canvas-enroll');
                if (!video || !canvas) return;

                const w = video.videoWidth || 960;
                const h = video.videoHeight || 720;
                canvas.width  = w;
                canvas.height = h;

                const ctx = canvas.getContext('2d');
                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(video, -w, 0, w, h);
                ctx.restore();

                const dataUrl = canvas.toDataURL('image/jpeg', 0.94);
                this.capturedImages.push(dataUrl);
            },

            renderAppleRingCanvas() {
                const canvas = document.getElementById('faceid-ring-canvas');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                const W = 340;
                const H = 340;
                ctx.clearRect(0, 0, W, H);

                const cx = W / 2;
                const cy = H / 2;
                const rIn = 126;
                const rOut = 146;
                const count = 60;

                // Smoothly increment display filled ticks towards target
                const baseTarget = this.capturedImages.length * 12;
                if (this.displayFilledTicks < baseTarget) {
                    this.displayFilledTicks += 0.8;
                    appleFx.playTick();
                    if (this.displayFilledTicks > baseTarget) this.displayFilledTicks = baseTarget;
                }

                const activePreview = this.isPoseMatching ? Math.round((this.holdProgress / 100) * 12) : 0;
                const totalLit = Math.min(60, Math.floor(this.displayFilledTicks) + activePreview);

                // 1. Draw 60 Radial Ticks (The iconic Apple Face ID circle)
                for (let i = 0; i < count; i++) {
                    const angle = (i * (Math.PI * 2 / count)) - Math.PI / 2;
                    const cos = Math.cos(angle);
                    const sin = Math.sin(angle);

                    const x1 = cx + rIn * cos;
                    const y1 = cy + rIn * sin;
                    const x2 = cx + rOut * cos;
                    const y2 = cy + rOut * sin;

                    ctx.beginPath();
                    ctx.lineCap = 'round';

                    if (i < Math.floor(this.displayFilledTicks)) {
                        // Completed Ticks: Apple Vibrant Green #30D158
                        ctx.lineWidth = 4.2;
                        ctx.strokeStyle = '#30D158';
                        ctx.shadowColor = 'rgba(48, 209, 88, 0.75)';
                        ctx.shadowBlur = 6;
                    } else if (i < totalLit) {
                        // Active Holding Ticks: Electric Sky Blue #38bdf8
                        ctx.lineWidth = 4.0;
                        ctx.strokeStyle = '#38bdf8';
                        ctx.shadowColor = 'rgba(56, 189, 248, 0.85)';
                        ctx.shadowBlur = 8;
                    } else {
                        // Inactive Ticks: Translucent Silver
                        ctx.lineWidth = 3.2;
                        ctx.strokeStyle = 'rgba(255, 255, 255, 0.22)';
                        ctx.shadowBlur = 0;
                    }

                    ctx.moveTo(x1, y1);
                    ctx.lineTo(x2, y2);
                    ctx.stroke();
                }

                // 2. Animated Radar Scanner Beam (Apple Futuristic Glow)
                this.radarAngle = (this.radarAngle + 0.035) % (Math.PI * 2);
                ctx.beginPath();
                ctx.arc(cx, cy, 136, this.radarAngle, this.radarAngle + Math.PI * 0.45);
                ctx.strokeStyle = 'rgba(56, 189, 248, 0.35)';
                ctx.lineWidth = 2.2;
                ctx.shadowColor = 'rgba(56, 189, 248, 0.6)';
                ctx.shadowBlur = 8;
                ctx.stroke();

                // 3. Subtle Apple Target Corner Brackets inside the 240px circle
                const bSize = 22;
                const bOffset = 90;
                const bRadius = 6;
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.strokeStyle = this.isFaceDetected
                    ? (this.isPoseMatching ? '#30D158' : 'rgba(255, 255, 255, 0.55)')
                    : 'rgba(255, 255, 255, 0.2)';
                ctx.shadowColor = this.isPoseMatching ? 'rgba(48, 209, 88, 0.6)' : 'transparent';
                ctx.shadowBlur = this.isPoseMatching ? 6 : 0;

                // Top-Left Bracket
                ctx.beginPath();
                ctx.moveTo(cx - bOffset + bSize, cy - bOffset);
                ctx.lineTo(cx - bOffset + bRadius, cy - bOffset);
                ctx.quadraticCurveTo(cx - bOffset, cy - bOffset, cx - bOffset, cy - bOffset + bRadius);
                ctx.lineTo(cx - bOffset, cy - bOffset + bSize);
                ctx.stroke();

                // Top-Right Bracket
                ctx.beginPath();
                ctx.moveTo(cx + bOffset - bSize, cy - bOffset);
                ctx.lineTo(cx + bOffset - bRadius, cy - bOffset);
                ctx.quadraticCurveTo(cx + bOffset, cy - bOffset, cx + bOffset, cy - bOffset + bRadius);
                ctx.lineTo(cx + bOffset, cy - bOffset + bSize);
                ctx.stroke();

                // Bottom-Left Bracket
                ctx.beginPath();
                ctx.moveTo(cx - bOffset, cy + bOffset - bSize);
                ctx.lineTo(cx - bOffset, cy + bOffset - bRadius);
                ctx.quadraticCurveTo(cx - bOffset, cy + bOffset, cx - bOffset + bRadius, cy + bOffset);
                ctx.lineTo(cx - bOffset + bSize, cy + bOffset);
                ctx.stroke();

                // Bottom-Right Bracket
                ctx.beginPath();
                ctx.moveTo(cx + bOffset, cy + bOffset - bSize);
                ctx.lineTo(cx + bOffset, cy + bOffset - bRadius);
                ctx.quadraticCurveTo(cx + bOffset, cy + bOffset, cx + bOffset - bRadius, cy + bOffset);
                ctx.lineTo(cx + bOffset - bSize, cy + bOffset);
                ctx.stroke();
            },

            restartEnroll() {
                this.capturedImages = [];
                this.displayFilledTicks = 0;
                this.targetFilledTicks = 0;
                this.isFailed = false;
                this.isProcessing = false;
                this.errorMessage = '';
                this.holdProgress = 0;
                this.isPoseMatching = false;
                this.cooldownUntil = performance.now() + 500;
            },

            async submitEnroll() {
                if (this.isProcessing) return;
                this.isProcessing = true;
                this.isFailed = false;
                this.errorMessage = '';

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
                    } catch (err) {
                        data = { success: false, message: 'Server merespons kode ' + resp.status };
                    }

                    if (data.success) {
                        this.displayFilledTicks = 60;
                        this.isSuccess = true;
                        appleFx.playVictory();

                        if (this.videoStream) {
                            this.videoStream.getTracks().forEach(t => t.stop());
                            this.videoStream = null;
                        }
                    } else {
                        this.isProcessing = false;
                        this.isFailed = true;
                        this.errorMessage = data.message || 'Gagal memproses biometrik di server AI.';
                    }
                } catch (e) {
                    console.error('Enroll error:', e);
                    this.isProcessing = false;
                    this.isFailed = true;
                    this.errorMessage = 'Koneksi ke server terputus. Silakan coba lagi.';
                }
            }
        };
    }
</script>
</body>
</html>
