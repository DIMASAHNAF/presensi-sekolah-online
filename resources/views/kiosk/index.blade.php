<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kiosk Presensi Face ID — {{ $schoolSetting->school_name ?? 'SMKN 1 Beringin' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
    
    <!-- Tailwind CSS & AlpineJS & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #070b14;
            color: #f8fafc;
            overflow: hidden;
            user-select: none;
        }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Camera feed base */
        #camera-feed {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.25s ease-out;
        }
        .mirror-mode {
            transform: scaleX(-1);
        }

        /* Glassmorphism Panels */
        .glass-panel {
            background: rgba(13, 20, 36, 0.82);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* AR Target Face Box — NO CSS transition, rAF lerp handles smoothness at 60fps */
        .ar-face-box {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
            border-radius: 12px;
            z-index: 20;
            will-change: transform, width, height;
        }
        .ar-corner {
            position: absolute;
            width: 20px;
            height: 20px;
            pointer-events: none;
            filter: drop-shadow(0 0 6px currentColor);
        }
        .ar-tl { top: -2px; left: -2px; border-top: 3.5px solid; border-left: 3.5px solid; border-top-left-radius: 8px; }
        .ar-tr { top: -2px; right: -2px; border-top: 3.5px solid; border-right: 3.5px solid; border-top-right-radius: 8px; }
        .ar-bl { bottom: -2px; left: -2px; border-bottom: 3.5px solid; border-left: 3.5px solid; border-bottom-left-radius: 8px; }
        .ar-br { bottom: -2px; right: -2px; border-bottom: 3.5px solid; border-right: 3.5px solid; border-bottom-right-radius: 8px; }

        /* Floating AR HUD Tag — NO CSS transition, rAF lerp handles at 60fps */
        .ar-hud-tag {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 25;
            will-change: transform;
        }

        /* Success Pop for modal fallback */
        @keyframes modal-pop {
            0% { transform: scale(0.9); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-pop { animation: modal-pop 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>
<body class="h-screen w-screen flex flex-col justify-between overflow-hidden bg-slate-950" x-data="kioskApp()" x-init="initKiosk()">

    <!-- TOP BAR: HEADER & CLOCK & CONTROLS -->
    <header class="glass-panel px-4 sm:px-6 py-3 z-30 flex items-center justify-between border-b border-white/10 shadow-xl">
        <div class="flex items-center space-x-3 sm:space-x-3.5">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-blue-600 via-cyan-500 to-emerald-400 p-0.5 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/logo.webp') }}" class="w-8 h-8 object-contain" alt="Logo" onerror="this.src='https://ui-avatars.com/api/?name=SMK&background=0D8ABC&color=fff'">
                </div>
            </div>
            <div>
                <h1 class="text-sm sm:text-base md:text-lg font-black tracking-wide text-white uppercase flex items-center gap-2">
                    {{ $schoolSetting->school_name ?? 'SMKN 1 BERINGIN' }}
                    <span class="text-[10px] bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 px-2 py-0.5 rounded-full font-mono font-bold tracking-widest">
                        GERBANG WALKTHROUGH
                    </span>
                    <span class="hidden lg:inline-flex text-[9px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-2 py-0.5 rounded-full font-mono font-bold items-center gap-1">
                        <i class="fas fa-brain text-[10px]"></i> AI MULTI-FACE (3-6m)
                    </span>
                </h1>
                <p class="text-[11px] sm:text-xs text-slate-400 font-medium hidden sm:block">Real-Time Multi-Person Long-Range Attendance (YuNet + ArcFace 512-D)</p>
            </div>
        </div>

        <!-- Clock & Controls -->
        <div class="flex items-center space-x-2 sm:space-x-4">
            <!-- Digital Clock -->
            <div class="hidden sm:flex flex-col text-right">
                <span class="text-lg sm:text-2xl font-black text-cyan-400 font-mono tracking-wider" x-text="currentTime">--:--:--</span>
                <span class="text-[10px] sm:text-[11px] text-slate-400 font-medium" x-text="currentDate">--</span>
            </div>

            <div class="h-8 w-px bg-white/10 hidden sm:block"></div>

            <!-- Action Controls -->
            <div class="flex items-center space-x-1.5 sm:space-x-2">
                <!-- Zoom Control Button (1.0x / 1.5x / 2.0x) -->
                <button @click="cycleZoom()" 
                        :title="'Zoom Kamera: ' + zoomLevel.toFixed(1) + 'x (Klik untuk perbesar jangkauan)'" 
                        class="h-9 sm:h-10 px-2.5 sm:px-3 rounded-xl bg-slate-800/90 hover:bg-slate-700 border border-white/15 text-cyan-300 flex items-center gap-1.5 transition active:scale-95 shadow">
                    <i class="fas fa-magnifying-glass-plus text-xs"></i>
                    <span class="text-xs font-mono font-extrabold" x-text="zoomLevel.toFixed(1) + 'x'">1.0x</span>
                </button>

                <!-- Camera Switcher (Kamera Belakang vs Depan) -->
                <button @click="switchCamera()" 
                        :title="cameraFacing === 'environment' ? 'Kamera Belakang Aktif (Gerbang) - Klik untuk ganti kamera' : 'Kamera Depan Aktif (Selfie) - Klik untuk ganti kamera'" 
                        class="h-9 sm:h-10 px-2.5 sm:px-3 rounded-xl border flex items-center gap-1.5 transition active:scale-95 shadow"
                        :class="cameraFacing === 'environment' ? 'bg-cyan-950/80 border-cyan-400/50 text-cyan-300' : 'bg-slate-800/90 border-white/15 text-slate-300'">
                    <i class="fas fa-camera-rotate text-xs"></i>
                    <span class="text-xs font-bold hidden md:inline" x-text="cameraFacing === 'environment' ? 'Kamera Belakang' : 'Kamera Depan'">Kamera</span>
                </button>

                <!-- Sync Database to RAM -->
                <button @click="syncDatabase()" title="Sinkronkan Biometrik ke RAM Server" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-800/80 hover:bg-slate-700 border border-white/10 text-cyan-400 flex items-center justify-center transition active:scale-95">
                    <i class="fas fa-arrows-rotate text-xs sm:text-sm" :class="isSyncing ? 'fa-spin text-amber-400' : ''"></i>
                </button>

                <!-- Audio TTS Mute Toggle -->
                <button @click="toggleVoice()" :title="voiceEnabled ? 'Suara Aktif' : 'Suara Dimatikan'" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-slate-800/80 hover:bg-slate-700 border border-white/10 flex items-center justify-center transition active:scale-95" :class="voiceEnabled ? 'text-emerald-400' : 'text-slate-500'">
                    <i class="fas text-xs sm:text-sm" :class="voiceEnabled ? 'fa-volume-high' : 'fa-volume-xmark'"></i>
                </button>

                <!-- Fullscreen Toggle -->
                <button @click="toggleFullscreen()" title="Layar Penuh" class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center transition active:scale-95 shadow-md shadow-blue-500/25">
                    <i class="fas text-xs sm:text-sm" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- MAIN BODY: CLEAN CAMERA VIEWPORT & AR FLOATING TAGS LAYER -->
    <main class="relative flex-1 w-full h-full overflow-hidden flex items-center justify-center bg-black">

        <!-- Live Camera Video (Clean, No Centered Dark Mask) -->
        <video id="camera-feed" 
               autoplay 
               playsinline 
               muted 
               :class="cameraFacing === 'user' ? 'mirror-mode' : ''"
               :style="getVideoTransformStyle()"></video>
        <canvas id="snapshot-canvas" class="hidden"></canvas>

        <!-- AR FLOATING HUD OVERLAY LAYER (Tracks Detected Faces) -->
        <div id="ar-overlay" class="absolute inset-0 pointer-events-none overflow-hidden z-20">
            
            <!-- RECOGNIZED STUDENTS (Multi-Person Floating HUD Cards) -->
            <template x-for="face in activeFaces" :key="face.id">
                <div>
                    <!-- Target Face Bounding Box with Futuristic Corner Brackets -->
                    <div class="ar-face-box" :id="'face-box-' + face.student.id" style="display:none">
                        <div class="ar-corner ar-tl" :class="face.already_attended ? 'border-amber-400 text-amber-400' : 'border-emerald-400 text-emerald-400'"></div>
                        <div class="ar-corner ar-tr" :class="face.already_attended ? 'border-amber-400 text-amber-400' : 'border-emerald-400 text-emerald-400'"></div>
                        <div class="ar-corner ar-bl" :class="face.already_attended ? 'border-amber-400 text-amber-400' : 'border-emerald-400 text-emerald-400'"></div>
                        <div class="ar-corner ar-br" :class="face.already_attended ? 'border-amber-400 text-amber-400' : 'border-emerald-400 text-emerald-400'"></div>
                        
                        <!-- Top Mini Tag Indicator -->
                        <div class="absolute -top-6 left-1/2 -translate-x-1/2 whitespace-nowrap px-2 py-0.5 rounded text-[9px] font-mono font-black uppercase tracking-wider shadow-md backdrop-blur-md"
                             :class="face.already_attended ? 'bg-amber-950/90 text-amber-300 border border-amber-500/40' : 'bg-emerald-950/90 text-emerald-300 border border-emerald-500/40'">
                            <span x-text="face.already_attended ? 'SUDAH HADIR' : 'VERIFIED 512-D'"></span>
                        </div>
                    </div>

                    <!-- Floating HUD Card (GPU tracking directly under/above student's face) -->
                    <div class="ar-hud-tag" :id="'face-tag-' + face.student.id" style="display:none">
                        
                        <div class="glass-panel w-[250px] sm:w-[275px] rounded-2xl p-3 border shadow-2xl relative overflow-hidden backdrop-blur-xl"
                             :class="face.already_attended ? 'border-amber-400/70 shadow-amber-500/20' : 'border-emerald-400/80 shadow-emerald-500/25'">

                            <!-- Top Glowing Accent -->
                            <div class="absolute top-0 left-0 right-0 h-1"
                                 :class="face.already_attended ? 'bg-gradient-to-r from-amber-500 to-yellow-400' : 'bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-400'"></div>

                            <!-- Header: Avatar + Status Badge + NISN -->
                            <div class="flex items-center gap-2.5">
                                <div class="relative w-11 h-11 flex-shrink-0">
                                    <img :src="face.student?.avatar_url || face.student?.scan_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(face.student?.name || 'S') + '&size=100&background=0284c7&color=fff'"
                                         class="w-full h-full rounded-xl object-cover border-2 shadow"
                                         :class="face.already_attended ? 'border-amber-400' : 'border-emerald-400'"
                                         alt="Foto">
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-md flex items-center justify-center text-[9px] text-white shadow"
                                         :class="face.already_attended ? 'bg-amber-500' : 'bg-emerald-500'">
                                        <i class="fas" :class="face.already_attended ? 'fa-clock' : 'fa-check'"></i>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider mb-0.5"
                                         :class="face.already_attended ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40'">
                                        <span x-text="face.already_attended ? 'SUDAH HADIR' : 'PRESENSI BERHASIL'"></span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono truncate">
                                        NISN: <span class="text-slate-200 font-semibold" x-text="face.student?.nisn || '-'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Student Name -->
                            <h3 class="text-xs sm:text-sm font-black text-white font-heading uppercase truncate mt-2 leading-tight" x-text="face.student?.name">Nama Siswa</h3>

                            <!-- Class & Scan Time Row -->
                            <div class="flex items-center justify-between gap-1.5 mt-1.5 pt-1.5 border-t border-white/10 text-[10px]">
                                <span class="px-2 py-0.5 rounded bg-blue-500/25 text-blue-300 border border-blue-500/30 font-bold" x-text="face.student?.kelas || 'Siswa'">Kelas</span>
                                <span class="text-cyan-300 font-mono font-bold" x-text="face.waktu_scan || '--:-- WIB'">--:-- WIB</span>
                            </div>

                            <!-- Meta Details (Accuracy & AI Engine) -->
                            <div class="flex items-center justify-between text-[9px] text-slate-400 mt-1 pt-1 border-t border-white/5">
                                <span>Akurasi: <strong class="text-emerald-400" x-text="(face.confidence || 'HIGH').toUpperCase()">HIGH</strong></span>
                                <span class="text-sky-300 font-mono truncate">ArcFace 512-D</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

        </div>

        <!-- SUBTLE BOTTOM STATUS PILL (Zero-Loading Indicator) -->
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-25 text-center pointer-events-none">
            <div class="inline-flex items-center space-x-2.5 px-5 py-2 rounded-full shadow-2xl backdrop-blur-xl border transition-all duration-300"
                 :class="{
                    'bg-slate-900/85 border-cyan-500/40 text-cyan-300': scanState === 'idle',
                    'bg-emerald-950/90 border-emerald-400 text-emerald-200': scanState === 'success',
                    'bg-amber-950/90 border-amber-400 text-amber-200': scanState === 'warning',
                    'bg-red-950/90 border-red-400 text-red-200': scanState === 'error'
                 }">
                <!-- Glowing Live Dot instead of spinning loader -->
                <span class="relative flex h-2.5 w-2.5" x-show="scanState === 'idle'">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>

                <i class="fas text-xs"
                   :class="{
                        'hidden': scanState === 'idle',
                        'fa-circle-check text-emerald-400': scanState === 'success',
                        'fa-circle-info text-amber-400': scanState === 'warning',
                        'fa-triangle-exclamation text-red-400': scanState === 'error'
                   }"></i>
                <span class="text-xs font-bold tracking-wide" x-text="statusMessage">Sistem Siap • Siswa silakan lewat gerbang</span>
            </div>
        </div>

        <!-- MODAL FALLBACK: SCAN QR / KETIK NISN MANUAL -->
        <div x-show="showNisnModal" x-cloak class="absolute inset-0 z-40 bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
            <div class="glass-panel w-full max-w-sm rounded-3xl p-6 border border-white/15 shadow-2xl relative animate-pop text-left">
                <button @click="showNisnModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-white w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center">
                    <i class="fas fa-xmark"></i>
                </button>

                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center border border-cyan-500/30">
                        <i class="fas fa-keyboard text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white">Input / Scan NISN</h3>
                        <p class="text-xs text-slate-400">Gunakan jika wajah belum terdaftar</p>
                    </div>
                </div>

                <form @submit.prevent="submitManualNisn()">
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-300 mb-1.5">Nomor Induk Siswa Nasional (NISN)</label>
                        <input type="text" x-model="manualNisn" x-ref="nisnInput" placeholder="Masukkan 10 digit NISN..."
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-center font-mono text-lg tracking-widest focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500">
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold py-3 rounded-xl shadow-lg shadow-cyan-500/25 flex items-center justify-center gap-2 active:scale-98 transition">
                        <i class="fas fa-camera"></i>
                        <span>Verifikasi & Catat Hadir</span>
                    </button>
                </form>
            </div>
        </div>

    </main>

    <!-- BOTTOM BAR: STATS & CONTROLS -->
    <footer class="glass-panel px-4 sm:px-6 py-3 z-30 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-2.5">
        
        <!-- Counter Info -->
        <div class="flex items-center space-x-4 sm:space-x-6 text-xs">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="text-slate-300 font-medium">Hadir Hari Ini:</span>
                <span class="text-emerald-400 font-black text-base font-mono" x-text="hadirCount">{{ $hadirHariIniCount }}</span>
                <span class="text-slate-500">/ {{ $totalSiswa }} Siswa</span>
            </div>

            <div class="h-4 w-px bg-white/10 hidden sm:block"></div>

            <div class="hidden sm:flex items-center space-x-2 text-slate-400">
                <i class="fas fa-database text-cyan-400"></i>
                <span>Biometrik RAM: <strong class="text-cyan-300">{{ $totalEnrolled }}</strong> siswa</span>
            </div>

            <div class="hidden lg:flex items-center space-x-1.5 text-slate-400">
                <i class="fas fa-circle-nodes text-emerald-400"></i>
                <span class="text-[11px] text-slate-300">Deteksi Multi-Wajah Aktif (Hingga 5 Siswa Sekaligus)</span>
            </div>
        </div>

        <!-- Action to open Manual NISN -->
        <div class="flex items-center space-x-3">
            <button @click="openNisnModal()" class="px-4 py-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 border border-white/10 text-xs font-semibold text-slate-200 flex items-center gap-2 active:scale-95 transition">
                <i class="fas fa-barcode text-cyan-400"></i>
                <span>Scan Barcode / Ketik NISN</span>
            </button>
        </div>
    </footer>

    <!-- JAVASCRIPT APPLICATION LOGIC -->
    <script>
        function kioskApp() {
            return {
                videoEl: null,
                canvasEl: null,
                stream: null,
                currentTime: '',
                currentDate: '',
                isFullscreen: false,
                voiceEnabled: true,
                isSyncing: false,
                isProcessing: false,
                scanState: 'idle',
                statusMessage: 'Sistem Siap • Siswa silakan lewat gerbang',
                
                // Camera facing mode: 'environment' (rear camera default for kiosk gate) or 'user' (front camera)
                cameraFacing: localStorage.getItem('kiosk_camera_facing') || 'environment',
                zoomLevel: 1.0,
                hasHardwareZoom: false,

                // Active recognized faces and unmatched faces
                activeFaces: [],
                unmatchedFaces: [],
                
                showNisnModal: false,
                manualNisn: '',
                hadirCount: {{ $hadirHariIniCount }},

                // Cooldown map: student_id -> last_attendance_timestamp
                cooldowns: {},
                lastSpoken: {},
                recomputeKey: 0,
                frameTick: 0,
                isLoopActive: false,

                // rAF lerp: smoothed display coords per student (keyed by student id)
                smoothedFaces: {},

                initKiosk() {
                    this.videoEl = document.getElementById('camera-feed');
                    this.canvasEl = document.getElementById('snapshot-canvas');

                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    this.startCamera();

                    // Start continuous zero-delay stream detection loop
                    this.startContinuousDetection();

                    // Start 60fps rAF lerp render loop for smooth face box / card tracking
                    this.startRenderLoop();

                    // Clean expired HUD tags every 200ms
                    setInterval(() => {
                        const now = Date.now();
                        this.activeFaces = this.activeFaces.filter(f => f.expiresAt > now);
                        this.unmatchedFaces = this.unmatchedFaces.filter(f => f.expiresAt > now);
                    }, 200);

                    // Polling Live Stats every 15 seconds
                    setInterval(() => this.fetchStats(), 15000);

                    // Window resize listener to recompute AR positions accurately
                    window.addEventListener('resize', () => {
                        this.recomputeKey++;
                    });
                },

                async startContinuousDetection() {
                    this.isLoopActive = true;
                    while (this.isLoopActive) {
                        if (!this.isProcessing && !this.showNisnModal && !document.hidden && this.videoEl && this.videoEl.readyState >= 2) {
                            await this.autoCaptureAndScan();
                        }
                        // Micro-pause 90ms for instant reactivity without CPU thrashing
                        await new Promise(r => setTimeout(r, 90));
                    }
                },

                startRenderLoop() {
                    // Lerp factor: 0.16 = smooth glide at ~60fps. Lower = laggier/smoother. Higher = snappier.
                    const LERP = 0.16;

                    const tick = () => {
                        const videoEl = this.videoEl;
                        if (!videoEl) { requestAnimationFrame(tick); return; }

                        // Compute how video is rendered inside the container (object-fit: cover)
                        const containerW = videoEl.clientWidth  || window.innerWidth;
                        const containerH = videoEl.clientHeight || window.innerHeight;
                        const videoW = videoEl.videoWidth  || 1280;
                        const videoH = videoEl.videoHeight || 720;

                        const containerRatio = containerW / containerH;
                        const videoRatio     = videoW / videoH;
                        let renderW, renderH, offsetX, offsetY;

                        if (containerRatio > videoRatio) {
                            renderW = containerW; renderH = containerW / videoRatio;
                            offsetX = 0; offsetY = (containerH - renderH) / 2;
                        } else {
                            renderH = containerH; renderW = containerH * videoRatio;
                            offsetX = (containerW - renderW) / 2; offsetY = 0;
                        }

                        // Build set of currently active student ids
                        const activeIds = new Set();

                        for (const face of this.activeFaces) {
                            const sid = face.student?.id;
                            if (!sid || !face.box_norm) continue;
                            activeIds.add(sid);

                            const bn = face.box_norm;
                            // Target pixel coords
                            const tx = offsetX + bn.x * renderW;
                            const ty = offsetY + bn.y * renderH;
                            const tw = bn.w * renderW;
                            const th = bn.h * renderH;

                            // Init smoothed position on first sight
                            if (!this.smoothedFaces[sid]) {
                                this.smoothedFaces[sid] = { x: tx, y: ty, w: tw, h: th };
                            }
                            const s = this.smoothedFaces[sid];

                            // Exponential lerp toward target
                            s.x += (tx - s.x) * LERP;
                            s.y += (ty - s.y) * LERP;
                            s.w += (tw - s.w) * LERP;
                            s.h += (th - s.h) * LERP;

                            // Apply directly to DOM elements — bypasses Alpine reactivity entirely
                            const boxEl = document.getElementById('face-box-' + sid);
                            const tagEl = document.getElementById('face-tag-' + sid);

                            if (boxEl) {
                                boxEl.style.display  = '';
                                boxEl.style.transform = `translate3d(${s.x|0}px,${s.y|0}px,0)`;
                                boxEl.style.width    = `${s.w|0}px`;
                                boxEl.style.height   = `${s.h|0}px`;
                            }
                            if (tagEl) {
                                const cx      = (s.x + s.w / 2) | 0;
                                const isAbove = (s.y + s.h + 160) > containerH;
                                tagEl.style.display = '';
                                if (isAbove) {
                                    const topPos = Math.max(12, (s.y - 12)) | 0;
                                    tagEl.style.transform = `translate3d(${cx}px,${topPos}px,0) translate(-50%,-100%)`;
                                } else {
                                    const topPos = (s.y + s.h + 14) | 0;
                                    tagEl.style.transform = `translate3d(${cx}px,${topPos}px,0) translate(-50%,0)`;
                                }
                            }
                        }

                        // Clean up smoothedFaces for students that expired / left frame
                        for (const sid of Object.keys(this.smoothedFaces)) {
                            if (!activeIds.has(parseInt(sid))) {
                                delete this.smoothedFaces[sid];
                                // Also hide their DOM elements immediately
                                const boxEl = document.getElementById('face-box-' + sid);
                                const tagEl = document.getElementById('face-tag-' + sid);
                                if (boxEl) boxEl.style.display = 'none';
                                if (tagEl) tagEl.style.display = 'none';
                            }
                        }

                        requestAnimationFrame(tick);
                    };

                    requestAnimationFrame(tick);
                },

                updateClock() {
                    const now = new Date();
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false });
                    this.currentDate = now.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                },

                async startCamera() {
                    try {
                        if (this.stream) {
                            this.stream.getTracks().forEach(t => t.stop());
                            this.stream = null;
                        }

                        const constraints = {
                            video: {
                                facingMode: { ideal: this.cameraFacing },
                                width: { ideal: 1920, min: 1280 },
                                height: { ideal: 1080, min: 720 }
                            },
                            audio: false
                        };

                        try {
                            this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                        } catch (errFallback) {
                            console.warn('Fallback basic getUserMedia:', errFallback);
                            this.stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                        }

                        this.videoEl.srcObject = this.stream;
                        this.scanState = 'idle';
                        this.statusMessage = 'Sistem Siap • Arahkan kamera ke gerbang masuk';

                        // Check for hardware zoom capability on the selected camera track
                        setTimeout(() => {
                            const track = this.stream?.getVideoTracks()[0];
                            const caps = track?.getCapabilities?.() || {};
                            this.hasHardwareZoom = Boolean(caps.zoom);
                            if (this.hasHardwareZoom && this.zoomLevel > 1.0) {
                                track.applyConstraints({
                                    advanced: [{ zoom: Math.min(this.zoomLevel, caps.zoom.max || 2.0) }]
                                }).catch(() => {});
                            }
                        }, 500);

                    } catch (err) {
                        console.error('Camera access error:', err);
                        this.statusMessage = 'Akses kamera gagal. Pastikan izin kamera aktif.';
                        this.scanState = 'error';
                    }
                },

                async switchCamera() {
                    this.cameraFacing = (this.cameraFacing === 'environment') ? 'user' : 'environment';
                    localStorage.setItem('kiosk_camera_facing', this.cameraFacing);
                    await this.startCamera();
                },

                async cycleZoom() {
                    const levels = [1.0, 1.5, 2.0];
                    const currentIndex = levels.indexOf(this.zoomLevel);
                    this.zoomLevel = levels[(currentIndex + 1) % levels.length];

                    if (this.hasHardwareZoom && this.stream) {
                        try {
                            const track = this.stream.getVideoTracks()[0];
                            const caps = track?.getCapabilities?.() || {};
                            await track.applyConstraints({
                                advanced: [{ zoom: Math.min(this.zoomLevel, caps.zoom.max || 2.0) }]
                            });
                        } catch (e) {
                            console.warn('Apply zoom error:', e);
                        }
                    }
                },

                getVideoTransformStyle() {
                    // If hardware zoom is NOT supported and zoom > 1.0, apply CSS scale transform
                    const baseScaleX = (this.cameraFacing === 'user') ? -1 : 1;
                    if (!this.hasHardwareZoom && this.zoomLevel > 1.0) {
                        return `transform: scale(${baseScaleX * this.zoomLevel}, ${this.zoomLevel});`;
                    }
                    return (this.cameraFacing === 'user') ? 'transform: scaleX(-1);' : 'transform: none;';
                },

                captureFrame() {
                    if (!this.videoEl || this.videoEl.readyState !== 4) return null;

                    const videoW = this.videoEl.videoWidth || 640;
                    const videoH = this.videoEl.videoHeight || 480;

                    // ── Deteksi Orientasi Perangkat ──
                    // HP landscape: videoW > videoH  → normal, tidak perlu rotasi
                    // HP portrait ditilting: MediaStream sudah handle rotasi hardware,
                    // tapi canvas perlu ikut agar YuNet tidak terima gambar yang diputar.
                    // Gunakan screen.orientation untuk deteksi kondisi ini.
                    const orientAngle = (screen.orientation?.angle ?? window.orientation ?? 0);
                    // orientAngle 90 / -90 / 270 = HP dipegang landscape
                    // Tapi video stream dari browser sudah di-rotate oleh browser itu sendiri —
                    // yang jadi masalah adalah: beberapa browser di Android TIDAK auto-rotate
                    // stream saat kamera belakang dipakai. Kita tangani dengan menggambar
                    // canvas pada dimensi yang benar berdasarkan aspect ratio stream.

                    // Batasi dimensi maksimal ke 960px:
                    // - 640px terlalu kecil untuk deteksi wajah jarak 2-3m
                    // - 1280px terlalu berat untuk mobile (>200ms encode)
                    // - 960px: sweet spot; YuNet deteksi wajah kecil + encode <30ms
                    const maxDim = 960;
                    let targetWidth  = videoW;
                    let targetHeight = videoH;

                    if (videoW >= videoH && videoW > maxDim) {
                        targetWidth  = maxDim;
                        targetHeight = Math.round((videoH / videoW) * maxDim);
                    } else if (videoH > videoW && videoH > maxDim) {
                        targetHeight = maxDim;
                        targetWidth  = Math.round((videoW / videoH) * maxDim);
                    }

                    this.canvasEl.width  = targetWidth;
                    this.canvasEl.height = targetHeight;

                    const ctx = this.canvasEl.getContext('2d');
                    ctx.clearRect(0, 0, targetWidth, targetHeight);

                    // Support software digital zoom crop if camera lacks hardware zoom
                    let sx = 0, sy = 0, sw = videoW, sh = videoH;
                    if (!this.hasHardwareZoom && this.zoomLevel > 1.0) {
                        sw = videoW / this.zoomLevel;
                        sh = videoH / this.zoomLevel;
                        sx = (videoW - sw) / 2;
                        sy = (videoH - sh) / 2;
                    }

                    // Mirror horizontally if user mode so coordinates match mirror screen
                    if (this.cameraFacing === 'user') {
                        ctx.translate(targetWidth, 0);
                        ctx.scale(-1, 1);
                    }

                    ctx.drawImage(this.videoEl, sx, sy, sw, sh, 0, 0, targetWidth, targetHeight);

                    return this.canvasEl.toDataURL('image/jpeg', 0.82);
                },

                async autoCaptureAndScan() {
                    const b64 = this.captureFrame();
                    if (!b64) return;

                    this.isProcessing = true;

                    try {
                        const controller = new AbortController();
                        const timeoutId = setTimeout(() => controller.abort(), 2500);

                        const response = await fetch("{{ route('kiosk.identify') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ face_image: b64 }),
                            signal: controller.signal
                        });

                        clearTimeout(timeoutId);
                        const res = await response.json();

                        if (res.success) {
                            if (res.results && Array.isArray(res.results)) {
                                this.handleMultiScanResults(res);
                            } else if (res.student) {
                                this.handleMultiScanResults({ results: [res], unmatched: [] });
                            }
                        }
                    } catch (err) {
                        // Silent catch on network timeout to maintain continuous loop
                    } finally {
                        this.isProcessing = false;
                    }
                },

                handleMultiScanResults(resData) {
                    const now = Date.now();
                    const newStudentsSpoken = [];
                    const results = resData.results || [];

                    for (const res of results) {
                        const studentId = res.student?.id;
                        if (!studentId) continue;

                        const lastSeen = this.cooldowns[studentId] || 0;
                        const isCooldownExpired = (now - lastSeen) > 15000; // 15 detik cooldown

                        if (isCooldownExpired) {
                            this.cooldowns[studentId] = now;
                            if (!res.already_attended) {
                                this.hadirCount++;
                                newStudentsSpoken.push(res.student.name);
                            } else {
                                if (this.voiceEnabled && (!this.lastSpoken[studentId] || (now - this.lastSpoken[studentId] > 20000))) {
                                    this.lastSpoken[studentId] = now;
                                    this.speak(`Halo ${res.student.name}, kamu sudah absen hari ini.`);
                                }
                            }
                        }

                        // Add or update active face object with reactive position tracking
                        const existingIdx = this.activeFaces.findIndex(f => f.student?.id === studentId);
                        if (existingIdx >= 0) {
                            // Update koordinat wajah secara reaktif agar card mengikuti gerakan siswa secara real-time
                            this.activeFaces[existingIdx].box_norm = res.box_norm;
                            this.activeFaces[existingIdx].expiresAt = now + 3500;
                            this.activeFaces[existingIdx].confidence = res.confidence;
                            this.activeFaces[existingIdx].distance = res.distance;
                        } else {
                            this.activeFaces.push({
                                id: 'face_' + studentId,
                                student: res.student,
                                already_attended: res.already_attended,
                                waktu_scan: res.waktu_scan,
                                confidence: res.confidence,
                                distance: res.distance,
                                engine: res.engine,
                                box_norm: res.box_norm,
                                expiresAt: now + 3500
                            });
                        }
                    }

                    // Increment frameTick untuk memicu render ulang CSS transform posisi box & card di layar
                    this.frameTick++;

                    // Play pleasant chime and speak names if newly recorded
                    if (newStudentsSpoken.length > 0) {
                        this.playSuccessChime();
                        this.scanState = 'success';
                        this.statusMessage = `${newStudentsSpoken.length} Siswa Berhasil Presensi`;

                        setTimeout(() => {
                            if (this.scanState === 'success') {
                                this.scanState = 'idle';
                                this.statusMessage = 'Sistem Siap • Siswa silakan lewat gerbang';
                            }
                        }, 3200);

                        if (this.voiceEnabled) {
                            if (newStudentsSpoken.length === 1) {
                                this.speak(`Selamat pagi ${newStudentsSpoken[0]}! Presensi berhasil dicatat.`);
                            } else if (newStudentsSpoken.length === 2) {
                                this.speak(`Selamat pagi ${newStudentsSpoken[0]} dan ${newStudentsSpoken[1]}! Presensi berhasil dicatat.`);
                            } else {
                                this.speak(`Presensi berhasil untuk ${newStudentsSpoken[0]}, ${newStudentsSpoken[1]}, dan siswa lainnya.`);
                            }
                        }
                    } else if (results.some(r => r.already_attended)) {
                        this.scanState = 'warning';
                        this.statusMessage = 'Siswa Sudah Hadir Sebelumnya';
                        setTimeout(() => {
                            if (this.scanState === 'warning') {
                                this.scanState = 'idle';
                                this.statusMessage = 'Sistem Siap • Siswa silakan lewat gerbang';
                            }
                        }, 2500);
                    }
                },

                // NOTE: getFaceScreenRect / getFaceBoxStyle / getHudTagStyle are replaced by
                // the startRenderLoop() rAF lerp engine above. Kept as no-ops for safety.
                getFaceBoxStyle() { return ''; },
                getHudTagStyle()  { return ''; },

                playSuccessChime() {
                    try {
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const now = audioCtx.currentTime;

                        // Chime Tone 1: 587.33 Hz (D5)
                        const osc1 = audioCtx.createOscillator();
                        const gain1 = audioCtx.createGain();
                        osc1.type = 'sine';
                        osc1.frequency.setValueAtTime(587.33, now);
                        gain1.gain.setValueAtTime(0.18, now);
                        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                        osc1.connect(gain1);
                        gain1.connect(audioCtx.destination);
                        osc1.start(now);
                        osc1.stop(now + 0.3);

                        // Chime Tone 2: 880 Hz (A5)
                        const osc2 = audioCtx.createOscillator();
                        const gain2 = audioCtx.createGain();
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(880, now + 0.1);
                        gain2.gain.setValueAtTime(0.22, now + 0.1);
                        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                        osc2.connect(gain2);
                        gain2.connect(audioCtx.destination);
                        osc2.start(now + 0.1);
                        osc2.stop(now + 0.5);
                    } catch (e) {
                        // Silent audio context error
                    }
                },

                speak(text) {
                    if ('speechSynthesis' in window) {
                        window.speechSynthesis.cancel();
                        const utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = 'id-ID';
                        utterance.rate = 1.05;
                        utterance.pitch = 1.0;
                        window.speechSynthesis.speak(utterance);
                    }
                },

                toggleVoice() {
                    this.voiceEnabled = !this.voiceEnabled;
                },

                toggleFullscreen() {
                    if (!document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => console.log(err));
                        this.isFullscreen = true;
                    } else {
                        if (document.exitFullscreen) {
                            document.exitFullscreen();
                            this.isFullscreen = false;
                        }
                    }
                },

                openNisnModal() {
                    this.showNisnModal = true;
                    this.manualNisn = '';
                    this.$nextTick(() => {
                        this.$refs.nisnInput?.focus();
                    });
                },

                async submitManualNisn() {
                    if (!this.manualNisn || this.manualNisn.trim().length < 4) {
                        alert('Silakan masukkan NISN yang valid.');
                        return;
                    }

                    const b64 = this.captureFrame();
                    if (!b64) {
                        alert('Kamera belum siap.');
                        return;
                    }

                    this.showNisnModal = false;
                    this.isProcessing = true;
                    this.scanState = 'scanning';
                    this.statusMessage = `Memverifikasi siswa NISN: ${this.manualNisn}...`;

                    try {
                        const response = await fetch("{{ route('kiosk.identify') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                face_image: b64,
                                nisn: this.manualNisn.trim()
                            })
                        });

                        const res = await response.json();

                        if (res.success) {
                            this.handleMultiScanResults({ results: [res], unmatched: [] });
                        } else {
                            this.scanState = 'error';
                            this.statusMessage = res.message || 'Wajah tidak cocok dengan data NISN.';
                            if (this.voiceEnabled) {
                                this.speak('Maaf, verifikasi wajah dengan data NISN gagal.');
                            }
                        }
                    } catch (err) {
                        this.scanState = 'error';
                        this.statusMessage = 'Terjadi kesalahan sistem.';
                    } finally {
                        this.isProcessing = false;
                    }
                },

                async syncDatabase() {
                    this.isSyncing = true;
                    try {
                        const response = await fetch("{{ route('kiosk.sync') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const res = await response.json();
                        alert(res.message || 'Sinkronisasi biometrik berhasil!');
                    } catch (err) {
                        alert('Gagal sinkronisasi data wajah.');
                    } finally {
                        this.isSyncing = false;
                    }
                },

                async fetchStats() {
                    try {
                        const res = await fetch("{{ route('kiosk.stats') }}");
                        const data = await res.json();
                        if (data.success && data.hadir_count !== undefined) {
                            this.hadirCount = data.hadir_count;
                        }
                    } catch (e) {
                        // Silent polling error
                    }
                }
            };
        }
    </script>
</body>
</html>
