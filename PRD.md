# PRODUCT REQUIREMENTS DOCUMENT (PRD) & ANTI-AI-SLOP RULES
**Project:** Sistem Presensi Digital SMKN 1 Beringin (`presensi.smknegeri1beringin.sch.id`)
**Core Features:** Face Recognition Attendance, Geofencing (GPS), Role-Based Dashboard, Leave/Permission Management, Monthly Matrix Report.

---

## BAGIAN 1: ATURAN CODING & ANTI-AI-SLOP (WAJIB DIIKUTI AI)

### 1.1 Larangan Keras Visual (Zero AI-Slop Policy)
1. **DILARANG** menggunakan gradasi warna (`bg-gradient-to-*`) pada background halaman, tombol utama, card, ataupun teks (`bg-clip-text`).
2. **DILARANG** menggunakan warna ungu/indigo menyala khas template AI (`indigo-500`, `purple-600`, `violet-500`) sebagai warna utama.
3. **DILARANG** memasukkan emoji apa pun (🚀, 👋, ✅, ❌, 📊, dll.) di dalam kode UI, label tombol, judul halaman, *empty state*, maupun *toast notification*. Gunakan ikon SVG dari `lucide-react` dengan ukuran konsisten (`w-4 h-4` atau `w-5 h-5`, `strokeWidth={1.75}`).
4. **DILARANG** menggunakan kalimat basa-basi khas AI pada UI (Contoh terlarang: *"Selamat datang kembali! Mari kelola kehadiran dengan mudah dan efisien hari ini"*). Gunakan teks fungsional dan langsung ke intinya (Contoh benar: *"Ringkasan Kehadiran — Selasa, 3 Nov 2026"*).
5. **DILARANG** menggunakan bayangan tebal (`shadow-lg`, `shadow-xl`, `shadow-2xl`) dan sudut terlalu bulat (`rounded-3xl`). Gunakan pendekatan **Border-First Design**: sudut `rounded-lg` atau `rounded-xl` dengan border tegas `border border-zinc-200 dark:border-zinc-800` dan bayangan maksimal `shadow-xs` / `shadow-sm`.

### 1.2 Sistem Desain & Tipografi (Linear / Vercel Aesthetic)
* **Palet Warna:**
  * **Background Utama:** `bg-zinc-50` (Light) / `bg-zinc-950` (Dark).
  * **Surface / Card / Tabel:** `bg-white` (Light) / `bg-zinc-900` (Dark).
  * **Border & Divider:** `border-zinc-200` (Light) / `border-zinc-800` (Dark).
  * **Primary Action (Tombol Utama):** Kontras tinggi (`bg-zinc-900 text-zinc-50 hover:bg-zinc-800` di Light Mode, atau `bg-zinc-100 text-zinc-900 hover:bg-zinc-200` di Dark Mode) atau 1 warna aksen solid identitas sekolah (`emerald-600` / `blue-600` tanpa gradasi).
  * **Semantic Status Badge (Gunakan background subtle + border tipis):**
    * **Hadir (Tepat Waktu):** `bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20`
    * **Terlambat:** `bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20`
    * **Izin / Sakit:** `bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-500/20`
    * **Alfa (Tanpa Keterangan):** `bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20`
* **Tipografi:**
  * Font UI Utama: `Inter` atau `Plus Jakarta Sans` (`tracking-tight` untuk heading).
  * Font Data & Angka: **WAJIB** menggunakan font monospace (`JetBrains Mono` / `Geist Mono`) dengan class `font-mono tabular-nums` untuk: Jam Digital, Timer, NIS/NISN, Koordinat GPS, Jarak Meter, dan Persentase Kehadiran.

### 1.3 Aturan Kelengkapan Fitur (Zero-Placeholder Policy)
1. **DILARANG** meninggalkan komentar `// TODO`, `// implement later`, atau fungsi kosong `() => {}` pada tombol.
2. Setiap tabel data **WAJIB** memiliki fitur:
   * Pencarian (*debounced search* berdasarkan nama/NISN).
   * Filter aktif (berdasarkan Jurusan, Kelas, Tanggal, dan Status Kehadiran).
   * Pagination atau *virtualized rows* yang berfungsi penuh di sisi backend/API.
   * *Loading Skeleton* yang presisi (bukan sekadar teks `"Loading..."`).
   * *Empty State* yang bersih dengan tombol aksi relevan (misal: *"Reset Filter"*).
3. Setiap aksi *mutation* (Submit, Update, Delete, Approve) **WAJIB** memiliki:
   * State `isLoading` / `isPending` yang menonaktifkan tombol saat proses berjalan.
   * Penanganan error yang menampilkan pesan spesifik dari backend melalui *Toast*.

