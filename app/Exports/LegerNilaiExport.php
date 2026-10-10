<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class LegerNilaiExport implements FromView, ShouldAutoSize, WithTitle, WithColumnWidths
{
    protected $kelas;
    protected $mapel;
    protected $legerData;
    protected $statsLeger;
    protected $guru;

    public function __construct($kelas, $mapel, $legerData, $statsLeger, $guru)
    {
        $this->kelas      = $kelas;
        $this->mapel      = $mapel;
        $this->legerData  = $legerData;
        $this->statsLeger = $statsLeger;
        $this->guru       = $guru;
    }

    public function view(): View
    {
        return view('dashboard.nilai.excel-leger', [
            'kelas'      => $this->kelas,
            'mapel'      => $this->mapel,
            'legerData'  => $this->legerData,
            'statsLeger' => $this->statsLeger,
            'guru'       => $this->guru,
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 16,  // NISN
            'C' => 34,  // Nama Siswa
            'D' => 12,  // Tugas
            'E' => 12,  // UH
            'F' => 14,  // Praktik
            'G' => 12,  // PTS
            'H' => 12,  // PAS
            'I' => 14,  // Nilai Akhir
            'J' => 10,  // Predikat
            'K' => 16,  // Status
        ];
    }

    public function title(): string
    {
        $namaKelas = $this->kelas ? $this->kelas->nama_kelas : 'Leger Nilai';
        return substr('Leger ' . $namaKelas, 0, 31);
    }
}
