<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .hero-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
            position: relative; 
            overflow: hidden;
        }
      .hero-bg::before {
            content:''; 
            position:absolute;
            top:-60px;
            right:-60px;
            width:250px; 
            height:250px; 
            background:rgba(255,255,255,0.06); 
            border-radius:50%;
        }
        .hero-bg::after {
            content:''; 
            position:absolute; 
            bottom:-80px; 
            left:-40px;
            width:300px; 
            height:300px; 
            background:rgba(255,255,255,0.04); 
            border-radius:50%;
        }
        .scan-btn {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 8px 30px rgba(37,99,235,0.35);
            transition: all 0.2s;
        }
        .scan-btn:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 12px 40px rgba(37,99,235,0.45); 
        }

        .scan-btn:active { 
            transform: scale(0.97); 
        }

        .badge-hadir  { background:#dcfce7; color:#16a34a; }
        .badge-izin   { background:#fef9c3; color:#ca8a04; }
        .badge-sakit  { background:#ffedd5; color:#ea580c; }
        .badge-alpa   { background:#fee2e2; color:#dc2626; }
        .badge { display:inline-block; padding:0.2rem 0.625rem; border-radius:9999px; font-size:0.75rem; font-weight:600; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">

    {{-- HERO HEADER --}}
    <div class="hero-bg text-white px-6 pt-10 pb-28 relative z-10">
        <div class="relative z-10 max-w-lg mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-white/15 rounded-xl flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-sm"></i>
                    </div>
                    <span class="font-semibold text-sm">Absensi Sekolah</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-blue-200 hover:text-white px-3 py-2 rounded-lg hover:bg-white/10 transition">
                        <i class="fas fa-right-from-bracket mr-1"></i> Keluar
                    </button>
                </form>
            </div>
            <p class="text-blue-200 text-sm">Selamat datang 👋</p>
            <h1 class="text-2xl font-extrabold mt-1">{{ $user->name }}</h1>
            <p class="text-blue-200/80 text-sm mt-1">
                <i class="fas fa-door-open text-xs mr-1"></i>
                {{ $user->kelas ? $user->kelas->nama_kelas : 'Belum ada kelas' }}
                &nbsp;·&nbsp;
                <i class="fas fa-id-card text-xs mr-1"></i>NISN: {{ $user->nisn ?? '-' }}
            </p>
        </div>
    </div>

    {{-- CONTENT --}}
    <div x-data="scannerApp()">
        <div class="px-4 -mt-10 max-w-lg mx-auto pb-24 relative z-20">

            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-4 py-3 text-sm flex items-center gap-2">
                    <i class="fas fa-circle-check text-emerald-500"></i> {{ session('success') }}
                </div>
            @endif

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-4 gap-3 mb-5" data-aos="fade-up">
                <div class="bg-white rounded-2xl p-3 shadow-sm text-center border border-slate-100">
                    <p class="text-xl font-extrabold text-emerald-600">{{ $stats['hadir'] }}</p>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">Hadir</p>
                </div>
                <div class="bg-white rounded-2xl p-3 shadow-sm text-center border border-slate-100">
                    <p class="text-xl font-extrabold text-amber-500">{{ $stats['izin'] }}</p>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">Izin</p>
                </div>
                <div class="bg-white rounded-2xl p-3 shadow-sm text-center border border-slate-100">
                    <p class="text-xl font-extrabold text-orange-500">{{ $stats['sakit'] }}</p>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">Sakit</p>
                </div>
                <div class="bg-white rounded-2xl p-3 shadow-sm text-center border border-slate-100">
                    <p class="text-xl font-extrabold text-red-500">{{ $stats['alpa'] }}</p>
                    <p class="text-xs text-slate-400 mt-0.5 font-medium">Alpa</p>
                </div>
            </div>

            {{-- SCAN BUTTON CARD --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 mb-5 text-center" data-aos="fade-up" data-aos-delay="80">
                <div class="w-20 h-20 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-qrcode text-4xl text-blue-600"></i>
                </div>
                <h2 class="text-lg font-bold text-slate-800 mb-1">Absen Sekarang</h2>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                    Scan QR code yang ditampilkan guru di kelas untuk mencatat kehadiranmu hari ini.
                </p>
                <button @click="openScanner" class="scan-btn inline-flex items-center gap-3 text-white font-bold px-8 py-4 rounded-2xl text-base w-full sm:w-auto justify-center">
                    <i class="fas fa-camera text-xl"></i>
                    Scan QR Code
                </button>
                <p class="text-xs text-slate-400 mt-4">
                    <i class="fas fa-info-circle mr-1"></i>Pastikan kamera aktif saat melakukan scan
                </p>
            </div>

            {{-- RIWAYAT ABSENSI --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up" data-aos-delay="140">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800 text-sm">
                    <i class="fas fa-history text-blue-500 mr-2"></i>Riwayat Absensi
                </h3>
                <span class="text-xs text-slate-400">10 terakhir</span>
            </div>

            @if($riwayat->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-inbox text-slate-400 text-2xl"></i>
                    </div>
                    <p class="text-sm text-slate-500">Belum ada riwayat absensi</p>
                    <p class="text-xs text-slate-400 mt-1">Mulai scan QR Code untuk absen</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($riwayat as $item)
                        <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50 transition">
                            <div>
                                <p class="text-sm font-medium text-slate-800">
                                    {{ optional($item->sesiAbsensi)->tanggal?->format('d M Y') ?? '-' }}
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    <i class="fas fa-door-open mr-1"></i>
                                    {{ optional(optional($item->sesiAbsensi)->kelas)->nama_kelas ?? '-' }}
                                    @if($item->keterangan)
                                        &nbsp;·&nbsp; {{ $item->keterangan }}
                                    @endif
                                </p>
                            </div>
                            <span class="badge badge-{{ $item->status }}">{{ $item->labelStatus() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- SCANNER MODAL (Outside main container to avoid z-index/transform issues) --}}
    <div x-show="isScanning" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm px-4">
        <div @click.away="closeScanner" class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden relative">
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold">Scan QR Code</h3>
                <button @click="closeScanner" class="text-white/70 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="p-6">
                <div id="reader" class="w-full bg-slate-100 rounded-2xl overflow-hidden min-h-[300px]"></div>
                
                <div x-show="scanStatus === 'processing'" class="mt-4 text-sm font-semibold text-blue-600 flex items-center justify-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i> Memproses barcode...
                </div>
                <div x-show="scanMessage" class="mt-4 text-sm font-semibold p-3 rounded-xl" 
                        :class="scanSuccess ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700'">
                    <span x-text="scanMessage"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    AOS.init({ once: true, duration: 400, offset: 20 });

    function scannerApp() {
        return {
            isScanning: false,
            scanStatus: 'idle', // idle, processing, done
            scanMessage: '',
            scanSuccess: false,
            html5QrcodeScanner: null,

            openScanner() {
                this.isScanning = true;
                this.scanStatus = 'idle';
                this.scanMessage = '';
                
                // Initialize scanner after modal opens
                setTimeout(() => {
                    this.html5QrcodeScanner = new Html5QrcodeScanner(
                        "reader",
                        { fps: 10, qrbox: {width: 250, height: 250}, aspectRatio: 1.0 },
                        /* verbose= */ false);
                    
                    this.html5QrcodeScanner.render(this.onScanSuccess.bind(this), this.onScanFailure.bind(this));
                }, 300);
            },

            closeScanner() {
                this.isScanning = false;
                if (this.html5QrcodeScanner) {
                    this.html5QrcodeScanner.clear().catch(error => {
                        console.error("Failed to clear html5QrcodeScanner. ", error);
                    });
                }
                if (this.scanSuccess) {
                    window.location.reload();
                }
            },

            onScanSuccess(decodedText, decodedResult) {
                if(this.scanStatus === 'processing' || this.scanStatus === 'done') return;
                
                this.scanStatus = 'processing';
                
                // Pause scanner temporarily if scanning
                if (this.html5QrcodeScanner) {
                    this.html5QrcodeScanner.pause();
                }

                fetch("{{ route('siswa.scan') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ token: decodedText })
                })
                .then(res => res.json())
                .then(data => {
                    this.scanStatus = 'done';
                    if (data.success) {
                        this.scanSuccess = true;
                        this.scanMessage = "Berhasil absen! " + data.message;
                        
                        // Auto-reload to fetch updated real-time history
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                        
                    } else {
                        this.scanSuccess = false;
                        this.scanMessage = data.message || "Gagal absen. QR tidak valid.";
                        // Resume scanning if failed so they can try again
                        setTimeout(() => {
                            this.scanStatus = 'idle';
                            this.scanMessage = '';
                            if (this.html5QrcodeScanner) this.html5QrcodeScanner.resume();
                        }, 2500);
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.scanStatus = 'done';
                    this.scanSuccess = false;
                    this.scanMessage = "Terjadi kesalahan sistem. Coba lagi.";
                    
                    setTimeout(() => {
                        this.scanStatus = 'idle';
                        this.scanMessage = '';
                        if (this.html5QrcodeScanner) this.html5QrcodeScanner.resume();
                    }, 2500);
                });
            },

            onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning
                if(String(error).includes("NotFoundError") || String(error).includes("NotAllowedError")) {
                    this.scanMessage = "Kamera diblokir/tidak ditemukan. Jika menggunakan HP, pastikan website diakses menggunakan HTTPS / Ngrok.";
                    this.scanSuccess = false;
                }
            }
        }
    }
</script>
</body>
</html>