<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PresensiBulananKelasExport implements FromView, WithTitle, WithEvents, WithStyles
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

    public function title(): string
    {
        return substr('Bulanan ' . $this->kelas->nama_kelas, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header area (SMKN 1 Beringin, etc)
            1    => ['font' => ['bold' => true, 'size' => 14]],
            2    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Jarak kolom No, NISN, Nama
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(35);

                // Ada berapa kolom hari?
                // Default width untuk tanggal (D sampai akhir bulan)
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                // Kita asumsikan kolom tanggal dimulai dari D (index 4)
                // Kita akan loop mengubah lebarnya jadi kotak-kotak kecil seperti buku absen
                for ($col = 'D'; $col !== $highestColumn; $col++) {
                    $sheet->getColumnDimension($col)->setWidth(4);
                }
                $sheet->getColumnDimension($highestColumn)->setWidth(4);

                // Rapikan kolom Rekap di ujung (H, S, I, A, %, Keterangan)
                // Kita perlebar sedikit 6 kolom terakhir dari highestColumn
                $cols = [];
                $curr = $highestColumn;
                for($i = 0; $i < 6; $i++) {
                    $cols[] = $curr;
                    // prev column (PHP doesn't have prev char natively for AA, so we do it by converting to index)
                    // Let's just set default widths and then target borders.
                }

                // Beri Border ke seluruh tabel (asumsi tabel data dimulai baris 6 atau 7)
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ]
                ]);
            },
        ];
    }
}
