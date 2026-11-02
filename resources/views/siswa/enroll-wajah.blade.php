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

        /* Apple Face ID Circle Container */
        .faceid-stage {
            position: relative;
            width: 320px;
            height: 320px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Central Circular Viewport */
        .faceid-viewport {
            position: relative;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            overflow: hidden;
            background: #000;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.1), 0 20px 40px rgba(0, 0, 0, 0.8);
            z-index: 10;
        }

        #video-enroll {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        /* Apple Radial Ticks SVG */
        .faceid-ticks-svg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 20;
        }

        .tick-mark {
            stroke-linecap: round;
            transition: stroke 0.28s cubic-bezier(0.16, 1, 0.3, 1), stroke-width 0.28s ease, filter 0.28s ease;
        }

        .tick-mark.unfilled {
            stroke: rgba(255, 255, 255, 0.22);
            stroke-width: 3.5px;
        }

        .tick-mark.active-glow {
            stroke: #38bdf8;
            stroke-width: 4px;
            filter: drop-shadow(0 0 6px rgba(56, 189, 248, 0.8));
        }

        .tick-mark.filled {
            stroke: #30D158;
            stroke-width: 4px;
            filter: drop-shadow(0 0 5px rgba(48, 209, 88, 0.75));
        }

        /* Apple Shutter Vignette Flash */
        .shutter-flash {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.4) 70%, transparent 100%);
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
            width: 254px;
            height: 254px;
            border-radius: 50%;
            border: 4px solid #30D158;
            box-shadow: 0 0 25px rgba(48, 209, 88, 0.6), inset 0 0 15px rgba(48, 209, 88, 0.3);
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
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 0.5px solid rgba(255, 255, 255, 0.15);
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
    </style>
