<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresensiSesiExport implements FromView, ShouldAutoSize, WithStyles, WithTitle, \Maatwebsite\Excel\Concerns\WithColumnWidths
{
    protected $sesiPresensi;

    public function __construct($sesiPresensi)
    {
        $this->sesiPresensi = $sesiPresensi;
    }

    public function view(): View
    {
        return view('dashboard.presensi.excel', [
            'sesiPresensi' => $this->sesiPresensi
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 15,
            'D' => 12,
            'E' => 45,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return substr('Sesi ' . $this->sesiPresensi->kelas->nama_kelas, 0, 31);
    }
}
