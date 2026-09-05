<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d9488">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Presensi">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
    <title>Dashboard Siswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/dist/face-api.min.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        .hero-bg {
            background: linear-gradient(135deg, rgba(6, 78, 59, 0.9) 0%, rgba(15, 23, 42, 0.95) 100%), url('{{ asset('images/bg-sekolah.jpg') }}');
            background-size: cover;
            background-position: center;
            position: relative;
            overflow: hidden;
        }
        .hero-bg::before {
            content:''; position:absolute; top:-60px; right:-60px;
            width:250px; height:250px; background:rgba(20, 184, 166, 0.15); border-radius:50%; filter: blur(40px);
        }
        .hero-bg::after {
            content:''; position:absolute; bottom:-80px; left:-40px;
            width:300px; height:300px; background:rgba(16, 185, 129, 0.15); border-radius:50%; filter: blur(40px);
        }

        /* ── Scan Face Button ── */
        .scan-btn {
            background: linear-gradient(135deg, #0d9488, #0f766e);
            box-shadow: 0 8px 30px rgba(13, 148, 136, 0.35);
            transition: all 0.22s;
        }
        .scan-btn:hover  { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(13, 148, 136, 0.45); }
        .scan-btn:active { transform: scale(0.97); }
        .scan-btn.success-state { background: linear-gradient(135deg,#059669,#047857); }

        /* ── Badges ── */
        .badge-hadir { background:#dcfce7; color:#16a34a; }
        .badge-izin  { background:#fef9c3; color:#ca8a04; }
        .badge-sakit { background:#ffedd5; color:#ea580c; }
        .badge-alpa  { background:#fee2e2; color:#dc2626; }
        .badge { display:inline-block; padding:0.2rem 0.625rem; border-radius:9999px; font-size:0.75rem; font-weight:600; }

        /* ── Sesi Active Card ── */
        .sesi-card {
            background: linear-gradient(135deg, #0f172a, #064e3b);
            border: 1px solid rgba(20, 184, 166, 0.2);
            position: relative;
            overflow: hidden;
        }
        .sesi-card::before {
            content:''; position:absolute; top:0; right:0; width:150px; height:150px; 
            background:radial-gradient(circle, rgba(20,184,166,0.15) 0%, rgba(20,184,166,0) 70%);
            border-radius:50%;
        }
        .pulse-dot {
            width:10px; height:10px; border-radius:50%; background:#34d399;
            animation: sesi-pulse 1.5s ease infinite;
        }
        @keyframes sesi-pulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(52,211,153,0.6); }
            50%      { box-shadow: 0 0 0 8px rgba(52,211,153,0); }
        }

        /* ── Face Modal Styles ── */
        .face-oval {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -55%);
            width: 200px; height: 250px;
            border-radius: 50% / 45%;
            border: 3px solid rgba(255,255,255,0.5);
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.5);
            pointer-events: none;
            z-index: 5;
            transition: border-color .3s, box-shadow .3s;
        }
        .face-oval.detected {
            border-color: #4ade80;
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.5), 0 0 20px rgba(74,222,128,0.5);
        }
        .face-oval.success {
            border-color: #4ade80;
            box-shadow: 0 0 0 9999px rgba(0,100,0,0.6), 0 0 30px rgba(74,222,128,0.8);
        }

        #video-scan { width:100%; height:100%; object-fit:cover; transform:scaleX(-1); }

        @keyframes checkmark-pop {
            0%   { transform: scale(0) rotate(-20deg); opacity: 0; }
            60%  { transform: scale(1.2) rotate(5deg);  opacity: 1; }
            100% { transform: scale(1) rotate(0deg);  opacity: 1; }
        }
        .checkmark-pop { animation: checkmark-pop .5s cubic-bezier(.17,.67,.4,1.2) forwards; }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%,60% { transform: translateX(-6px); }
            40%,80% { transform: translateX(6px); }
        }
        .shake { animation: shake .4s ease; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">

    <x-page-loader />

    {{-- HERO HEADER --}}
    <div class="hero-bg text-white px-6 pt-10 pb-28 relative z-10">
        <div class="relative z-10 max-w-lg mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SMK" class="w-full h-full object-contain p-1">
                    </div>
                    <span class="font-bold text-base">Presensi Sekolah</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-teal-100 hover:text-white px-3 py-2 rounded-lg hover:bg-white/10 transition backdrop-blur-sm border border-transparent hover:border-white/20">
                        <i class="fas fa-right-from-bracket mr-1"></i> Keluar
                    </button>
                </form>
            </div>
            <p class="text-teal-100 text-sm font-medium">Selamat datang 👋</p>
            <h1 class="text-3xl font-extrabold mt-1 tracking-tight">{{ $user->name }}</h1>
            <p class="text-teal-50/80 text-sm mt-2 flex items-center gap-3">
                <span class="flex items-center gap-1.5 bg-white/10 px-2.5 py-1 rounded-md backdrop-blur-sm border border-white/10">
                    <i class="fas fa-door-open text-xs"></i>
                    {{ $user->kelas ? $user->kelas->nama_kelas : 'Belum ada kelas' }}
                </span>
                <span class="flex items-center gap-1.5 bg-white/10 px-2.5 py-1 rounded-md backdrop-blur-sm border border-white/10">
                    <i class="fas fa-id-card text-xs"></i> NISN: {{ $user->nisn ?? '-' }}
                </span>
            </p>
            @if(!$user->isFaceEnrolled())
                <div class="mt-3 bg-amber-400/20 border border-amber-400/40 text-amber-100 rounded-xl px-4 py-3 text-sm flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-amber-400 mt-0.5 shrink-0"></i>
                    <div class="flex-1">
                        <p class="font-semibold text-amber-200">Wajah belum terdaftar!</p>
                        <p class="text-xs opacity-80 mt-0.5">Daftarkan wajah kamu untuk bisa absen mandiri.</p>
                        <a href="{{ route('siswa.enroll') }}"
                           class="inline-block mt-2 bg-amber-400 hover:bg-amber-500 text-amber-900 font-bold text-xs px-3 py-1.5 rounded-lg transition">
                            <i class="fas fa-face-smile mr-1"></i> Daftarkan Wajah Sekarang
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- CONTENT --}}
    <div x-data="dashboardApp()" x-init="init()">
        <div class="px-4 -mt-10 max-w-lg mx-auto pb-24 relative z-20">

            {{-- PWA Install Banner --}}
            <div x-show="showInstallPrompt" x-cloak
                 class="mb-4 bg-gradient-to-r from-teal-700 to-emerald-700 rounded-2xl p-3.5 text-white shadow-lg shadow-teal-900/20 flex items-center justify-between gap-3 border border-teal-500/30"
                 data-aos="fade-down">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center shrink-0">
                        <i class="fas fa-mobile-screen-button text-teal-200 text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold text-xs">Jadikan Aplikasi di Layar HP</p>
                        <p class="text-[11px] text-teal-100/90">Akses presensi instan tanpa buka browser</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button @click="installApp()" class="bg-white hover:bg-teal-50 text-teal-800 text-xs font-bold px-3 py-1.5 rounded-xl shadow transition">
                        Install
                    </button>
                    <button @click="showInstallPrompt = false" class="text-white/70 hover:text-white text-xs p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

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

            {{-- SESI AKTIF CARD (polling) --}}
            <div class="mb-5" data-aos="fade-up" data-aos-delay="60">
                {{-- Loading State --}}
                <div x-show="sesiLoading" class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 flex flex-col items-center justify-center gap-3">
                    <i class="fas fa-circle-notch fa-spin text-teal-500 text-3xl"></i>
                    <span class="text-sm font-medium text-slate-500">Mengecek sesi presensi...</span>
                </div>

                {{-- Ada sesi aktif, sudah hadir --}}
                <div x-show="!sesiLoading && sesiData && sudahHadir" x-cloak
                     class="sesi-card rounded-3xl shadow-lg p-5 text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="pulse-dot"></div>
                                <span class="text-xs font-bold text-teal-400 uppercase tracking-widest">Sesi Aktif</span>
                            </div>
                            <p x-text="sesiData?.kelas" class="font-extrabold text-xl tracking-tight text-white mt-1"></p>
                            <p x-text="'Guru: ' + sesiData?.guru" class="text-teal-100 text-sm mt-1 font-medium"></p>
                        </div>
                        <div class="bg-green-500/20 rounded-2xl px-3 py-2 text-center border border-green-400/30">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                            <p class="text-green-300 text-xs font-bold mt-0.5">HADIR</p>
                        </div>
                    </div>
                    <div class="mt-4 bg-green-500/10 border border-green-400/20 rounded-xl px-4 py-2.5 text-sm text-green-300 flex items-center gap-2">
                        <i class="fas fa-face-smile"></i>
                        Absensi hari ini sudah tercatat. Selamat belajar! 🎉
                    </div>
                </div>

                {{-- Ada sesi aktif, belum hadir --}}
                <div x-show="!sesiLoading && sesiData && !sudahHadir" x-cloak
                     class="sesi-card rounded-3xl shadow-lg p-5 text-white">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="pulse-dot"></div>
                                <span class="text-xs font-bold text-teal-400 uppercase tracking-widest">Sesi Aktif</span>
                            </div>
                            <p x-text="sesiData?.kelas" class="font-extrabold text-xl tracking-tight text-white mt-1"></p>
                            <p x-text="sesiData?.tanggal" class="text-teal-100 text-sm mt-1 font-medium"></p>
                            <p x-text="'Dibuat oleh: ' + sesiData?.guru" class="text-teal-200/70 text-xs mt-1"></p>
                        </div>
                        <div class="bg-orange-500/20 rounded-2xl px-3 py-2 text-center border border-orange-400/30">
                            <i class="fas fa-clock text-orange-400 text-xl"></i>
                            <p class="text-orange-300 text-xs font-bold mt-0.5">BELUM</p>
                        </div>
                    </div>

                    {{-- Geofencing GPS Status Indicator --}}
                    <div x-show="geofencingActive" class="my-3 pt-3 border-t border-white/10">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-teal-200/80 font-medium flex items-center gap-1.5">
                                <i class="fas fa-satellite-dish text-[11px] text-teal-400"></i> Radar Lokasi GPS:
                            </span>
                            <button type="button" @click="checkLocation(true)" title="Perbarui titik GPS"
                                    class="text-teal-300 hover:text-white text-xs px-2 py-0.5 rounded-lg hover:bg-white/10 transition flex items-center gap-1 font-medium">
                                <i class="fas fa-arrows-rotate text-[10px]" :class="isRequestingGeo ? 'fa-spin' : ''"></i> Refresh GPS
                            </button>
                        </div>

                        {{-- Checking --}}
                        <div x-show="geoStatus === 'checking'" class="bg-teal-950/40 border border-teal-500/20 rounded-xl px-3 py-2 text-xs text-teal-200 flex items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin text-teal-400"></i>
                            <span>Menentukan jarak GPS ke sekolah...</span>
                        </div>

                        {{-- Valid / In Radius --}}
                        <div x-show="geoStatus === 'valid'" class="bg-emerald-950/50 border border-emerald-400/30 rounded-xl px-3 py-2 text-xs text-emerald-200 flex items-center gap-2">
                            <i class="fas fa-circle-check text-emerald-400 text-sm"></i>
                            <span>Di Area Sekolah (Jarak: <strong x-text="geoDistance + ' m'"></strong>, Maks: <span x-text="schoolRadius + 'm'"></span>)</span>
                        </div>

                        {{-- Outside Radius Warning --}}
                        <div x-show="geoStatus === 'outside'" class="bg-rose-950/60 border border-rose-400/40 rounded-xl px-3 py-2.5 text-xs text-rose-200 space-y-1">
                            <div class="flex items-center gap-2 font-bold text-rose-300">
                                <i class="fas fa-triangle-exclamation text-rose-400"></i>
                                <span>Di Luar Radius Sekolah (<span x-text="formatDistance(geoDistance)"></span>)</span>
                            </div>
                            <p class="text-[11px] text-rose-200/80 leading-relaxed">
                                Presensi hanya dapat dilakukan di dalam lingkungan sekolah (maksimal <span x-text="schoolRadius"></span> meter).
                            </p>
                        </div>

                        {{-- GPS Error / Permission Required --}}
                        <div x-show="geoStatus === 'error'" class="bg-amber-950/60 border border-amber-400/40 rounded-xl px-3 py-2 text-xs text-amber-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-location-slash text-amber-400"></i>
                                <span>Akses GPS belum diizinkan.</span>
                            </div>
                            <button type="button" @click="checkLocation(true)" class="underline font-bold text-white hover:text-amber-200">
                                Aktifkan
                            </button>
                        </div>
                    </div>

                    {{-- Scan Button with Geofence check --}}
                    <button @click="openFaceScanner()"
                            :disabled="geofencingActive && geoStatus === 'outside'"
                            :class="geofencingActive && geoStatus === 'outside' ? 'opacity-60 cursor-not-allowed bg-slate-700/80 shadow-none' : 'scan-btn'"
                            class="w-full text-white font-bold py-4 rounded-2xl flex items-center justify-center gap-3 text-base shadow-lg transition">
                        <i class="fas" :class="geofencingActive && geoStatus === 'outside' ? 'fa-lock' : 'fa-face-viewfinder text-xl'"></i>
                        <span x-text="geofencingActive && geoStatus === 'outside' ? 'Terkunci: Di Luar Sekolah' : 'Absen Wajah Sekarang'"></span>
                    </button>
                </div>

                {{-- Tidak ada sesi aktif --}}
                <div x-show="!sesiLoading && !sesiData" x-cloak
                     class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-hourglass-half text-slate-400 text-2xl"></i>
                    </div>
                    <h2 class="text-base font-bold text-slate-700 mb-1">Belum Ada Sesi Presensi</h2>
                    <p class="text-sm text-slate-500">Menunggu guru membuat sesi kelas pagi ini.</p>
                    <p class="text-xs text-slate-400 mt-1">Halaman ini otomatis update setiap 5 detik.</p>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up" data-aos-delay="140">
                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center">
                        <i class="fas fa-history text-teal-600 mr-2.5"></i>Riwayat Presensi
                    </h3>
                    <span class="text-xs font-medium text-slate-400 bg-white px-2 py-1 rounded-md border border-slate-200">10 terakhir</span>
                </div>

                @if($riwayat->isEmpty())
                    <div class="py-12 text-center">
                        <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-inbox text-slate-400 text-2xl"></i>
                        </div>
                        <p class="text-sm text-slate-500">Belum ada riwayat presensi</p>
                        <p class="text-xs text-slate-400 mt-1">Lakukan absen wajah saat ada sesi aktif</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($riwayat as $item)
                            <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition duration-200 cursor-pointer">
                                <div>
                                    <p class="text-sm font-medium text-slate-800">
                                        {{ optional($item->sesiPresensi)->tanggal?->format('d M Y') ?? '-' }}
                                    </p>
                                    <p class="text-xs text-slate-400 mt-0.5 flex items-center flex-wrap gap-1">
                                        <i class="fas fa-door-open mr-1"></i>
                                        {{ optional(optional($item->sesiPresensi)->kelas)->nama_kelas ?? '-' }}

                                        @if(optional($item->sesiPresensi)->mataPelajaran)
                                            <span class="bg-teal-50 text-teal-700 border border-teal-100 px-1.5 py-0.5 rounded ml-1">{{ $item->sesiPresensi->mataPelajaran->nama_mapel }}</span>
                                        @endif
                                        @if(optional($item->sesiPresensi)->jam_pelajaran)
                                            <span class="bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded"><i class="fas fa-clock mr-1"></i>{{ $item->sesiPresensi->jam_pelajaran }}</span>
                                        @endif

                                        @if($item->keterangan)
                                            <span class="ml-1 text-slate-500 italic">({{ $item->keterangan }})</span>
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

        {{-- ──────────────────────────────────────────── --}}
        {{-- FACE SCANNER MODAL --}}
        {{-- ──────────────────────────────────────────── --}}
        <div x-show="isScanning" x-cloak
             class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/85 backdrop-blur-sm px-4">
            <div @click.away="closeFaceScanner()" class="bg-white rounded-3xl w-full max-w-sm shadow-2xl overflow-hidden relative">

                {{-- Header --}}
                <div class="bg-gradient-to-r from-teal-700 to-teal-500 px-5 py-4 flex justify-between items-center text-white">
                    <div>
                        <h3 class="font-bold text-base">Verifikasi Wajah</h3>
                        <p class="text-teal-100 text-xs mt-0.5">Posisikan wajah dalam bingkai oval</p>
                    </div>
                    <button @click="closeFaceScanner()" class="text-white/70 hover:text-white transition text-xl w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Camera Area --}}
                <div class="relative bg-slate-900" style="height: 300px;">
                    <video id="video-scan" autoplay playsinline muted class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                    <canvas id="canvas-scan" class="hidden"></canvas>

                    {{-- Oval guide --}}
                    <div class="face-oval" :class="scanState === 'detected' ? 'detected' : (scanState === 'success' ? 'success' : '')"></div>

                    {{-- State overlays --}}
                    <div x-show="scanState === 'idle'" class="absolute bottom-3 left-0 right-0 flex justify-center">
                        <div class="bg-black/60 text-white text-xs px-4 py-1.5 rounded-full flex items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin"></i> Mengarahkan kamera...
                        </div>
                    </div>
                    <div x-show="scanState === 'detecting'" class="absolute bottom-3 left-0 right-0 flex justify-center">
                        <div class="bg-black/60 text-white text-xs px-4 py-1.5 rounded-full">
                            Posisikan wajah & Kedipkan mata
                        </div>
                    </div>
                    <div x-show="scanState === 'detected'" class="absolute bottom-3 left-0 right-0 flex justify-center">
                        <div class="bg-green-500/80 text-white text-xs px-4 py-1.5 rounded-full font-semibold">
                            <i class="fas fa-check mr-1"></i> Kedipan Terdeteksi!
                        </div>
                    </div>
                    <div x-show="scanState === 'processing'" class="absolute inset-0 bg-slate-950/85 flex items-center justify-center backdrop-blur-md z-30">
                        <x-loading-school />
                    </div>
                    <div x-show="scanState === 'success'" class="absolute inset-0 bg-green-900/60 flex items-center justify-center">
                        <div class="text-center text-white checkmark-pop">
                            <i class="fas fa-circle-check text-5xl text-green-400"></i>
                            <p class="mt-2 font-extrabold text-lg">HADIR ✓</p>
                        </div>
                    </div>
                    <div x-show="scanState === 'failed'" class="absolute inset-0 bg-red-900/60 flex items-center justify-center">
                        <div class="text-center text-white shake">
                            <i class="fas fa-face-frown text-5xl text-red-400"></i>
                            <p class="mt-2 font-bold">Wajah tidak dikenali</p>
                        </div>
                    </div>
                </div>

                {{-- Bottom info / message --}}
                <div class="p-4">
                    <div x-show="scanMessage" class="text-sm font-semibold p-3 rounded-xl mb-3 text-center"
                         :class="scanSuccess ? 'bg-emerald-50 text-emerald-700' : (scanState === 'idle' ? 'bg-blue-50 text-blue-700' : 'bg-red-50 text-red-700')">
                        <span x-text="scanMessage"></span>
                    </div>

                    <button @click="retryScan()"
                            x-show="scanState === 'failed'"
                            class="w-full bg-slate-700 hover:bg-slate-800 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fas fa-rotate-right"></i> Coba Lagi
                    </button>

                    <p x-show="scanState === 'idle' || scanState === 'detecting'" class="text-xs text-slate-400 text-center mt-2">
                        <i class="fas fa-info-circle mr-1"></i> Pastikan pencahayaan cukup & wajah terlihat jelas
                    </p>
                </div>
            </div>
        </div>

    </div>{{-- end x-data --}}

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 400, offset: 20 });

    // ── Web Audio API Synthesizer (No external MP3 files needed) ──
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
        // Quick high pop when blink is detected
        playBlink() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(750, now);
                osc.frequency.exponentialRampToValueAtTime(1250, now + 0.07);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.07);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.07);
            } catch (e) { console.warn(e); }
        },
        // Pleasant modern dual-tone chime on successful attendance
        playSuccess() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                
                // Tone 1: E5 (659.25Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, now);
                gain1.gain.setValueAtTime(0.3, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.3);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.3);

                // Tone 2: A5 (880Hz) with gentle delay
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880, now + 0.1);
                gain2.gain.setValueAtTime(0.35, now + 0.1);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.1);
                osc2.stop(now + 0.6);
            } catch (e) { console.warn(e); }
        },
        // Low tone cue on error / warning
        playError() {
            try {
                const ctx = this.getCtx();
                if (!ctx) return;
                const now = ctx.currentTime;
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(220, now);
                osc.frequency.setValueAtTime(160, now + 0.1);
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.3);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(now);
                osc.stop(now + 0.3);
            } catch (e) { console.warn(e); }
        }
    };

    function dashboardApp() {
        return {
            // ── Sesi polling state ──
            sesiLoading: true,
            sesiData: null,
            sudahHadir: false,
            currentSesiId: null,
            pollInterval: null,

            // ── Geofencing state ──
            schoolLat: {{ $schoolSetting->latitude }},
            schoolLng: {{ $schoolSetting->longitude }},
            schoolRadius: {{ $schoolSetting->radius_meters }},
            geofencingActive: {{ $schoolSetting->is_geofencing_active ? 'true' : 'false' }},
            userLat: null,
            userLng: null,
            geoDistance: null,
            geoStatus: 'checking', // 'checking' | 'valid' | 'outside' | 'error'
            isRequestingGeo: false,

            // ── PWA install prompt ──
            showInstallPrompt: false,
            deferredPrompt: null,

            // ── Face scanner state ──
            isScanning: false,
            scanState: 'idle',   // idle | detecting | detected | processing | success | failed
            scanMessage: '',
            scanSuccess: false,
            videoStream: null,
            detectionInterval: null,
            blinkState: 'open',

            init() {
                this.pollSesiAktif();
                this.pollInterval = setInterval(() => this.pollSesiAktif(), 5000);
                if (this.geofencingActive) {
                    this.checkLocation();
                } else {
                    this.geoStatus = 'disabled';
                }

                // PWA Service Worker Registration
                if ('serviceWorker' in navigator) {
                    window.addEventListener('load', () => {
                        navigator.serviceWorker.register('/sw.js').catch(console.warn);
                    });
                }

                // PWA Install Prompt Listener
                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    this.deferredPrompt = e;
                    this.showInstallPrompt = true;
                });
            },

            // ── Install PWA Mobile App ──
            installApp() {
                if (!this.deferredPrompt) return;
                this.deferredPrompt.prompt();
                this.deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        this.showInstallPrompt = false;
                    }
                    this.deferredPrompt = null;
                });
            },

            // ── Hitung Jarak GPS Siswa ke Sekolah (Haversine) ──
            checkLocation(force = false) {
                if (!this.geofencingActive && !force) {
                    this.geoStatus = 'disabled';
                    return;
                }

                if (!navigator.geolocation) {
                    this.geoStatus = 'error';
                    return;
                }

                this.isRequestingGeo = true;
                if (force) this.geoStatus = 'checking';

                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        this.isRequestingGeo = false;
                        this.userLat = pos.coords.latitude;
                        this.userLng = pos.coords.longitude;

                        const R = 6371000; // meter
                        const dLat = (this.schoolLat - this.userLat) * Math.PI / 180;
                        const dLon = (this.schoolLng - this.userLng) * Math.PI / 180;
                        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                                  Math.cos(this.userLat * Math.PI / 180) * Math.cos(this.schoolLat * Math.PI / 180) *
                                  Math.sin(dLon / 2) * Math.sin(dLon / 2);
                        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                        this.geoDistance = Math.round(R * c);

                        if (!this.geofencingActive || this.geoDistance <= this.schoolRadius) {
                            this.geoStatus = 'valid';
                        } else {
                            this.geoStatus = 'outside';
                        }
                    },
                    (err) => {
                        this.isRequestingGeo = false;
                        this.geoStatus = 'error';
                        console.warn('Geolocation error:', err);
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 10000 }
                );
            },

            formatDistance(m) {
                if (!m && m !== 0) return '-';
                if (m >= 1000) return (m / 1000).toFixed(1) + ' km';
                return m + ' meter';
            },

            // ── Polling sesi aktif dari server ──
            pollSesiAktif() {
                fetch('{{ route('siswa.sesiaktif') }}')
                    .then(r => r.json())
                    .then(data => {
                        this.sesiLoading = false;
                        if (data.success && data.sesi) {
                            this.sesiData      = data.sesi;
                            this.currentSesiId = data.sesi.id;
                            this.sudahHadir    = data.sudah_hadir;
                        } else {
                            this.sesiData      = null;
                            this.currentSesiId = null;
                            this.sudahHadir    = false;
                        }

                        // Sinkronkan geofencing jika ada pembaruan dari admin
                        if (data.geofencing) {
                            this.geofencingActive = data.geofencing.active;
                            this.schoolLat = data.geofencing.latitude;
                            this.schoolLng = data.geofencing.longitude;
                            this.schoolRadius = data.geofencing.radius_meters;
                        }
                    })
                    .catch(() => { this.sesiLoading = false; });
            },

            // ── Open face scanner modal ──
            async openFaceScanner() {
                // Validasi geofencing sebelum menyalakan kamera
                if (this.geofencingActive && this.geoStatus === 'outside') {
                    audioFx.playError();
                    alert(`Presensi ditolak: Anda berada di luar radius sekolah (${this.formatDistance(this.geoDistance)}). Batas maksimal: ${this.schoolRadius} meter.`);
                    return;
                }

                if (this.geofencingActive && !this.userLat) {
                    this.checkLocation(true);
                }

                this.isScanning   = true;
                this.scanState    = 'idle';
                this.scanMessage  = 'Memuat Model AI...';
                this.scanSuccess  = false;
                this.blinkState   = 'open';

                try {
                    this.videoStream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                        audio: false
                    });
                    const video = document.getElementById('video-scan');
                    video.srcObject = this.videoStream;
                    
                    const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/model/';
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                        faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL)
                    ]);
                    
                    this.scanMessage = '';
                    
                    video.onloadedmetadata = () => {
                        video.width = video.videoWidth;
                        video.height = video.videoHeight;
                    };
                    
                    await video.play();
                    this.scanState = 'detecting';
                    this.startDetectionLoop(video);

                } catch (err) {
                    audioFx.playError();
                    this.scanMessage = 'Gagal mengakses kamera/AI: ' + err.message;
                    this.scanState = 'failed';
                }
            },

            startDetectionLoop(video) {
                this.detectionInterval = setInterval(async () => {
                    if (this.scanState !== 'detecting') return;
                    
                    const detections = await faceapi.detectSingleFace(
                        video, 
                        new faceapi.TinyFaceDetectorOptions({ scoreThreshold: 0.3, inputSize: 224 })
                    ).withFaceLandmarks();
                    
                    if (detections) {
                        const box = detections.detection.box;
                        if (box.width > 80) {
                            const landmarks = detections.landmarks;
                            const leftEye = landmarks.getLeftEye();
                            const rightEye = landmarks.getRightEye();
                            
                            // Calculate EAR
                            const getEAR = (eye) => {
                                const width = Math.hypot(eye[0].x - eye[3].x, eye[0].y - eye[3].y);
                                const h1 = Math.hypot(eye[1].x - eye[5].x, eye[1].y - eye[5].y);
                                const h2 = Math.hypot(eye[2].x - eye[4].x, eye[2].y - eye[4].y);
                                return (h1 + h2) / (2.0 * width);
                            };
                            
                            const avgEAR = (getEAR(leftEye) + getEAR(rightEye)) / 2;
                            
                            if (avgEAR < 0.27) {
                                this.blinkState = 'closed';
                            } else if (avgEAR > 0.28 && this.blinkState === 'closed') {
                                // Blink detected! Bunyikan audio pop
                                audioFx.playBlink();
                                this.blinkState = 'open';
                                this.scanState = 'detected';
                                clearInterval(this.detectionInterval);
                                setTimeout(() => {
                                    this.captureAndSend();
                                }, 500);
                            }
                        }
                    }
                }, 150);
            },

            captureAndSend() {
                const video  = document.getElementById('video-scan');
                const canvas = document.getElementById('canvas-scan');
                canvas.width  = video.videoWidth  || 640;
                canvas.height = video.videoHeight || 480;
                const ctx = canvas.getContext('2d');
                ctx.save();
                ctx.scale(-1, 1);
                ctx.drawImage(video, -canvas.width, 0, canvas.width, canvas.height);
                ctx.restore();

                const imageB64 = canvas.toDataURL('image/jpeg', 0.85);

                this.scanState = 'processing';

                fetch('{{ route('siswa.scanwajah') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        sesi_id:    this.currentSesiId,
                        face_image: imageB64,
                        latitude:   this.userLat,
                        longitude:  this.userLng,
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        audioFx.playSuccess();
                        this.scanState   = 'success';
                        this.scanSuccess = true;
                        this.scanMessage = '🎉 Berhasil! Anda tercatat HADIR.';
                        this.sudahHadir  = true;
                        this.stopCamera();
                        setTimeout(() => {
                            this.isScanning = false;
                            window.location.reload();
                        }, 2000);
                    } else {
                        audioFx.playError();
                        this.scanState   = 'failed';
                        this.scanSuccess = false;
                        this.scanMessage = data.message || 'Wajah tidak dikenali.';
                    }
                })
                .catch(() => {
                    audioFx.playError();
                    this.scanState   = 'failed';
                    this.scanMessage = 'Terjadi kesalahan koneksi. Coba lagi.';
                });
            },

            retryScan() {
                this.scanState   = 'detecting';
                this.scanMessage = '';
                const video = document.getElementById('video-scan');
                this.startDetectionLoop(video);
            },

            closeFaceScanner() {
                this.isScanning = false;
                this.stopCamera();
                if (this.scanSuccess) window.location.reload();
            },

            stopCamera() {
                if (this.detectionInterval) {
                    clearInterval(this.detectionInterval);
                }
                if (this.videoStream) {
                    this.videoStream.getTracks().forEach(t => t.stop());
                    this.videoStream = null;
                }
            },
        };
    }
</script>
</body>
</html>