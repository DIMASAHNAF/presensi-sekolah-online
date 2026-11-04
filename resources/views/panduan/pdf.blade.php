<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Panduan & SOP Presensi SMKN 1 Beringin</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm 16mm 18mm 16mm;
            @bottom-right {
                content: counter(page);
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                font-size: 8pt;
                color: #64748b;
            }
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #1e293b;
            background: #ffffff;
            font-size: 9.5pt;
            line-height: 1.55;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .font-mono {
            font-family: "SFMono-Regular", Consolas, "Liberation Mono", Menlo, Courier, monospace;
            font-size: 8.5pt;
        }

        /* ─── Page Breaks ─── */
        .page-break {
            page-break-after: always;
            break-after: page;
        }

        .avoid-break {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* ─── Running Header / Footer ─── */
        .doc-header {
            border-bottom: 1.5pt solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 7.5pt;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .doc-header .school-title {
            color: #0f172a;
            font-weight: 700;
        }

        .doc-footer {
            border-top: 1pt solid #e2e8f0;
            padding-top: 8px;
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            font-size: 7.5pt;
            color: #94a3b8;
        }

        /* ─── Cover Page ─── */
        .cover-page {
            height: 100%;
            min-height: 250mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            padding: 15mm 5mm 5mm;
        }

        .cover-kop {
            border-bottom: 3pt double #0f172a;
            padding-bottom: 14px;
            margin-bottom: 35px;
        }

        .cover-kop h3 {
            font-size: 11pt;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #334155;
            text-transform: uppercase;
        }

        .cover-kop h2 {
            font-size: 12pt;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
        }

        .cover-kop h1 {
            font-size: 16pt;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin: 4px 0;
        }

        .cover-kop p {
            font-size: 8pt;
            color: #64748b;
        }

        .cover-logo-wrapper {
            margin: 25px auto;
            width: 110px;
            height: 110px;
        }

        .cover-logo-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .cover-main-title {
            margin: 20px 0;
        }

        .cover-tag {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff;
            font-size: 8pt;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 14px;
        }

        .cover-title-big {
            font-size: 21pt;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.25;
            text-transform: uppercase;
            letter-spacing: -0.01em;
            margin-bottom: 12px;
        }

        .cover-title-sub {
            font-size: 11pt;
            color: #475569;
            font-weight: 500;
            max-width: 460px;
            margin: 0 auto;
            line-height: 1.45;
        }

        .cover-meta-box {
            background-color: #f8fafc;
            border: 1pt solid #cbd5e1;
            border-radius: 8px;
            padding: 16px 20px;
            margin: 35px auto 0;
            max-width: 440px;
            text-align: left;
            font-size: 8.5pt;
        }

        .cover-meta-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            border-bottom: 0.5pt dashed #e2e8f0;
        }

        .cover-meta-row:last-child {
            border-bottom: none;
        }

        .cover-meta-label {
            color: #64748b;
            font-weight: 600;
        }

        .cover-meta-val {
            color: #0f172a;
            font-weight: 700;
            text-align: right;
        }

        .cover-footer {
            margin-top: 40px;
            font-size: 8pt;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ─── Typography & Headings ─── */
        h1.section-title {
            font-size: 15pt;
            font-weight: 800;
            color: #0f172a;
            border-bottom: 2pt solid #0f172a;
            padding-bottom: 6px;
            margin-top: 10px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        h2.section-subtitle {
            font-size: 11pt;
            font-weight: 700;
            color: #1e293b;
            margin-top: 16px;
            margin-bottom: 8px;
            border-left: 3.5pt solid #0f172a;
            padding-left: 8px;
        }

        h3.subsection-title {
            font-size: 10pt;
            font-weight: 700;
            color: #334155;
            margin-top: 12px;
            margin-bottom: 6px;
        }

        p {
            margin-bottom: 8px;
            color: #334155;
            text-align: justify;
        }

        /* ─── Callout Boxes ─── */
        .callout {
            border-radius: 6px;
            padding: 10px 14px;
            margin: 10px 0 14px 0;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }

        .callout-info {
            background-color: #f0f9ff;
            border-left: 3.5pt solid #0284c7;
            color: #0369a1;
        }

        .callout-success {
            background-color: #f0fdf4;
            border-left: 3.5pt solid #16a34a;
            color: #15803d;
        }

        .callout-warning {
            background-color: #fffbeb;
            border-left: 3.5pt solid #d97706;
            color: #b45309;
        }

        .callout-danger {
            background-color: #fef2f2;
            border-left: 3.5pt solid #dc2626;
            color: #b91c1c;
        }

        .callout-title {
            font-weight: 700;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ─── Badges ─── */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-hadir {
            background: #dcfce7;
            color: #15803d;
            border: 0.5pt solid #86efac;
        }

        .badge-terlambat {
            background: #fef3c7;
            color: #b45309;
            border: 0.5pt solid #fde68a;
        }

        .badge-izin {
            background: #e0f2fe;
            color: #0369a1;
            border: 0.5pt solid #bae6fd;
        }

        .badge-alpa {
            background: #fee2e2;
            color: #b91c1c;
            border: 0.5pt solid #fca5a5;
        }

        /* ─── Lists & Steps ─── */
        ol.step-list {
            padding-left: 18px;
            margin-bottom: 12px;
        }

        ol.step-list li {
            margin-bottom: 6px;
            color: #334155;
            text-align: justify;
        }

        ol.step-list li strong {
            color: #0f172a;
        }

        ul.bullet-list {
            padding-left: 18px;
            margin-bottom: 12px;
        }

        ul.bullet-list li {
            margin-bottom: 5px;
            color: #334155;
        }

        /* ─── Tables ─── */
        table.doc-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 16px 0;
            font-size: 8.5pt;
            page-break-inside: avoid;
        }

        table.doc-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 7px 10px;
            text-align: left;
            border: 1pt solid #0f172a;
        }

        table.doc-table td {
            padding: 6px 10px;
            border: 1pt solid #cbd5e1;
            color: #334155;
            vertical-align: top;
        }

        table.doc-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* ─── Grids ─── */
        .grid-2 {
            display: flex;
            gap: 12px;
            margin: 10px 0 14px 0;
        }

        .grid-2 > div {
            flex: 1;
            background-color: #ffffff;
            border: 1pt solid #cbd5e1;
            border-radius: 6px;
            padding: 10px 12px;
        }

        .grid-3 {
            display: flex;
            gap: 10px;
            margin: 10px 0 14px 0;
        }

        .grid-3 > div {
            flex: 1;
            background-color: #f8fafc;
            border: 1pt solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
        }

        /* ─── Signature Block ─── */
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 44%;
            text-align: center;
            font-size: 8.5pt;
        }

        .signature-space {
            height: 65px;
        }

        .signature-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }

        .signature-nip {
            font-family: monospace;
            font-size: 8pt;
            color: #64748b;
        }
    </style>
