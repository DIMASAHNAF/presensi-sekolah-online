<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class PresensiBulananKelasExport implements WithTitle, WithEvents
{
    protected $kelas, $bulanDate, $siswaList, $hariList, $activeDates, $matrix, $sesiList;

    // Kolom tetap: A=No, B=NISN, C=Nama → tanggal mulai kolom D (index 4)
    const COL_NO   = 1; // A
    const COL_NISN = 2; // B
    const COL_NAMA = 3; // C
    const COL_DATE_START = 4; // D

    public function __construct($kelas, $bulanDate, $siswaList, $hariList, $activeDates, $matrix, $sesiList)
    {
        $this->kelas       = $kelas;
        $this->bulanDate   = $bulanDate;
        $this->siswaList   = $siswaList;
        $this->hariList    = $hariList;
        $this->activeDates = $activeDates;
        $this->matrix      = $matrix;
        $this->sesiList    = $sesiList;
    }

    public function title(): string
    {
        return substr('Bulanan ' . $this->kelas->nama_kelas, 0, 31);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $this->buildSheet($sheet);
            },
        ];
    }

    private function buildSheet(Worksheet $sheet): void
    {
        $daftarHari  = collect($this->hariList)->values();
        $jumlahHari  = $daftarHari->count();
        $totalCols   = self::COL_DATE_START - 1 + $jumlahHari + 6; // +6 = H,S,I,A,%,Keterangan
        $lastColLetter = Coordinate::stringFromColumnIndex($totalCols);

        // ─── Kolom rekap (H, S, I, A, %, Ket) ─────────────────────────────
        $colH   = Coordinate::stringFromColumnIndex(self::COL_DATE_START + $jumlahHari);
        $colS   = Coordinate::stringFromColumnIndex(self::COL_DATE_START + $jumlahHari + 1);
        $colI   = Coordinate::stringFromColumnIndex(self::COL_DATE_START + $jumlahHari + 2);
        $colA   = Coordinate::stringFromColumnIndex(self::COL_DATE_START + $jumlahHari + 3);
        $colPct = Coordinate::stringFromColumnIndex(self::COL_DATE_START + $jumlahHari + 4);
        $colKet = Coordinate::stringFromColumnIndex(self::COL_DATE_START + $jumlahHari + 5);

        // ================================================================
        // ROW 1: Nama Sekolah (kop surat)
        // ================================================================
        $sheet->setCellValue('A1', config('app.school_name', 'SMKN 1 BERINGIN'));
        $sheet->mergeCells("A1:{$lastColLetter}1");
        $this->styleCell($sheet, 'A1', [
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // ================================================================
        // ROW 2: Judul Rekap
        // ================================================================
        $sheet->setCellValue('A2', 'REKAPITULASI PRESENSI BULANAN KELAS');
        $sheet->mergeCells("A2:{$lastColLetter}2");
        $this->styleCell($sheet, 'A2', [
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // ================================================================
        // ROW 3: Periode
        // ================================================================
        $sheet->setCellValue('A3', 'Laporan Kehadiran Siswa — Periode ' . $this->bulanDate->translatedFormat('F Y'));
        $sheet->mergeCells("A3:{$lastColLetter}3");
        $this->styleCell($sheet, 'A3', [
            'font'      => ['size' => 10, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(14);

        // ================================================================
        // ROW 4: Info Kelas (3 kelompok)
        // ================================================================
        $colMid1 = Coordinate::stringFromColumnIndex(intval($totalCols / 3));
        $colMid2 = Coordinate::stringFromColumnIndex(intval($totalCols * 2 / 3));

        $sheet->setCellValue('A4', "Kelas: {$this->kelas->nama_kelas}   |   Wali Kelas: " . ($this->kelas->wali_kelas?->name ?? '-'));
        $sheet->mergeCells("A4:{$colMid1}4");
        $sheet->setCellValue(Coordinate::stringFromColumnIndex(intval($totalCols / 3) + 1) . '4',
            "Bulan/Tahun: " . $this->bulanDate->translatedFormat('F Y') . "   |   Jumlah Siswa: " . $this->siswaList->count() . " Orang");
        $sheet->mergeCells((Coordinate::stringFromColumnIndex(intval($totalCols / 3) + 1)) . "4:{$colMid2}4");
        $sheet->setCellValue(Coordinate::stringFromColumnIndex(intval($totalCols * 2 / 3) + 1) . '4',
            "Hari Efektif: " . count($this->activeDates) . " Hari   |   Dicetak: " . now()->translatedFormat('d F Y'));
        $sheet->mergeCells((Coordinate::stringFromColumnIndex(intval($totalCols * 2 / 3) + 1)) . "4:{$lastColLetter}4");

        $this->styleRange($sheet, "A4:{$lastColLetter}4", [
            'font'      => ['size' => 9, 'bold' => true, 'color' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(16);

        // ================================================================
        // ROW 5: Empty separator
        // ================================================================
        $sheet->getRowDimension(5)->setRowHeight(4);

        // ================================================================
        // ROW 6: Header utama (No, NISN, Nama, Tanggal 1-N, Total Rekap, %, Ket)
        // ================================================================
        $dateStartColLetter = Coordinate::stringFromColumnIndex(self::COL_DATE_START);
        $dateEndColLetter   = Coordinate::stringFromColumnIndex(self::COL_DATE_START + $jumlahHari - 1);

        // No, NISN, Nama — rowspan 2 → merge row 6 dan 7
        $sheet->setCellValue('A6', 'No');
        $sheet->mergeCells('A6:A7');

        $sheet->setCellValue('B6', 'NISN');
        $sheet->mergeCells('B6:B7');

        $sheet->setCellValue('C6', 'Nama Lengkap Siswa');
        $sheet->mergeCells('C6:C7');

        // Header Tanggal (colspan = jumlahHari)
        if ($jumlahHari > 0) {
            $sheet->setCellValue("{$dateStartColLetter}6", 'Tanggal (1 - ' . $jumlahHari . ')');
            $sheet->mergeCells("{$dateStartColLetter}6:{$dateEndColLetter}6");
        }

        // Header Total Rekap
        $sheet->setCellValue("{$colH}6", 'Total Rekap');
        $sheet->mergeCells("{$colH}6:{$colA}6");

        // % dan Keterangan — rowspan 2
        $sheet->setCellValue("{$colPct}6", '% Hadir');
        $sheet->mergeCells("{$colPct}6:{$colPct}7");

        $sheet->setCellValue("{$colKet}6", 'Keterangan');
        $sheet->mergeCells("{$colKet}6:{$colKet}7");

        $this->styleRange($sheet, "A6:{$lastColLetter}6", [
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '93C5FD']]],
        ]);
        $sheet->getRowDimension(6)->setRowHeight(20);

        // ================================================================
        // ROW 7: Sub-header tanggal dan H/S/I/A
        // ================================================================
        foreach ($daftarHari as $idx => $h) {
            $col = Coordinate::stringFromColumnIndex(self::COL_DATE_START + $idx);
            $namaHari = mb_strtoupper(substr($h['nama'], 0, 3));
            $sheet->setCellValue("{$col}7", $h['tgl']);

            // Warna berbeda untuk weekend
            $fillColor = $h['libur'] ? 'FEE2E2' : 'EFF6FF';
            $fontColor = $h['libur'] ? 'B91C1C' : '1E3A8A';
            $this->styleCell($sheet, "{$col}7", [
                'font'      => ['bold' => true, 'size' => 8, 'color' => ['rgb' => $fontColor]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
            ]);
        }

        // Sub-header H S I A
        $rekapHeaders = [
            $colH => ['H', 'DCFCE7', '166534'],
            $colS => ['S', 'FFEDD5', '9A3412'],
            $colI => ['I', 'FEF9C3', '854D0E'],
            $colA => ['A', 'FEE2E2', '991B1B'],
        ];
        foreach ($rekapHeaders as $col => [$label, $bg, $fg]) {
            $sheet->setCellValue("{$col}7", $label);
            $this->styleCell($sheet, "{$col}7", [
                'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $fg]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
            ]);
        }

        $this->styleRange($sheet, "A7:{$lastColLetter}7", [
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '93C5FD']]],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(16);

        // ================================================================
        // ROW 8+: DATA SISWA
        // ================================================================
        $grandH = 0; $grandS = 0; $grandI = 0; $grandA = 0;
        $perDateCount = [];
        foreach ($daftarHari as $h) {
            $perDateCount[$h['date']] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
        }

        $dataRow = 8;

        if ($this->siswaList->isEmpty()) {
            $sheet->setCellValue("A{$dataRow}", 'Belum ada data siswa di kelas ini.');
            $sheet->mergeCells("A{$dataRow}:{$lastColLetter}{$dataRow}");
            $this->styleCell($sheet, "A{$dataRow}", [
                'font'      => ['italic' => true, 'color' => ['rgb' => '94A3B8']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $dataRow++;
        } else {
            foreach ($this->siswaList as $idx => $siswa) {
                $countH = 0; $countS = 0; $countI = 0; $countA = 0;
                $isEven = ($idx % 2 === 1);
                $rowBg  = $isEven ? 'F8FAFC' : 'FFFFFF';

                // No, NISN, Nama
                $sheet->setCellValue("A{$dataRow}", $idx + 1);
                $sheet->setCellValue("B{$dataRow}", $siswa->nisn ?: '-');
                $sheet->setCellValue("C{$dataRow}", $siswa->name);

                $this->styleRange($sheet, "A{$dataRow}:{$lastColLetter}{$dataRow}", [
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rowBg]],
                ]);
                $this->styleCell($sheet, "A{$dataRow}", [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'font' => ['size' => 9],
                ]);
                $this->styleCell($sheet, "B{$dataRow}", [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'font' => ['size' => 9, 'color' => ['rgb' => '64748B']],
                ]);
                $this->styleCell($sheet, "C{$dataRow}", [
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '0F172A']],
                ]);

                // Kolom Tanggal
                foreach ($daftarHari as $i => $h) {
                    $col   = Coordinate::stringFromColumnIndex(self::COL_DATE_START + $i);
                    $tglStr = $h['date'];
                    $st    = $this->matrix[$siswa->id][$tglStr] ?? null;

                    if ($st === 'hadir') {
                        $countH++; $perDateCount[$tglStr]['H']++;
                        $cellVal = 'H'; $bg = 'DCFCE7'; $fg = '166534';
                    } elseif ($st === 'sakit') {
                        $countS++; $perDateCount[$tglStr]['S']++;
                        $cellVal = 'S'; $bg = 'FFEDD5'; $fg = '9A3412';
                    } elseif ($st === 'izin') {
                        $countI++; $perDateCount[$tglStr]['I']++;
                        $cellVal = 'I'; $bg = 'FEF9C3'; $fg = '854D0E';
                    } elseif ($st === 'alpa') {
                        $countA++; $perDateCount[$tglStr]['A']++;
                        $cellVal = 'A'; $bg = 'FEE2E2'; $fg = '991B1B';
                    } else {
                        $cellVal = '-'; $bg = $h['libur'] ? 'FEF2F2' : $rowBg; $fg = 'CBD5E1';
                    }

                    $sheet->setCellValue("{$col}{$dataRow}", $cellVal);
                    $this->styleCell($sheet, "{$col}{$dataRow}", [
                        'font'      => ['bold' => ($cellVal !== '-'), 'size' => 9, 'color' => ['rgb' => $fg]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                    ]);
                }

                // Rekap H S I A
                $grandH += $countH; $grandS += $countS; $grandI += $countI; $grandA += $countA;
                $totalSesi = $countH + $countS + $countI + $countA;
                $pct = $totalSesi > 0 ? round(($countH / $totalSesi) * 100) : 0;

                $sheet->setCellValue("{$colH}{$dataRow}", $countH);
                $sheet->setCellValue("{$colS}{$dataRow}", $countS);
                $sheet->setCellValue("{$colI}{$dataRow}", $countI);
                $sheet->setCellValue("{$colA}{$dataRow}", $countA);
                $sheet->setCellValue("{$colPct}{$dataRow}", $pct . '%');

                if ($totalSesi === 0) $ket = 'Belum ada sesi';
                elseif ($pct === 100) $ket = 'Sangat Baik';
                elseif ($pct >= 85)   $ket = 'Baik';
                elseif ($pct >= 75)   $ket = 'Cukup';
                else                  $ket = 'Perlu Pembinaan';
                $sheet->setCellValue("{$colKet}{$dataRow}", $ket);

                // Rekap cell colors
                $this->styleCell($sheet, "{$colH}{$dataRow}", ['font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '16A34A']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                $this->styleCell($sheet, "{$colS}{$dataRow}", ['font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'EA580C']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                $this->styleCell($sheet, "{$colI}{$dataRow}", ['font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'CA8A04']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                $this->styleCell($sheet, "{$colA}{$dataRow}", ['font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'DC2626']], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                $pctColor = $pct >= 85 ? '16A34A' : ($pct >= 75 ? 'CA8A04' : 'DC2626');
                $this->styleCell($sheet, "{$colPct}{$dataRow}", ['font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $pctColor]], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);
                $this->styleCell($sheet, "{$colKet}{$dataRow}", ['font' => ['size' => 9, 'color' => ['rgb' => $pctColor]], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]]);

                // Border seluruh baris
                $this->styleRange($sheet, "A{$dataRow}:{$lastColLetter}{$dataRow}", [
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                ]);
                $sheet->getRowDimension($dataRow)->setRowHeight(16);
                $dataRow++;
            }
        }

        // ================================================================
        // ROW Total
        // ================================================================
        $totalRow = $dataRow;
        $sheet->setCellValue("A{$totalRow}", 'TOTAL');
        $sheet->mergeCells("A{$totalRow}:C{$totalRow}");

        foreach ($daftarHari as $i => $h) {
            $col   = Coordinate::stringFromColumnIndex(self::COL_DATE_START + $i);
            $dCount = $perDateCount[$h['date']];
            $val   = $dCount['H'] . 'H';
            if ($dCount['A'] > 0) $val .= '/' . $dCount['A'] . 'A';
            $sheet->setCellValue("{$col}{$totalRow}", $val);
            $this->styleCell($sheet, "{$col}{$totalRow}", [
                'font'      => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '15803D']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
            ]);
        }

        $grandTotal = $grandH + $grandS + $grandI + $grandA;
        $grandPct   = $grandTotal > 0 ? round(($grandH / $grandTotal) * 100) : 0;

        $sheet->setCellValue("{$colH}{$totalRow}", $grandH);
        $sheet->setCellValue("{$colS}{$totalRow}", $grandS);
        $sheet->setCellValue("{$colI}{$totalRow}", $grandI);
        $sheet->setCellValue("{$colA}{$totalRow}", $grandA);
        $sheet->setCellValue("{$colPct}{$totalRow}", $grandPct . '%');
        $sheet->setCellValue("{$colKet}{$totalRow}", 'Rata-rata Kelas');

        $this->styleRange($sheet, "A{$totalRow}:{$lastColLetter}{$totalRow}", [
            'font'    => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '0F172A']],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN,  'color' => ['rgb' => '93C5FD']],
                'top'        => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1D4ED8']],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $this->styleCell($sheet, "A{$totalRow}", [
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension($totalRow)->setRowHeight(18);

        // ================================================================
        // ROW Keterangan Legend
        // ================================================================
        $legendRow = $totalRow + 2;
        $sheet->setCellValue("A{$legendRow}", 'Keterangan: H = Hadir  |  S = Sakit  |  I = Izin  |  A = Alpa (Tanpa Keterangan)  |  - = Tidak ada sesi / Libur');
        $sheet->mergeCells("A{$legendRow}:{$lastColLetter}{$legendRow}");
        $this->styleCell($sheet, "A{$legendRow}", [
            'font'      => ['italic' => true, 'size' => 8.5, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getRowDimension($legendRow)->setRowHeight(14);

        // ================================================================
        // ROW Tanda Tangan
        // ================================================================
        $ttdRow = $legendRow + 3;
        $halfCol = Coordinate::stringFromColumnIndex(intval($totalCols / 2));
        $halfColNext = Coordinate::stringFromColumnIndex(intval($totalCols / 2) + 1);

        $sheet->setCellValue("A{$ttdRow}", 'Mengetahui,');
        $sheet->mergeCells("A{$ttdRow}:{$halfCol}{$ttdRow}");
        $sheet->setCellValue("{$halfColNext}{$ttdRow}", 'Beringin, ' . now()->translatedFormat('d F Y'));
        $sheet->mergeCells("{$halfColNext}{$ttdRow}:{$lastColLetter}{$ttdRow}");

        $sheet->setCellValue('A' . ($ttdRow + 1), 'Kepala Sekolah SMKN 1 Beringin');
        $sheet->mergeCells('A' . ($ttdRow + 1) . ":{$halfCol}" . ($ttdRow + 1));
        $sheet->setCellValue("{$halfColNext}" . ($ttdRow + 1), 'Wali Kelas ' . $this->kelas->nama_kelas);
        $sheet->mergeCells("{$halfColNext}" . ($ttdRow + 1) . ":{$lastColLetter}" . ($ttdRow + 1));

        $ttdSpaceRow = $ttdRow + 5;
        $sheet->setCellValue("A{$ttdSpaceRow}", 'H. ILYAS, M.Pd');
        $sheet->mergeCells("A{$ttdSpaceRow}:{$halfCol}{$ttdSpaceRow}");
        $sheet->setCellValue("{$halfColNext}{$ttdSpaceRow}", $this->kelas->wali_kelas?->name ?? '________________________');
        $sheet->mergeCells("{$halfColNext}{$ttdSpaceRow}:{$lastColLetter}{$ttdSpaceRow}");

        $sheet->setCellValue('A' . ($ttdSpaceRow + 1), 'NIP. 19680512 199403 1 005');
        $sheet->mergeCells('A' . ($ttdSpaceRow + 1) . ":{$halfCol}" . ($ttdSpaceRow + 1));
        $sheet->setCellValue("{$halfColNext}" . ($ttdSpaceRow + 1), 'NIP/NIK. ' . ($this->kelas->wali_kelas?->nik ?? '-'));
        $sheet->mergeCells("{$halfColNext}" . ($ttdSpaceRow + 1) . ":{$lastColLetter}" . ($ttdSpaceRow + 1));

        $this->styleRange($sheet, "A{$ttdRow}:{$lastColLetter}" . ($ttdSpaceRow + 1), [
            'font'      => ['size' => 10, 'color' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $this->styleRange($sheet, "A{$ttdSpaceRow}:{$lastColLetter}{$ttdSpaceRow}", [
            'font' => ['bold' => true, 'underline' => Font::UNDERLINE_SINGLE],
        ]);

        // ================================================================
        // LEBAR KOLOM
        // ================================================================
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(14);
        $sheet->getColumnDimension('C')->setWidth(30);

        foreach ($daftarHari as $i => $h) {
            $col = Coordinate::stringFromColumnIndex(self::COL_DATE_START + $i);
            $sheet->getColumnDimension($col)->setWidth(4.5);
        }

        $sheet->getColumnDimension($colH)->setWidth(7);
        $sheet->getColumnDimension($colS)->setWidth(7);
        $sheet->getColumnDimension($colI)->setWidth(7);
        $sheet->getColumnDimension($colA)->setWidth(7);
        $sheet->getColumnDimension($colPct)->setWidth(9);
        $sheet->getColumnDimension($colKet)->setWidth(18);

        // ================================================================
        // FREEZE PANES (beku header + kolom nama)
        // ================================================================
        $sheet->freezePane("D8");

        // ================================================================
        // PAGE SETUP
        // ================================================================
        $sheet->getPageSetup()
            ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
            ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
            ->setFitToPage(true)
            ->setFitToWidth(1)
            ->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.3)->setRight(0.3);
    }

    // ──────────────────────────────────────────────────────────────────
    //  HELPERS
    // ──────────────────────────────────────────────────────────────────

    private function styleCell(Worksheet $sheet, string $cell, array $style): void
    {
        $sheet->getStyle($cell)->applyFromArray($this->buildStyle($style));
    }

    private function styleRange(Worksheet $sheet, string $range, array $style): void
    {
        $sheet->getStyle($range)->applyFromArray($this->buildStyle($style));
    }

    private function buildStyle(array $style): array
    {
        $result = [];

        if (isset($style['font'])) {
            $result['font'] = [];
            if (isset($style['font']['bold']))      $result['font']['bold']      = $style['font']['bold'];
            if (isset($style['font']['italic']))     $result['font']['italic']    = $style['font']['italic'];
            if (isset($style['font']['underline']))  $result['font']['underline'] = $style['font']['underline'];
            if (isset($style['font']['size']))       $result['font']['size']      = $style['font']['size'];
            if (isset($style['font']['color']))      $result['font']['color']     = ['argb' => 'FF' . ltrim($style['font']['color']['rgb'], '#')];
        }

        if (isset($style['alignment'])) {
            $result['alignment'] = $style['alignment'];
        }

        if (isset($style['fill'])) {
            $result['fill'] = [
                'fillType'   => $style['fill']['fillType'],
                'startColor' => ['argb' => 'FF' . ltrim($style['fill']['startColor']['rgb'], '#')],
            ];
        }

        if (isset($style['borders'])) {
            $result['borders'] = $style['borders'];
            foreach ($result['borders'] as &$borderDef) {
                if (is_array($borderDef) && isset($borderDef['color']['rgb'])) {
                    $borderDef['color'] = ['argb' => 'FF' . ltrim($borderDef['color']['rgb'], '#')];
                }
            }
        }

        return $result;
    }
}
