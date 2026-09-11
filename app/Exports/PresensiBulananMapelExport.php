<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class PresensiBulananMapelExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $kelas, $mapel, $bulanDate, $sesiList, $siswaList, $matrix, $guruNama, $guruNik;

    public function __construct($kelas, $mapel, $bulanDate, $sesiList, $siswaList, $matrix, $guruNama, $guruNik)
    {
        $this->kelas = $kelas;
        $this->mapel = $mapel;
        $this->bulanDate = $bulanDate;
        $this->sesiList = $sesiList;
        $this->siswaList = $siswaList;
        $this->matrix = $matrix;
        $this->guruNama = $guruNama;
        $this->guruNik = $guruNik;
    }

    public function view(): View
    {
        return view('dashboard.presensi.print-bulanan-mapel', [
            'kelas' => $this->kelas,
            'mapel' => $this->mapel,
            'bulanDate' => $this->bulanDate,
            'sesiList' => $this->sesiList,
            'siswaList' => $this->siswaList,
            'matrix' => $this->matrix,
            'guruNama' => $this->guruNama,
            'guruNik' => $this->guruNik
        ]);
    }

    public function title(): string
    {
        return substr('Mapel ' . $this->mapel->nama_mapel, 0, 31);
    }
}