</head>
<body>

    <!-- ======================================================== -->
    <!-- HALAMAN 1: COVER RESMI                                   -->
    <!-- ======================================================== -->
    <div class="cover-page page-break">
        <div>
            <div class="cover-kop">
                <h3>Pemerintah Provinsi Sumatera Utara</h3>
                <h2>Dinas Pendidikan — Cabang Dinas Wilayah I</h2>
                <h1>SMK Negeri 1 Beringin</h1>
                <p>Jl. Pendidikan No. 1, Kec. Beringin, Kab. Deli Serdang, Sumatera Utara 20552</p>
                <p>Website: presensi.smknegeri1beringin.sch.id &bull; NPSN: 10214151 &bull; Akreditasi: A</p>
            </div>

            <div class="cover-logo-wrapper">
                <img src="file:///www/wwwroot/presensi.tiksmkn1beringin.my.id/public/images/logo.png" alt="Logo SMKN 1 Beringin">
            </div>

            <div class="cover-main-title">
                <span class="cover-tag">Dokumen Standar Operasional Prosedur (SOP)</span>
                <div class="cover-title-big">
                    BUKU PANDUAN PENGGUNAAN<br>
                    SISTEM PRESENSI BIOMETRIK WAJAH<br>
                    &amp; GEOFENCING DIGITAL
                </div>
                <div class="cover-title-sub">
                    Pedoman Operasional Lengkap untuk Siswa, Dewan Guru, Wali Kelas, dan Tim Administrator IT Sekolah
                </div>
            </div>
        </div>

        <div>
            <div class="cover-meta-box">
                <div class="cover-meta-row">
                    <span class="cover-meta-label">Nama Aplikasi</span>
                    <span class="cover-meta-val">Presensi SMKN 1 Beringin (v2.0)</span>
                </div>
                <div class="cover-meta-row">
                    <span class="cover-meta-label">Alamat Portal</span>
                    <span class="cover-meta-val font-mono">presensi.smknegeri1beringin.sch.id</span>
                </div>
                <div class="cover-meta-row">
                    <span class="cover-meta-label">Tahun Pelajaran</span>
                    <span class="cover-meta-val">2026 / 2027 (Semester Ganjil)</span>
                </div>
                <div class="cover-meta-row">
                    <span class="cover-meta-label">Teknologi Inti</span>
                    <span class="cover-meta-val">AI ArcFace 512-D &bull; GPS Geofence &bull; PWA</span>
                </div>
                <div class="cover-meta-row">
                    <span class="cover-meta-label">Penyusun</span>
                    <span class="cover-meta-val">Tim Pengembang TIK SMKN 1 Beringin</span>
                </div>
            </div>

            <div class="cover-footer">
                Edisi Revisi II &bull; Diterbitkan November 2026 &bull; Hak Cipta Dilindungi
            </div>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- HALAMAN 2: DAFTAR ISI & GAMBARAN UMUM                    -->
    <!-- ======================================================== -->
    <div class="page-break">
        <div class="doc-header">
            <span class="school-title">SMK Negeri 1 Beringin</span>
            <span>Buku Panduan Presensi Biometrik &bull; Halaman 2</span>
        </div>

        <h1 class="section-title">DAFTAR ISI</h1>

        <table class="doc-table" style="margin-bottom: 24px;">
            <thead>
                <tr>
                    <th style="width: 12%;">Bab</th>
                    <th>Judul Materi &amp; Ruang Lingkup</th>
                    <th style="width: 15%; text-align: center;">Halaman</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-mono"><strong>BAB 1</strong></td>
                    <td><strong>Gambaran Umum &amp; Arsitektur Sistem Presensi Digital</strong><br><small style="color: #64748b;">Pengenalan Wajah AI, Validasi GPS Haversine, WhatsApp Gateway, dan PWA.</small></td>
                    <td style="text-align: center; font-weight: bold;">3</td>
                </tr>
                <tr>
                    <td class="font-mono"><strong>BAB 2</strong></td>
                    <td><strong>Panduan Lengkap Siswa</strong><br><small style="color: #64748b;">Login 3 Opsi, Rekam Biometrik 4-Pose, Absensi Kelas, Kiosk Gerbang, Pengajuan Izin/Sakit, FAQ Kendala.</small></td>
                    <td style="text-align: center; font-weight: bold;">4 - 5</td>
                </tr>
                <tr>
                    <td class="font-mono"><strong>BAB 3</strong></td>
                    <td><strong>Panduan Dewan Guru &amp; Wali Kelas</strong><br><small style="color: #64748b;">Login NIP/NUPTK, Buka Sesi Kelas, Live Monitoring Proyektor, Manual Override, Tutup Sesi &amp; Auto-Alfa.</small></td>
                    <td style="text-align: center; font-weight: bold;">6 - 7</td>
                </tr>
                <tr>
                    <td class="font-mono"><strong>BAB 4</strong></td>
                    <td><strong>Laporan Rekapitulasi Matriks Bulanan &amp; Leger Nilai</strong><br><small style="color: #64748b;">Format Matriks 1-31 Hari, Cetak PDF Resmi Ber-Kop, Ekspor Excel Leger Wali Kelas.</small></td>
                    <td style="text-align: center; font-weight: bold;">8</td>
                </tr>
                <tr>
                    <td class="font-mono"><strong>BAB 5</strong></td>
                    <td><strong>Panduan Administrator IT &amp; Tata Usaha</strong><br><small style="color: #64748b;">Master Data, Reset Face Descriptor, Konfigurasi Geofencing, Manajemen Storage 100GB, Audit Log.</small></td>
                    <td style="text-align: center; font-weight: bold;">9</td>
                </tr>
                <tr>
                    <td class="font-mono"><strong>BAB 6</strong></td>
                    <td><strong>Panduan Instalasi PWA (APK di HP) &amp; Lembar Pengesahan</strong><br><small style="color: #64748b;">Instalasi di Android Chrome, iPhone Safari, Laptop, Kontak Bantuan, dan Lembar Tanda Tangan.</small></td>
                    <td style="text-align: center; font-weight: bold;">10</td>
                </tr>
            </tbody>
        </table>

        <h1 class="section-title">KATA PENGANTAR</h1>
        <p>
            Puji syukur kehadirat Tuhan Yang Maha Esa atas terwujudnya sistem transformasi digital presensi di SMK Negeri 1 Beringin. Buku panduan ini disusun sebagai pedoman operasional resmi (SOP) bagi seluruh warga sekolah—termasuk peserta didik, bapak/ibu guru mata pelajaran, wali kelas, serta jajaran staf administrasi sekolah.
        </p>
        <p>
            Sistem Presensi Biometrik Wajah dan Geofencing ini dirancang untuk mewujudkan pencatatan kehadiran yang akuntabel, transparan, cepat, dan terbebas dari praktik kecurangan titip absen (*anti-proxy*). Melalui integrasi kecerdasan buatan (*Face Recognition*) dan koordinat GPS sekolah, kami berharap kedisiplinan dan budaya tepat waktu di lingkungan SMKN 1 Beringin dapat semakin meningkat.
        </p>
        <p>
            Diharapkan seluruh pihak dapat membaca, memahami, dan memedomani langkah-langkah yang tercantum di dalam buku panduan ini.
        </p>

        <div style="margin-top: 18px; text-align: right; font-size: 8.5pt;">
            Beringin, November 2026<br>
            <strong>Kepala SMK Negeri 1 Beringin</strong>
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- HALAMAN 3: BAB 1 - GAMBARAN UMUM SISTEM                  -->
    <!-- ======================================================== -->
    <div class="page-break">
        <div class="doc-header">
            <span class="school-title">SMK Negeri 1 Beringin</span>
            <span>Buku Panduan Presensi Biometrik &bull; Halaman 3</span>
        </div>

        <h1 class="section-title">BAB 1: GAMBARAN UMUM &amp; ARSITEKTUR SISTEM</h1>

        <p>
            Sistem Presensi Digital SMKN 1 Beringin adalah platform web berbasis cloud modern yang menggabungkan verifikasi biometrik citra wajah berbasis kecerdasan buatan (*Artificial Intelligence*) dengan sistem pembatasan lokasi geografis (*GPS Geofencing*).
        </p>

        <h2 class="section-subtitle">1.1 Pilar Utama Keamanan &amp; Fitur Sistem</h2>

        <div class="grid-2">
            <div>
                <h3 class="subsection-title">1. Biometrik Wajah AI (ArcFace 512-D)</h3>
                <p style="font-size: 8.5pt; margin-bottom: 4px;">
                    Mendeteksi dan mengekstrak 512 titik vektor matematis wajah siswa. Sistem memiliki mekanisme *liveness detection* dan anti-spoofing untuk menolak foto cetak kertas maupun foto dari layar HP lain.
                </p>
            </div>
            <div>
                <h3 class="subsection-title">2. Geofencing GPS (Formula Haversine)</h3>
                <p style="font-size: 8.5pt; margin-bottom: 4px;">
                    Memverifikasi posisi fisik siswa secara real-time berdasarkan koordinat GPS SMKN 1 Beringin (<span class="font-mono">Lat: 3.6335, Lng: 98.8687</span>) dengan radius toleransi 150 meter.
                </p>
            </div>
        </div>

        <div class="grid-2">
            <div>
                <h3 class="subsection-title">3. WhatsApp Notification Gateway</h3>
                <p style="font-size: 8.5pt; margin-bottom: 4px;">
                    Setiap kali siswa berhasil melakukan presensi, sistem secara otomatis mengirimkan pesan konfirmasi instan kepada nomor WhatsApp orang tua/wali murid yang terdaftar di Dapodik.
                </p>
            </div>
            <div>
                <h3 class="subsection-title">4. Progressive Web App (PWA)</h3>
                <p style="font-size: 8.5pt; margin-bottom: 4px;">
                    Dapat diinstal ke Home Screen ponsel siswa (Android &amp; iOS) menjadi aplikasi mandiri (*fullscreen*) berbobot sangat ringan (&lt; 2 MB), tanpa perlu mengunduh file APK berat.
                </p>
            </div>
        </div>

        <h2 class="section-subtitle">1.2 Alur Kerja Presensi Digital Harian</h2>

        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 28%;">Tahap</th>
                    <th>Deskripsi Kerja Sistem &amp; Validasi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="font-mono" style="text-align: center;">01</td>
                    <td><strong>Pemeriksaan Waktu Server</strong></td>
                    <td>Jam absensi divalidasi langsung dari server sekolah (WIB / GMT+7), mencegah manipulasi pengaturan jam di HP siswa.</td>
                </tr>
                <tr>
                    <td class="font-mono" style="text-align: center;">02</td>
                    <td><strong>Validasi Jarak GPS</strong></td>
                    <td>Peramban membaca titik koordinat siswa. Jika berada &gt; 150 meter di luar gerbang sekolah, tombol scan wajah terkunci otomatis.</td>
                </tr>
                <tr>
                    <td class="font-mono" style="text-align: center;">03</td>
                    <td><strong>Scan &amp; Verifikasi Wajah</strong></td>
                    <td>Kamera depan mencocokkan wajah siswa dengan sampel di database dalam waktu &lt; 3 detik. Euclidean Distance ketat &le; 0.45.</td>
                </tr>
                <tr>
                    <td class="font-mono" style="text-align: center;">04</td>
                    <td><strong>Pencatatan &amp; Notifikasi</strong></td>
                    <td>Menyimpan snapshot foto WebP (&le; 150KB), kalkulasi menit keterlambatan otomatis, dan trigger pesan WhatsApp ke orang tua.</td>
                </tr>
            </tbody>
        </table>

        <div class="callout callout-info">
            <div class="callout-title">Catatan Standar Waktu Sekolah:</div>
            Senin s.d. Kamis &amp; Sabtu, batas kehadiran tepat waktu adalah pukul <strong>07:15:00 WIB</strong>. Khusus hari Jumat, batas waktu adalah pukul <strong>07:45:00 WIB</strong>. Presensi yang dilakukan setelah jam tersebut otomatis ditandai status <span class="badge badge-terlambat">Terlambat</span> disertai catatan selisih menit kedatangan.
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- HALAMAN 4: BAB 2 - PANDUAN LENGKAP SISWA (BAGIAN 1)      -->
    <!-- ======================================================== -->
    <div class="page-break">
        <div class="doc-header">
            <span class="school-title">SMK Negeri 1 Beringin</span>
            <span>Buku Panduan Presensi Biometrik &bull; Halaman 4</span>
        </div>

        <h1 class="section-title">BAB 2: PANDUAN LENGKAP SISWA</h1>

        <h2 class="section-subtitle">2.1 Akses &amp; Tiga Pilihan Cara Login Siswa</h2>
        <p>
            Buka browser pada smartphone Anda dan akses: <span class="font-mono" style="font-weight: bold; color: #0284c7;">https://presensi.smknegeri1beringin.sch.id</span>.
            Pada tab <strong>Siswa</strong>, masukkan salah satu dari 3 pilihan identitas berikut pada kolom pertama:
        </p>

        <ul class="bullet-list">
            <li><strong>Pilihan A (Nama Lengkap):</strong> Ketik nama lengkap Anda sesuai data absen kelas. Contoh: <span class="font-mono">Dimas Ahnaf Fathurrahman</span>.</li>
            <li><strong>Pilihan B (Username Akun):</strong> Ketik username pendek Anda yang terdaftar. Contoh: <span class="font-mono">dimas</span>.</li>
            <li><strong>Pilihan C (10 Digit NISN):</strong> Ketik 10 digit Nomor Induk Siswa Nasional Anda. Contoh: <span class="font-mono">0071234567</span>.</li>
        </ul>

        <div class="callout callout-warning">
            <div class="callout-title">Informasi Password Awal Siswa:</div>
            Bagi siswa yang baru pertama kali login, kolom Password <strong>dapat dikosongkan</strong> (cukup masukkan Nama/NISN lalu klik Masuk). Setelah berhasil masuk, siswa sangat disarankan mengatur password pribadi pada menu Profil untuk keamanan akun.
        </div>

        <h2 class="section-subtitle">2.2 Perekaman Wajah Awal (Biometrik 4-Pose Face ID)</h2>
        <p>
            Jika biometrik wajah Anda belum terdaftar di sistem, portal akan mengarahkan secara otomatis ke panduan perekaman wajah. Perekaman hanya dilakukan <strong>1 kali di awal tahun ajaran</strong>:
        </p>

        <div class="grid-2">
            <div>
                <p><strong>4 Tahapan Pose Wajah:</strong></p>
                <ol class="step-list">
                    <li><strong>Pose 1 (Lurus):</strong> Tatap kamera depan dengan pandangan tegak.</li>
                    <li><strong>Pose 2 (Toleh Kanan):</strong> Putar kepala sedikit ke kanan (~15&deg;).</li>
                    <li><strong>Pose 3 (Toleh Kiri):</strong> Putar kepala sedikit ke kiri (~15&deg;).</li>
                    <li><strong>Pose 4 (Senyum):</strong> Tampilkan ekspresi wajah rileks/tersenyum wajar.</li>
                </ol>
            </div>
            <div>
                <p><strong>Tips Agar Cepat Lolos Verifikasi:</strong></p>
                <ul class="bullet-list">
                    <li>Pastikan berada di ruangan dengan pencahayaan terang dan merata.</li>
                    <li>Hindari membelakangi lampu terang atau jendela (*backlight*).</li>
                    <li>Lepaskan kacamata hitam, masker, atau topi.</li>
                    <li>Singkirkan poni rambut yang menutupi area alis atau mata.</li>
                </ul>
            </div>
        </div>

        <h2 class="section-subtitle">2.3 Prosedur Presensi Masuk di Kelas</h2>
        <ol class="step-list">
            <li>Pastikan Anda sudah tiba di <strong>area lingkungan sekolah SMKN 1 Beringin</strong>.</li>
            <li>Aktifkan fitur <strong>GPS / Lokasi HP</strong> Anda (pastikan opsi <em>Akurasi Tinggi</em> aktif).</li>
            <li>Buka portal presensi dan pilih menu <strong>Beranda</strong>.</li>
            <li>Pada sesi presensi aktif kelas Anda, tekan tombol <strong>"Verifikasi Wajah Sekarang"</strong>.</li>
            <li>Berikan izin kamera jika browser meminta konfirmasi perizinan (*Allow*).</li>
            <li>Arahkan wajah Anda ke dalam bingkai oval di layar:
                <ul class="bullet-list" style="margin-top: 4px;">
                    <li>Kamera memproses pencocokan biometrik dalam 1-3 detik.</li>
                    <li>Layar akan menampilkan konfirmasi status <span class="badge badge-hadir">Hadir</span> atau <span class="badge badge-terlambat">Terlambat</span>.</li>
                    <li>Notifikasi WhatsApp konfirmasi kehadiran otomatis terkirim ke HP orang tua/wali murid.</li>
                </ul>
            </li>
        </ol>
    </div>

    <!-- ======================================================== -->
    <!-- HALAMAN 5: BAB 2 - PANDUAN LENGKAP SISWA (BAGIAN 2)      -->
    <!-- ======================================================== -->
    <div class="page-break">
        <div class="doc-header">
            <span class="school-title">SMK Negeri 1 Beringin</span>
            <span>Buku Panduan Presensi Biometrik &bull; Halaman 5</span>
        </div>

        <h2 class="section-subtitle">2.4 Presensi di Tablet Kiosk Gerbang Sekolah</h2>
        <p>
            Bagi siswa yang mengalami kendala seperti <strong>tidak membawa HP</strong>, HP kehabisan baterai, atau kendala jaringan internet, sekolah menyediakan tablet <strong>Kiosk Gerbang Mandiri</strong> di pos keamanan/gerbang utama:
        </p>

        <ol class="step-list">
            <li>Berdiri tegak di depan layar tablet Kiosk pada jarak sekitar 0.5 hingga 1 meter.</li>
            <li>Posisikan wajah Anda tepat menghadap kamera tablet Kiosk.</li>
            <li>Sistem Face Recognition pada Kiosk akan mendeteksi wajah secara otomatis tanpa Anda perlu menyentuh layar tablet (*touchless*).</li>
            <li>Layar Kiosk akan menampilkan nama Anda, rombel kelas, jam kedatangan, dan membunyikan suara nada konfirmasi berhasil.</li>
        </ol>

        <h2 class="section-subtitle">2.5 Pengajuan Surat Izin, Sakit, atau Dispensasi</h2>
        <p>
            Jika siswa berhalangan hadir ke sekolah karena alasan sakit atau urusan keluarga resmi, pengajuan wajib dilakukan secara online sebelum batas jam absensi berakhir:
        </p>

        <ol class="step-list">
            <li>Masuk ke akun siswa, lalu ketuk menu <strong>Izin &amp; Sakit</strong>.</li>
            <li>Pilih kategori permohonan:
                <ul class="bullet-list" style="margin-top: 4px;">
                    <li><span class="badge badge-izin">Sakit</span> — Wajib melampirkan foto Surat Keterangan Dokter atau surat bertanda tangan orang tua.</li>
                    <li><span class="badge badge-izin">Izin</span> — Wajib melampirkan foto surat permohonan izin dari orang tua/wali.</li>
                    <li><span class="badge badge-izin">Dispensasi</span> — Untuk siswa yang mengikuti kegiatan lomba dinas, OSIS, atau tugas sekolah resmi.</li>
                </ul>
            </li>
            <li>Tentukan rentang tanggal izin dan tuliskan alasan singkat pada kolom keterangan.</li>
            <li>Unggah foto bukti surat (format JPG/PNG/WebP maks. 2 MB) lalu klik <strong>Kirim Pengajuan</strong>.</li>
            <li>Status permohonan akan dipantau oleh Wali Kelas dan Guru Piket untuk disetujui (*Approve*).</li>
        </ol>

        <h2 class="section-subtitle">2.6 FAQ &amp; Solusi Kendala Teknis Siswa</h2>

        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 32%;">Pesan Kendala / Error</th>
                    <th>Penyebab &amp; Langkah Solusi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>"Lokasi Anda Berada di Luar Radius Sekolah"</strong></td>
                    <td>GPS HP belum mengunci titik akurat. Buka aplikasi Google Maps di HP selama 5 detik hingga titik biru tepat berada di SMKN 1 Beringin, lalu kembali ke browser presensi dan refresh halaman.</td>
                </tr>
                <tr>
                    <td><strong>"Izin Kamera Ditolak / Kamera Tidak Terbuka"</strong></td>
                    <td>Peramban Chrome/Safari memblokir akses kamera. Buka Pengaturan HP &gt; Aplikasi &gt; Chrome/Safari &gt; Izin Aplikasi &gt; Kamera &gt; Pilih "Izinkan Saat Menggunakan Aplikasi".</td>
                </tr>
                <tr>
                    <td><strong>"Wajah Tidak Dikenali / Tidak Cocok"</strong></td>
                    <td>Pencahayaan terlalu gelap atau sudut wajah berbeda drastis. Lepas masker/kacamata, cari lokasi terang. Jika tetap gagal, lapor ke Wali Kelas untuk dibantu absensi manual atau reset sampel wajah.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ======================================================== -->
    <!-- HALAMAN 6: BAB 3 - PANDUAN GURU & WALI KELAS (BAGIAN 1)  -->
    <!-- ======================================================== -->
    <div class="page-break">
        <div class="doc-header">
            <span class="school-title">SMK Negeri 1 Beringin</span>
            <span>Buku Panduan Presensi Biometrik &bull; Halaman 6</span>
        </div>

        <h1 class="section-title">BAB 3: PANDUAN DEWAN GURU &amp; WALI KELAS</h1>

        <h2 class="section-subtitle">3.1 Akses &amp; Cara Login Guru / Staf</h2>
        <p>
            Buka portal presensi dan pilih tab <strong>Guru / Staf</strong>. Masukkan salah satu identitas pada kolom akun:
        </p>

        <ul class="bullet-list">
            <li><strong>NIP (18 Digit):</strong> Khusus bagi Bapak/Ibu Guru ASN (PNS dan PPPK).</li>
            <li><strong>NUPTK (16 Digit):</strong> Bagi Bapak/Ibu Guru Honorer yang telah memiliki NUPTK.</li>
            <li><strong>Username Akun / Nama Lengkap:</strong> Dapat juga menggunakan username yang telah diberikan oleh tim IT sekolah.</li>
        </ul>
        <p>Masukkan <strong>Password</strong> akun Anda, kemudian klik tombol <strong>Masuk Guru / Staf</strong>.</p>

        <h2 class="section-subtitle">3.2 Membuka Sesi Presensi Jam Pelajaran / Jam Masuk</h2>
        <p>
            Setiap memulai jam pelajaran atau sesi presensi harian kelas, Guru Mata Pelajaran atau Wali Kelas membuka sesi absensi:
        </p>

        <ol class="step-list">
            <li>Masuk ke bilah navigasi kiri, pilih menu <strong>Kelola Presensi</strong>.</li>
            <li>Klik tombol <strong>"+ Buka Sesi Presensi"</strong> pada sudut kanan atas.</li>
            <li>Lengkapi formulir parameter sesi presensi:
                <ul class="bullet-list" style="margin-top: 4px;">
                    <li><strong>Pilih Kelas / Rombel:</strong> Contoh <span class="font-mono">X PPLG 1</span> atau <span class="font-mono">XI TJKT 2</span>.</li>
                    <li><strong>Mata Pelajaran:</strong> Pilih mapel yang diampu (atau pilih *Jam Masuk Wali Kelas*).</li>
                    <li><strong>Batas Jam Masuk:</strong> Batas waktu kehadiran tepat waktu (misal: 07:15).</li>
                    <li><strong>Toleransi Keterlambatan:</strong> Menit batas keterlambatan yang ditoleransi.</li>
                </ul>
            </li>
            <li>Klik <strong>"Mulai Sesi"</strong>. Sistem membuka pintu absensi bagi seluruh siswa di kelas tersebut.</li>
        </ol>

        <h2 class="section-subtitle">3.3 Live Monitoring Kehadiran Siswa Real-time (Layar Proyektor)</h2>
        <p>
            Klik pada sesi kelas yang sedang berjalan untuk membuka halaman <strong>Live Monitoring</strong>. Halaman ini memperbarui daftar absensi siswa secara otomatis tanpa perlu refresh:
        </p>

        <ul class="bullet-list">
            <li>Menampilkan snapshot foto selfie verifikasi siswa beserta jam kedatangan (detik demi detik).</li>
            <li>Menampilkan status instan: <span class="badge badge-hadir">Hadir</span>, <span class="badge badge-terlambat">Terlambat</span>, atau <span class="badge" style="background: #f1f5f9; color: #475569;">Belum Hadir</span>.</li>
            <li><strong>Mode Proyektor:</strong> Guru dapat menghubungkan laptop ke proyektor LCD di depan kelas sehingga seluruh kelas dapat melihat rekap kehadiran secara transparan.</li>
        </ul>

        <div class="callout callout-success">
            <div class="callout-title">Keunggulan Live Monitoring:</div>
            Mencegah siswa meninggalkan kelas setelah absensi, serta memberikan konfirmasi langsung kepada ketua kelas dan guru mengenai siswa mana saja yang belum berada di ruangan.
        </div>
    </div>

    <!-- ======================================================== -->
    <!-- HALAMAN 7: BAB 3 - PANDUAN GURU & WALI KELAS (BAGIAN 2)  -->
    <!-- ======================================================== -->
    <div class="page-break">
        <div class="doc-header">
            <span class="school-title">SMK Negeri 1 Beringin</span>
            <span>Buku Panduan Presensi Biometrik &bull; Halaman 7</span>
        </div>

        <h2 class="section-subtitle">3.4 Tindakan Koreksi Manual (Manual Override)</h2>
        <p>
            Jika ada siswa yang hadir secara fisik di kelas namun mengalami kendala teknis (misal HP rusak, kamera HP pecah, atau kuota habis), guru dapat melakukan intervensi manual secara bertanggung jawab:
        </p>

        <ol class="step-list">
            <li>Pada tabel daftar siswa sesi aktif, temukan nama siswa terkait.</li>
            <li>Klik tombol aksi status kehadiran yang sesuai:
                <ul class="bullet-list" style="margin-top: 4px;">
                    <li><span class="badge badge-hadir">Hadir Manual</span> — Untuk siswa yang hadir fisik namun tanpa perangkat HP.</li>
                    <li><span class="badge badge-izin">Sakit</span> — Untuk siswa yang menyertakan surat keterangan sakit.</li>
                    <li><span class="badge badge-izin">Izin</span> — Untuk siswa dengan izin tertulis orang tua.</li>
                    <li><span class="badge badge-izin">Dispensasi</span> — Untuk siswa yang sedang bertugas mewakili sekolah.</li>
                </ul>
            </li>
            <li><strong>Wajib Mengisi Catatan Alasan:</strong> Masukkan alasan singkat (contoh: <em>"HP siswa mati total, hadir fisik di bangku baris 2"</em>).</li>
            <li>Klik <strong>Simpan Perubahan</strong>. Riwayat override tercatat rapi di Audit Log demi transparansi.</li>
        </ol>

        <h2 class="section-subtitle">3.5 Verifikasi Permohonan Izin / Sakit Siswa</h2>
        <p>
            Wali Kelas dan Guru Piket memiliki wewenang meninjau pengajuan surat izin online dari siswa:
        </p>
        <ol class="step-list">
            <li>Buka menu <strong>Approval Izin/Sakit</strong> pada dashboard.</li>
            <li>Klik pada pengajuan siswa untuk melihat lampiran foto surat keterangan dokter atau surat orang tua.</li>
            <li>Tentukan keputusan:
                <ul class="bullet-list" style="margin-top: 4px;">
                    <li><strong>Setujui (Approve):</strong> Status kehadiran siswa otomatis berubah menjadi Sakit/Izin pada tanggal terkait.</li>
                    <li><strong>Tolak (Reject):</strong> Masukkan alasan penolakan (misal: <em>"Foto surat tidak terbaca / tanggal lampiran kadaluarsa"</em>). Siswa akan mendapatkan notifikasi untuk mengunggah ulang.</li>
                </ul>
            </li>
        </ol>

        <h2 class="section-subtitle">3.6 Menutup Sesi &amp; Mekanisme Otomatis Alpa (Auto-Alfa)</h2>
        <p>
            Setelah jam pembelajaran selesai atau waktu absensi habis:
        </p>
        <ol class="step-list">
            <li>Guru menekan tombol <strong>"Tutup Sesi"</strong>.</li>
            <li>Sistem mengunci sesi absensi secara permanen.</li>
            <li><strong>Konversi Auto-Alfa:</strong> Siswa yang hingga sesi ditutup belum melakukan absensi dan tidak memiliki keterangan izin/sakit yang sah, secara otomatis dicatat berstatus <span class="badge badge-alpa">Alpa (A)</span>.</li>
        </ol>
    </div>

    <!-- ======================================================== -->
    <!-- HALAMAN 8: BAB 4 - LAPORAN MATRIKS BULANAN & LEGER       -->
    <!-- ======================================================== -->
    <div class="page-break">
        <div class="doc-header">
            <span class="school-title">SMK Negeri 1 Beringin</span>
            <span>Buku Panduan Presensi Biometrik &bull; Halaman 8</span>
        </div>

        <h1 class="section-title">BAB 4: REKAPITULASI MATRIKS BULANAN &amp; NILAI</h1>

        <h2 class="section-subtitle">4.1 Format Rekapitulasi Matriks Bulanan (Tanggal 1–31)</h2>
        <p>
            Untuk memenuhi standar administrasi resmi Dinas Pendidikan Provinsi Sumatera Utara, sistem menyediakan laporan rekapitulasi matriks bulanan absensi kelas dengan struktur kolom tanggal 1 sampai 31:
        </p>

        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Kode Status</th>
                    <th style="width: 30%;">Keterangan</th>
                    <th>Simbol Tampilan Tabel Matriks</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>H</strong></td>
                    <td>Hadir Tepat Waktu</td>
                    <td>Huruf <strong>H</strong> berwarna hijau</td>
                </tr>
                <tr>
                    <td><strong>T</strong></td>
                    <td>Terlambat Masuk</td>
                    <td>Huruf <strong>T</strong> berwarna oranye disertai selisih menit</td>
                </tr>
                <tr>
                    <td><strong>S</strong></td>
                    <td>Sakit (Surat Dokter)</td>
                    <td>Huruf <strong>S</strong> berwarna biru langit</td>
                </tr>
                <tr>
                    <td><strong>I</strong></td>
                    <td>Izin Urusan Keluarga</td>
                    <td>Huruf <strong>I</strong> berwarna biru langit</td>
                </tr>
                <tr>
                    <td><strong>D</strong></td>
                    <td>Dispensasi Tugas Sekolah</td>
                    <td>Huruf <strong>D</strong> berwarna ungu</td>
                </tr>
                <tr>
                    <td><strong>A</strong></td>
                    <td>Alpa (Tanpa Keterangan)</td>
                    <td>Huruf <strong>A</strong> tebal berwarna merah</td>
                </tr>
                <tr>
                    <td><strong>-</strong></td>
                    <td>Hari Libur / Minggu</td>
                    <td>Garis strip (-) abu-abu redup</td>
                </tr>
            </tbody>
        </table>

        <h2 class="section-subtitle">4.2 Ekspor Laporan Excel &amp; Cetak PDF Ber-Kop Surat</h2>
        <p>
            Wali Kelas dan Tata Usaha dapat mencetak laporan resmi ini kapan saja:
        </p>
        <ol class="step-list">
            <li>Buka menu <strong>Semua Presensi &gt; Ekspor Bulanan Kelas</strong>.</li>
            <li>Pilih <strong>Tahun Ajaran</strong>, <strong>Bulan</strong>, dan <strong>Kelas / Rombel</strong> yang diinginkan.</li>
            <li>Pilih format ekspor:
                <ul class="bullet-list" style="margin-top: 4px;">
                    <li><strong>Unduh Excel (.xlsx):</strong> Menghasilkan spreadsheet lengkap berumus persentase kehadiran untuk arsip digital wali kelas.</li>
                    <li><strong>Cetak PDF Resmi:</strong> Menghasilkan dokumen A4 landscape ber-Kop Surat Dinas Pendidikan Provinsi Sumatera Utara lengkap dengan kolom tanda tangan Wali Kelas dan Kepala Sekolah.</li>
                </ul>
            </li>
        </ol>

        <h2 class="section-subtitle">4.3 Pengelolaan Rekap Nilai Siswa</h2>
        <p>
            Selain presensi, sistem terintegrasi dengan modul penilaian akademik:
        </p>
        <ul class="bullet-list">
            <li>Guru dapat menginput nilai harian (tugas/kuis), PTS, dan PAS secara batch langsung per kelas.</li>
            <li>Sistem menghitung nilai akhir secara otomatis berdasarkan bobot persentase kurikulum yang berlaku.</li>
            <li>Fitur ekspor format Leger Nilai resmi ke format Excel siap cetak untuk pengisian rapor siswa.</li>
        </ul>
    </div>

    <!-- ======================================================== -->
    <!-- HALAMAN 9: BAB 5 - PANDUAN ADMINISTRATOR IT SEKOLAH      -->
    <!-- ======================================================== -->
    <div class="page-break">
        <div class="doc-header">
            <span class="school-title">SMK Negeri 1 Beringin</span>
            <span>Buku Panduan Presensi Biometrik &bull; Halaman 9</span>
        </div>

        <h1 class="section-title">BAB 5: PANDUAN ADMINISTRATOR IT &amp; TU</h1>

        <p>
            Bab ini ditujukan khusus bagi Tim IT, Pengelola Data Pokok Pendidikan (Dapodik), dan Staf Tata Usaha SMKN 1 Beringin yang memegang hak akses akun level Super Administrator.
        </p>

        <h2 class="section-subtitle">5.1 Manajemen Master Data &amp; Akun</h2>
        <ul class="bullet-list">
            <li><strong>Data Siswa &amp; Guru:</strong> CRUD data pokok (Nama, NISN, NIP, NUPTK, Rombel, No. WhatsApp Orang Tua). Mendukung import massal file Excel/CSV.</li>
            <li><strong>Rombel &amp; Jurusan:</strong> Pengelolaan kelas untuk seluruh konsentrasi keahlian (PPLG, TJKT, AKL, MP, dll.).</li>
            <li><strong>Kalender Akademik &amp; Hari Libur:</strong> Menetapkan kalender libur semester, tanggal merah nasional, dan kegiatan khusus agar sistem tidak memberlakukan absensi pada hari libur.</li>
        </ul>

        <h2 class="section-subtitle">5.2 Reset Face Descriptor Siswa</h2>
        <p>
            Jika seorang siswa mengalami perubahan fisik signifikan (contoh: kacamata baru, penyesuaian hijab, atau salah rekam awal):
        </p>
        <ol class="step-list">
            <li>Masuk ke menu <strong>Data Siswa</strong> &gt; Cari nama siswa yang bersangkutan.</li>
            <li>Klik tombol <strong>"Reset Wajah"</strong>.</li>
            <li>Vektor descriptor biometrik lama akan dihapus aman dari server. Saat siswa login berikutnya, sistem otomatis mengaktifkan wizard perekaman 4-pose wajah baru.</li>
        </ol>

        <h2 class="section-subtitle">5.3 Konfigurasi Geofencing Titik Sekolah</h2>
        <p>
            Administrator dapat menyesuaikan titik koordinat gerbang dan batas radius toleransi:
        </p>
        <ul class="bullet-list">
            <li>Buka menu <strong>Radius Geofencing</strong>.</li>
            <li>Peta satelit interaktif menampilkan titik patokan sekolah SMKN 1 Beringin.</li>
            <li>Masukkan nilai radius (standar rekomendasi: <strong>100 – 150 meter</strong> untuk mengcover seluruh ruang kelas, bengkel, dan lapangan sekolah).</li>
        </ul>

        <h2 class="section-subtitle">5.4 Manajemen Storage 100GB &amp; Pembersihan Snapshot</h2>
        <ul class="bullet-list">
            <li>Setiap foto snapshot presensi dikompresi otomatis ke format WebP (&le; 150 KB).</li>
            <li>Administrator dapat memantau grafik penggunaan storage server di menu <strong>Manajemen Storage</strong>.</li>
            <li>Tersedia tombol <em>Cleanup Arsip Usang</em> untuk mengompresi atau mengarsipkan foto presensi semester sebelumnya secara otomatis.</li>
        </ul>

        <h2 class="section-subtitle">5.5 Audit Log &amp; Keamanan Sistem</h2>
        <p>
            Setiap aksi manual override, pergantian status, login dari perangkat baru, atau perubahan data master tersimpan otomatis di menu <strong>Audit Log Presensi</strong> lengkap dengan timestamp mikrodetik, nama operator, dan alamat IP perangkat.
        </p>
    </div>

    <!-- ======================================================== -->
    <!-- HALAMAN 10: BAB 6 - PWA & LEMBAR PENGESAHAN              -->
    <!-- ======================================================== -->
    <div class="page-break">
        <div class="doc-header">
            <span class="school-title">SMK Negeri 1 Beringin</span>
            <span>Buku Panduan Presensi Biometrik &bull; Halaman 10</span>
        </div>

        <h1 class="section-title">BAB 6: PANDUAN INSTALASI APLIKASI (PWA)</h1>

        <div class="grid-3">
            <div>
                <h3 class="subsection-title">1. Android (Chrome)</h3>
                <ol style="text-align: left; font-size: 8pt; padding-left: 14px; margin-top: 4px;">
                    <li>Buka tautan di Chrome.</li>
                    <li>Ketuk titik tiga (⋮) di kanan atas.</li>
                    <li>Pilih <strong>"Instal aplikasi"</strong>.</li>
                    <li>Ikon muncul di layar HP.</li>
                </ol>
            </div>
            <div>
                <h3 class="subsection-title">2. iPhone (Safari)</h3>
                <ol style="text-align: left; font-size: 8pt; padding-left: 14px; margin-top: 4px;">
                    <li>Buka tautan di Safari.</li>
                    <li>Ketuk ikon Share (⎋).</li>
                    <li>Pilih <strong>"Add to Home Screen"</strong>.</li>
                    <li>Ketuk "Tambah".</li>
                </ol>
            </div>
            <div>
                <h3 class="subsection-title">3. Laptop (PC)</h3>
                <ol style="text-align: left; font-size: 8pt; padding-left: 14px; margin-top: 4px;">
                    <li>Buka di Chrome/Edge.</li>
                    <li>Klik ikon komputer (↓) di address bar.</li>
                    <li>Pilih <strong>"Instal"</strong>.</li>
                    <li>Buka seperti aplikasi desktop.</li>
                </ol>
            </div>
        </div>

        <div class="callout callout-info" style="margin-top: 14px;">
            <div class="callout-title">Layanan Bantuan &amp; Helpdesk TIK Sekolah:</div>
            Jika menemui kendala teknis yang tidak tercantum dalam buku panduan ini, hubungi Tim Pengembang TIK SMKN 1 Beringin via Ruang Laboratorium Komputer atau email resmi: <span class="font-mono">helpdesk@smknegeri1beringin.sch.id</span>.
        </div>

        <h1 class="section-title" style="margin-top: 25px;">LEMBAR PENGESAHAN DOKUMEN</h1>
        <p style="text-align: center; font-size: 9pt; margin-bottom: 20px;">
            Buku Panduan Penggunaan dan Standar Operasional Prosedur (SOP) Sistem Presensi Biometrik Wajah dan Geofencing ini dinyatakan sah dan berlaku secara resmi di lingkungan SMK Negeri 1 Beringin mulai Tahun Ajaran 2026/2027.
        </p>

        <div class="signature-section">
            <div class="signature-box">
                <p>Mengetahui &amp; Menyetujui,</p>
                <p><strong>Kepala SMK Negeri 1 Beringin</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">H. ILYAS, S.Pd., M.Si.</p>
                <p class="signature-nip">NIP. 19680512 199303 1 004</p>
            </div>

            <div class="signature-box">
                <p>Deli Serdang, 4 November 2026</p>
                <p><strong>Koordinator Tim Pengembang TIK</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">DIMAS AHNAF F., S.Kom.</p>
                <p class="signature-nip">NIP. 19950824 202221 1 002</p>
            </div>
        </div>

        <div class="doc-footer" style="margin-top: 35px;">
            <span>Dokumen Resmi SMK Negeri 1 Beringin &bull; SOP Presensi v2.0</span>
            <span>presensi.smknegeri1beringin.sch.id</span>
        </div>
    </div>

</body>
</html>
