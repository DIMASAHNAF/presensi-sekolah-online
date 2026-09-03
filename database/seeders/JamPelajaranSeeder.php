<?php

namespace Database\Seeders;

use App\Models\JamPelajaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JamPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        JamPelajaran::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            // ============ SENIN (ada Upacara Bendera, jam berbeda) ============
            ['nomor' => 1,  'nama' => 'Pembiasaan (Kebersihan Kelas)', 'jam_mulai' => '07:00', 'jam_selesai' => '07:15', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => 'Pembiasaan pagi'],
            ['nomor' => 2,  'nama' => 'Les 1 (Upacara)',               'jam_mulai' => '07:15', 'jam_selesai' => '08:00', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => 'Upacara Bendera'],
            ['nomor' => 3,  'nama' => 'Les 2',                         'jam_mulai' => '08:00', 'jam_selesai' => '08:41', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 4,  'nama' => 'Les 3',                         'jam_mulai' => '08:41', 'jam_selesai' => '09:23', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 5,  'nama' => 'Les 4',                         'jam_mulai' => '09:23', 'jam_selesai' => '10:04', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 6,  'nama' => 'Les 5',                         'jam_mulai' => '10:04', 'jam_selesai' => '10:45', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 7,  'nama' => 'ISHOMA',                        'jam_mulai' => '10:45', 'jam_selesai' => '11:00', 'hari' => 'senin', 'is_istirahat' => true,  'keterangan' => 'Istirahat / Sholat / Makan'],
            ['nomor' => 8,  'nama' => 'Les 6',                         'jam_mulai' => '11:00', 'jam_selesai' => '11:41', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 9,  'nama' => 'Les 7',                         'jam_mulai' => '11:41', 'jam_selesai' => '12:22', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 10, 'nama' => 'ISHOMA 2',                      'jam_mulai' => '12:22', 'jam_selesai' => '13:12', 'hari' => 'senin', 'is_istirahat' => true,  'keterangan' => 'Istirahat / Sholat Dzuhur'],
            ['nomor' => 11, 'nama' => 'Les 8',                         'jam_mulai' => '13:12', 'jam_selesai' => '13:53', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 12, 'nama' => 'Les 9',                         'jam_mulai' => '13:53', 'jam_selesai' => '14:34', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 13, 'nama' => 'Les 10',                        'jam_mulai' => '14:34', 'jam_selesai' => '15:15', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 14, 'nama' => 'Les 11',                        'jam_mulai' => '15:15', 'jam_selesai' => '16:00', 'hari' => 'senin', 'is_istirahat' => false, 'keterangan' => null],

            // ============ SELASA – KAMIS (jam standar) ============
            ['nomor' => 1,  'nama' => 'Pembiasaan (Kebersihan Kelas)', 'jam_mulai' => '07:00', 'jam_selesai' => '07:15', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => 'Pembiasaan pagi'],
            ['nomor' => 2,  'nama' => 'Les 1',                         'jam_mulai' => '07:15', 'jam_selesai' => '07:56', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 3,  'nama' => 'Les 2',                         'jam_mulai' => '07:56', 'jam_selesai' => '08:37', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 4,  'nama' => 'Les 3',                         'jam_mulai' => '08:37', 'jam_selesai' => '09:18', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 5,  'nama' => 'Les 4',                         'jam_mulai' => '09:18', 'jam_selesai' => '09:59', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 6,  'nama' => 'Les 5',                         'jam_mulai' => '09:59', 'jam_selesai' => '10:40', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 7,  'nama' => 'ISHOMA',                        'jam_mulai' => '10:40', 'jam_selesai' => '11:00', 'hari' => 'semua', 'is_istirahat' => true,  'keterangan' => 'Istirahat / Sholat / Makan'],
            ['nomor' => 8,  'nama' => 'Les 6',                         'jam_mulai' => '11:00', 'jam_selesai' => '11:41', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 9,  'nama' => 'Les 7',                         'jam_mulai' => '11:41', 'jam_selesai' => '12:22', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 10, 'nama' => 'ISHOMA 2',                      'jam_mulai' => '12:22', 'jam_selesai' => '13:12', 'hari' => 'semua', 'is_istirahat' => true,  'keterangan' => 'Istirahat / Sholat Dzuhur'],
            ['nomor' => 11, 'nama' => 'Les 8',                         'jam_mulai' => '13:12', 'jam_selesai' => '13:53', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 12, 'nama' => 'Les 9',                         'jam_mulai' => '13:53', 'jam_selesai' => '14:34', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 13, 'nama' => 'Les 10',                        'jam_mulai' => '14:34', 'jam_selesai' => '15:15', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 14, 'nama' => 'Les 11',                        'jam_mulai' => '15:15', 'jam_selesai' => '16:00', 'hari' => 'semua', 'is_istirahat' => false, 'keterangan' => null],

            // ============ JUMAT (jam berbeda + kegiatan khusus) ============
            ['nomor' => 1,  'nama' => 'Senam / Pembiasaan Jumat',      'jam_mulai' => '07:00', 'jam_selesai' => '07:45', 'hari' => 'jumat', 'is_istirahat' => false, 'keterangan' => 'Senam Anak Hebat / Kegiatan Jumat Bersih'],
            ['nomor' => 2,  'nama' => 'Les 1',                         'jam_mulai' => '07:45', 'jam_selesai' => '08:25', 'hari' => 'jumat', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 3,  'nama' => 'Les 2',                         'jam_mulai' => '08:25', 'jam_selesai' => '09:05', 'hari' => 'jumat', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 4,  'nama' => 'Les 3',                         'jam_mulai' => '09:05', 'jam_selesai' => '09:45', 'hari' => 'jumat', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 5,  'nama' => 'Les 4',                         'jam_mulai' => '09:45', 'jam_selesai' => '10:25', 'hari' => 'jumat', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 6,  'nama' => 'ISHOMA',                        'jam_mulai' => '10:25', 'jam_selesai' => '10:40', 'hari' => 'jumat', 'is_istirahat' => true,  'keterangan' => 'Istirahat / Sholat Jumat'],
            ['nomor' => 7,  'nama' => 'Les 5',                         'jam_mulai' => '11:00', 'jam_selesai' => '11:20', 'hari' => 'jumat', 'is_istirahat' => false, 'keterangan' => 'Pengembangan Mandiri Berbasis Agama'],
            ['nomor' => 8,  'nama' => 'Les 6',                         'jam_mulai' => '11:20', 'jam_selesai' => '12:00', 'hari' => 'jumat', 'is_istirahat' => false, 'keterangan' => null],
            ['nomor' => 9,  'nama' => 'Kegiatan Ekstrakurikuler',      'jam_mulai' => '13:45', 'jam_selesai' => '16:00', 'hari' => 'jumat', 'is_istirahat' => false, 'keterangan' => 'Ekstrakurikuler'],
        ];

        foreach ($data as $row) {
            JamPelajaran::create($row);
        }
    }
}
