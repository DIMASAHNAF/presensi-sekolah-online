<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Saku Panduan Presensi Digital — SMKN 1 Beringin</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #09090b;
            background: #ffffff;
            font-size: 8.5pt;
            line-height: 1.42;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .font-mono {
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
            font-variant-numeric: tabular-nums;
        }

        /* ── Page Container ── */
        .page {
            width: 100%;
            height: 275mm;
            max-height: 275mm;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .page-break {
            page-break-after: always;
            break-after: page;
        }

        /* ── Header Kop ── */
        .kop-wrapper {
            border-bottom: 2pt solid #09090b;
            padding-bottom: 6px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .kop-logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .kop-text {
            flex: 1;
        }

        .kop-instansi {
            font-size: 7.5pt;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #52525b;
            text-transform: uppercase;
        }

        .kop-sekolah {
            font-size: 11pt;
            font-weight: 900;
            color: #09090b;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            line-height: 1.2;
        }

        .kop-meta {
            font-size: 7pt;
            color: #71717a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2px;
        }

        .kop-page-badge {
            font-family: "SFMono-Regular", Consolas, monospace;
            font-size: 7.5pt;
            font-weight: 700;
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
            padding: 2px 7px;
            border-radius: 4px;
            color: #18181b;
        }

        /* ── Page Title Bar ── */
        .page-title-bar {
            background: #f4f4f5;
            border: 1px solid #e4e4e7;
            border-left: 4px solid #18181b;
            padding: 6px 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title-text {
            font-size: 9.5pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #09090b;
        }

        .page-subtitle-text {
            font-size: 7.5pt;
            color: #52525b;
            font-weight: 500;
        }

        /* ── Cards & Grids ── */
        .card {
            border: 1px solid #e4e4e7;
            border-radius: 6px;
            background: #ffffff;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 5px;
            border-bottom: 1px dashed #f4f4f5;
            padding-bottom: 4px;
        }

        .card-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 17px;
            height: 17px;
            border-radius: 4px;
            background: #18181b;
            color: #fafafa;
            font-size: 7pt;
            font-weight: 800;
            font-family: monospace;
        }

        .card-title {
            font-size: 8.5pt;
            font-weight: 700;
            color: #09090b;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 6px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 6px;
        }

        /* ── Step List ── */
        .step-item {
            margin-bottom: 5px;
            padding-left: 14px;
            position: relative;
            font-size: 8pt;
            color: #27272a;
        }

        .step-item::before {
            content: "•";
            position: absolute;
            left: 4px;
            top: -1px;
            font-size: 11pt;
            color: #71717a;
        }

        .step-item strong {
            color: #09090b;
        }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            font-size: 7pt;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid transparent;
            line-height: 1.2;
        }

        .badge-hadir {
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
            border-color: rgba(16, 185, 129, 0.25);
        }

        .badge-terlambat {
            background: rgba(245, 158, 11, 0.1);
            color: #92400e;
            border-color: rgba(245, 158, 11, 0.25);
        }

        .badge-izin {
            background: rgba(14, 165, 233, 0.1);
            color: #0369a1;
            border-color: rgba(14, 165, 233, 0.25);
        }

        .badge-alpa {
            background: rgba(244, 63, 94, 0.1);
            color: #9f1239;
            border-color: rgba(244, 63, 94, 0.25);
        }

        /* ── Tables ── */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-top: 4px;
        }

        .table-custom th {
            background: #f4f4f5;
            color: #18181b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 4px 6px;
            border: 1px solid #e4e4e7;
            text-align: left;
        }

        .table-custom td {
            padding: 4px 6px;
            border: 1px solid #e4e4e7;
            color: #27272a;
            vertical-align: top;
        }

        .table-custom tr:nth-child(even) td {
            background: #fafafa;
        }

        /* ── Callout Box ── */
        .callout {
            border: 1px solid #e4e4e7;
            background: #fafafa;
            border-left: 3px solid #18181b;
            padding: 6px 8px;
            border-radius: 4px;
            font-size: 7.5pt;
            margin-top: 6px;
        }

        .callout-title {
            font-weight: 700;
            color: #09090b;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-size: 7pt;
            letter-spacing: 0.03em;
        }

        /* ── Footer ── */
        .page-footer {
            border-top: 1px solid #e4e4e7;
            padding-top: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 7pt;
            color: #71717a;
        }

        .sign-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 8px;
            text-align: center;
            font-size: 7.5pt;
        }

        .sign-space {
            height: 38px;
        }
    </style>
