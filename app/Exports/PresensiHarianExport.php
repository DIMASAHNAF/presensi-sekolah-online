<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresensiHarianExport implements FromView, ShouldAutoSize, WithStyles, WithTitle
{
    protected $kelas, $tanggal, $sesiList, $siswaList;

    public function __construct($kelas, $tanggal, $sesiList, $siswaList)
    {
        $this->kelas = $kelas;
        $this->tanggal = $tanggal;
        $this->sesiList = $sesiList;
        $this->siswaList = $siswaList;
    }

    public function view(): View
    {
        return view('dashboard.presensi.print-harian', [
            'kelas' => $this->kelas,
            'tanggal' => $this->tanggal,
            'sesiList' => $this->sesiList,
            'siswaList' => $this->siswaList
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return substr('Harian ' . $this->kelas->nama_kelas, 0, 31);
    }
}