---

## BAGIAN 2: ARSITEKTUR ROLE & HAK AKSES (RBAC)

1. **Siswa:**
   * Melakukan presensi masuk & pulang (Kamera Wajah + GPS).
   * Melihat riwayat kehadiran pribadi & statistik bulanan.
   * Mengajukan permohonan Izin / Sakit / Dispensasi beserta unggah bukti surat.
   * Pendaftaran/Pembaruan sampel wajah (*Face Enrollment*) — hanya bisa dilakukan jika diizinkan Admin (*lock/unlock enrollment*).
2. **Wali Kelas / Guru Piket:**
   * Memantau kehadiran siswa secara *real-time* (khusus kelas binaannya untuk Wali Kelas, atau seluruh kelas untuk Guru Piket).
   * Melakukan verifikasi (*Approve / Reject*) permohonan Izin/Sakit siswa.
   * Mengubah status kehadiran manual (*Manual Override*) jika siswa mengalami kendala perangkat, wajib disertai catatan alasan.
3. **Super Admin (Kajur / Tim IT / Tata Usaha):**
   * CRUD Master Data: Tahun Ajaran, Jurusan (RPL/PPLG, TJKT, dll.), Kelas, Siswa, Guru, dan Akun User.
   * Import & Export data siswa via Excel/CSV.
   * Konfigurasi Geolocation & Geofencing (Titik Koordinat Poligon/Radius SMKN 1 Beringin).
   * Konfigurasi Jam Operasional per Hari (Jam Mulai Masuk, Batas Terlambat, Jam Buka Pulang) & Kalender Hari Libur.
   * Reset data sampel wajah siswa (*Reset Face Descriptor*) & *Device Binding*.
   * Cetak / Export Laporan Rekapitulasi Bulanan (Format Matriks Tanggal 1–31) ke Excel (`.xlsx`) dan PDF resmi ber-kop surat.

---

## BAGIAN 3: SPESIFIKASI FITUR & EDGE CASES (END-TO-END)

### 3.1 Modul Presensi Siswa (Face Recognition + Geofencing)
**Alur Utama:**
1. **Pemeriksaan Waktu (Time Gate):**
   * Sistem mengambil waktu dari **Server (WIB / `Asia/Jakarta`)**, bukan jam lokal HP siswa (untuk mencegah kecurangan *ubah jam HP*).
   * Jika di luar jam operasional atau hari libur, kunci layar kamera dan tampilkan status jadwal.
2. **Pemeriksaan Lokasi (Geofencing):**
   * Mengambil koordinat GPS dengan `enableHighAccuracy: true`.
   * Menghitung jarak siswa ke titik pusat SMKN 1 Beringin menggunakan **Haversine Formula** (dalam satuan meter).
   * **Edge Case GPS:**
     * Jika izin lokasi ditolak browser: Tampilkan panduan langkah mengaktifkan izin lokasi.
     * Jika akurasi GPS buruk (`accuracy > 100 meter`): Beri peringatan *"Sinyal GPS lemah, pastikan berada di ruang terbuka"*.
     * Jika di luar radius (misal `> 100 meter` dari titik sekolah): Kunci tombol presensi, tampilkan jarak aktual secara *real-time* (`font-mono`).
3. **Verifikasi Wajah (`face-api.js` / Face Descriptor Matching):**
   * Memuat model deteksi wajah dengan indikator progress yang jelas.
   * Mencocokkan *Face Descriptor* (128-float array) dari kamera langsung dengan *descriptor* siswa yang tersimpan di database menggunakan *Euclidean Distance* (Threshold ketat: `< 0.45`).
   * **Anti-Spoofing / Liveness Sederhana:** Wajib mendeteksi kedipan mata atau perubahan ekspresi/gerakan kepala ringan sebelum menangkap frame, atau mewajibkan pencocokan stabil selama 3 frame berturut-turut.
   * **Edge Case Kamera:**
     * Jika siswa belum mendaftarkan wajah: Arahkan otomatis ke halaman *Face Enrollment* (ambil 3 sudut wajah: depan, sedikit serong kiri, sedikit serong kanan).
     * Jika terdeteksi lebih dari 1 wajah di frame: Tolak presensi, tampilkan pesan *"Pastikan hanya ada 1 wajah di dalam bingkai kamera"*.
4. **Kalkulasi Status Kehadiran:**
   * Masuk $\le$ `batas_jam_masuk` $\rightarrow$ Status: `HADIR` (`late_minutes = 0`).
   * Masuk $>$ `batas_jam_masuk` $\rightarrow$ Status: `TERLAMBAT` (hitung selisih menit keterlambatan secara otomatis di backend).
   * Menyimpan foto *snapshot* saat presensi (dikompresi ke format WebP, ukuran maks 150KB) + koordinat lat/long + jarak meter.