</head>
<body>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- HALAMAN 1: PANDUAN CEPAT SISWA                            -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="page page-break">
        <div>
            {{-- Kop Dokumen --}}
            <div class="kop-wrapper">
                <img src="file:///www/wwwroot/presensi.tiksmkn1beringin.my.id/public/images/logo.png" alt="Logo" class="kop-logo">
                <div class="kop-text">
                    <div class="kop-instansi">Pemerintah Provinsi Sumatera Utara &bull; Dinas Pendidikan &bull; Cabdis Wilayah I</div>
                    <div class="kop-sekolah">SMK NEGERI 1 BERINGIN</div>
                    <div class="kop-meta">
                        <span>Portal: <strong class="font-mono">presensi.smknegeri1beringin.sch.id</strong></span>
                        <span class="kop-page-badge">HALAMAN 1 DARI 3</span>
                    </div>
                </div>
            </div>

            {{-- Title Bar --}}
            <div class="page-title-bar">
                <div>
                    <div class="page-title-text">Buku Saku Siswa: Panduan Presensi Mandiri &amp; Kiosk</div>
                    <div class="page-subtitle-text">Panduan praktis kehadiran harian berbasis Verifikasi Wajah AI &amp; Radius Lokasi Sekolah</div>
                </div>
                <div>
                    <span class="badge badge-hadir">SOP RESMI SISWA</span>
                </div>
            </div>

            {{-- Aturan Jadwal & Batas Waktu --}}
            <div class="card" style="padding: 6px 10px; margin-bottom: 6px;">
                <div class="grid-4" style="text-align: center;">
                    <div style="border-right: 1px solid #e4e4e7; padding-right: 4px;">
                        <span class="badge badge-hadir">Tepat Waktu</span>
                        <div class="font-mono" style="font-weight: 700; font-size: 8pt; margin-top: 2px;">06:30 – 07:30 WIB</div>
                        <div style="font-size: 6.5pt; color: #71717a;">Status Hadir Normal</div>
                    </div>
                    <div style="border-right: 1px solid #e4e4e7; padding-right: 4px;">
                        <span class="badge badge-terlambat">Terlambat</span>
                        <div class="font-mono" style="font-weight: 700; font-size: 8pt; margin-top: 2px;">07:31 – 08:00 WIB</div>
                        <div style="font-size: 6.5pt; color: #71717a;">Tercatat Menit Keterlambatan</div>
                    </div>
                    <div style="border-right: 1px solid #e4e4e7; padding-right: 4px;">
                        <span class="badge badge-izin">Izin / Dispensasi</span>
                        <div class="font-mono" style="font-weight: 700; font-size: 8pt; margin-top: 2px;">Sebelum 08:00 WIB</div>
                        <div style="font-size: 6.5pt; color: #71717a;">Wajib Lampirkan Surat Bukti</div>
                    </div>
                    <div>
                        <span class="badge badge-alpa">Auto-Alfa</span>
                        <div class="font-mono" style="font-weight: 700; font-size: 8pt; margin-top: 2px;">&gt; 08:00 WIB</div>
                        <div style="font-size: 6.5pt; color: #71717a;">Otomatis Tanpa Keterangan</div>
                    </div>
                </div>
            </div>

            {{-- Alur Utama Siswa --}}
            <div class="grid-2">
                {{-- Modul 1: Login & Akses --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-num">01</span>
                        <span class="card-title">Akses &amp; Login Akun</span>
                    </div>
                    <div class="step-item">
                        <strong>Buka Browser di HP:</strong> Kunjungi <span class="font-mono">presensi.smknegeri1beringin.sch.id</span> (disarankan Google Chrome).
                    </div>
                    <div class="step-item">
                        <strong>Kredensial Siswa:</strong> Masukkan <strong>NISN</strong> sebagai Username dan Password default yang diberikan pihak sekolah.
                    </div>
                    <div class="step-item">
                        <strong>Pasang Aplikasi (PWA):</strong> Klik menu titik tiga Chrome &rarr; pilih <strong>"Tambahkan ke Layar Utama" (Install App)</strong> agar portal terbuka satu kali sentuh tanpa mengetik ulang link.
                    </div>
                </div>

                {{-- Modul 2: Pendaftaran Wajah Awal --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-num">02</span>
                        <span class="card-title">Pendaftaran Wajah (Siswa Baru)</span>
                    </div>
                    <div class="step-item">
                        <strong>Hanya Sekali di Awal:</strong> Muncul otomatis bagi siswa yang belum memiliki data sampel biometrik wajah.
                    </div>
                    <div class="step-item">
                        <strong>Rekam 4 Pose Kepala:</strong> Arahkan kamera ke wajah: (1) Tegak Lurus, (2) Senyum, (3) Toleh Kiri sedikit, (4) Toleh Kanan sedikit.
                    </div>
                    <div class="step-item">
                        <strong>Keamanan Anti-Joki:</strong> Sistem otomatis menolak jika wajah sudah pernah didaftarkan atas nama siswa lain.
                    </div>
                </div>
            </div>

            <div class="grid-2">
                {{-- Modul 3: Presensi Mandiri di HP --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-num">03</span>
                        <span class="card-title">Presensi Mandiri di Kelas (HP)</span>
                    </div>
                    <div class="step-item">
                        <strong>Radius Geofencing Sekolah:</strong> Pastikan Anda sudah berada di dalam lingkungan sekolah (radius maksimal 100 meter).
                    </div>
                    <div class="step-item">
                        <strong>Aktifkan GPS &amp; Kamera:</strong> Klik <strong>"Izinkan" (Allow)</strong> saat Chrome meminta izin lokasi &amp; kamera.
                    </div>
                    <div class="step-item">
                        <strong>Verifikasi 1 Detik:</strong> Posisikan wajah di lingkaran kamera &rarr; klik tombol <strong>"Scan Presensi Sekarang"</strong>. Indikator hijau menandakan kehadiran berhasil tercatat di jam server.
                    </div>
                </div>

                {{-- Modul 4: Presensi di Tablet Kiosk Gerbang --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-num">04</span>
                        <span class="card-title">Presensi Tablet Kiosk Gerbang</span>
                    </div>
                    <div class="step-item">
                        <strong>Solusi Bebas Kuota &amp; HP Tertinggal:</strong> Siswa yang tidak membawa HP, habis baterai, atau tanpa kuota dapat langsung memakai Kiosk.
                    </div>
                    <div class="step-item">
                        <strong>Cukup Berdiri di Depan Kamera:</strong> Hadapkan wajah ke tablet gerbang dalam jarak 0,8 – 1,5 meter.
                    </div>
                    <div class="step-item">
                        <strong>Auto Multi-Face:</strong> Sistem mengenali hingga 4 siswa sekaligus secara otomatis dengan suara konfirmasi nama &amp; kelas.
                    </div>
                </div>
            </div>

            {{-- Modul 5: Prosedur Izin / Sakit --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-num">05</span>
                    <span class="card-title">Alur Pengajuan Izin, Sakit, atau Dispensasi</span>
                </div>
                <div class="grid-3">
                    <div class="step-item">
                        <strong>1. Pilih Kategori:</strong> Buka tab <strong>"Pengajuan Izin"</strong>, tentukan kategori: <em>Sakit</em>, <em>Izin Urusan Keluarga</em>, atau <em>Dispensasi Lomba</em>.
                    </div>
                    <div class="step-item">
                        <strong>2. Unggah Bukti:</strong> Foto surat keterangan dokter, surat orang tua bermaterai, atau surat tugas sekolah (maksimal 3 MB).
                    </div>
                    <div class="step-item">
                        <strong>3. Verifikasi Guru:</strong> Status kehadiran otomatis terbit sebagai <strong>Izin / Sakit</strong> setelah diverifikasi Guru Piket / Wali Kelas.
                    </div>
                </div>
            </div>

            {{-- Ringkasan Disiplin Siswa --}}
            <div class="callout">
                <div class="callout-title">Aturan Disiplin Presensi Siswa</div>
                Presensi hanya sah jika dilakukan di dalam radius SMKN 1 Beringin dengan verifikasi wajah asli (foto layar HP lain ditolak oleh sistem <em>Anti-Spoofing</em>). Setiap presensi berhasil akan mengirim notifikasi konfirmasi kehadiran ke WhatsApp Orang Tua/Wali Murid.
            </div>
        </div>

        {{-- Footer --}}
        <div class="page-footer">
            <span>Sistem Presensi Digital SMKN 1 Beringin &bull; Versi 2.0 Enterprise</span>
            <span class="font-mono">Dokumen Panduan Resmi Siswa</span>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- HALAMAN 2: PANDUAN CEPAT GURU & WALI KELAS                -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="page page-break">
        <div>
            {{-- Kop Dokumen --}}
            <div class="kop-wrapper">
                <img src="file:///www/wwwroot/presensi.tiksmkn1beringin.my.id/public/images/logo.png" alt="Logo" class="kop-logo">
                <div class="kop-text">
                    <div class="kop-instansi">Pemerintah Provinsi Sumatera Utara &bull; Dinas Pendidikan &bull; Cabdis Wilayah I</div>
                    <div class="kop-sekolah">SMK NEGERI 1 BERINGIN</div>
                    <div class="kop-meta">
                        <span>Portal: <strong class="font-mono">presensi.smknegeri1beringin.sch.id</strong></span>
                        <span class="kop-page-badge">HALAMAN 2 DARI 3</span>
                    </div>
                </div>
            </div>

            {{-- Title Bar --}}
            <div class="page-title-bar">
                <div>
                    <div class="page-title-text">Buku Saku Guru &amp; Wali Kelas: Live Monitoring &amp; Override</div>
                    <div class="page-subtitle-text">Panduan membuka sesi jam pelajaran, pemantauan proyektor kelas, dan verifikasi izin siswa</div>
                </div>
                <div>
                    <span class="badge badge-izin">SOP RESMI GURU</span>
                </div>
            </div>

            {{-- Alur Utama Guru --}}
            <div class="grid-2">
                {{-- Modul 1: Membuka Sesi Presensi --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-num">01</span>
                        <span class="card-title">Membuka Sesi Presensi Jam Pelajaran</span>
                    </div>
                    <div class="step-item">
                        <strong>Login Akun Guru:</strong> Masuk menggunakan Username/NIP dan Password Guru yang terdaftar.
                    </div>
                    <div class="step-item">
                        <strong>Pilih Jadwal Mengajar:</strong> Di halaman dashboard, pilih mata pelajaran dan kelas yang sedang aktif hari ini.
                    </div>
                    <div class="step-item">
                        <strong>Klik "Buka Sesi Presensi":</strong> Gerbang presensi untuk seluruh siswa di kelas tersebut otomatis terbuka. Siswa dapat langsung melakukan scan dari meja masing-masing.
                    </div>
                    <div class="step-item">
                        <strong>Batas Waktu Otomatis:</strong> Sesi presensi dapat diatur durasinya (misal 15 menit) atau ditutup secara manual saat jam pelajaran usai.
                    </div>
                </div>

                {{-- Modul 2: Live Monitoring Proyektor --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-num">02</span>
                        <span class="card-title">Live Monitoring Layar Kelas</span>
                    </div>
                    <div class="step-item">
                        <strong>Mode Tampilan Penuh (Fullscreen):</strong> Hubungkan laptop ke proyektor kelas dan aktifkan mode <strong>Live Monitoring</strong>.
                    </div>
                    <div class="step-item">
                        <strong>Pembaruan Otomatis Tanpa Refresh:</strong> Saat siswa menekan scan di HP, kartu nama &amp; foto snapshot siswa langsung muncul di layar secara seketika (*real-time*).
                    </div>
                    <div class="step-item">
                        <strong>Ringkasan Cepat:</strong> Menampilkan statistik total Hadir, Terlambat, Izin, dan Belum Hadir secara transparan di hadapan seluruh siswa.
                    </div>
                </div>
            </div>

            <div class="grid-2">
                {{-- Modul 3: Koreksi Manual (Override) --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-num">03</span>
                        <span class="card-title">Koreksi Manual (Manual Override)</span>
                    </div>
                    <div class="step-item">
                        <strong>Kondisi Khusus Siswa:</strong> Digunakan bila siswa fisik berada di kelas namun perangkat HP mati, kamera rusak, atau kendala GPS sekolah.
                    </div>
                    <div class="step-item">
                        <strong>Langkah Ubah Status:</strong> Cari nama siswa &rarr; klik tombol <strong>"Override / Ubah Status"</strong> &rarr; tentukan status (Hadir/Izin/Sakit).
                    </div>
                    <div class="step-item">
                        <strong>Wajib Catatan Alasan:</strong> Guru wajib mengisi alasan perubahan (misal: <em>"HP siswa habis baterai, fisik hadir di kelas"</em>).
                    </div>
                    <div class="step-item">
                        <strong>Transparansi Audit Log:</strong> Setiap tindakan koreksi tercatat nama guru dan jam eksekusi untuk audit Kepala Sekolah.
                    </div>
                </div>

                {{-- Modul 4: Verifikasi Izin / Sakit --}}
                <div class="card">
                    <div class="card-header">
                        <span class="card-num">04</span>
                        <span class="card-title">Verifikasi Permohonan Izin / Sakit</span>
                    </div>
                    <div class="step-item">
                        <strong>Notifikasi Permohonan Masuk:</strong> Muncul tanda pada tab <strong>"Permohonan Izin"</strong> kelas binaan Wali Kelas / Guru Piket.
                    </div>
                    <div class="step-item">
                        <strong>Periksa Surat Bukti:</strong> Klik baris siswa untuk memperbesar foto surat dokter atau surat permohonan orang tua.
                    </div>
                    <div class="step-item">
                        <strong>Eksekusi Persetujuan:</strong>
                        <ul style="margin-top: 3px; padding-left: 12px; list-style-type: circle;">
                            <li><strong>Setujui:</strong> Status otomatis berubah menjadi Izin/Sakit di rekap harian &amp; bulanan.</li>
                            <li><strong>Tolak:</strong> Beri catatan alasan penolakan (misal surat tidak terbaca).</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Rekap Hak Akses Wali Kelas & Guru Piket --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-num">05</span>
                    <span class="card-title">Perbedaan Hak Akses Guru Piket &amp; Wali Kelas</span>
                </div>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Peran Akun</th>
                            <th style="width: 45%;">Ruang Lingkup Monitoring</th>
                            <th style="width: 30%;">Kewenangan Khusus</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Guru Mata Pelajaran</strong></td>
                            <td>Hanya pada kelas &amp; jam pelajaran yang sedang diajarkan sesuai jadwal.</td>
                            <td>Buka/tutup sesi dan koreksi kehadiran jam pelajaran.</td>
                        </tr>
                        <tr>
                            <td><strong>Wali Kelas</strong></td>
                            <td>Pemantauan penuh 24 jam khusus siswa di kelas binaannya.</td>
                            <td>Verifikasi izin/sakit, cetak rekap bulanan, konseling siswa alpa.</td>
                        </tr>
                        <tr>
                            <td><strong>Guru Piket Harian</strong></td>
                            <td>Seluruh kelas (lintas jurusan) dari jam 06:30 s/d 16:00 WIB.</td>
                            <td>Pencatatan siswa terlambat di gerbang &amp; approval izin darurat.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Penutupan Sesi & Auto-Alfa --}}
            <div class="callout">
                <div class="callout-title">Penting: Menutup Sesi &amp; Mekanisme Auto-Alfa</div>
                Setelah jam pelajaran atau batas toleransi kehadiran berakhir, Guru menutup sesi presensi. Seluruh siswa yang belum melakukan verifikasi wajah dan tidak memiliki surat izin sah akan <strong>otomatis terkunci sebagai status Alpa</strong> pada jam rekap harian.
            </div>
        </div>

        {{-- Footer --}}
        <div class="page-footer">
            <span>Sistem Presensi Digital SMKN 1 Beringin &bull; Versi 2.0 Enterprise</span>
            <span class="font-mono">Dokumen Panduan Resmi Guru &amp; Wali Kelas</span>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- HALAMAN 3: REKAP LAPORAN & SOLUSI KENDALA                 -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="page">
        <div>
            {{-- Kop Dokumen --}}
            <div class="kop-wrapper">
                <img src="file:///www/wwwroot/presensi.tiksmkn1beringin.my.id/public/images/logo.png" alt="Logo" class="kop-logo">
                <div class="kop-text">
                    <div class="kop-instansi">Pemerintah Provinsi Sumatera Utara &bull; Dinas Pendidikan &bull; Cabdis Wilayah I</div>
                    <div class="kop-sekolah">SMK NEGERI 1 BERINGIN</div>
                    <div class="kop-meta">
                        <span>Portal: <strong class="font-mono">presensi.smknegeri1beringin.sch.id</strong></span>
                        <span class="kop-page-badge">HALAMAN 3 DARI 3</span>
                    </div>
                </div>
            </div>

            {{-- Title Bar --}}
            <div class="page-title-bar">
                <div>
                    <div class="page-title-text">Rekapitulasi Laporan &amp; Solusi Kendala Teknis Lapangan</div>
                    <div class="page-subtitle-text">Format laporan matriks bulanan, cetak dokumen resmi, dan tabel penyelesaian kendala</div>
                </div>
                <div>
                    <span class="badge badge-terlambat">LAPORAN &amp; HELPDESK</span>
                </div>
            </div>

            {{-- Bagian A: Laporan Matriks Bulanan --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-num">01</span>
                    <span class="card-title">Rekapitulasi Matriks Bulanan (Tanggal 1–31) &amp; Ekspor Data</span>
                </div>
                <div class="grid-2">
                    <div>
                        <div class="step-item">
                            <strong>Format Matriks Tanggal 1 s/d 31:</strong> Menampilkan rekap kehadiran seluruh siswa dalam 1 tabel utuh dengan kode status resmi:
                            <span class="font-mono" style="font-weight: 700; color: #09090b;">H</span> (Hadir),
                            <span class="font-mono" style="font-weight: 700; color: #d97706;">T</span> (Terlambat),
                            <span class="font-mono" style="font-weight: 700; color: #0284c7;">I</span> (Izin),
                            <span class="font-mono" style="font-weight: 700; color: #0284c7;">S</span> (Sakit),
                            <span class="font-mono" style="font-weight: 700; color: #e11d48;">A</span> (Alpa), dan
                            <span class="font-mono" style="font-weight: 700; color: #71717a;">L</span> (Hari Libur).
                        </div>
                        <div class="step-item">
                            <strong>Persentase Kehadiran Otomatis:</strong> Kolom rekapitulasi total menghitung persentase kehadiran per siswa untuk dasar penilaian rapor dan kedisiplinan.
                        </div>
                    </div>
                    <div>
                        <div class="step-item">
                            <strong>Ekspor Excel (<span class="font-mono">.xlsx</span>):</strong> Format siap cetak rapi, lengkap dengan rumus persentase dan kop surat sekolah untuk arsip Tata Usaha.
                        </div>
                        <div class="step-item">
                            <strong>Cetak Laporan PDF Resmi:</strong> Menghasilkan lembar PDF resmi ber-kop Dinas Pendidikan, nomor statistik sekolah, serta kolom tanda tangan Wali Kelas dan Kepala Sekolah.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian B: Matriks Troubleshooting Cepat --}}
            <div class="card">
                <div class="card-header">
                    <span class="card-num">02</span>
                    <span class="card-title">Matriks Penanganan Cepat Kendala Teknis (Troubleshooting)</span>
                </div>
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Pesan / Kendala Lapangan</th>
                            <th style="width: 32%;">Akar Masalah</th>
                            <th style="width: 43%;">Solusi Cepat (Kurang dari 30 Detik)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>"Di luar radius sekolah (&gt; 100m)"</strong></td>
                            <td>GPS ponsel belum mengunci titik akurat atau mode hemat daya aktif.</td>
                            <td>Buka Google Maps sebentar agar GPS akurat, pastikan mode <em>Akurasi Tinggi</em> aktif, lalu refresh portal presensi.</td>
                        </tr>
                        <tr>
                            <td><strong>"Wajah tidak terdeteksi / tidak cocok"</strong></td>
                            <td>Pencahayaan membelakangi cahaya (silau) atau wajah tertutup masker/topi.</td>
                            <td>Posisikan wajah menghadap sumber cahaya, lepas masker dan topi, tahan posisi tegak di dalam lingkaran selama 2 detik.</td>
                        </tr>
                        <tr>
                            <td><strong>"Kamera / GPS tidak aktif di browser"</strong></td>
                            <td>Izin akses perangkat sempat tertekan "Tolak" (Block) di Chrome.</td>
                            <td>Klik ikon gembok / setelan di sebelah kiri link browser &rarr; ubah izin <strong>Kamera</strong> &amp; <strong>Lokasi</strong> menjadi <strong>"Allow / Izinkan"</strong>.</td>
                        </tr>
                        <tr>
                            <td><strong>Laptop "ERR_NAME_NOT_RESOLVED"</strong></td>
                            <td>Cache DNS lama pada Windows atau jaringan Wi-Fi sekolah.</td>
                            <td>Buka CMD di laptop, ketik <span class="font-mono">ipconfig /flushdns</span> lalu Enter. Di Chrome buka <span class="font-mono">chrome://net-internals/#dns</span> dan klik <em>Clear host cache</em>.</td>
                        </tr>
                        <tr>
                            <td><strong>HP mati / kuota internet habis</strong></td>
                            <td>Siswa tidak dapat mengakses portal presensi mandiri.</td>
                            <td>Gunakan <strong>Tablet Kiosk di Gerbang Sekolah</strong> atau minta <strong>Koreksi Manual (Override)</strong> kepada Guru yang mengajar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Modul 03: Pusat Layanan Bantuan & Dukungan Teknis --}}
            <div class="card" style="margin-top: 6px;">
                <div class="card-header">
                    <span class="card-num">03</span>
                    <span class="card-title">Pusat Layanan &amp; Kontak Bantuan Teknis (Service Desk)</span>
                </div>
                <div class="grid-2">
                    <div style="border-right: 1px solid #e4e4e7; padding-right: 8px;">
                        <div style="font-size: 7.5pt; font-weight: 700; color: #09090b; text-transform: uppercase; margin-bottom: 4px;">
                            Kontak Layanan Cepat
                        </div>
                        <div style="margin-bottom: 5px;">
                            <span style="font-size: 7pt; color: #71717a;">Hotline / WhatsApp Layanan:</span><br>
                            <span class="font-mono" style="font-size: 9.5pt; font-weight: 800; color: #09090b;">0822-6116-8543</span>
                        </div>
                        <div>
                            <span style="font-size: 7pt; color: #71717a;">Email Dukungan Administrator:</span><br>
                            <span class="font-mono" style="font-size: 8.5pt; font-weight: 700; color: #09090b;">dimasjoki0909@gmail.com</span>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 7.5pt; font-weight: 700; color: #09090b; text-transform: uppercase; margin-bottom: 4px;">
                            Cakupan Layanan &amp; Jam Operasional
                        </div>
                        <div class="step-item" style="font-size: 7.5pt; margin-bottom: 3px;">
                            <strong>Jam Operasional:</strong> Senin – Jumat pukul 06:30 – 17:00 WIB.
                        </div>
                        <div class="step-item" style="font-size: 7.5pt; margin-bottom: 3px;">
                            <strong>Layanan Siswa:</strong> Reset sampel wajah biometrik, kendala akun login NISN, dan panduan presensi perangkat.
                        </div>
                        <div class="step-item" style="font-size: 7.5pt; margin-bottom: 0;">
                            <strong>Layanan Guru:</strong> Panduan sesi presensi kelas, koreksi override, dan bantuan teknis rekapitulasi data.
                        </div>
                    </div>
                </div>
            </div>

            {{-- Integritas Data Biometrik --}}
            <div class="callout" style="margin-top: 4px;">
                <div class="callout-title">Keamanan &amp; Integritas Data Biometrik</div>
                Data biometrik siswa disimpan dalam bentuk vektor matematis terenkripsi satu arah (*one-way mathematical vector*), bukan foto polos. Sistem menjamin keamanan data dan seluruh operasional presensi terhubung langsung dengan sistem informasi sekolah.
            </div>
        </div>

        {{-- Footer --}}
        <div class="page-footer">
            <span>Sistem Presensi Digital SMKN 1 Beringin &bull; Versi 2.0 Enterprise</span>
            <span class="font-mono">Dokumen Panduan Resmi Laporan &amp; Helpdesk</span>
        </div>
    </div>

</body>
</html>
