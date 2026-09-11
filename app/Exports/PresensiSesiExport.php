<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresensiSesiExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $sesiPresensi;

    public function __construct($sesiPresensi)
    {
        $this->sesiPresensi = $sesiPresensi;
    }

    public function view(): View
    {
        return view('dashboard.presensi.print', [
            'sesiPresensi' => $this->sesiPresensi
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
