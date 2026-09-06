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
    <title>Portal Presensi Siswa - SMKN 1 Beringin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1/dist/face-api.min.js"></script>
    <style>
        body, button, input, select { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }

        /* ── Academic Navy Student Card ── */
        .student-card-bg {
            background: linear-gradient(145deg, #0d1527 0%, #172554 60%, #1e1b4b 100%);
            border: 1px solid rgba(255, 255, 255, 0.12);
            position: relative;
            overflow: hidden;
        }
        .student-card-bg::before {
            content: '';
            position: absolute;
            top: 0; right: 0; bottom: 0; left: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px);
            background-size: 16px 16px;
            pointer-events: none;
        }

        /* ── Scan Face Button ── */
        .scan-btn {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            box-shadow: 0 4px 14px rgba(29, 78, 216, 0.35);
            transition: all 0.2s;
        }
        .scan-btn:hover  { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(29, 78, 216, 0.45); }
        .scan-btn:active { transform: scale(0.98); }
        .scan-btn.success-state { background: linear-gradient(135deg, #15803d, #166534); }

        /* ── Micro Badges ── */
        .badge-hadir { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-izin  { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
        .badge-sakit { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-alpa  { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }
        .badge { display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.72rem; font-weight: 600; }

        /* ── Sesi Active Card ── */
        .sesi-card {
            background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
            border: 1px solid rgba(59, 130, 246, 0.25);
            position: relative;
            overflow: hidden;
        }
        .pulse-dot {
            width: 8px; height: 8px; border-radius: 50%; background: #22c55e;
            animation: sesi-pulse 1.8s ease infinite;
        }
        @keyframes sesi-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            50%      { box-shadow: 0 0 0 7px rgba(34, 197, 94, 0); }
        }

        /* ── Face Modal Styles ── */
        .face-oval {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -55%);
            width: 200px; height: 250px;
            border-radius: 50% / 45%;
            border: 2px solid rgba(255,255,255,0.6);
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.55);
            pointer-events: none;
            z-index: 5;
            transition: border-color .3s, box-shadow .3s;
        }
        .face-oval.detected {
            border-color: #22c55e;
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.55), 0 0 20px rgba(34, 197, 94, 0.6);
        }
        .face-oval.success {
            border-color: #22c55e;
            box-shadow: 0 0 0 9999px rgba(0,60,20,0.7), 0 0 30px rgba(34, 197, 94, 0.9);
        }

        #video-scan { width:100%; height:100%; object-fit:cover; transform:scaleX(-1); }

        @keyframes checkmark-pop {
            0%   { transform: scale(0) rotate(-20deg); opacity: 0; }
            60%  { transform: scale(1.15) rotate(4deg);  opacity: 1; }
            100% { transform: scale(1) rotate(0deg);  opacity: 1; }
        }
        .checkmark-pop { animation: checkmark-pop .45s cubic-bezier(.17,.67,.4,1.2) forwards; }

        @keyframes shake {
            0%,100% { transform: translateX(0); }
            20%,60% { transform: translateX(-5px); }
            40%,80% { transform: translateX(5px); }
        }
        .shake { animation: shake .4s ease; }
    </style>
</head>
<body class="bg-slate-100/80 min-h-screen text-slate-800 antialiased">

    <x-page-loader />

    {{-- KARTU IDENTITAS DIGITAL SISWA (HERO) --}}
    <div class="student-card-bg text-white px-5 pt-8 pb-24 relative z-10 shadow-md">
        <div class="relative z-10 max-w-lg mx-auto">
            {{-- Institutional Top Bar --}}
            <div class="flex items-center justify-between mb-5 border-b border-white/10 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-white rounded-lg p-1 flex items-center justify-center shadow-xs">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo SMKN 1 Beringin" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="font-heading font-bold text-xs uppercase tracking-wider block text-white">SMKN 1 BERINGIN</span>
                        <span class="text-[10px] text-blue-200/80 block font-mono">PORTAL PRESENSI SISWA</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[11px] font-semibold text-slate-300 hover:text-white px-2.5 py-1.5 rounded-md hover:bg-white/10 transition border border-white/10 flex items-center gap-1.5">
                        <i class="fas fa-arrow-right-from-bracket text-[10px]"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>

            {{-- Student Identity Block --}}
            <div class="flex items-start gap-4">
                @php
                    $words = explode(' ', trim($user->name));
                    $initials = count($words) >= 2 
                        ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1) 
                        : mb_substr($words[0], 0, 2);
                @endphp
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 border-2 border-white/20 flex items-center justify-center text-white font-heading font-extrabold text-lg shrink-0 shadow-sm relative">
                    {{ strtoupper($initials) }}
                    @if($user->isFaceEnrolled())
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full border-2 border-slate-900 flex items-center justify-center text-[9px] text-white" title="Wajah Terdaftar">
                            <i class="fas fa-check"></i>
                        </span>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-400/30 text-[10px] font-semibold font-mono uppercase">
                            {{ $user->kelas ? $user->kelas->nama_kelas : 'Tanpa Kelas' }}
                        </span>
                        <span class="text-slate-400 text-xs">•</span>
                        <span class="text-slate-300 text-[11px] font-mono">NISN: {{ $user->nisn ?? '-' }}</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-heading font-extrabold text-white tracking-tight truncate">
                        {{ $user->name }}
                    </h1>
                    <p class="text-slate-300 text-xs mt-1 flex items-center gap-2 font-medium">
                        <span class="inline-flex items-center gap-1">
                            <i class="fas fa-school text-[10px] text-blue-300"></i> SMKN 1 Beringin
                        </span>
                        <span class="text-slate-500">|</span>
                        <span class="text-blue-200/90 text-[11px] font-mono">T.A. 2026/2027</span>
                    </p>
                </div>
            </div>

            @if(!$user->isFaceEnrolled())
                <div class="mt-4 bg-amber-500/20 border border-amber-400/30 text-amber-100 rounded-xl px-4 py-3 text-xs flex items-start gap-3 backdrop-blur-xs">
                    <i class="fas fa-triangle-exclamation text-amber-400 text-sm mt-0.5 shrink-0"></i>
                    <div class="flex-1">
                        <p class="font-bold text-amber-200">Biometrik Wajah Belum Terdaftar</p>
                        <p class="text-[11px] text-amber-100/80 mt-0.5 leading-relaxed">Wajah kamu belum tersimpan di database Face Recognition. Daftarkan sekarang untuk bisa absen mandiri.</p>
                        <a href="{{ route('siswa.enroll') }}"
                           class="inline-flex items-center gap-1.5 mt-2 bg-amber-400 hover:bg-amber-300 text-amber-950 font-bold text-xs px-3 py-1.5 rounded-md transition shadow-2xs">
                            <i class="fas fa-camera text-[11px]"></i> Daftarkan Wajah Sekarang
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- CONTENT --}}
    <div x-data="dashboardApp()" x-init="init()">
        <div class="px-4 -mt-10 max-w-lg mx-auto pb-20 relative z-20">

            {{-- PWA Install Banner --}}
            <div x-show="showInstallPrompt" x-cloak
                 class="mb-4 bg-gradient-to-r from-slate-900 to-blue-950 rounded-xl p-3.5 text-white shadow-md flex items-center justify-between gap-3 border border-blue-500/30"
                 data-aos="fade-down">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-500/20 border border-blue-400/30 rounded-lg flex items-center justify-center shrink-0">
                        <i class="fas fa-mobile-screen-button text-blue-300 text-base"></i>
                    </div>
                    <div>
                        <p class="font-bold text-xs">Aplikasi Layar Utama (PWA)</p>
                        <p class="text-[11px] text-slate-300">Akses presensi instan tanpa mengetik URL</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 shrink-0">
                    <button @click="installApp()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xs transition">
                        Install
                    </button>
                    <button @click="showInstallPrompt = false" class="text-slate-400 hover:text-white text-xs p-1">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-xs font-medium flex items-center gap-2 shadow-2xs">
                    <i class="fas fa-circle-check text-emerald-600"></i> {{ session('success') }}
                </div>
            @endif

            {{-- STAT CARDS (TIGHTENED ROUNDED-XL) --}}
            <div class="grid grid-cols-4 gap-2.5 mb-5" data-aos="fade-up">
                <div class="bg-white rounded-xl p-3 shadow-2xs text-center border border-slate-200">
                    <p class="text-xl font-heading font-extrabold text-emerald-700 font-mono">{{ $stats['hadir'] }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5 font-semibold">Hadir</p>
                </div>
                <div class="bg-white rounded-xl p-3 shadow-2xs text-center border border-slate-200">
                    <p class="text-xl font-heading font-extrabold text-amber-700 font-mono">{{ $stats['izin'] }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5 font-semibold">Izin</p>
                </div>
                <div class="bg-white rounded-xl p-3 shadow-2xs text-center border border-slate-200">
                    <p class="text-xl font-heading font-extrabold text-blue-700 font-mono">{{ $stats['sakit'] }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5 font-semibold">Sakit</p>
                </div>
                <div class="bg-white rounded-xl p-3 shadow-2xs text-center border border-slate-200">
                    <p class="text-xl font-heading font-extrabold text-rose-700 font-mono">{{ $stats['alpa'] }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5 font-semibold">Alpa</p>
                </div>
            </div>

            {{-- SESI AKTIF CARD (POLLING) --}}
            <div class="mb-5" data-aos="fade-up" data-aos-delay="60">
                {{-- Loading State --}}
                <div x-show="sesiLoading" class="bg-white rounded-xl shadow-2xs border border-slate-200 p-8 flex flex-col items-center justify-center gap-3">
                    <i class="fas fa-circle-notch fa-spin text-blue-600 text-2xl"></i>
                    <span class="text-xs font-semibold text-slate-600">Menghubungkan ke server presensi...</span>
                </div>

                {{-- Ada sesi aktif, sudah hadir --}}
                <div x-show="!sesiLoading && sesiData && sudahHadir" x-cloak
                     class="sesi-card rounded-xl shadow-sm p-5 text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="pulse-dot"></div>
                                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest font-mono">Sesi Sedang Berlangsung</span>
                            </div>
                            <p x-text="sesiData?.kelas" class="font-heading font-extrabold text-lg text-white tracking-tight"></p>
                            <p x-text="'Guru: ' + sesiData?.guru" class="text-slate-300 text-xs mt-0.5 font-medium"></p>
                        </div>
                        <div class="bg-emerald-500/20 rounded-lg px-2.5 py-1.5 text-center border border-emerald-400/40">
                            <i class="fas fa-circle-check text-emerald-400 text-lg"></i>
                            <p class="text-emerald-300 text-[10px] font-bold mt-0.5 font-mono">HADIR</p>
                        </div>
                    </div>
                    <div class="mt-4 bg-emerald-500/10 border border-emerald-400/20 rounded-lg px-3.5 py-2.5 text-xs text-emerald-300 flex items-center gap-2">
                        <i class="fas fa-check text-emerald-400"></i>
                        <span>Kehadiran Anda telah terverifikasi biometrik. Selamat belajar!</span>
                    </div>
                </div>

                {{-- Ada sesi aktif, belum hadir --}}
                <div x-show="!sesiLoading && sesiData && !sudahHadir" x-cloak
                     class="sesi-card rounded-xl shadow-sm p-5 text-white">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="pulse-dot"></div>
                                <span class="text-[10px] font-bold text-blue-300 uppercase tracking-widest font-mono">Sesi Dibuka</span>
                            </div>
                            <p x-text="sesiData?.kelas" class="font-heading font-extrabold text-lg text-white tracking-tight"></p>
                            <p x-text="sesiData?.tanggal" class="text-slate-300 text-xs mt-0.5 font-mono"></p>
                            <p x-text="'Pengampu: ' + sesiData?.guru" class="text-slate-400 text-[11px] mt-0.5"></p>
                        </div>
                        <div class="bg-amber-500/20 rounded-lg px-2.5 py-1.5 text-center border border-amber-400/40">
                            <i class="fas fa-clock text-amber-400 text-lg"></i>
                            <p class="text-amber-300 text-[10px] font-bold mt-0.5 font-mono">BELUM</p>
                        </div>
                    </div>

                    {{-- Geofencing GPS Status Indicator --}}
                    <div x-show="geofencingActive" class="my-3 pt-3 border-t border-white/10">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] text-slate-300 font-medium flex items-center gap-1.5">
                                <i class="fas fa-satellite text-blue-400"></i> Radar Geofencing GPS:
                            </span>
                            <button type="button" @click="checkLocation(true)" title="Perbarui titik GPS"
                                    class="text-blue-300 hover:text-white text-[11px] px-2 py-0.5 rounded hover:bg-white/10 transition flex items-center gap-1 font-medium">
                                <i class="fas fa-arrows-rotate text-[10px]" :class="isRequestingGeo ? 'fa-spin' : ''"></i> Refresh
                            </button>
                        </div>

                        {{-- Checking --}}
                        <div x-show="geoStatus === 'checking'" class="bg-blue-950/60 border border-blue-500/30 rounded-lg px-3 py-2 text-xs text-blue-200 flex items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin text-blue-400 text-xs"></i>
                            <span>Menghitung jarak koordinat GPS ke sekolah...</span>
                        </div>

                        {{-- Valid / In Radius --}}
                        <div x-show="geoStatus === 'valid'" class="bg-emerald-950/60 border border-emerald-400/30 rounded-lg px-3 py-2 text-xs text-emerald-200 flex items-center gap-2">
                            <i class="fas fa-circle-check text-emerald-400 text-sm"></i>
                            <span>Dalam Radius Sekolah (Jarak: <strong x-text="geoDistance + ' m'" class="font-mono"></strong>, Maks: <span x-text="schoolRadius + 'm'" class="font-mono"></span>)</span>
                        </div>

                        {{-- Outside Radius Warning --}}
                        <div x-show="geoStatus === 'outside'" class="bg-rose-950/70 border border-rose-400/40 rounded-lg px-3 py-2.5 text-xs text-rose-200 space-y-1">
                            <div class="flex items-center gap-2 font-bold text-rose-300">
                                <i class="fas fa-triangle-exclamation text-rose-400"></i>
                                <span>Di Luar Radius Sekolah (<span x-text="formatDistance(geoDistance)" class="font-mono"></span>)</span>
                            </div>
                            <p class="text-[11px] text-rose-200/90 leading-relaxed">
                                Presensi wajib dilakukan di dalam zona sekolah (radius maks <span x-text="schoolRadius" class="font-mono"></span> m).
                            </p>
                        </div>

                        {{-- GPS Error / Permission Required --}}
                        <div x-show="geoStatus === 'error'" class="bg-amber-950/70 border border-amber-400/40 rounded-lg px-3 py-2 text-xs text-amber-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-location-slash text-amber-400"></i>
                                <span>Akses GPS perangkat belum diizinkan.</span>
                            </div>
                            <button type="button" @click="checkLocation(true)" class="underline font-bold text-white hover:text-amber-200">
                                Izinkan
                            </button>
                        </div>
                    </div>

                    {{-- Scan Button with Geofence check --}}
                    <button @click="openFaceScanner()"
                            :disabled="geofencingActive && geoStatus === 'outside'"
                            :class="geofencingActive && geoStatus === 'outside' ? 'opacity-60 cursor-not-allowed bg-slate-800 shadow-none border border-slate-700' : 'scan-btn'"
                            class="w-full text-white font-bold py-3.5 rounded-lg flex items-center justify-center gap-2.5 text-sm shadow-sm transition">
                        <i class="fas" :class="geofencingActive && geoStatus === 'outside' ? 'fa-lock text-xs' : 'fa-camera text-base'"></i>
                        <span x-text="geofencingActive && geoStatus === 'outside' ? 'Terkunci: Di Luar Sekolah' : 'Mulai Verifikasi Wajah'"></span>
                    </button>
                </div>

                {{-- Tidak ada sesi aktif --}}
                <div x-show="!sesiLoading && !sesiData" x-cloak
                     class="bg-white rounded-xl shadow-2xs border border-slate-200 p-6 text-center">
                    <div class="w-12 h-12 bg-slate-100 rounded-xl text-slate-400 border border-slate-200 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-hourglass-half text-lg"></i>
                    </div>
                    <h2 class="text-sm font-heading font-bold text-slate-800 mb-0.5">Belum Ada Sesi Presensi Aktif</h2>
                    <p class="text-xs text-slate-500">Menunggu guru pengampu membuat sesi presensi kelas hari ini.</p>
                    <p class="text-[10px] text-slate-400 mt-2 font-mono">Auto-sync aktif setiap 5 detik</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-2xs border border-slate-200 overflow-hidden" data-aos="fade-up" data-aos-delay="140">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 bg-slate-50/60">
                    <h3 class="font-heading font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-clock-rotate-left text-blue-700"></i> Riwayat Presensi Anda
                    </h3>
                    <span class="text-[10px] font-mono text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">10 Terakhir</span>
                </div>

                @if($riwayat->isEmpty())
                    <div class="py-10 text-center">
                        <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center mx-auto mb-2 text-slate-400 border border-slate-200">
                            <i class="fas fa-inbox text-base"></i>
                        </div>
                        <p class="text-xs font-semibold text-slate-700">Belum ada riwayat presensi</p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Lakukan verifikasi wajah saat sesi kelas dimulai.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($riwayat as $item)
                            <div class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50/80 transition-colors">
                                <div>
                                    <p class="text-xs font-bold text-slate-900 font-mono">
                                        {{ optional($item->sesiPresensi)->tanggal?->format('d M Y') ?? '-' }}
                                    </p>
                                    <p class="text-[11px] text-slate-500 mt-0.5 flex items-center flex-wrap gap-1">
                                        <i class="fas fa-school text-[10px] text-slate-400"></i>
                                        <span>{{ optional(optional($item->sesiPresensi)->kelas)->nama_kelas ?? '-' }}</span>

                                        @if(optional($item->sesiPresensi)->mataPelajaran)
                                            <span class="bg-blue-50 text-blue-800 border border-blue-200 px-1.5 py-0.2 rounded font-medium">{{ $item->sesiPresensi->mataPelajaran->nama_mapel }}</span>
                                        @endif
                                        @if(optional($item->sesiPresensi)->jam_pelajaran)
                                            <span class="bg-slate-100 text-slate-600 px-1 py-0.2 rounded font-mono text-[10px]">{{ $item->sesiPresensi->jam_pelajaran }}</span>
                                        @endif

                                        @if($item->keterangan)
                                            <span class="text-slate-500 italic">({{ $item->keterangan }})</span>
                                        @endif
                                    </p>
                                </div>
                                <span class="badge badge-{{ $item->status }}">{{ $item->labelStatus() }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Institutional Footer --}}
            <footer class="mt-8 mb-4 text-center text-[11px] text-slate-400 font-mono">
                Sistem Presensi Biometrik &copy; 2026 SMKN 1 BERINGIN
            </footer>

        </div>

        {{-- ──────────────────────────────────────────── --}}
        {{-- FACE SCANNER MODAL (ROUNDED-XL HUD) --}}
        {{-- ──────────────────────────────────────────── --}}
        <div x-show="isScanning" x-cloak
             class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/80 backdrop-blur-xs px-4">
            <div @click.away="closeFaceScanner()" class="bg-white rounded-xl w-full max-w-sm shadow-2xl border border-slate-700 overflow-hidden relative">

                {{-- Header --}}
                <div class="bg-slate-900 px-5 py-3.5 flex justify-between items-center text-white border-b border-slate-800">
                    <div>
                        <h3 class="font-heading font-bold text-sm text-white">Verifikasi Biometrik Wajah</h3>
                        <p class="text-slate-400 text-[11px] mt-0.5">Posisikan wajah tepat di dalam bingkai oval</p>
                    </div>
                    <button @click="closeFaceScanner()" class="text-slate-400 hover:text-white transition text-base w-7 h-7 flex items-center justify-center rounded hover:bg-white/10">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                {{-- Camera Area --}}
                <div class="relative bg-slate-950" style="height: 300px;">
                    <video id="video-scan" autoplay playsinline muted class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>
                    <canvas id="canvas-scan" class="hidden"></canvas>

                    {{-- Oval guide --}}
                    <div class="face-oval" :class="scanState === 'detected' ? 'detected' : (scanState === 'success' ? 'success' : '')"></div>

                    {{-- State overlays --}}
                    <div x-show="scanState === 'idle'" class="absolute bottom-3 left-0 right-0 flex justify-center">
                        <div class="bg-black/70 text-white text-xs px-3.5 py-1.5 rounded-full flex items-center gap-2">
                            <i class="fas fa-circle-notch fa-spin text-xs"></i> Mempersiapkan modul kamera...
                        </div>
                    </div>
                    <div x-show="scanState === 'detecting'" class="absolute bottom-3 left-0 right-0 flex justify-center">
                        <div class="bg-black/70 text-white text-xs px-3.5 py-1.5 rounded-full font-medium">
                            Posisikan wajah & Kedipkan mata
                        </div>
                    </div>
                    <div x-show="scanState === 'detected'" class="absolute bottom-3 left-0 right-0 flex justify-center">
                        <div class="bg-emerald-600 text-white text-xs px-3.5 py-1.5 rounded-full font-semibold">
                            <i class="fas fa-check mr-1"></i> Kedipan Terdeteksi!
                        </div>
                    </div>
                    <div x-show="scanState === 'processing'" class="absolute inset-0 bg-slate-950/85 flex items-center justify-center backdrop-blur-xs z-30">
                        <x-loading-school />
                    </div>
                    <div x-show="scanState === 'success'" class="absolute inset-0 bg-emerald-950/80 flex items-center justify-center">
                        <div class="text-center text-white checkmark-pop">
                            <i class="fas fa-circle-check text-5xl text-emerald-400"></i>
                            <p class="mt-2 font-heading font-extrabold text-lg text-white">TERVERIFIKASI HADIR ✓</p>
                        </div>
                    </div>
                    <div x-show="scanState === 'failed'" class="absolute inset-0 bg-rose-950/80 flex items-center justify-center">
                        <div class="text-center text-white shake">
                            <i class="fas fa-circle-xmark text-5xl text-rose-400"></i>
                            <p class="mt-2 font-heading font-bold text-sm">Wajah Tidak Dikenali</p>
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