<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mapel = [
            'Agama', 'Bahasa Indonesia', 'Bahasa Inggris', 'Matematika', 'Pancasila', 'Sejarah', 
            'PJOK', 'Keamanan', 'Perencanaan', 'Mobile', 'Basis Data', 'Kewirausahaan (KWU)',
            'Konfigurasi', 'IoT', 'Koding dan KA', 'Administrasi', 'Print Design', 'UI/UX',
            'GIM', 'Pemrograman Web', 'Dasar TJKT', 'Dasar PPLG', 'Proyek IPA', 'Proyek IPS',
            'Seni Musik', 'Seni Tari', 'Food & Bev', 'FO', 'Barista', 'Body Treatment', 
            'Dasar Kecantikan', 'Dasar Busana', 'MICE', 'Tarif', 'Bulu Mata', 'Penataan & Pemangkasan',
            'Menjahit', 'K3LH', 'Mandarin', 'Digital Marketing'
        ];

        foreach ($mapel as $m) {
            \App\Models\MataPelajaran::firstOrCreate(['nama_mapel' => $m]);
        }
    }
}
