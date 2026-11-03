<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Panduan &amp; SOP Presensi — SMKN 1 Beringin</title>
    <meta name="description" content="Buku Panduan Penggunaan Sistem Presensi Biometrik Wajah SMKN 1 Beringin untuk Siswa, Guru, Wali Kelas, dan Staf.">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
    
    <!-- PWA Settings -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Tailwind & Alpine --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, .font-heading { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; }
            .print-break { page-break-after: always; }
            .shadow-sm, .shadow-md, .shadow-lg, .shadow-xl { box-shadow: none !important; }
            .border { border-color: #cbd5e1 !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 antialiased selection:bg-blue-600 selection:text-white"
      x-data="{ 
          activeTab: '{{ request("role", "siswa") }}',
          searchQuery: ''
      }">

    {{-- Top Banner Bar --}}
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 text-white text-xs py-2 px-4 no-print">
        <div class="max-w-6xl mx-auto flex items-center justify-between gap-3">
            <div class="flex items-center gap-2 truncate">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="font-bold">SMKN 1 BERINGIN</span>
                <span class="opacity-60 hidden sm:inline">•</span>
                <span class="opacity-90 hidden sm:inline truncate">SOP &amp; Buku Panduan Operasional Presensi Biometrik Wajah</span>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button type="button" onclick="window.print()" class="hover:text-yellow-300 font-bold flex items-center gap-1 transition">
                    <i class="fas fa-print text-xs"></i>
                    <span class="hidden sm:inline">Cetak / Simpan PDF</span>
                </button>
                <span class="opacity-40">|</span>
                <a href="{{ url('/') }}" class="hover:text-yellow-300 font-bold flex items-center gap-1 transition">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span>Kembali ke Portal</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-sm sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 bg-blue-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-1.5 flex items-center justify-center shrink-0 shadow-xs">
                    <img src="{{ asset('images/logo.webp') }}" alt="Logo SMKN 1" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="font-heading font-black text-xl sm:text-2xl text-slate-900 dark:text-white leading-tight">
                        Pusat Panduan Presensi
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                        Standar Operasional Prosedur (SOP) Guru, Wali Kelas &amp; Siswa
                    </p>
                </div>
            </div>

            {{-- Tab Switcher --}}
            <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl border border-slate-200/80 dark:border-slate-700 no-print">
                <button type="button" @click="activeTab = 'siswa'"
                        class="px-4 py-2 rounded-xl text-xs font-black flex items-center gap-2 transition"
                        :class="activeTab === 'siswa' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                    <i class="fas fa-user-graduate"></i>
                    <span>Panduan Siswa</span>
                </button>
                <button type="button" @click="activeTab = 'guru'"
                        class="px-4 py-2 rounded-xl text-xs font-black flex items-center gap-2 transition"
                        :class="activeTab === 'guru' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                    <i class="fas fa-chalkboard-user"></i>
                    <span>Panduan Guru</span>
                </button>
                <button type="button" @click="activeTab = 'pwa'"
                        class="px-4 py-2 rounded-xl text-xs font-black flex items-center gap-2 transition"
                        :class="activeTab === 'pwa' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                    <i class="fas fa-mobile-screen-button"></i>
                    <span>Pasang Aplikasi</span>
                </button>
            </div>

        </div>
    </header>

    {{-- Main Container --}}
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8 space-y-8">

        {{-- Quick Banner: Pasang Aplikasi Shortcut --}}
        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 rounded-3xl p-6 text-white shadow-xl shadow-blue-500/15 flex flex-col sm:flex-row sm:items-center justify-between gap-5 border border-blue-400/30 no-print">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center shrink-0 border border-white/20">
                    <i class="fas fa-mobile-screen-button text-2xl text-yellow-300"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-heading font-black text-lg">Gunakan Versi Aplikasi di HP Anda</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-400 text-slate-900 uppercase">APK / PWA</span>
                    </div>
                    <p class="text-xs text-blue-100 mt-1 max-w-xl">
                        Tidak perlu lagi repot membuka browser atau mengetik link. Pasang pintasan aplikasi ke layar utama HP Android &amp; iPhone Anda dalam 1 kali sentuh!
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2.5 shrink-0">
                <button type="button" @click="$dispatch('open-pwa-install')"
                        class="py-3 px-5 rounded-2xl bg-white text-blue-700 hover:bg-blue-50 active:scale-95 font-black text-xs shadow-md transition flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    <span>Pasang Aplikasi Sekarang</span>
                </button>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- TAB 1: PANDUAN SISWA                                   --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'siswa'" x-cloak class="space-y-6">

            <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
                <span class="text-xs font-mono font-bold text-blue-600 dark:text-blue-400 uppercase tracking-widest">Buku Petunjuk Operasional Siswa</span>
                <h2 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 dark:text-white mt-1">Panduan Presensi Siswa SMKN 1 Beringin</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Langkah lengkap mulai dari cara login, rekam wajah, verifikasi kelas, hingga rekap kehadiran.</p>
            </div>

            {{-- Step Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Card 1: Cara Login --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-3.5">
                        <span class="w-10 h-10 rounded-2xl bg-blue-100 dark:bg-blue-950/70 text-blue-600 dark:text-blue-400 font-black text-sm flex items-center justify-center shrink-0">1</span>
                        <div>
                            <h3 class="font-heading font-black text-base text-slate-900 dark:text-white">Cara Login ke Akun Siswa</h3>
                            <p class="text-xs text-slate-400">Mudah &amp; Fleksibel</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Anda dapat masuk ke portal presensi menggunakan <strong>salah satu dari 3 pilihan berikut</strong> pada kolom pertama:
                    </p>
                    <div class="space-y-2 text-xs">
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="font-bold text-slate-800 dark:text-white">A. Nama Lengkap:</span>
                            <span class="text-slate-500 dark:text-slate-400 block mt-0.5">Contoh: <code class="text-blue-600 dark:text-blue-400 font-mono">Dimas Ahnaf Fathurrahman</code></span>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="font-bold text-slate-800 dark:text-white">B. Username Akun:</span>
                            <span class="text-slate-500 dark:text-slate-400 block mt-0.5">Contoh: <code class="text-blue-600 dark:text-blue-400 font-mono">dimas</code></span>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="font-bold text-slate-800 dark:text-white">C. 10 Digit NISN:</span>
                            <span class="text-slate-500 dark:text-slate-400 block mt-0.5">Nomor Induk Siswa Nasional dari Dapodik.</span>
                        </div>
                    </div>
                    <div class="p-3.5 bg-blue-50 dark:bg-blue-950/40 rounded-xl border border-blue-200 dark:border-blue-900/60 text-[11px] text-blue-800 dark:text-blue-200">
                        <i class="fas fa-key text-blue-500 mr-1.5"></i>
                        <strong>Password:</strong> Untuk login pertama kali, kolom password <strong>dapat dikosongkan</strong>. Jika sudah membuat password baru di menu profil, gunakan password tersebut.
                    </div>
                </div>

                {{-- Card 2: Perekaman Wajah (Enrollment) --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-3.5">
                        <span class="w-10 h-10 rounded-2xl bg-indigo-100 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 font-black text-sm flex items-center justify-center shrink-0">2</span>
                        <div>
                            <h3 class="font-heading font-black text-base text-slate-900 dark:text-white">Perekaman Wajah Awal (Face ID)</h3>
                            <p class="text-xs text-slate-400">Hanya dilakukan 1 kali di awal</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Jika Anda belum pernah mendaftarkan biometrik, sistem akan otomatis membuka panduan perekaman wajah. Sistem merekam 4 pose sudut wajah:
                    </p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                            <i class="fas fa-user text-blue-500 mb-1"></i>
                            <p class="font-bold">Pose 1: Lurus</p>
                            <span class="text-[10px] text-slate-400">Tatap kamera depan</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                            <i class="fas fa-arrow-right text-indigo-500 mb-1"></i>
                            <p class="font-bold">Pose 2: Toleh Kanan</p>
                            <span class="text-[10px] text-slate-400">Miringkan kepala sedikit</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                            <i class="fas fa-arrow-left text-indigo-500 mb-1"></i>
                            <p class="font-bold">Pose 3: Toleh Kiri</p>
                            <span class="text-[10px] text-slate-400">Miringkan kepala sedikit</span>
                        </div>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800 text-center">
                            <i class="fas fa-face-smile text-emerald-500 mb-1"></i>
                            <p class="font-bold">Pose 4: Senyum</p>
                            <span class="text-[10px] text-slate-400">Senyum wajar / rileks</span>
                        </div>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-200 dark:border-amber-900/60 text-[11px] text-amber-800 dark:text-amber-200">
                        <i class="fas fa-lightbulb text-amber-500 mr-1.5"></i>
                        <strong>Tips Sukses:</strong> Berada di tempat dengan cahaya terang (jangan membelakangi lampu/jendela). Lepas masker, kacamata hitam, dan rapikan poni yang menutupi mata.
                    </div>
                </div>

                {{-- Card 3: Cara Presensi Harian --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-3.5">
                        <span class="w-10 h-10 rounded-2xl bg-emerald-100 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 font-black text-sm flex items-center justify-center shrink-0">3</span>
                        <div>
                            <h3 class="font-heading font-black text-base text-slate-900 dark:text-white">Cara Presensi Masuk di Kelas</h3>
                            <p class="text-xs text-slate-400">Cepat, akurat, di bawah 3 detik</p>
                        </div>
                    </div>
                    <ol class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 list-decimal list-inside leading-relaxed">
                        <li>Pastikan Anda sudah tiba di <strong>lingkungan SMKN 1 Beringin</strong> (di dalam radius sekolah).</li>
                        <li>Nyalakan <strong>GPS / Lokasi HP</strong> dengan mode Akurasi Tinggi.</li>
                        <li>Buka aplikasi Presensi, di tab <strong>Beranda</strong>, ketuk tombol besar <strong>"Verifikasi Wajah Sekarang"</strong>.</li>
                        <li>Izinkan peramban mengakses kamera depan jika diminta.</li>
                        <li>Posisikan wajah Anda tepat di dalam bingkai oval.</li>
                        <li>Sistem AI akan otomatis memvalidasi wajah dan lokasi Anda. Jika cocok, status langsung berubah menjadi <strong class="text-emerald-600">HADIR</strong>.</li>
                    </ol>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border border-emerald-200 dark:border-emerald-900/60 text-[11px] text-emerald-800 dark:text-emerald-200">
                        <i class="fab fa-whatsapp text-emerald-600 mr-1.5"></i>
                        <strong>WhatsApp Notifikasi:</strong> Setiap kali Anda berhasil presensi, sistem secara otomatis mengirimkan pesan konfirmasi ke nomor WhatsApp Orang Tua Anda!
                    </div>
                </div>

                {{-- Card 4: Kiosk Gerbang & Riwayat --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-3.5">
                        <span class="w-10 h-10 rounded-2xl bg-purple-100 dark:bg-purple-950/70 text-purple-600 dark:text-purple-400 font-black text-sm flex items-center justify-center shrink-0">4</span>
                        <div>
                            <h3 class="font-heading font-black text-base text-slate-900 dark:text-white">Presensi Gerbang &amp; Riwayat</h3>
                            <p class="text-xs text-slate-400">Tanpa HP &amp; Pantau Kehadiran</p>
                        </div>
                    </div>
                    <div class="space-y-3 text-xs text-slate-600 dark:text-slate-300">
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <p class="font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                <i class="fas fa-tablet-screen-button text-purple-500"></i>
                                Presensi Tablet Gerbang Sekolah (Kiosk)
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                Jika tidak membawa HP, Anda cukup berdiri di depan Tablet Kiosk di gerbang sekolah. Kamera akan otomatis mengenali wajah Anda tanpa sentuh layar.
                            </p>
                        </div>

                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <p class="font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                <i class="fas fa-chart-pie text-blue-500"></i>
                                Cek Rekap Kehadiran Bulanan
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1">
                                Buka tab <strong>Kehadiran</strong> di aplikasi untuk melihat rincian presensi: total Hadir, Izin, Sakit, Alpa, serta jam persis Anda melakukan presensi setiap hari.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Troubleshooting Siswa --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400 font-heading font-black text-base sm:text-lg">
                    <i class="fas fa-circle-question"></i>
                    <span>Tanya Jawab &amp; Solusi Kendala Siswa (FAQ)</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-1.5">
                        <p class="font-bold text-slate-900 dark:text-white">Q: Muncul pesan "Lokasi di Luar Radius Sekolah"?</p>
                        <p class="text-slate-600 dark:text-slate-300 text-[11px] leading-relaxed">
                            Pastikan GPS aktif dalam mode "Akurasi Tinggi". Buka aplikasi Google Maps sebentar agar sinyal satelit GPS HP Anda terkunci, lalu refresh halaman presensi.
                        </p>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-1.5">
                        <p class="font-bold text-slate-900 dark:text-white">Q: Kamera tidak mau menyala atau izin ditolak?</p>
                        <p class="text-slate-600 dark:text-slate-300 text-[11px] leading-relaxed">
                            Buka pengaturan Google Chrome / Safari > Pengaturan Situs > Kamera > Pastikan izin kamera untuk situs ini diubah menjadi "Izinkan (Allow)".
                        </p>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-1.5">
                        <p class="font-bold text-slate-900 dark:text-white">Q: Wajah tidak cocok atau sulit terverifikasi?</p>
                        <p class="text-slate-600 dark:text-slate-300 text-[11px] leading-relaxed">
                            Pindah ke tempat yang lebih terang dan bersihkan lensa kamera depan. Jika Anda mengubah penampilan drastis (potong rambut/kacamata baru), mintalah Wali Kelas/Admin untuk melakukan <em>Reset Wajah</em> agar bisa rekam ulang.
                        </p>
                    </div>

                    <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-1.5">
                        <p class="font-bold text-slate-900 dark:text-white">Q: Muncul tulisan "419 Page Expired"?</p>
                        <p class="text-slate-600 dark:text-slate-300 text-[11px] leading-relaxed">
                            Sistem secara otomatis memperbarui sesi Anda. Cukup lakukan refresh halaman atau masuk kembali dengan nama/username Anda.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- TAB 2: PANDUAN GURU & WALI KELAS                       --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'guru'" x-cloak class="space-y-6">

            <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
                <span class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Buku Petunjuk Operasional Guru &amp; Wali Kelas</span>
                <h2 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 dark:text-white mt-1">Panduan Pengelolaan Presensi Dewan Guru</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Panduan membuka sesi presensi kelas, monitor siswa real-time, ubah status izin/sakit, dan ekspor leger rekap.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Guru Card 1: Membuka Sesi --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-3.5">
                        <span class="w-10 h-10 rounded-2xl bg-indigo-100 dark:bg-indigo-950/70 text-indigo-600 dark:text-indigo-400 font-black text-sm flex items-center justify-center shrink-0">1</span>
                        <div>
                            <h3 class="font-heading font-black text-base text-slate-900 dark:text-white">Membuka Sesi Presensi Kelas</h3>
                            <p class="text-xs text-slate-400">Setiap jam pelajaran / pagi</p>
                        </div>
                    </div>
                    <ol class="space-y-2 text-xs text-slate-600 dark:text-slate-300 list-decimal list-inside leading-relaxed">
                        <li>Login dengan <strong>NIP (18 digit ASN)</strong>, <strong>NUPTK (16 digit)</strong>, <strong>Username</strong>, atau <strong>Nama Lengkap Guru</strong>.</li>
                        <li>Masuk ke menu <strong>"Kelola Presensi"</strong> di bilah navigasi kiri.</li>
                        <li>Klik tombol biru <strong>"+ Buka Sesi Presensi"</strong>.</li>
                        <li>Pilih <strong>Kelas / Rombel</strong> dan <strong>Mata Pelajaran</strong> yang diajar.</li>
                        <li>Tentukan batas toleransi keterlambatan (misal 15 menit), lalu klik <strong>"Mulai Sesi"</strong>.</li>
                    </ol>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 rounded-xl border border-indigo-200 dark:border-indigo-900/60 text-[11px] text-indigo-800 dark:text-indigo-200">
                        <i class="fas fa-bullhorn text-indigo-600 mr-1.5"></i>
                        Setelah sesi dibuka, siswa di kelas tersebut langsung dapat melakukan verifikasi wajah di HP masing-masing atau di Kiosk.
                    </div>
                </div>

                {{-- Guru Card 2: Monitoring Live --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-3.5">
                        <span class="w-10 h-10 rounded-2xl bg-blue-100 dark:bg-blue-950/70 text-blue-600 dark:text-blue-400 font-black text-sm flex items-center justify-center shrink-0">2</span>
                        <div>
                            <h3 class="font-heading font-black text-base text-slate-900 dark:text-white">Monitoring Live &amp; Tampilan Proyektor</h3>
                            <p class="text-xs text-slate-400">Pantau siswa detik demi detik</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Klik pada sesi yang sedang berjalan untuk membuka halaman <strong>Presensi Detail</strong>:
                    </p>
                    <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-300 list-disc list-inside leading-relaxed">
                        <li>Guru dapat menampilkan layar ini ke proyektor kelas.</li>
                        <li>Foto selfie verifikasi wajah siswa dan jam masuk akan muncul secara realtime tanpa perlu reload halaman.</li>
                        <li>Warna hijau menandakan <strong>Tepat Waktu</strong>, kuning untuk <strong>Terlambat</strong>, dan abu-abu untuk siswa yang belum hadir.</li>
                    </ul>
                </div>

                {{-- Guru Card 3: Edit Status Manual & Dispensasi --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-3.5">
                        <span class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-950/70 text-amber-600 dark:text-amber-400 font-black text-sm flex items-center justify-center shrink-0">3</span>
                        <div>
                            <h3 class="font-heading font-black text-base text-slate-900 dark:text-white">Ubah Status Manual (Izin, Sakit, Alpa)</h3>
                            <p class="text-xs text-slate-400">Dispensasi &amp; Surat Keterangan</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Jika ada siswa yang berhalangan atau tidak membawa HP:
                    </p>
                    <div class="space-y-2 text-xs">
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="font-bold text-amber-600 dark:text-amber-400">Siswa Izin / Sakit:</span>
                            <span class="text-slate-500 dark:text-slate-400 block mt-0.5">Klik tombol status di baris nama siswa, ubah ke <strong class="text-amber-600">Izin</strong> atau <strong class="text-blue-600">Sakit</strong>, dan cantumkan catatan (misal: "Surat dokter").</span>
                        </div>
                        <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">Siswa Tanpa HP / HP Rusak:</span>
                            <span class="text-slate-500 dark:text-slate-400 block mt-0.5">Guru dapat mengklik status siswa menjadi <strong class="text-emerald-600">Hadir</strong> secara manual. Hak presensi siswa tetap terjamin.</span>
                        </div>
                    </div>
                </div>

                {{-- Guru Card 4: Ekspor Laporan & Leger --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-3.5">
                        <span class="w-10 h-10 rounded-2xl bg-emerald-100 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 font-black text-sm flex items-center justify-center shrink-0">4</span>
                        <div>
                            <h3 class="font-heading font-black text-base text-slate-900 dark:text-white">Ekspor Rekap Excel &amp; PDF</h3>
                            <p class="text-xs text-slate-400">Administrasi &amp; Laporan Dinas</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                        Sistem menyediakan berbagai format laporan otomatis siap cetak:
                    </p>
                    <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-300 list-disc list-inside leading-relaxed">
                        <li><strong>Rekap Sesi / Harian:</strong> Unduh PDF atau Excel per tanggal untuk arsip kelas.</li>
                        <li><strong>Rekap Bulanan Kelas (Format Leger):</strong> Kolom Hadir, Izin, Sakit, Alpa lengkap dengan persentase kehadiran bulanan untuk Wali Kelas.</li>
                        <li><strong>Rekap Nilai Siswa:</strong> Di menu "Rekap Nilai", guru dapat menginput nilai harian/ujian dan mengekspor Leger Nilai ke Excel.</li>
                    </ul>
                </div>

            </div>

            {{-- Fitur Admin / Tim IT --}}
            <div class="bg-slate-900 text-white rounded-3xl p-6 sm:p-8 space-y-4 shadow-xl">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white font-bold text-sm">
                        <i class="fas fa-shield-halved"></i>
                    </span>
                    <div>
                        <h3 class="font-heading font-black text-lg sm:text-xl">Panduan Khusus Tim IT / Administrator Sekolah</h3>
                        <p class="text-xs text-slate-400">Pengaturan Biometrik, Lokasi Geofencing, dan Backup Data</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 text-xs">
                    <div class="p-4 bg-slate-800/80 rounded-2xl border border-slate-700 space-y-1.5">
                        <p class="font-bold text-cyan-400"><i class="fas fa-arrows-rotate mr-1"></i> Reset Wajah Siswa</p>
                        <p class="text-slate-300 text-[11px] leading-relaxed">
                            Buka menu <strong>Data Siswa</strong> > Cari siswa > Klik tombol <strong>Reset Wajah</strong>. Wajah biometrik lama dihapus sehingga siswa dapat merekam ulang dari HP mereka.
                        </p>
                    </div>

                    <div class="p-4 bg-slate-800/80 rounded-2xl border border-slate-700 space-y-1.5">
                        <p class="font-bold text-yellow-400"><i class="fas fa-map-location-dot mr-1"></i> Radius Geofencing GPS</p>
                        <p class="text-slate-300 text-[11px] leading-relaxed">
                            Buka menu <strong>Radius Geofencing</strong> untuk mengatur titik koordinat gerbang/gedung SMKN 1 Beringin dan batas toleransi jarak (misal 150 meter).
                        </p>
                    </div>

                    <div class="p-4 bg-slate-800/80 rounded-2xl border border-slate-700 space-y-1.5">
                        <p class="font-bold text-emerald-400"><i class="fas fa-hard-drive mr-1"></i> Manajemen Storage 100GB</p>
                        <p class="text-slate-300 text-[11px] leading-relaxed">
                            Buka menu <strong>Manajemen Storage</strong> untuk memantau kapasitas disk dan menjalankan cleanup foto presensi lama secara berkala.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- TAB 3: CARA PASANG APLIKASI (PWA / APK)                --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div x-show="activeTab === 'pwa'" x-cloak class="space-y-6">

            <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
                <span class="text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Tutorial Pemasangan PWA</span>
                <h2 class="font-heading font-black text-2xl sm:text-3xl text-slate-900 dark:text-white mt-1">Panduan Memasang Aplikasi ke Layar Utama HP</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Aplikasi Presensi SMKN 1 Beringin berbasis PWA (Progressive Web App) yang dapat dipasang layaknya file APK atau App Store tanpa memakan memori besar.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Android --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl">
                        <i class="fab fa-android"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-lg text-slate-900 dark:text-white">Android (Chrome / Xiaomi / Samsung)</h3>
                        <p class="text-xs text-slate-400">Paling umum digunakan siswa &amp; guru</p>
                    </div>
                    <ol class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 list-decimal list-inside leading-relaxed">
                        <li>Buka tautan <span class="text-blue-600 font-mono">{{ request()->getHost() }}</span> di <strong>Google Chrome</strong>.</li>
                        <li>Jika muncul banner <em>"Pasang Aplikasi Presensi"</em>, langsung ketuk tombol <strong>Pasang</strong>.</li>
                        <li>Jika tidak muncul otomatis: ketuk tombol <strong>titik tiga (⋮)</strong> di pojok kanan atas browser.</li>
                        <li>Pilih menu <strong>"Instal aplikasi"</strong> atau <strong>"Tambahkan ke Layar Utama"</strong>.</li>
                        <li>Konfirmasi dengan memilih <strong>"Instal"</strong>.</li>
                    </ol>
                    <div class="pt-2">
                        <button type="button" @click="$dispatch('open-pwa-install')"
                                class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-2">
                            <i class="fas fa-download"></i>
                            <span>Coba Pasang di Android</span>
                        </button>
                    </div>
                </div>

                {{-- iPhone / iOS --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-white flex items-center justify-center text-2xl">
                        <i class="fab fa-apple"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-lg text-slate-900 dark:text-white">iPhone / iPad (Apple iOS)</h3>
                        <p class="text-xs text-slate-400">Wajib menggunakan browser Safari</p>
                    </div>
                    <ol class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 list-decimal list-inside leading-relaxed">
                        <li>Buka web presensi menggunakan peramban bawaan <strong>Safari</strong>.</li>
                        <li>Ketuk ikon <strong>Bagikan (Share)</strong> berupa kotak dengan panah ke atas (<i class="fas fa-arrow-up-from-bracket text-blue-500"></i>) di bilah bawah layar iPhone.</li>
                        <li>Gulir ke bawah menu hingga menemukan opsi <strong>"Tambah ke Layar Utama" (Add to Home Screen)</strong>.</li>
                        <li>Ketuk <strong>"Tambah"</strong> di pojok kanan atas.</li>
                        <li>Ikon Presensi SMKN 1 Beringin akan muncul di Home Screen iPhone Anda.</li>
                    </ol>
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800 text-[11px] text-slate-500 dark:text-slate-400">
                        <i class="fas fa-info-circle text-blue-500 mr-1"></i> Aplikasi di iPhone berjalan secara mandiri (fullscreen) dan mendukung Face ID perangkat.
                    </div>
                </div>

                {{-- Komputer / Laptop --}}
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-950/70 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-lg text-slate-900 dark:text-white">Laptop / PC (Windows &amp; Mac)</h3>
                        <p class="text-xs text-slate-400">Ideal untuk Dewan Guru &amp; Piket</p>
                    </div>
                    <ol class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 list-decimal list-inside leading-relaxed">
                        <li>Buka situs di Google Chrome atau Microsoft Edge pada laptop Anda.</li>
                        <li>Perhatikan ujung kanan pada kolom alamat URL, terdapat ikon komputer dengan panah ke bawah (<i class="fas fa-circle-down text-blue-600"></i>).</li>
                        <li>Klik ikon tersebut, lalu pilih <strong>"Instal"</strong>.</li>
                        <li>Aplikasi Presensi akan memiliki jendela terpisah di Taskbar Windows / Dock Mac tanpa address bar yang mengganggu.</li>
                    </ol>
                    <div class="p-3 bg-blue-50 dark:bg-blue-950/40 rounded-xl border border-blue-200 dark:border-blue-900/60 text-[11px] text-blue-800 dark:text-blue-200">
                        <i class="fas fa-check text-blue-600 mr-1"></i> Sangat cocok untuk mengoperasikan layar Kiosk atau proyektor kelas.
                    </div>
                </div>

            </div>

        </div>

    </main>

    {{-- Footer --}}
    <footer class="mt-12 py-8 border-t border-slate-200 dark:border-slate-800 text-center text-xs text-slate-500 dark:text-slate-400 space-y-2 no-print">
        <div class="flex items-center justify-center gap-3">
            <img src="{{ asset('images/logo.webp') }}" class="h-6 w-auto object-contain opacity-80" alt="Logo SMKN 1">
            <span class="font-bold text-slate-700 dark:text-slate-300">SMK NEGERI 1 BERINGIN</span>
        </div>
        <p>Sistem Presensi Biometrik Wajah &amp; Geofencing Realtime &copy; {{ date('Y') }} — Hak Cipta Dilindungi</p>
    </footer>

    {{-- Universal PWA Install Modal Component --}}
    <x-pwa-modal />

</body>
</html>
