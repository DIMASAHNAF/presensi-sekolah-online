<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan Wajah Face ID — Presensi Siswa</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="{{ asset('vendor/face-api/face-api.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b1120;
            background-image: 
                radial-gradient(at 15% 15%, rgba(37, 99, 235, 0.15) 0px, transparent 50%),
                radial-gradient(at 85% 85%, rgba(16, 185, 129, 0.15) 0px, transparent 50%);
        }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Oval Viewport Face Guide */
        .face-oval-container {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -52%);
            width: 220px; height: 280px;
            pointer-events: none;
            z-index: 10;
        }

        .face-oval-mask {
            position: absolute;
            inset: 0;
            border-radius: 50% / 45%;
            box-shadow: 0 0 0 9999px rgba(11, 17, 32, 0.72);
            transition: all 0.3s ease;
        }

        .face-oval-border {
            position: absolute;
            inset: 0;
            border-radius: 50% / 45%;
            border: 3px solid rgba(255, 255, 255, 0.35);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .face-oval-border.aligned {
            border-color: #10b981;
            box-shadow: 0 0 25px rgba(16, 185, 129, 0.75), inset 0 0 15px rgba(16, 185, 129, 0.3);
        }

        .face-oval-border.holding {
            border-color: #38bdf8;
            box-shadow: 0 0 35px rgba(56, 189, 248, 0.9), inset 0 0 20px rgba(56, 189, 248, 0.4);
        }

        .face-oval-border.wrong-pose {
            border-color: #f59e0b;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.6), inset 0 0 10px rgba(245, 158, 11, 0.2);
        }

        .scanner-laser {
            position: absolute;
            left: 8%; right: 8%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #38bdf8, #10b981, #38bdf8, transparent);
            box-shadow: 0 0 14px #10b981, 0 0 24px #38bdf8;
            border-radius: 50%;
            animation: laser-scan 1.6s ease-in-out infinite alternate;
        }

        @keyframes laser-scan {
            0%   { top: 10%; opacity: 0.3; }
            50%  { opacity: 1; }
            100% { top: 90%; opacity: 0.3; }
        }

        /* SVG Oval Progress Ring */
        .svg-progress-ring {
            position: absolute;
            inset: -6px;
            width: calc(100% + 12px);
            height: calc(100% + 12px);
            pointer-events: none;
            transform: rotate(-90deg);
        }

        .progress-ring-ellipse {
            fill: none;
            stroke-width: 5;
            stroke-linecap: round;
            transition: stroke-dashoffset 0.1s linear, stroke 0.3s ease;
        }

        #video-enroll {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        /* Shutter Camera Flash */
        .shutter-flash {
            position: absolute;
            inset: 0;
            background: #ffffff;
            z-index: 40;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.05s ease-in;
        }
        .shutter-flash.active {
            opacity: 0.9;
            transition: none;
        }

        @keyframes checkmark-pop {
            0%   { transform: scale(0); opacity: 0; }
            70%  { transform: scale(1.15); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .check-pop { animation: checkmark-pop 0.4s cubic-bezier(0.17, 0.67, 0.4, 1.2) forwards; }

        @keyframes pulse-ring {
            0%   { transform: scale(0.96); opacity: 0.6; }
            50%  { transform: scale(1.02); opacity: 1; }
            100% { transform: scale(0.96); opacity: 0.6; }
        }
        .pulse-subtle { animation: pulse-ring 2s infinite ease-in-out; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4 py-6 text-slate-100"
      x-data="enrollTouchlessApp()" x-init="init()">

    <div class="bg-slate-900/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-slate-700/70 p-5 sm:p-7 w-full max-w-md relative overflow-hidden">
        
        {{-- Decorative top accent line --}}
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-500 via-emerald-400 to-teal-400"></div>

        {{-- Header & Info --}}
        <div class="text-center mb-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-950/80 border border-emerald-500/40 text-emerald-400 text-[10px] font-mono font-bold uppercase tracking-wider mb-2.5 shadow-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                <i class="fas fa-fingerprint text-[11px]"></i> Face ID Multi-Pose Enrollment
            </div>
            <h1 class="text-2xl font-black text-white font-heading tracking-tight">Perekaman Wajah</h1>
            <p class="text-slate-400 text-xs mt-1">
                Halo, <strong class="text-emerald-400">{{ $user->name }}</strong>! Ikuti gerakan kepala sesuai panduan.
            </p>
        </div>

        {{-- Instruksi Dinamis (Real-Time Pose & Direction Guidance) --}}
        <div x-show="!isSuccess" class="bg-slate-800/80 rounded-2xl p-3.5 text-center mb-3.5 border border-slate-700/80 shadow-inner">
            <div class="flex items-center justify-center gap-2 mb-1">
                <span class="text-lg" x-text="currentStepInfo.emoji"></span>
                <span class="text-[10px] font-mono uppercase tracking-widest font-extrabold text-emerald-400"
                      x-text="'Langkah ' + (capturedImages.length + 1) + ' dari 5'"></span>
            </div>
            <p x-text="currentStepInfo.instruction" class="text-white font-extrabold text-sm sm:text-base leading-tight"></p>
            <p x-text="poseMessage" class="text-[11px] mt-1 font-bold transition-colors"
               :class="isPoseMatching ? 'text-emerald-400' : 'text-amber-400'"></p>
        </div>

        {{-- Progress Bar 5 Tahap --}}
        <div x-show="!isSuccess" class="flex justify-center gap-1.5 mb-3.5">
            <template x-for="i in 5" :key="i">
                <div class="h-2 rounded-full transition-all duration-300"
                     :class="capturedImages.length >= i ? 'bg-gradient-to-r from-emerald-400 to-teal-400 w-12 shadow-xs shadow-emerald-500/50' : 'bg-slate-800 w-7'"></div>
            </template>
        </div>

        {{-- Camera Viewport with Oval Guide --}}
        <div x-show="!isSuccess" class="relative bg-slate-950 rounded-2xl overflow-hidden mb-3.5 shadow-xl border border-slate-800" style="height: 310px;">
            <video id="video-enroll" autoplay playsinline muted></video>
            <canvas id="canvas-enroll" class="hidden"></canvas>
            
            {{-- Shutter Flash overlay --}}
            <div class="shutter-flash" :class="{ 'active': showFlash }"></div>

            {{-- Face Oval Frame & Auto-Hold Meter --}}
            <div class="face-oval-container">
                <div class="face-oval-mask"></div>
                
                {{-- SVG Circular/Elliptical Progress Meter --}}
                <svg class="svg-progress-ring" viewBox="0 0 232 292">
                    <!-- Background Track -->
                    <ellipse cx="116" cy="146" rx="108" ry="138"
                             stroke="rgba(255, 255, 255, 0.15)" stroke-width="4" fill="none" />
                    <!-- Animated Progress Stroke -->
                    <ellipse cx="116" cy="146" rx="108" ry="138"
                             class="progress-ring-ellipse"
                             :stroke="holdProgress >= 100 ? '#10b981' : (holdProgress > 20 ? '#38bdf8' : 'rgba(255,255,255,0.4)')"
                             :stroke-dasharray="ellipsePerimeter"
                             :stroke-dashoffset="ellipsePerimeter - (ellipsePerimeter * (holdProgress / 100))" />
                </svg>

                <div class="face-oval-border"
                     :class="{
                         'aligned': isPoseMatching && holdProgress < 30,
                         'holding': isPoseMatching && holdProgress >= 30,
                         'wrong-pose': isFaceInOval && !isPoseMatching
                     }">
                    <div class="scanner-laser" x-show="isCameraReady && !isProcessing && capturedImages.length < 5"></div>
                </div>
            </div>

            {{-- Live Feedback Pill on Screen --}}
            <div class="absolute bottom-3 left-0 right-0 flex justify-center z-20">
                <template x-if="!isProcessing && capturedImages.length < 5">
                    <div class="bg-slate-900/85 backdrop-blur-md px-3.5 py-1.5 rounded-full border border-slate-700/80 flex items-center gap-2 shadow-lg">
                        <template x-if="isPoseMatching">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                <span class="text-xs font-mono font-black text-emerald-400" x-text="Math.round(holdProgress) + '% Tahan Posisi...'"></span>
                            </div>
                        </template>
                        <template x-if="!isPoseMatching && isFaceInOval">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-400 pulse-subtle"></span>
                                <span class="text-[11px] font-bold text-amber-300" x-text="poseMessage"></span>
                            </div>
                        </template>
                        <template x-if="!isFaceInOval">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-slate-400 pulse-subtle"></span>
                                <span class="text-[11px] font-bold text-slate-300">Posisikan wajah di dalam oval</span>
                            </div>
                        </template>
                    </div>
                </template>
                <template x-if="isProcessing || capturedImages.length >= 5">
                    <div class="bg-emerald-600/90 backdrop-blur-md text-white text-xs px-4 py-1.5 rounded-full font-bold border border-emerald-400/40 shadow-lg flex items-center gap-2">
                        <i class="fas fa-spinner fa-spin text-sm"></i>
                        <span>Memproses Biometrik AI Server...</span>
                    </div>
                </template>
            </div>
        </div>

        {{-- 5 Step Thumbnails --}}
        <div x-show="!isSuccess" class="flex gap-2 justify-center mb-3.5">
            <template x-for="i in 5" :key="i">
                <div class="w-11 h-11 rounded-xl overflow-hidden border-2 transition-all relative shadow-sm"
                     :class="capturedImages.length >= i ? 'border-emerald-400 ring-2 ring-emerald-500/40 bg-slate-900' : (capturedImages.length === i - 1 ? 'border-blue-400 bg-slate-800' : 'border-slate-800 bg-slate-900/60')">
                    <template x-if="capturedImages[i-1]">
                        <div class="relative w-full h-full">
                            <img :src="capturedImages[i-1]" class="w-full h-full object-cover" style="transform: scaleX(-1);">
                            <div class="absolute inset-0 bg-emerald-500/25 flex items-center justify-center text-white text-xs">
                                <i class="fas fa-check drop-shadow"></i>
                            </div>
                        </div>
                    </template>
                    <template x-if="!capturedImages[i-1]">
                        <div class="w-full h-full flex flex-col items-center justify-center text-slate-500 text-[10px] font-mono font-bold">
                            <i :class="stepIcons[i-1]" class="text-xs mb-0.5"></i>
                            <span x-text="i"></span>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Cadangan Tombol Manual (Jika Sensor Perangkat Lambat) --}}
        <div x-show="!isSuccess && capturedImages.length < 5" class="mb-2 text-center">
            <button type="button" @click="manualCapture()"
                    :disabled="isProcessing || !isCameraReady"
                    class="text-[11px] text-slate-400 hover:text-slate-200 underline cursor-pointer py-1 transition flex items-center justify-center gap-1.5 mx-auto">
                <i class="fas fa-camera text-slate-400"></i>
                <span>Gerakan sulit terdeteksi? Klik untuk ambil langkah ini secara manual</span>
            </button>
        </div>

        {{-- Error Alert & Retry Button --}}
        <div x-show="!isSuccess && isFailed" x-cloak class="mt-2 space-y-2">
            <div class="bg-rose-950/80 border border-rose-800/80 text-rose-300 rounded-xl p-3 text-xs flex items-start gap-2">
                <i class="fas fa-circle-exclamation mt-0.5 text-rose-400 shrink-0"></i>
                <span x-text="errorMessage || 'Gagal memproses sampel wajah. Silakan ulangi dengan pencahayaan yang cukup.'"></span>
            </div>
            <button type="button" @click="restartEnroll()"
                    class="w-full bg-slate-800 hover:bg-slate-700 text-white font-extrabold py-3 px-4 rounded-xl transition flex items-center justify-center gap-2 text-xs cursor-pointer border border-slate-700">
                <i class="fas fa-rotate-right"></i>
                <span>Ulangi Perekaman Otomatis</span>
            </button>
        </div>

        {{-- Success State --}}
        <div x-show="isSuccess" x-cloak class="text-center py-6 check-pop">
            <div class="w-20 h-20 bg-emerald-950/80 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-emerald-400 shadow-xl shadow-emerald-500/20">
                <i class="fas fa-check text-4xl text-emerald-400"></i>
            </div>
            <h2 class="text-2xl font-black text-white mb-1 font-heading">Biometrik Berhasil Didaftarkan! 🎉</h2>
            <p class="text-slate-300 text-xs max-w-xs mx-auto leading-relaxed">
                Profil Face ID Anda kini tersimpan & disinkronkan. Wajah Anda siap digunakan untuk presensi di Kiosk Gerbang & HP.
            </p>
            <a href="{{ route('siswa.dashboard') }}"
               class="mt-6 flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-extrabold py-3.5 px-6 rounded-xl transition shadow-lg shadow-emerald-500/30 text-sm">
                <span>Lanjut ke Dashboard Siswa</span> <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <a href="{{ route('siswa.dashboard') }}" x-show="!isSuccess && !isProcessing" class="block text-center text-slate-500 hover:text-slate-300 text-xs mt-3 transition font-medium">
            Batal dan Kembali &rarr;
        </a>
    </div>

<script>
    // ── Web Audio API Synthesizer (Zero External Dependencies) ──
    const audioFx = {
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
        playAlign() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, now);
                osc.frequency.exponentialRampToValueAtTime(1174.66, now + 0.08); // A5 -> D6
                gain.gain.setValueAtTime(0.15, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.08);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.08);
            } catch (e) {}
        },
        playTick() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(1046.5, now); // C6
                gain.gain.setValueAtTime(0.08, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.03);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.03);
            } catch (e) {}
        },
        playShutter() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;

                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(400, now);
                osc1.frequency.exponentialRampToValueAtTime(120, now + 0.04);
                gain1.gain.setValueAtTime(0.3, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.04);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.04);

                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(750, now + 0.045);
                osc2.frequency.exponentialRampToValueAtTime(180, now + 0.1);
                gain2.gain.setValueAtTime(0.35, now + 0.045);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.1);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.045);
                osc2.stop(now + 0.1);
            } catch (e) {}
        },
        playStepSuccess() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                const notes = [659.25, 880]; // E5, A5
                notes.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.08);
                    gain.gain.setValueAtTime(0.2, now + idx * 0.08);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.08 + 0.2);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + idx * 0.08);
                    osc.stop(now + idx * 0.08 + 0.2);
                });
            } catch (e) {}
        },
        playVictory() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                const chord = [523.25, 659.25, 783.99, 1046.5]; // C5, E5, G5, C6
                chord.forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.09);
                    gain.gain.setValueAtTime(0.25, now + idx * 0.09);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + idx * 0.09 + 0.45);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(now + idx * 0.09);
                    osc.stop(now + idx * 0.09 + 0.45);
                });
            } catch (e) {}
        }
    };

    function enrollTouchlessApp() {
        return {
            capturedImages: [],
            isProcessing: false,
            isSuccess: false,
            isFailed: false,
            errorMessage: '',
            isCameraReady: false,
            videoStream: null,
            showFlash: false,

            // Tracking & Pose Verification States
            isFaceInOval: false,
            isPoseMatching: false,
            poseMessage: 'Posisikan wajah di dalam bingkai oval',
            holdProgress: 0,
            holdDurationMs: 850, // Waktu stabil memenuhi pose sebelum otomatis snap (~0.85s)
            lastFrameTime: 0,
            cooldownUntil: 0,
            trackingLoopId: null,

            // face-api Landmark & Expression Engine
            isFaceApiLoaded: false,
            aiYawRatio: 1.0,
            aiSmileScore: 0.0,
            lastAiCheck: 0,

            ellipsePerimeter: 776,
            stepIcons: ['fa-user', 'fa-arrow-left', 'fa-arrow-right', 'fa-face-smile', 'fa-lock'],

            steps: [
                {
                    emoji: '🎯',
                    instruction: 'Langkah 1: Hadap lurus ke depan di tengah oval',
                    hintWaiting: 'Posisikan wajah tegak lurus ke depan...',
                    hintHolding: 'Posisi lurus pas! Tahan sejenak...'
                },
                {
                    emoji: '👈',
                    instruction: 'Langkah 2: Tolehkan kepala sedikit ke KIRI',
                    hintWaiting: 'Tolehkan kepala sedikit ke KIRI 👈...',
                    hintHolding: 'Sudut toleh kiri pas! Tahan sejenak...'
                },
                {
                    emoji: '👉',
                    instruction: 'Langkah 3: Tolehkan kepala sedikit ke KANAN',
                    hintWaiting: 'Sekarang tolehkan kepala ke KANAN 👉...',
                    hintHolding: 'Sudut toleh kanan pas! Tahan sejenak...'
                },
                {
                    emoji: '😊',
                    instruction: 'Langkah 4: Tersenyumlah santai ke arah kamera',
                    hintWaiting: 'Silakan tersenyum santai ke kamera 😊...',
                    hintHolding: 'Senyuman terdeteksi! Tahan senyuman Anda 😊...'
                },
                {
                    emoji: '🔒',
                    instruction: 'Langkah 5: Hadap lurus kembali (Kunci Biometrik)',
                    hintWaiting: 'Hadap lurus ke depan sekali lagi...',
                    hintHolding: 'Mengunci 68 titik koordinat biometrik...'
                }
            ],

            get currentStepInfo() {
                const idx = Math.min(this.capturedImages.length, 4);
                return this.steps[idx];
            },

            async init() {
                this.loadFaceApi();
                await this.startCamera();
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
                        console.warn('face-api models load note:', e);
                    }
                }
            },

            async startCamera() {
                try {
                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        throw new Error("Akses kamera tidak didukung atau memerlukan HTTPS.");
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
                        video.width = video.videoWidth || 960;
                        video.height = video.videoHeight || 720;
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

                // Offscreen canvas berkecepatan tinggi (80x100)
                const offCanvas = document.createElement('canvas');
                offCanvas.width = 80;
                offCanvas.height = 100;
                const offCtx = offCanvas.getContext('2d', { willReadFrequently: true });

                let lastTickTime = 0;

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

                    // Lewati analisa jika masih dalam masa jeda setelah snapshot
                    if (now < this.cooldownUntil) {
                        this.holdProgress = 0;
                        this.isPoseMatching = false;
                        this.poseMessage = 'Bersiap untuk langkah berikutnya...';
                        this.trackingLoopId = requestAnimationFrame(checkLoop);
                        return;
                    }

                    // 1. Eksekusi Model AI Landmark jika siap (~setiap 120ms)
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

                                if (detection.expressions && typeof detection.expressions.happy === 'number') {
                                    this.aiSmileScore = detection.expressions.happy;
                                }
                            }
                        } catch (e) {}
                    }

                    const vw = video.videoWidth || 960;
                    const vh = video.videoHeight || 720;

                    // Crop area oval tengah
                    const cropW = vw * 0.52;
                    const cropH = vh * 0.72;
                    const cropX = (vw - cropW) / 2;
                    const cropY = (vh - cropH) * 0.44;

                    offCtx.drawImage(video, cropX, cropY, cropW, cropH, 0, 0, 80, 100);
                    const imgData = offCtx.getImageData(0, 0, 80, 100);
                    const data = imgData.data;

                    // 2. Analisis Geometri & Fotometrik Asimetri Wajah (Real-Time 60 FPS)
                    let ovalPix = 0;
                    let skinPix = 0;
                    let foreheadSkin = 0;
                    let chinSkin = 0;
                    let eyeContrastSum = 0;

                    let leftHalfSkin = 0;  // x < 40 (Sisi kanan fisik siswa pada raw video)
                    let rightHalfSkin = 0; // x >= 40 (Sisi kiri fisik siswa pada raw video)

                    let cheekSkinLeft = 0;  // x: 14..32, y: 44..66
                    let cheekSkinRight = 0; // x: 48..66, y: 44..66

                    let minLipX = 80, maxLipX = 0;
                    let lipPixelCount = 0;

                    for (let y = 0; y < 100; y++) {
                        for (let x = 0; x < 80; x++) {
                            const idx = (y * 80 + x) * 4;
                            const r = data[idx];
                            const g = data[idx + 1];
                            const b = data[idx + 2];

                            const Y  =  0.299 * r + 0.587 * g + 0.114 * b;
                            const Cb = 128 - 0.1687 * r - 0.3313 * g + 0.5 * b;
                            const Cr = 128 + 0.5 * r - 0.4187 * g - 0.0813 * b;

                            const dx = (x - 40) / 32;
                            const dy = (y - 50) / 42;
                            const inOval = (dx * dx + dy * dy) <= 1.0;

                            const isSkin = (Y >= 35 && Y <= 245 && Cr >= 126 && Cr <= 178 && Cb >= 72 && Cb <= 135 && (Cr - Cb) >= 2);

                            if (inOval) {
                                ovalPix++;
                                if (isSkin) {
                                    skinPix++;
                                    if (x < 40) leftHalfSkin++;
                                    else rightHalfSkin++;
                                }
                            }

                            if (y >= 15 && y <= 28 && x >= 25 && x <= 55 && isSkin) {
                                foreheadSkin++;
                            }
                            if (y >= 80 && y <= 94 && x >= 28 && x <= 52 && isSkin) {
                                chinSkin++;
                            }
                            if (y >= 32 && y <= 46 && x >= 20 && x <= 60 && y > 32) {
                                const prevIdx = ((y - 1) * 80 + x) * 4;
                                const prevY = 0.299 * data[prevIdx] + 0.587 * data[prevIdx + 1] + 0.114 * data[prevIdx + 2];
                                eyeContrastSum += Math.abs(Y - prevY);
                            }

                            // Pipi Kiri vs Kanan (Asimetri toleh kepala)
                            if (y >= 44 && y <= 66) {
                                if (x >= 14 && x <= 32 && isSkin) cheekSkinLeft++;
                                if (x >= 48 && x <= 66 && isSkin) cheekSkinRight++;
                            }

                            // Area Mulut (y: 68..84, x: 22..58) untuk deteksi Senyum
                            if (y >= 68 && y <= 84 && x >= 22 && x <= 58) {
                                const isLip = ((Cr - Cb) >= 16 && r > g * 1.18 && r >= 55);
                                if (isLip) {
                                    lipPixelCount++;
                                    if (x < minLipX) minLipX = x;
                                    if (x > maxLipX) maxLipX = x;
                                }
                            }
                        }
                    }

                    const skinRatio = ovalPix > 0 ? (skinPix / ovalPix) : 0;
                    this.isFaceInOval = (
                        skinRatio >= 0.16 &&
                        skinRatio <= 0.88 &&
                        foreheadSkin >= 16 &&
                        chinSkin >= 12 &&
                        eyeContrastSum > 90
                    );

                    if (!this.isFaceInOval) {
                        this.isPoseMatching = false;
                        this.poseMessage = 'Posisikan wajah di dalam bingkai oval';
                        this.holdProgress = Math.max(0, this.holdProgress - (dt / 200) * 100);
                        this.trackingLoopId = requestAnimationFrame(checkLoop);
                        return;
                    }

                    // 3. VALIDASI GERAKAN & SUDUT KEPALA SPESIFIK (ANTI-CHEAT POSE DETECTOR)
                    const currentStep = this.capturedImages.length; // 0: Depan, 1: Kiri, 2: Kanan, 3: Senyum, 4: Depan
                    const rawLeftRatio = (leftHalfSkin + 1) / (rightHalfSkin + 1);
                    const cheekRatio = (cheekSkinLeft + 1) / (cheekSkinRight + 1);

                    const mouthWidth = maxLipX >= minLipX ? (maxLipX - minLipX + 1) : 0;
                    const faceWidth = Math.max(20, Math.sqrt(skinPix));
                    const smileRatio = mouthWidth / faceWidth;

                    let stepPoseValid = false;
                    let feedbackText = '';

                    if (currentStep === 0) {
                        // ── LANGKAH 1: Hadap Lurus ke Depan ──
                        const isCentered = (rawLeftRatio >= 0.72 && rawLeftRatio <= 1.38) && (this.aiYawRatio >= 0.70 && this.aiYawRatio <= 1.40);
                        if (isCentered) {
                            stepPoseValid = true;
                            feedbackText = 'Posisi lurus pas! Tahan sejenak...';
                        } else {
                            feedbackText = 'Posisikan kepala tegak lurus menghadap kamera';
                        }
                    }
                    else if (currentStep === 1) {
                        // ── LANGKAH 2: Wajib Toleh ke KIRI ──
                        // Siswa menoleh ke kiri -> di selfie mirror video, sisi kanan kamera membesar
                        const turnedLeft = (cheekRatio >= 1.25 || rawLeftRatio >= 1.22 || this.aiYawRatio >= 1.40);
                        const turnedOpposite = (cheekRatio <= 0.75 || rawLeftRatio <= 0.78 || this.aiYawRatio <= 0.70);

                        if (turnedLeft) {
                            stepPoseValid = true;
                            feedbackText = 'Sudut toleh kiri terdeteksi! Tahan...';
                        } else if (turnedOpposite) {
                            feedbackText = '⚠️ Salah arah: Tolehkan kepala ke KIRI 👈 (bukan kanan)';
                        } else {
                            feedbackText = 'Tolehkan kepala sedikit ke KIRI 👈...';
                        }
                    }
                    else if (currentStep === 2) {
                        // ── LANGKAH 3: Wajib Toleh ke KANAN ──
                        // Siswa menoleh ke kanan -> sisi kiri kamera membesar
                        const turnedRight = (cheekRatio <= 0.80 || rawLeftRatio <= 0.82 || this.aiYawRatio <= 0.70);
                        const stillFacingLeft = (cheekRatio >= 1.25 || rawLeftRatio >= 1.22 || this.aiYawRatio >= 1.35);

                        if (turnedRight) {
                            stepPoseValid = true;
                            feedbackText = 'Sudut toleh kanan terdeteksi! Tahan...';
                        } else if (stillFacingLeft) {
                            feedbackText = '⚠️ Jangan hadap kiri! Sekarang tolehkan ke KANAN 👉';
                        } else {
                            feedbackText = 'Tolehkan kepala sedikit ke KANAN 👉...';
                        }
                    }
                    else if (currentStep === 3) {
                        // ── LANGKAH 4: Wajib Tersenyum 😊 ──
                        const isSmiling = (this.aiSmileScore >= 0.40) || (smileRatio >= 0.45 && lipPixelCount >= 18);
                        if (isSmiling) {
                            stepPoseValid = true;
                            feedbackText = 'Senyuman terdeteksi! Tahan senyuman Anda 😊...';
                        } else {
                            feedbackText = 'Silakan tersenyum manis ke arah kamera 😊';
                        }
                    }
                    else if (currentStep === 4) {
                        // ── LANGKAH 5: Hadap Lurus Kembali ──
                        const isCentered = (rawLeftRatio >= 0.74 && rawLeftRatio <= 1.34) && (this.aiYawRatio >= 0.72 && this.aiYawRatio <= 1.38);
                        if (isCentered) {
                            stepPoseValid = true;
                            feedbackText = 'Kembali lurus pas! Mengunci biometrik...';
                        } else {
                            feedbackText = 'Kembalikan posisi kepala tegak lurus ke depan';
                        }
                    }

                    this.isPoseMatching = stepPoseValid;
                    this.poseMessage = feedbackText;

                    if (stepPoseValid) {
                        if (this.holdProgress === 0) {
                            audioFx.playAlign();
                        }

                        // Naikkan hold progress hingga 100%
                        const progressIncrement = (dt / this.holdDurationMs) * 100;
                        this.holdProgress = Math.min(100, this.holdProgress + progressIncrement);

                        if (now - lastTickTime > 200 && this.holdProgress < 95) {
                            audioFx.playTick();
                            lastTickTime = now;
                        }

                        // JIKA MENCAPAI 100% -> OTOMATIS AMBIL FOTO LANGKAH INI!
                        if (this.holdProgress >= 100) {
                            this.autoSnap();
                            this.cooldownUntil = performance.now() + 900; // Cooldown 900ms agar siswa bersiap ke instruksi berikutnya
                        }
                    } else {
                        // Gerakan belum cocok: turunkan progress secara tegas agar tidak terpotret tanpa pose
                        this.holdProgress = Math.max(0, this.holdProgress - (dt / 180) * 100);
                    }

                    this.trackingLoopId = requestAnimationFrame(checkLoop);
                };

                this.trackingLoopId = requestAnimationFrame(checkLoop);
            },

            autoSnap() {
                if (this.capturedImages.length >= 5 || this.isProcessing) return;

                audioFx.playShutter();
                this.triggerFlash();
                this.captureFrame();
                audioFx.playStepSuccess();

                this.holdProgress = 0;
                this.isPoseMatching = false;

                if (this.capturedImages.length >= 5) {
                    this.submitEnroll();
                }
            },

            manualCapture() {
                if (this.capturedImages.length >= 5 || this.isProcessing || !this.isCameraReady) return;
                this.autoSnap();
            },

            triggerFlash() {
                this.showFlash = true;
                setTimeout(() => {
                    this.showFlash = false;
                }, 160);
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
                        data = { success: false, message: 'Server merespons kode status ' + resp.status + '. Silakan coba lagi.' };
                    }

                    if (data.success) {
                        audioFx.playVictory();
                        this.isSuccess = true;

                        if (this.videoStream) {
                            this.videoStream.getTracks().forEach(t => t.stop());
                            this.videoStream = null;
                        }
                    } else {
                        audioFx.playShutter();
                        this.isProcessing = false;
                        this.isFailed = true;
                        this.errorMessage = data.message || 'Gagal memproses wajah di server AI.';
                    }
                } catch (e) {
                    console.error('Enroll error:', e);
                    this.isProcessing = false;
                    this.isFailed = true;
                    this.errorMessage = 'Koneksi ke server terputus. Pastikan jaringan internet Anda aktif.';
                }
            }
        };
    }
</script>
</body>
</html>
