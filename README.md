# Sistem Absensi Sekolah Berbasis QR Code

Aplikasi sistem presensi mandiri untuk siswa menggunakan pemindai QR Code, dibangun dengan framework Laravel. Sistem ini dirancang untuk mempermudah guru dan sekolah dalam melacak kehadiran siswa secara harian.

## 🚀 Fitur Utama

### 📱 Panel Siswa
- **Scan QR Code Mandiri**: Siswa dapat melakukan presensi secara mandiri dari perangkat mereka dengan melakukan scan barcode yang ditampilkan oleh guru.
- **Riwayat Absensi Real-time**: Siswa dapat melihat riwayat kehadiran (Hadir, Sakit, Izin, Alpa) mereka secara seketika setelah berhasil melakukan presensi.
- **Auto-Refresh**: Halaman otomatis memperbarui data ketika siswa berhasil melakukan pemindaian (scan) ke sistem.

### 👨‍🏫 Panel Guru (Kelola Absensi)
- **Buat Sesi Kelas Harian**: Guru dapat membuka barcode sesi absensi khusus untuk suatu kelas di hari tersebut.
- **Dashboard Overview Interaktif**: Guru dapat melihat ringkasan statisik kehadiran kelas hari ini dengan tampilan *pop-out* modal tabel yang dinamis dan dapat dicari *(searchable)*.
- **Tutup & Kunci Barcode**: Sesi dapat ditutup secara manual, yang otomatis menonaktifkan barcode sehingga tidak dapat dipindai ulang oleh siswa yang terlambat atau membagikan screenshot.
- **Manajemen Kehadiran Manual**: Guru dapat mengubah status siswa (Hadir/Sakit/Izin/Alpa) jika ada siswa yang lupa absen, tidak masuk, atau telat.
- **Cetak Laporan**: Laporan kehadiran per sesi dapat dicetak (Print/PDF) dengan tampilan yang rapi tanpa perlu ekstensi *server* tambahan.

### 👑 Panel Admin (Manajemen Sistem)
- **Semua Fitur Guru**: Admin memiliki kendali penuh terhadap manajemen sesi absensi.
- **Manajemen Master Data**: Akses penuh untuk *Create, Read, Update, Delete (CRUD)* terhadap data **Kelas**, **Guru**, dan **Siswa**.
- **Log Perubahan Real-time (Zona Waktu Asia/Jakarta)**: Setiap kali status absen siswa diubah (contoh: dari Alpa ke Sakit), sistem akan mencatat nama guru yang merubah, kapan diubah, beserta keterangannya ke dalam *Log Perubahan* dengan zona waktu yang akurat. Log juga dapat difilter berdasarkan Kelas.
- **Reset Kehadiran (Undo Sesi)**: Jika terjadi kesalahan teknis pada suatu sesi, admin dapat mereset sesi tersebut ke kondisi semula (menghapus riwayat scan dan mereset status seluruh murid ke *Alpa*).
- **Hapus Semua Riwayat Absensi (Wipe)**: Fitur *Danger Zone* untuk membersihkan dan mengosongkan seluruh riwayat absensi dari *database* (cocok untuk pergantian semester).

## 🛠️ Stack Teknologi
- **Backend**: Laravel 11, PHP 8.2+
- **Frontend**: Blade Templating, Alpine.js (Reaktivitas UI), Tailwind CSS v3 (Desain Sistem)
- **Database**: MySQL
- **Animasi & Interaksi**: AOS (Animate On Scroll), HTML5-QRCode (Web QR Scanner)

## 📦 Panduan Instalasi
1. Clone repositori ini.
2. Jalankan `composer install` dan `npm install`.
3. Duplikat file `.env.example` menjadi `.env` lalu atur konfigurasi database kamu (pastikan `APP_TIMEZONE=Asia/Jakarta`).
4. Jalankan `php artisan key:generate`.
5. Jalankan migrasi dan *seeder* awal untuk membuat kelas & akun default: `php artisan migrate --seed`.
6. Kompilasi aset frontend: `npm run dev` atau `npm run build`.
7. Jalankan *server* lokal dengan `php artisan serve`.

## 📌 Catatan Keamanan Barcode
Barcode yang digenerate oleh sistem dijamin **unik** per sesi pembuatannya. Menggunakan *token hash random* sehingga mustahil bagi siswa untuk memprediksi token absensi. Barcode juga memiliki masa kedaluwarsa 30 menit. Jika guru mengakhiri kelas, modal barcode akan hilang dan dikunci sehingga tidak dapat digunakan kembali.
