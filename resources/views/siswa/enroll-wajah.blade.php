<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
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

        /* Apple Face ID Stage (320px) */
        .faceid-stage {
            position: relative;
            width: 320px;
            height: 320px;
            max-width: 92vw;
            max-height: 92vw;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Central Circular Camera Viewport (220px) */
        .faceid-viewport {
            position: relative;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            overflow: hidden;
            background: #000;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.14), 0 20px 40px rgba(0, 0, 0, 0.85);
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
            width: 320px;
            height: 320px;
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
            width: 228px;
            height: 228px;
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
<body class="min-h-screen w-screen flex flex-col justify-between items-center px-4 py-5 bg-black select-none"
      x-data="faceIdAppleApp()" x-init="init()">

    <!-- TOP: APPLE HEADER & ICON -->
    <header class="w-full max-w-md text-center pt-1 sm:pt-2">
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
            <!-- Central Circular Camera Viewport (220px) -->
            <div class="faceid-viewport">
                <video id="video-enroll" autoplay playsinline muted></video>
                <canvas id="canvas-enroll" class="hidden"></canvas>

                <!-- Shutter Vignette Flash -->
                <div class="shutter-flash" :class="{ 'active': showFlash }"></div>

                <!-- Camera Denied Viewport Overlay -->
                <div x-show="isCameraDenied" x-cloak class="absolute inset-0 bg-zinc-950/95 flex flex-col items-center justify-center p-4 text-center z-30">
                    <div class="w-12 h-12 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2zM3 3l18 18"/></svg>
                    </div>
                    <span class="text-xs font-semibold text-zinc-200">Izin Kamera Dibutuhkan</span>
                    <span class="text-[10px] text-zinc-400 mt-0.5">Akses kamera peramban belum aktif</span>
                </div>

                <!-- Success Overlay with Apple Checkmark -->
                <div x-show="isSuccess" x-cloak class="absolute inset-0 bg-black/75 backdrop-blur-md flex flex-col items-center justify-center z-40">
                    <svg class="w-20 h-20" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="24" />
                        <path class="checkmark-check" d="M14 27l8 8 16-16" />
                    </svg>
                </div>
            </div>

            <!-- 60 Radial Ticks Canvas (320px) Overlay — 60fps Native High-DPI Rendering -->
            <canvas id="faceid-ring-canvas" width="320" height="320"></canvas>

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
                    <span class="inline-flex items-center gap-1.5 bounce-left">
                        <i class="fas fa-arrow-left text-xs"></i> Tolehkan sedikit ke kiri
                    </span>
                </template>
                <template x-if="capturedImages.length === 2">
                    <span class="inline-flex items-center gap-1.5 bounce-right">
                        Tolehkan sedikit ke kanan <i class="fas fa-arrow-right text-xs"></i>
                    </span>
                </template>
                <template x-if="capturedImages.length === 3">
                    <span class="inline-flex items-center gap-1">
                        Tersenyumlah santai ke kamera
                    </span>
                </template>
                <template x-if="capturedImages.length === 4">
                    <span class="inline-flex items-center gap-1">
                        Hadap lurus ke depan untuk mengunci data
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

        <!-- Error Alert & Action Buttons -->
        <div x-show="!isSuccess && isFailed" x-cloak class="mt-3 w-full max-w-sm px-4">
            <div class="apple-pill border-rose-500/40 text-rose-300 rounded-2xl p-3 text-xs flex items-start gap-2 shadow-lg">
                <i class="fas fa-triangle-exclamation mt-0.5 text-rose-400 shrink-0"></i>
                <span x-text="errorMessage || 'Gagal merekam sampel wajah. Pastikan pencahayaan cukup dan wajah terlihat jelas.'"></span>
            </div>

            <!-- Tombol Khusus Jika Izin Kamera Ditolak (Permission Denied) -->
            <template x-if="isCameraDenied">
                <div class="mt-2.5 flex flex-col gap-2">
                    <button type="button" @click="startCamera()"
                            class="w-full bg-zinc-100 hover:bg-white text-zinc-950 font-bold py-2.5 px-4 rounded-xl transition text-xs flex items-center justify-center gap-2 cursor-pointer shadow-sm active:scale-[0.98]">
                        <svg class="w-4 h-4 text-zinc-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Izinkan Kamera Ulang</span>
                    </button>
                    
                    <button type="button" @click="showPermissionGuide = !showPermissionGuide"
                            class="w-full apple-pill hover:bg-white/10 text-zinc-300 font-medium py-2 px-3 rounded-xl transition text-[11px] flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span x-text="showPermissionGuide ? 'Sembunyikan Panduan Izin' : 'Cara Mengaktifkan Izin Kamera'"></span>
                    </button>
                </div>
            </template>

            <!-- Tombol Retry Standar Jika Bukan Masalah Izin -->
            <template x-if="!isCameraDenied">
                <button type="button" @click="restartEnroll()"
                        class="mt-2 w-full apple-pill hover:bg-white/10 text-white font-semibold py-2 px-4 rounded-xl transition text-xs flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fas fa-rotate-right text-xs"></i>
                    <span>Ulangi Perekaman</span>
                </button>
            </template>
        </div>

        <!-- Panduan Langkah Mengaktifkan Kamera di Browser -->
        <div x-show="!isSuccess && isCameraDenied && showPermissionGuide" x-cloak class="mt-3 w-full max-w-sm px-4">
            <div class="apple-pill border-zinc-700 bg-zinc-900/95 rounded-2xl p-4 text-xs text-zinc-300 space-y-3 shadow-xl">
                <div class="flex items-center justify-between border-b border-zinc-800 pb-2">
                    <span class="font-bold text-white flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Cara Buka Blokir Kamera
                    </span>
                    <button type="button" @click="showPermissionGuide = false" class="text-zinc-500 hover:text-white p-1">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>

                <div class="space-y-2.5 text-[11px] leading-relaxed">
                    <div class="p-2.5 rounded-xl bg-zinc-800/60 border border-zinc-700/60">
                        <p class="font-bold text-zinc-100 flex items-center gap-1.5 mb-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                            Google Chrome (Android / PC):
                        </p>
                        <ol class="list-decimal list-inside space-y-1 text-zinc-400 pl-1">
                            <li>Ketuk ikon <b>Gembok / Setelan Situs</b> di kiri bilah URL web.</li>
                            <li>Pilih <b>Izin (Permissions)</b> ➔ <b>Kamera</b> ➔ Ubah ke <b>Izinkan (Allow)</b>.</li>
                            <li>Tekan tombol Muat Ulang di bawah.</li>
                        </ol>
                    </div>

                    <div class="p-2.5 rounded-xl bg-zinc-800/60 border border-zinc-700/60">
                        <p class="font-bold text-zinc-100 flex items-center gap-1.5 mb-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                            Safari (iPhone / iPad):
                        </p>
                        <ol class="list-decimal list-inside space-y-1 text-zinc-400 pl-1">
                            <li>Ketuk tombol <b>aA</b> di sebelah kiri kolom alamat URL web.</li>
                            <li>Pilih <b>Pengaturan Situs Web</b> ➔ <b>Kamera</b> ➔ <b>Izinkan</b>.</li>
                            <li>Tekan tombol Muat Ulang di bawah.</li>
                        </ol>
                    </div>
                </div>

                <button type="button" onclick="window.location.reload()"
                        class="w-full bg-zinc-800 hover:bg-zinc-700 text-white font-semibold py-2 px-3 rounded-xl transition text-xs flex items-center justify-center gap-1.5 border border-zinc-700 cursor-pointer">
                    <i class="fas fa-rotate text-xs text-zinc-400"></i>
                    <span>Muat Ulang Halaman</span>
                </button>
            </div>
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
                gain.gain.setValueAtTime(0.04, now);
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

    // Unlock Audio Context on first mobile touch
    window.addEventListener('touchstart', () => appleFx.getCtx(), { once: true, passive: true });
    window.addEventListener('mousedown', () => appleFx.getCtx(), { once: true, passive: true });

    function faceIdAppleApp() {
        return {
            capturedImages: [],
            isProcessing: false,
            isSuccess: false,
            isFailed: false,
            errorMessage: '',
            isCameraReady: false,
            isCameraDenied: false,
            showPermissionGuide: false,
            videoStream: null,
            showFlash: false,

            // Canvas & Animation State
            displayFilledTicks: 0,
            targetFilledTicks: 0,
            radarAngle: 0,

            // Tracking & Pose States
            isFaceDetected: false,
            isPoseMatching: false,
            currentYaw: 0.0,
            baselineYaw: 0.0,
            isBaselineCalibrated: false,
            currentSmile: 0.0,
            holdProgress: 0,
            cooldownUntil: 0,
            stepEnteredAt: performance.now(),
            animLoopId: null,

            // Detection Loop throttling & Grace Period
            isDetecting: false,
            lastDetectTime: 0,
            lastDetectionSuccess: 0,

            steps: [
                {
                    instruction: 'Hadap lurus ke depan',
                    appleInstruction: 'Langkah 1: Posisikan wajah Anda di dalam lingkaran, hadap lurus ke depan.'
                },
                {
                    instruction: 'Tolehkan kepala sedikit ke kiri',
                    appleInstruction: 'Langkah 2: Tolehkan kepala Anda perlahan sedikit ke kiri.'
                },
                {
                    instruction: 'Tolehkan kepala sedikit ke kanan',
                    appleInstruction: 'Langkah 3: Tolehkan kepala Anda perlahan sedikit ke kanan.'
                },
                {
                    instruction: 'Tersenyumlah santai',
                    appleInstruction: 'Langkah 4: Tersenyumlah santai menghadap kamera.'
                },
                {
                    instruction: 'Hadap lurus ke depan',
                    appleInstruction: 'Langkah 5: Hadap lurus ke depan sekali lagi untuk menyelesaikan pemindaian.'
                }
            ],

            get currentStepInfo() {
                const idx = Math.min(this.capturedImages.length, 4);
                return this.steps[idx];
            },

            get currentStatusLabel() {
                if (this.isPoseMatching) {
                    const step = this.capturedImages.length;
                    const pct = Math.min(100, Math.round(this.holdProgress));
                    if (step === 0) return `Posisikan Wajah... ${pct}%`;
                    if (step === 1) return `Toleh Kiri... ${pct}%`;
                    if (step === 2) return `Toleh Kanan... ${pct}%`;
                    if (step === 3) return `Senyum... ${pct}%`;
                    if (step === 4) return `Kunci Face ID... ${pct}%`;
                    return `${pct}% Tahan Posisi...`;
                }
                if (this.isFaceDetected) {
                    return this.currentStepInfo.instruction;
                }
                return 'Posisikan wajah di dalam lingkaran';
            },

            init() {
                this.initCanvas();
                this.startAnimationLoop(); // Mulai render ticks canvas seketika sejak frame 0
                this.loadFaceApi();
                this.startCamera();
            },

            initCanvas() {
                const canvas = document.getElementById('faceid-ring-canvas');
                if (canvas) {
                    const dpr = window.devicePixelRatio || 1;
                    canvas.width = 320 * dpr;
                    canvas.height = 320 * dpr;
                    canvas.style.width = '320px';
                    canvas.style.height = '320px';
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
                this.isFailed = false;
                this.errorMessage = '';
                this.isCameraDenied = false;

                try {
                    if (!navigator.mediaDevices?.getUserMedia) {
                        throw new Error("Akses kamera tidak didukung pada peramban ini. Pastikan Anda menggunakan HTTPS.");
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
                        this.isCameraDenied = false;
                        this.isFailed = false;
                        this.showPermissionGuide = false;
                    };
                } catch (err) {
                    this.isCameraReady = false;
                    this.isFailed = true;

                    const isDenied = err.name === 'NotAllowedError' 
                        || err.name === 'PermissionDeniedError' 
                        || (err.message && (err.message.toLowerCase().includes('denied') || err.message.toLowerCase().includes('permission')));
                    
                    this.isCameraDenied = isDenied;
                    if (isDenied) {
                        this.errorMessage = 'Izin kamera ditolak oleh peramban (Permission Denied). Tekan tombol "Izinkan Kamera Ulang" di bawah atau aktifkan melalui setelan peramban.';
                    } else {
                        this.errorMessage = 'Gagal mengakses kamera: ' + err.message;
                    }
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
                        // Toleransi tinggi (inputSize 320, scoreThreshold 0.15 agar wajah miring tidak drop)
                        const detection = await faceapi.detectSingleFace(
                            video,
                            new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.15 })
                        ).withFaceLandmarks(true).withFaceExpressions();

                        if (detection && detection.landmarks) {
                            this.lastDetectionSuccess = performance.now();
                            this.isFaceDetected = true;

                            const pts = detection.landmarks.positions;
                            const nose = pts[30];
                            const leftEye = pts[36];
                            const rightEye = pts[45];
                            const box = detection.detection.box;

                            // 1. Pergeseran hidung terhadap titik tengah kotak wajah (Sangat stabil, bebas oklusi rahang)
                            const boxCenterX = box.x + (box.width / 2);
                            const noseOffset = (nose.x - boxCenterX) / Math.max(1, box.width);

                            // 2. Asimetri mata terhadap hidung
                            const eyeMidX = (leftEye.x + rightEye.x) / 2;
                            const eyeSpan = Math.max(1, rightEye.x - leftEye.x);
                            const eyeOffset = (nose.x - eyeMidX) / eyeSpan;

                            // Metrik yaw yang sangat toleran dan stabil
                            const rawYaw = (noseOffset * 2.0) + (eyeOffset * 0.8);
                            this.currentYaw = (this.currentYaw * 0.5) + (rawYaw * 0.5);

                            if (detection.expressions?.happy !== undefined) {
                                this.currentSmile = (this.currentSmile * 0.6) + (detection.expressions.happy * 0.4);
                            }
                        } else if (detection && detection.box) {
                            // Kotak wajah terdeteksi meskipun landmark sedang kabur sesaat
                            this.lastDetectionSuccess = performance.now();
                            this.isFaceDetected = true;
                        }
                    } else {
                        // Fallback detection if faceapi still loading
                        this.lastDetectionSuccess = performance.now();
                        this.isFaceDetected = true;
                    }
                } catch (e) {
                    // Silent failover
                } finally {
                    this.isDetecting = false;
                }
            },

            evaluatePoseState(dt) {
                // Buffer toleransi 1200ms: Jangan pernah reset progress jika hanya kehilangan frame sekejap saat bergerak
                const timeSinceLastDetection = performance.now() - this.lastDetectionSuccess;
                this.isFaceDetected = timeSinceLastDetection < 1200;

                if (!this.isFaceDetected) {
                    this.isPoseMatching = false;
                    this.holdProgress = Math.max(0, this.holdProgress - (dt / 300) * 100);
                    return;
                }

                const step = this.capturedImages.length;
                let matches = false;

                // Hitung rotasi relatif terhadap posisi natural kepala pengguna
                const deltaLeft  = this.currentYaw - this.baselineYaw; // bernilai positif saat toleh ke kiri
                const deltaRight = this.baselineYaw - this.currentYaw; // bernilai positif saat toleh ke kanan
                const timeInCurrentStep = performance.now() - this.stepEnteredAt;

                switch (step) {
                    case 0: // 1. Lurus depan
                        matches = Math.abs(this.currentYaw) < 0.25;
                        if (matches && !this.isBaselineCalibrated) {
                            this.baselineYaw = (this.baselineYaw * 0.7) + (this.currentYaw * 0.3);
                        }
                        break;

                    case 1: // 2. Toleh KIRI
                        // Cukup toleh sedikit (delta > 0.035) atau dipandu santai ~1.3 detik saat wajah di frame
                        matches = (deltaLeft > 0.035) || (timeInCurrentStep > 1300);
                        break;

                    case 2: // 3. Toleh KANAN
                        // Cukup toleh sedikit (delta > 0.035) atau dipandu santai ~1.3 detik saat wajah di frame
                        matches = (deltaRight > 0.035) || (timeInCurrentStep > 1300);
                        break;

                    case 3: // 4. Senyum santai
                        matches = (this.currentSmile > 0.18) || (timeInCurrentStep > 1100);
                        break;

                    case 4: // 5. Kunci Face ID (Hadap lurus kembali)
                        matches = (Math.abs(this.currentYaw - this.baselineYaw) < 0.08) || (timeInCurrentStep > 1100);
                        break;
                }

                this.isPoseMatching = matches;

                // Progress mengalir cepat (320ms jika pose pas, atau 500ms saat transisi terbimbing)
                const targetHoldMs = matches ? 320 : 500;

                if (matches) {
                    this.holdProgress += (dt / targetHoldMs) * 100;
                    if (this.holdProgress >= 100) {
                        this.holdProgress = 0;
                        this.autoSnapStep();
                    }
                } else {
                    this.holdProgress = Math.max(0, this.holdProgress - (dt / 350) * 100);
                }
            },

            autoSnapStep() {
                // Kunci baseline saat langkah 0 selesai
                if (this.capturedImages.length === 0) {
                    this.baselineYaw = this.currentYaw;
                    this.isBaselineCalibrated = true;
                }

                this.triggerFlash();
                this.captureFrame();
                appleFx.playStepSnap();

                this.targetFilledTicks = this.capturedImages.length * 12;

                if (this.capturedImages.length >= 5) {
                    this.submitEnroll();
                } else {
                    this.cooldownUntil = performance.now() + 400;
                    this.stepEnteredAt = performance.now() + 400;
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
                const dpr = window.devicePixelRatio || 1;
                const W = 320;
                const H = 320;

                ctx.save();
                ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
                ctx.clearRect(0, 0, W, H);

                const cx = W / 2;
                const cy = H / 2;
                const rIn = 116;
                const rOut = 138;
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
                        // Completed Ticks: Apple Vibrant Neon Green #30D158
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
                        // Inactive Ticks: Frosted Silver
                        ctx.lineWidth = 3.2;
                        ctx.strokeStyle = 'rgba(255, 255, 255, 0.28)';
                        ctx.shadowColor = 'transparent';
                        ctx.shadowBlur = 0;
                    }

                    ctx.moveTo(x1, y1);
                    ctx.lineTo(x2, y2);
                    ctx.stroke();
                }

                // 2. Animated Apple Radar Scanner Arc
                this.radarAngle = (this.radarAngle + 0.035) % (Math.PI * 2);
                ctx.beginPath();
                ctx.arc(cx, cy, 127, this.radarAngle, this.radarAngle + Math.PI * 0.45);
                ctx.strokeStyle = 'rgba(56, 189, 248, 0.35)';
                ctx.lineWidth = 2.2;
                ctx.shadowColor = 'rgba(56, 189, 248, 0.6)';
                ctx.shadowBlur = 8;
                ctx.stroke();

                // 3. Apple Viewport Corner Guide Brackets
                const bSize = 20;
                const bOffset = 82;
                const bRadius = 5;
                ctx.lineWidth = 2.5;
                ctx.lineCap = 'round';
                ctx.strokeStyle = this.isFaceDetected
                    ? (this.isPoseMatching ? '#30D158' : 'rgba(255, 255, 255, 0.6)')
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
                ctx.quadraticCurveTo(cx + bOffset, cy + bOffset, cx + bOffset, cy + bOffset - bRadius);
                ctx.lineTo(cx + bOffset - bSize, cy + bOffset);
                ctx.stroke();

                ctx.restore();
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
                this.baselineYaw = 0.0;
                this.currentYaw = 0.0;
                this.isBaselineCalibrated = false;
                this.cooldownUntil = performance.now() + 400;
                this.stepEnteredAt = performance.now() + 400;

                if (!this.isCameraReady || !this.videoStream) {
                    this.startCamera();
                }
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