### 3.2 Modul Izin, Sakit, dan Dispensasi
* **Form Pengajuan Siswa:**
  * Pilih kategori: `SAKIT`, `IZIN`, atau `DISPENSASI` (Lomba/Kegiatan Sekolah).
  * Pilih rentang tanggal (Mulai - Selesai) dan keterangan singkat.
  * Unggah lampiran bukti (Foto surat dokter / surat orang tua / surat tugas) dengan fitur *client-side compression* sebelum diunggah.
* **Alur Verifikasi Guru Piket / Wali Kelas:**
  * Notifikasi badge jumlah permohonan berstatus `PENDING`.
  * Preview foto surat langsung di dalam *Modal/Drawer* tanpa harus mendownload file.
  * Jika di-*Approve*, sistem otomatis meng-insert/update record di tabel `attendances` untuk rentang tanggal tersebut menjadi status terkait. Jika di-*Reject*, wajib mengisi alasan penolakan.

### 3.3 Modul Dashboard Admin & Rekapitulasi Matriks
* **Layout Dashboard Admin:**
  * **Top Bar:** Ringkasan metrik hari ini dalam 1 baris kompak (Total Siswa, Hadir, Terlambat, Izin/Sakit, Belum Absen/Alfa) lengkap dengan persentase.
  * **Live Attendance Feed & Tabel Utama:** Daftar siswa yang baru saja melakukan presensi lengkap dengan thumbnail foto *snapshot*, jam presensi (`font-mono`), kelas, status, dan menit keterlambatan.
* **Fitur Auto-Alfa (Cronjob / Scheduled Task):**
  * Setiap pukul 16:00 WIB pada hari sekolah aktif (bukan Minggu dan bukan tanggal merah di tabel `holidays`), sistem otomatis menandai siswa yang belum memiliki record presensi maupun izin sebagai `ALFA`.
* **Laporan Rekap Matriks Bulanan:**
  * Filter berdasarkan: **Bulan, Tahun, Jurusan, dan Kelas**.
  * Tampilan tabel matriks: Baris adalah daftar siswa (urut abjad), Kolom adalah tanggal `01` s/d `31` dengan kode warna (`H`, `T`, `S`, `I`, `A`, atau `-` untuk hari libur), serta kolom total di ujung kanan (`Total H`, `Total T`, `Total S/I`, `Total A`, dan `% Kehadiran`).
  * **Export Excel & PDF:** Menghasilkan dokumen siap cetak dengan Kop Surat Resmi SMKN 1 Beringin, tabel matriks rapi, dan blok tanda tangan Wali Kelas / Kepala Sekolah di bagian bawah.

---

## BAGIAN 4: SKEMA DATABASE INTI (REFERENSI)

Pastikan struktur tabel mencakup entitas dan kolom krusial berikut:
1. `users`: `id`, `name`, `username` (NISN/NIP), `password`, `role` (`admin`, `teacher`, `picket`, `student`), `is_active`, `device_id` (opsional).
2. `departments` (Jurusan): `id`, `code` (contoh: `PPLG`, `TJKT`), `name`.
3. `classrooms` (Kelas): `id`, `department_id`, `grade_level` (`X`, `XI`, `XII`), `name` (contoh: `XI PPLG 3`), `homeroom_teacher_id`.
4. `students`: `id`, `user_id`, `classroom_id`, `nisn`, `nis`, `gender`, `face_descriptor` (JSON/Text array float), `face_enrolled_at`, `is_face_locked` (boolean).
5. `attendance_settings`: `id`, `school_latitude`, `school_longitude`, `max_radius_meters`, `work_days` (JSON per hari: `check_in_start`, `check_in_late_threshold`, `check_out_start`, `check_out_end`).
6. `holidays`: `id`, `date`, `description`.
7. `attendances`: `id`, `student_id`, `date` (Indexed), `check_in_time`, `check_in_status` (`ONTIME`, `LATE`), `late_minutes`, `check_in_lat`, `check_in_lng`, `check_in_distance`, `check_in_photo`, `check_out_time`, `check_out_lat`, `check_out_lng`, `check_out_photo`, `attendance_type` (`PRESENT`, `SICK`, `PERMISSION`, `DISPENSATION`, `ABSENT`), `override_by`, `override_note`.
8. `leave_requests`: `id`, `student_id`, `type` (`SICK`, `PERMISSION`, `DISPENSATION`), `start_date`, `end_date`, `reason`, `attachment_path`, `status` (`PENDING`, `APPROVED`, `REJECTED`), `reviewed_by`, `reviewed_at`, `rejection_note`.