</head>
<body class="min-h-screen w-screen flex flex-col justify-between items-center px-4 py-8 bg-black select-none"
      x-data="faceIdAppleApp()" x-init="init()">

    <!-- TOP: APPLE HEADER & ICON -->
    <header class="w-full max-w-md text-center pt-2 sm:pt-4">
        <!-- Iconic Apple Face ID Icon (Square with bracket corners & eyes/smile) -->
        <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-zinc-900/90 border border-white/10 mb-3 shadow-lg shadow-black">
            <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <!-- 4 Outer Corner Brackets -->
                <path d="M4 8V6a2 2 0 0 1 2-2h2" />
                <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                <path d="M20 16v2a2 2 0 0 1-2 2h-2" />
                <path d="M8 20H6a2 2 0 0 1-2-2v-2" />
                <!-- Eyes & Nose & Smile -->
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
        
        <p class="text-zinc-400 text-sm mt-1.5 px-4 font-normal leading-relaxed"
           x-text="isSuccess ? 'Wajah Anda berhasil didaftarkan dan siap digunakan untuk presensi gerbang.' : currentStepInfo.appleInstruction">
            Posisikan wajah Anda di dalam bingkai, lalu gerakkan kepala Anda perlahan membentuk lingkaran.
        </p>
    </header>

    <!-- MIDDLE: APPLE RADIAL TICK CIRCLE STAGE -->
    <main class="w-full flex flex-col items-center justify-center my-auto py-2">
        
        <div class="faceid-stage">
            <!-- 60 Radial Ticks SVG around Circle -->
            <svg class="faceid-ticks-svg" viewBox="0 0 320 320">
                <template x-for="tick in ticks" :key="tick.id">
                    <line :x1="tick.x1" :y1="tick.y1" :x2="tick.x2" :y2="tick.y2"
                          class="tick-mark"
                          :class="{
                              'filled': tick.id < filledTicksCount,
                              'active-glow': tick.id >= filledTicksCount && tick.id < (filledTicksCount + activeGlowTicks),
                              'unfilled': tick.id >= (filledTicksCount + activeGlowTicks)
                          }" />
                </template>
            </svg>

            <!-- Success Solid Ring when Complete -->
            <div x-show="isSuccess" x-cloak class="success-solid-ring"></div>

            <!-- Central Circular Camera Viewport -->
            <div class="faceid-viewport">
                <!-- Video Stream -->
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
        </div>

        <!-- Dynamic Apple Status Pill -->
        <div x-show="!isSuccess" class="mt-6 flex justify-center">
            <div class="apple-pill px-4 py-2 rounded-full flex items-center gap-2.5 shadow-xl transition-all duration-300">
                <template x-if="isProcessing || capturedImages.length >= 5">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-circle-notch fa-spin text-xs text-emerald-400"></i>
                        <span class="text-xs font-semibold text-zinc-200">Menyimpan Biometrik 512-D ke Server...</span>
                    </div>
                </template>

                <template x-if="!isProcessing && capturedImages.length < 5">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full"
                              :class="{
                                  'bg-emerald-400 animate-ping': isPoseMatching,
                                  'bg-amber-400': isFaceInOval && !isPoseMatching,
                                  'bg-zinc-500': !isFaceInOval
                              }"></span>
                        <span class="text-xs font-semibold tracking-wide"
                              :class="{
                                  'text-emerald-300': isPoseMatching,
                                  'text-amber-300': isFaceInOval && !isPoseMatching,
                                  'text-zinc-300': !isFaceInOval
                              }"
                              x-text="currentStatusLabel">
                            Posisikan wajah Anda di dalam bingkai
                        </span>
                    </div>
                </template>
            </div>
        </div>

        <!-- 5 Steps Minimal Capsules (Apple Segmented Indicator) -->
        <div x-show="!isSuccess" class="flex items-center gap-1.5 mt-5">
            <template x-for="i in 5" :key="i">
                <div class="h-1.5 rounded-full transition-all duration-300"
                     :class="capturedImages.length >= i ? 'w-8 bg-[#30D158] shadow-[0_0_8px_rgba(48,209,88,0.7)]' : (capturedImages.length === i - 1 ? 'w-6 bg-white/40' : 'w-3 bg-white/10')">
                </div>
            </template>
        </div>

        <!-- Error Alert & Retry Button -->
        <div x-show="!isSuccess && isFailed" x-cloak class="mt-4 w-full max-w-sm px-4">
            <div class="apple-pill border-rose-500/40 text-rose-300 rounded-2xl p-3.5 text-xs flex items-start gap-2.5 shadow-lg">
                <i class="fas fa-triangle-exclamation mt-0.5 text-rose-400 shrink-0"></i>
                <span x-text="errorMessage || 'Gagal merekam sampel wajah. Pastikan pencahayaan cukup dan wajah terlihat jelas.'"></span>
            </div>
            <button type="button" @click="restartEnroll()"
                    class="mt-2.5 w-full apple-pill hover:bg-white/10 text-white font-semibold py-2.5 px-4 rounded-xl transition text-xs flex items-center justify-center gap-2 cursor-pointer">
                <i class="fas fa-rotate-right text-xs"></i>
                <span>Coba Ulangi</span>
            </button>
        </div>

        <!-- Fallback Manual Snap Button (Jika Sensor HP Pelajar Bermasalah) -->
        <div x-show="!isSuccess && capturedImages.length < 5" class="mt-3 text-center">
            <button type="button" @click="manualCapture()"
                    :disabled="isProcessing || !isCameraReady"
                    class="text-[11px] text-zinc-500 hover:text-zinc-300 transition py-1 flex items-center justify-center gap-1.5 mx-auto">
                <i class="fas fa-camera text-[10px]"></i>
                <span>Gerakan sulit terbaca? Ambil langkah ini secara manual</span>
            </button>
        </div>

    </main>

    <!-- BOTTOM: ACTIONS & DONE BUTTON -->
    <footer class="w-full max-w-md pb-4 pt-2 text-center">
        <!-- Success "Selesai" Apple Button -->
        <div x-show="isSuccess" x-cloak class="px-4">
            <a href="{{ route('siswa.dashboard') }}"
               class="apple-button w-full py-4 text-base shadow-xl flex items-center justify-center gap-2">
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

        // Apple Mechanical Tick Click
        playTick() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(1600, now);
                gain.gain.setValueAtTime(0.04, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.02);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.02);
            } catch (e) {}
            // Haptic mikro
            if (navigator.vibrate) navigator.vibrate(8);
        },

        // Apple Step Snap Sound
        playStepSnap() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                // Double note G5 -> C6
                [783.99, 1046.5].forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.06);
                    gain.gain.setValueAtTime(0.12, now + idx * 0.06);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.06 + 0.15);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + idx * 0.06);
                    osc.stop(now + idx * 0.06 + 0.15);
                });
            } catch (e) {}
            if (navigator.vibrate) navigator.vibrate(22);
        },

        // Apple Face ID Harmonized Victory Chime (E5, G#5, B5, E6)
        playVictory() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                const chord = [659.25, 830.61, 987.77, 1318.51];
                chord.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.07);
                    gain.gain.setValueAtTime(0.18, now + idx * 0.07);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.07 + 0.6);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + idx * 0.07);
                    osc.stop(now + idx * 0.07 + 0.6);
                });
            } catch (e) {}
            // Apple Taptic double vibration
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

            // Apple 60 Radial Ticks Data
            ticks: [],
            totalTicks: 60,
            filledTicksCount: 0,
            activeGlowTicks: 0,

            // Tracking & Pose States
            isFaceInOval: false,
            isPoseMatching: false,
            holdProgress: 0,
            holdDurationMs: 800,
            lastFrameTime: 0,
            cooldownUntil: 0,
            trackingLoopId: null,

            // face-api Landmark & Pose Data
            isFaceApiLoaded: false,
            aiYawRatio: 1.0,
            aiSmileScore: 0.0,
            lastAiCheck: 0,

            steps: [
                {
                    instruction: 'Hadap lurus ke depan',
                    appleInstruction: 'Posisikan wajah Anda di dalam bingkai, hadap lurus ke depan.',
                    targetTickCount: 12
                },
                {
                    instruction: 'Tolehkan kepala sedikit ke KIRI',
                    appleInstruction: 'Tolehkan kepala Anda perlahan sedikit ke KIRI.',
                    targetTickCount: 24
                },
                {
                    instruction: 'Tolehkan kepala sedikit ke KANAN',
                    appleInstruction: 'Tolehkan kepala Anda perlahan sedikit ke KANAN.',
                    targetTickCount: 36
                },
                {
                    instruction: 'Tersenyumlah santai ke arah kamera',
                    appleInstruction: 'Tersenyumlah santai menghadap kamera.',
                    targetTickCount: 48
                },
                {
                    instruction: 'Hadap lurus kembali (Kunci Biometrik)',
                    appleInstruction: 'Hadap lurus ke depan sekali lagi untuk menyelesaikan pemindaian.',
                    targetTickCount: 60
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
                if (this.isFaceInOval) {
                    return this.currentStepInfo.instruction;
                }
                return 'Posisikan wajah di dalam lingkaran';
            },

            init() {
                this.generateAppleTicks();
                this.loadFaceApi();
                this.startCamera();
            },

            // Generate 60 Radial Ticks around 240px circular viewport
            generateAppleTicks() {
                const count = 60;
                const cx = 160;
                const cy = 160;
                const rInner = 128;
                const rOuter = 146;
                const generated = [];

                for (let i = 0; i < count; i++) {
                    // Start at top (12 o'clock, -90 degrees)
                    const angleDeg = (i * (360 / count)) - 90;
                    const rad = (angleDeg * Math.PI) / 180;
                    const x1 = Math.round(cx + rInner * Math.cos(rad));
                    const y1 = Math.round(cy + rInner * Math.sin(rad));
                    const x2 = Math.round(cx + rOuter * Math.cos(rad));
                    const y2 = Math.round(cy + rOuter * Math.sin(rad));

                    generated.push({ id: i, x1, y1, x2, y2 });
                }
                this.ticks = generated;
            },

            async loadFaceApi() {
                if (window.faceapi) {
                    try {
                        await Promise.all([
                            faceapi.nets.tinyFaceDetector.loadFromUri('/vendor/face-api/models'),
                            faceapi.nets.faceLandmark68TinyNet.loadFromUri('/vendor/face-api/models'),
                            faceapi.nets.faceExpressionNet.loadFromUri('/vendor/face-api/models')
                        ]);
                        this.isFaceApiLoaded = true;
                    } catch (e) {
                        console.warn('face-api note:', e);
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
                            width: { ideal: 1280 },
                            height: { ideal: 720 },
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
                        this.startFaceTracker();
                    };
                } catch (err) {
                    this.isCameraReady = false;
                    this.isFailed = true;
                    this.errorMessage = 'Gagal mengakses kamera: ' + err.message;
                }
            },

            startFaceTracker() {
                if (this.trackingLoopId) {
                    cancelAnimationFrame(this.trackingLoopId);
                }

                const offCanvas = document.createElement('canvas');
                offCanvas.width = 80;
                offCanvas.height = 80;
                const offCtx = offCanvas.getContext('2d', { willReadFrequently: true });

                const checkLoop = async (now) => {
                    if (this.isSuccess || this.capturedImages.length >= 5 || this.isProcessing) {
                        return;
                    }

                    const video = document.getElementById('video-enroll');
                    if (!video || video.paused || video.ended || video.readyState < 2) {
                        this.trackingLoopId = requestAnimationFrame(checkLoop);
                        return;
                    }

                    const dt = this.lastFrameTime ? Math.min(now - this.lastFrameTime, 100) : 16;
                    this.lastFrameTime = now;

                    if (now < this.cooldownUntil) {
                        this.holdProgress = 0;
                        this.isPoseMatching = false;
                        this.activeGlowTicks = 0;
                        this.trackingLoopId = requestAnimationFrame(checkLoop);
                        return;
                    }

                    // 1. Jalankan Landmark jika loaded (~tiap 120ms)
                    if (this.isFaceApiLoaded && (now - this.lastAiCheck > 120)) {
                        this.lastAiCheck = now;
                        try {
                            const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224, scoreThreshold: 0.4 }))
                                .withFaceLandmarks(true)
                                .withFaceExpressions();

                            if (detection && detection.landmarks) {
                                const pts = detection.landmarks.positions;
                                const noseX = pts[30].x;
                                const leftJawX = pts[0].x;
                                const rightJawX = pts[16].x;
                                const leftDist = Math.max(1, noseX - leftJawX);
                                const rightDist = Math.max(1, rightJawX - noseX);
                                this.aiYawRatio = leftDist / rightDist;

                                if (detection.expressions?.happy !== undefined) {
                                    this.aiSmileScore = detection.expressions.happy;
                                }
                            }
                        } catch (e) {}
                    }

                    // 2. Real-time skin & position check di area tengah bulat
                    const vw = video.videoWidth || 960;
                    const vh = video.videoHeight || 720;
                    const minDim = Math.min(vw, vh);
                    const cropSize = minDim * 0.65;
                    const cropX = (vw - cropSize) / 2;
                    const cropY = (vh - cropSize) / 2;

                    offCtx.drawImage(video, cropX, cropY, cropSize, cropSize, 0, 0, 80, 80);
                    const imgData = offCtx.getImageData(0, 0, 80, 80);
                    const data = imgData.data;

                    let skinPix = 0;
                    let leftSkin = 0;
                    let rightSkin = 0;

                    for (let y = 0; y < 80; y++) {
                        for (let x = 0; x < 80; x++) {
                            const dx = (x - 40) / 36;
                            const dy = (y - 40) / 36;
                            if ((dx * dx + dy * dy) <= 1.0) {
                                const idx = (y * 80 + x) * 4;
                                const r = data[idx];
                                const g = data[idx + 1];
                                const b = data[idx + 2];
                                const Y  =  0.299 * r + 0.587 * g + 0.114 * b;
                                const Cb = 128 - 0.1687 * r - 0.3313 * g + 0.5 * b;
                                const Cr = 128 + 0.5 * r - 0.4187 * g - 0.0813 * b;

                                if (Y >= 35 && Y <= 245 && Cr >= 126 && Cr <= 178 && Cb >= 72 && Cb <= 135 && (Cr - Cb) >= 2) {
                                    skinPix++;
                                    if (x < 40) leftSkin++;
                                    else rightSkin++;
                                }
                            }
                        }
                    }

                    this.isFaceInOval = skinPix >= 450;
                    const asymRatio = leftSkin > 0 ? (rightSkin / leftSkin) : 1.0;

                    // 3. Evaluasi Kesesuaian Pose per Langkah
                    const stepIdx = this.capturedImages.length;
                    let matchesCurrentPose = false;

                    if (this.isFaceInOval) {
                        switch (stepIdx) {
                            case 0: // Lurus depan
                                matchesCurrentPose = (this.aiYawRatio >= 0.72 && this.aiYawRatio <= 1.40) &&
                                                     (asymRatio >= 0.75 && asymRatio <= 1.35);
                                break;
                            case 1: // Toleh KIRI
                                matchesCurrentPose = (this.aiYawRatio > 1.25) || (asymRatio > 1.30);
                                break;
                            case 2: // Toleh KANAN
                                matchesCurrentPose = (this.aiYawRatio < 0.80) || (asymRatio < 0.75);
                                break;
                            case 3: // Senyum
                                matchesCurrentPose = (this.aiSmileScore > 0.45) || (skinPix >= 450);
                                break;
                            case 4: // Lurus mengunci
                                matchesCurrentPose = (this.aiYawRatio >= 0.75 && this.aiYawRatio <= 1.35);
                                break;
                        }
                    }

                    this.isPoseMatching = matchesCurrentPose;

                    // 4. Progress bar hold & Tick Glow Simulation
                    const stepTargetTicks = (stepIdx + 1) * 12;

                    if (matchesCurrentPose) {
                        this.holdProgress += (dt / this.holdDurationMs) * 100;
                        // Light up ticks in advance as holding
                        const fraction = Math.min(1, this.holdProgress / 100);
                        this.activeGlowTicks = Math.round(fraction * 12);

                        if (this.holdProgress >= 100) {
                            this.holdProgress = 0;
                            this.activeGlowTicks = 0;
                            this.autoSnap(stepTargetTicks);
                            this.trackingLoopId = requestAnimationFrame(checkLoop);
                            return;
                        }
                    } else {
                        this.holdProgress = Math.max(0, this.holdProgress - (dt / 150) * 100);
                        this.activeGlowTicks = 0;
                    }

                    this.trackingLoopId = requestAnimationFrame(checkLoop);
                };

                this.trackingLoopId = requestAnimationFrame(checkLoop);
            },

            autoSnap(targetTickCount) {
                this.triggerFlash();
                this.captureFrame();
                appleFx.playStepSnap();

                // Smoothly sweep fill the ticks for this step
                this.animateTicksSweep(targetTickCount || (this.capturedImages.length * 12));

                if (this.capturedImages.length >= 5) {
                    this.submitEnroll();
                } else {
                    this.cooldownUntil = performance.now() + 650;
                }
            },

            animateTicksSweep(target) {
                const step = () => {
                    if (this.filledTicksCount < target) {
                        this.filledTicksCount++;
                        appleFx.playTick();
                        requestAnimationFrame(step);
                    }
                };
                requestAnimationFrame(step);
            },

            manualCapture() {
                if (this.capturedImages.length >= 5 || this.isProcessing || !this.isCameraReady) return;
                const nextTarget = (this.capturedImages.length + 1) * 12;
                this.autoSnap(nextTarget);
            },

            triggerFlash() {
                this.showFlash = true;
                setTimeout(() => {
                    this.showFlash = false;
                }, 140);
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

            restartEnroll() {
                this.capturedImages = [];
                this.filledTicksCount = 0;
                this.activeGlowTicks = 0;
                this.isFailed = false;
                this.isProcessing = false;
                this.errorMessage = '';
                this.holdProgress = 0;
                this.isPoseMatching = false;
                this.cooldownUntil = performance.now() + 600;
                this.startFaceTracker();
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
                        this.filledTicksCount = 60;
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
