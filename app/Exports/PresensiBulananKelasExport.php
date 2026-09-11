<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PresensiBulananKelasExport implements FromView, ShouldAutoSize
{
    protected $kelas, $bulanDate, $siswaList, $hariList, $activeDates, $matrix, $sesiList;

    public function __construct($kelas, $bulanDate, $siswaList, $hariList, $activeDates, $matrix, $sesiList)
    {
        $this->kelas = $kelas;
        $this->bulanDate = $bulanDate;
        $this->siswaList = $siswaList;
        $this->hariList = $hariList;
        $this->activeDates = $activeDates;
        $this->matrix = $matrix;
        $this->sesiList = $sesiList;
    }

    public function view(): View
    {
        return view('dashboard.presensi.print-bulanan-kelas', [
            'kelas' => $this->kelas,
            'bulanDate' => $this->bulanDate,
            'siswaList' => $this->siswaList,
            'hariList' => $this->hariList,
            'activeDates' => $this->activeDates,
            'matrix' => $this->matrix,
            'sesiList' => $this->sesiList
        ]);
    }
}
