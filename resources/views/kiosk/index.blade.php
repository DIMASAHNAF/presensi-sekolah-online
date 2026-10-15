<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kiosk Presensi Face ID — {{ $schoolSetting->school_name ?? 'SMKN 1 Beringin' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
    
    <!-- Tailwind CSS & Google Fonts -->
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
            background-color: #090d16;
            color: #f8fafc;
            overflow: hidden;
            user-select: none;
        }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Face Scanner Oval Ring */
        .face-oval {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -52%);
            width: 250px;
            height: 330px;
            border-radius: 50% / 46%;
            border: 3.5px dashed rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 9999px rgba(9, 13, 22, 0.65);
            pointer-events: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 20;
        }
        @media (min-width: 768px) {
            .face-oval { width: 300px; height: 390px; }
        }

        .face-oval.state-idle {
            border: 3px solid rgba(56, 189, 248, 0.6);
            box-shadow: 0 0 0 9999px rgba(9, 13, 22, 0.65), 0 0 25px rgba(56, 189, 248, 0.3);
        }
        .face-oval.state-scanning {
            border: 3.5px solid #38bdf8;
            box-shadow: 0 0 0 9999px rgba(9, 13, 22, 0.65), 0 0 45px rgba(56, 189, 248, 0.65);
        }
        .face-oval.state-success {
            border: 4px solid #10b981;
            box-shadow: 0 0 0 9999px rgba(9, 13, 22, 0.65), 0 0 50px rgba(16, 185, 129, 0.8);
        }
        .face-oval.state-warning {
            border: 4px solid #f59e0b;
            box-shadow: 0 0 0 9999px rgba(9, 13, 22, 0.65), 0 0 40px rgba(245, 158, 11, 0.7);
        }
        .face-oval.state-error {
            border: 4px solid #ef4444;
            box-shadow: 0 0 0 9999px rgba(9, 13, 22, 0.65), 0 0 40px rgba(239, 68, 68, 0.7);
        }

        /* Sci-Fi Laser Scan Line */
        .laser-line {
            position: absolute;
            left: 8%;
            right: 8%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #38bdf8, #60a5fa, #38bdf8, transparent);
            box-shadow: 0 0 15px #38bdf8, 0 0 30px #60a5fa;
            border-radius: 50%;
            animation: scan-move 1.8s ease-in-out infinite alternate;
        }
        @keyframes scan-move {
            0% { top: 12%; opacity: 0.3; }
            50% { opacity: 1; }
            100% { top: 88%; opacity: 0.3; }
        }

        /* Pulsing Target Markers */
        .target-corner {
            position: absolute;
            width: 22px;
            height: 22px;
            border-color: #38bdf8;
            pointer-events: none;
            transition: all .3s;
        }
        .target-tl { top: -2px; left: -2px; border-top: 3.5px solid; border-left: 3.5px solid; border-top-left-radius: 6px; }
        .target-tr { top: -2px; right: -2px; border-top: 3.5px solid; border-right: 3.5px solid; border-top-right-radius: 6px; }
        .target-bl { bottom: -2px; left: -2px; border-bottom: 3.5px solid; border-left: 3.5px solid; border-bottom-left-radius: 6px; }
        .target-br { bottom: -2px; right: -2px; border-bottom: 3.5px solid; border-right: 3.5px solid; border-bottom-right-radius: 6px; }

        #camera-feed {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        /* Glassmorphism Panels */
        .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Success Pop Pop Animation */
        @keyframes modal-pop {
            0% { transform: scale(0.85); opacity: 0; }
            70% { transform: scale(1.03); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-pop { animation: modal-pop 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>
<body class="h-screen w-screen flex flex-col justify-between overflow-hidden" x-data="kioskApp()" x-init="initKiosk()">

    <!-- TOP BAR: HEADER & CLOCK -->
    <header class="glass-panel px-6 py-3.5 z-30 flex items-center justify-between border-b border-white/10 shadow-lg">
        <div class="flex items-center space-x-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-cyan-400 p-0.5 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                <div class="w-full h-full bg-slate-900 rounded-[14px] flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('images/logo.webp') }}" class="w-8 h-8 object-contain" alt="Logo" onerror="this.src='https://ui-avatars.com/api/?name=SMK&background=0D8ABC&color=fff'">
                </div>
            </div>
            <div>
                <h1 class="text-base sm:text-lg font-black tracking-wide text-white uppercase flex items-center gap-2">
                    {{ $schoolSetting->school_name ?? 'SMKN 1 BERINGIN' }}
                    <span class="text-[10px] bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 px-2 py-0.5 rounded-full font-mono font-bold tracking-widest">
                        KIOSK GERBANG
                    </span>
                </h1>
                <p class="text-xs text-slate-400 font-medium">Sistem Presensi Biometrik Wajah Otomatis (Face ID)</p>
            </div>
        </div>

        <!-- Clock & Controls -->
        <div class="flex items-center space-x-5">
            <!-- Digital Clock -->
            <div class="hidden sm:flex flex-col text-right">
                <span class="text-xl sm:text-2xl font-black text-cyan-400 font-mono tracking-wider" x-text="currentTime">--:--:--</span>
                <span class="text-[11px] text-slate-400 font-medium" x-text="currentDate">--</span>
            </div>

            <div class="h-8 w-px bg-white/10 hidden sm:block"></div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                <!-- Sync Candidate Cache Button -->
                <button @click="syncDatabase()" title="Sinkronkan Database Wajah ke RAM" class="w-10 h-10 rounded-xl bg-slate-800/80 hover:bg-slate-700 border border-white/10 text-cyan-400 flex items-center justify-center transition active:scale-95">
                    <i class="fas fa-arrows-rotate text-sm" :class="isSyncing ? 'fa-spin text-amber-400' : ''"></i>
                </button>

                <!-- Voice Audio Mute Toggle -->
                <button @click="toggleVoice()" :title="voiceEnabled ? 'Suara Aktif' : 'Suara Dimatikan'" class="w-10 h-10 rounded-xl bg-slate-800/80 hover:bg-slate-700 border border-white/10 flex items-center justify-center transition active:scale-95" :class="voiceEnabled ? 'text-emerald-400' : 'text-slate-500'">
                    <i class="fas text-sm" :class="voiceEnabled ? 'fa-volume-high' : 'fa-volume-xmark'"></i>
                </button>

                <!-- Fullscreen Toggle -->
                <button @click="toggleFullscreen()" title="Layar Penuh" class="w-10 h-10 rounded-xl bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center transition active:scale-95 shadow-md shadow-blue-500/25">
                    <i class="fas text-sm" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- MAIN BODY: CAMERA VIEWPORT & OVERLAY -->
    <main class="relative flex-1 w-full h-full overflow-hidden flex items-center justify-center bg-black">

        <!-- Live Camera Video -->
        <video id="camera-feed" autoplay playsinline muted></video>
        <canvas id="snapshot-canvas" class="hidden"></canvas>

        <!-- Face Target Scanner Overlay (Face ID Oval) -->
        <div class="face-oval" :class="'state-' + scanState">
            <!-- Target Corners -->
            <div class="target-corner target-tl"></div>
            <div class="target-corner target-tr"></div>
            <div class="target-corner target-bl"></div>
            <div class="target-corner target-br"></div>

            <!-- Animated Laser Scanning Line -->
            <div class="laser-line" x-show="scanState === 'scanning'"></div>
        </div>

        <!-- Center Guide / Status Pill -->
        <div class="absolute bottom-28 md:bottom-24 left-1/2 -translate-x-1/2 z-25 text-center pointer-events-none">
            <div class="inline-flex items-center space-x-2.5 px-6 py-2.5 rounded-full shadow-2xl backdrop-blur-xl border transition-all duration-300"
                 :class="{
                    'bg-slate-900/80 border-cyan-500/40 text-cyan-300': scanState === 'idle',
                    'bg-blue-900/90 border-blue-400 text-blue-200 animate-pulse': scanState === 'scanning',
                    'bg-emerald-950/90 border-emerald-400 text-emerald-200': scanState === 'success',
                    'bg-amber-950/90 border-amber-400 text-amber-200': scanState === 'warning',
                    'bg-red-950/90 border-red-400 text-red-200': scanState === 'error'
                 }">
                <i class="fas text-sm"
                   :class="{
                        'fa-face-viewfinder': scanState === 'idle',
                        'fa-spinner fa-spin text-cyan-400': scanState === 'scanning',
                        'fa-circle-check text-emerald-400': scanState === 'success',
                        'fa-circle-exclamation text-amber-400': scanState === 'warning',
                        'fa-triangle-exclamation text-red-400': scanState === 'error'
                   }"></i>
                <span class="text-sm font-bold tracking-wide" x-text="statusMessage">Posisikan wajah di dalam lingkaran...</span>
            </div>
        </div>

        <!-- POPUP: SUCCESS / VERIFIED STUDENT CARD -->
        <div x-show="showResultModal" x-cloak
             class="absolute inset-0 z-40 bg-slate-950/75 backdrop-blur-md flex items-center justify-center p-4">
            
            <div class="glass-panel w-full max-w-sm rounded-3xl p-6 border shadow-2xl text-center relative overflow-hidden animate-pop"
                 :class="resultData.already_attended ? 'border-amber-500/40' : 'border-emerald-500/50'">

                <!-- Top Glow Banner -->
                <div class="absolute top-0 left-0 right-0 h-2"
                     :class="resultData.already_attended ? 'bg-gradient-to-r from-amber-500 to-yellow-400' : 'bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-400'"></div>

                <!-- Avatar with Badge -->
                <div class="relative w-28 h-28 mx-auto mb-4 mt-2">
                    <img :src="resultData.student?.avatar_url || resultData.student?.scan_url || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(resultData.student?.name || 'S') + '&size=200&background=0284c7&color=fff'"
                         class="w-full h-full rounded-2xl object-cover border-2 shadow-xl"
                         :class="resultData.already_attended ? 'border-amber-400' : 'border-emerald-400'"
                         alt="Foto Siswa">
                    
                    <div class="absolute -bottom-2 -right-2 w-9 h-9 rounded-xl flex items-center justify-center text-white shadow-lg"
                         :class="resultData.already_attended ? 'bg-amber-500' : 'bg-emerald-500'">
                        <i class="fas" :class="resultData.already_attended ? 'fa-clock' : 'fa-check'"></i>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="inline-block px-3.5 py-1 rounded-full text-xs font-black uppercase tracking-wider mb-2"
                     :class="resultData.already_attended ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'">
                    <span x-text="resultData.already_attended ? 'SUDAH HADIR' : 'PRESENSI BERHASIL'"></span>
                </div>

                <!-- Student Details -->
                <h2 class="text-xl sm:text-2xl font-black text-white font-heading leading-tight" x-text="resultData.student?.name">Nama Siswa</h2>
                
                <div class="flex items-center justify-center gap-2 mt-2">
                    <span class="px-2.5 py-0.5 rounded-lg bg-blue-500/20 text-blue-300 border border-blue-500/30 font-bold text-xs" x-text="resultData.student?.kelas">Kelas</span>
                    <span class="text-slate-400 text-xs font-mono">NISN: <strong class="text-slate-200" x-text="resultData.student?.nisn">0000000000</strong></span>
                </div>

                <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-around text-xs">
                    <div>
                        <p class="text-slate-400">Waktu Masuk</p>
                        <p class="text-cyan-400 font-bold text-sm font-mono" x-text="resultData.waktu_scan">--:-- WIB</p>
                    </div>
                    <div class="h-6 w-px bg-white/10"></div>
                    <div>
                        <p class="text-slate-400">Tingkat Akurasi</p>
                        <p class="text-emerald-400 font-bold text-sm" x-text="resultData.confidence ? resultData.confidence.toUpperCase() : 'OPTIMAL'">HIGH</p>
                    </div>
                </div>
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
                        <p class="text-xs text-slate-400">Gunakan jika wajah tidak terdeteksi</p>
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
                        <span>Verifikasi & Ambil Foto</span>
                    </button>
                </form>
            </div>
        </div>

    </main>

    <!-- BOTTOM BAR: STATS & LIVE TICKER -->
    <footer class="glass-panel px-6 py-3.5 z-30 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-3">
        
        <!-- Counter Info -->
        <div class="flex items-center space-x-6 text-xs">
            <div class="flex items-center space-x-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="text-slate-300 font-medium">Hadir Hari Ini:</span>
                <span class="text-emerald-400 font-extrabold text-base font-mono" x-text="hadirCount">{{ $hadirHariIniCount }}</span>
                <span class="text-slate-500">/ {{ $totalSiswa }} Siswa</span>
            </div>

            <div class="h-4 w-px bg-white/10 hidden sm:block"></div>

            <div class="hidden sm:flex items-center space-x-2 text-slate-400">
                <i class="fas fa-database text-cyan-400"></i>
                <span>Biometrik Aktif: <strong class="text-cyan-300">{{ $totalEnrolled }}</strong> siswa di RAM</span>
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
                scanState: 'idle', // idle, scanning, success, warning, error
                statusMessage: 'Posisikan wajah di dalam lingkaran oval...',
                
                showResultModal: false,
                resultData: {},
                modalTimer: null,
                
                showNisnModal: false,
                manualNisn: '',
                hadirCount: {{ $hadirHariIniCount }},

                // Cooldown map: student_id -> last_scan_timestamp
                cooldowns: {},

                initKiosk() {
                    this.videoEl = document.getElementById('camera-feed');
                    this.canvasEl = document.getElementById('snapshot-canvas');

                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);

                    this.startCamera();

                    // Start Continuous Detection Loop (every 1.5 seconds)
                    setInterval(() => {
                        if (!this.isProcessing && !this.showResultModal && !this.showNisnModal) {
                            this.autoCaptureAndScan();
                        }
                    }, 1500);

                    // Polling Live Stats every 15 seconds
                    setInterval(() => this.fetchStats(), 15000);
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
                        const constraints = {
                            video: {
                                width: { ideal: 1280 },
                                height: { ideal: 720 },
                                facingMode: 'user'
                            },
                            audio: false
                        };
                        this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                        this.videoEl.srcObject = this.stream;
                    } catch (err) {
                        console.error('Camera access error:', err);
                        this.statusMessage = 'Akses kamera gagal. Pastikan izin kamera aktif.';
                        this.scanState = 'error';
                    }
                },

                captureFrame() {
                    if (!this.videoEl || this.videoEl.readyState !== 4) return null;
                    const width = 640;
                    const height = Math.round((this.videoEl.videoHeight / this.videoEl.videoWidth) * width);

                    this.canvasEl.width = width;
                    this.canvasEl.height = height;

                    const ctx = this.canvasEl.getContext('2d');
                    // Mirroring horizontal to match video display
                    ctx.translate(width, 0);
                    ctx.scale(-1, 1);
                    ctx.drawImage(this.videoEl, 0, 0, width, height);

                    return this.canvasEl.toDataURL('image/jpeg', 0.85);
                },

                async autoCaptureAndScan() {
                    const b64 = this.captureFrame();
                    if (!b64) return;

                    this.isProcessing = true;
                    this.scanState = 'scanning';
                    this.statusMessage = 'Memindai biometrik wajah...';

                    try {
                        const response = await fetch("{{ route('kiosk.identify') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ face_image: b64 })
                        });

                        const res = await response.json();

                        if (res.success) {
                            const studentId = res.student?.id;
                            const now = Date.now();

                            // Cooldown: Abaikan jika siswa yang sama baru saja ter-scan dalam 15 detik terakhir
                            if (studentId && this.cooldowns[studentId] && (now - this.cooldowns[studentId] < 15000)) {
                                this.resetToIdle();
                                return;
                            }
                            this.cooldowns[studentId] = now;

                            this.handleScanSuccess(res);
                        } else {
                            // Gagal atau wajah tidak cocok
                            this.scanState = 'idle';
                            this.statusMessage = 'Posisikan wajah di dalam lingkaran oval...';
                            this.isProcessing = false;
                        }
                    } catch (err) {
                        console.error('Scan error:', err);
                        this.scanState = 'idle';
                        this.statusMessage = 'Posisikan wajah di dalam lingkaran oval...';
                        this.isProcessing = false;
                    }
                },

                handleScanSuccess(res) {
                    this.resultData = res;
                    this.showResultModal = true;
                    this.scanState = res.already_attended ? 'warning' : 'success';
                    this.statusMessage = res.message;

                    if (!res.already_attended) {
                        this.hadirCount++;
                    }

                    // Suara Sambutan Audio TTS
                    if (this.voiceEnabled) {
                        this.speak(res.already_attended
                            ? `Halo ${res.student.name}, kamu sudah absen hari ini.`
                            : `Selamat pagi ${res.student.name}! Kelas ${res.student.kelas}, presensi berhasil dicatat.`
                        );
                    }

                    // Auto dismiss modal after 2.2 seconds & reset for next student
                    if (this.modalTimer) clearTimeout(this.modalTimer);
                    this.modalTimer = setTimeout(() => {
                        this.showResultModal = false;
                        this.resetToIdle();
                    }, 2400);
                },

                resetToIdle() {
                    this.scanState = 'idle';
                    this.statusMessage = 'Posisikan wajah di dalam lingkaran oval...';
                    this.isProcessing = false;
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
                            this.handleScanSuccess(res);
                        } else {
                            this.scanState = 'error';
                            this.statusMessage = res.message || 'Wajah tidak cocok dengan data NISN.';
                            if (this.voiceEnabled) {
                                this.speak('Maaf, verifikasi wajah dengan data NISN gagal.');
                            }
                            setTimeout(() => this.resetToIdle(), 3000);
                        }
                    } catch (err) {
                        this.scanState = 'error';
                        this.statusMessage = 'Terjadi kesalahan sistem.';
                        setTimeout(() => this.resetToIdle(), 3000);
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
                        alert(res.message || 'Sinkronisasi berhasil!');
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
