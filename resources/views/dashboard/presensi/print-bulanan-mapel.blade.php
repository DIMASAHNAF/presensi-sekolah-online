<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi Mapel {{ $mapel->nama_mapel }} - {{ $kelas->nama_kelas }} - {{ $bulanDate->translatedFormat('F Y') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 24px;
            font-size: 11px;
            background: #fff;
        }

        /* ===== KOP SURAT ===== */
        .kop {
            text-align: center;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .kop h1 {
            font-size: 16px;
            font-weight: 800;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
        }
        .kop h2 {
            font-size: 13px;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #1e40af;
            text-transform: uppercase;
        }
        .mapel-pill {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            font-weight: 800;
            font-size: 12px;
            padding: 3px 14px;
            border-radius: 20px;
            margin-top: 4px;
        }

        /* ===== INFO METADATA ===== */
        .info-grid {
            display: flex;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 16px;
            margin-bottom: 16px;
            font-size: 11px;
        }
        .info-col table td {
            padding: 2px 6px 2px 0;
        }
        .info-col table td:first-child {
            color: #64748b;
            font-weight: 500;
        }
        .info-col table td:last-child {
            font-weight: 700;
            color: #0f172a;
        }

        /* ===== MAIN TABLE ===== */
        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 4px;
            text-align: center;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
        }
        table.data-table th.th-main {
            background-color: #e2e8f0;
        }
        table.data-table th.th-pertemuan {
            background-color: #eff6ff;
            color: #1e40af;
        }
        table.data-table td.nama {
            text-align: left;
            padding-left: 8px;
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
        }
        table.data-table td.nis {
            font-size: 9.5px;
            color: #64748b;
        }

        /* Badge status */
        .badge-h { background: #dcfce7; color: #15803d; font-weight: 700; padding: 2px 5px; border-radius: 4px; }
        .badge-s { background: #ffedd5; color: #c2410c; font-weight: 700; padding: 2px 5px; border-radius: 4px; }
        .badge-i { background: #fef9c3; color: #a16207; font-weight: 700; padding: 2px 5px; border-radius: 4px; }
        .badge-a { background: #fee2e2; color: #b91c1c; font-weight: 700; padding: 2px 5px; border-radius: 4px; }
        .badge-off { color: #cbd5e1; font-weight: normal; }

        /* Rekap highlight */
        .rekap-h { color: #16a34a; font-weight: 700; }
        .rekap-s { color: #ea580c; font-weight: 700; }
        .rekap-i { color: #ca8a04; font-weight: 700; }
        .rekap-a { color: #dc2626; font-weight: 700; }
        .pct-good { color: #16a34a; font-weight: 800; }
        .pct-warn { color: #ca8a04; font-weight: 800; }
        .pct-danger { color: #dc2626; font-weight: 800; }

        /* Row total */
        tr.total-row {
            background-color: #f8fafc;
            font-weight: 700;
        }
        tr.total-row td {
            border-top: 2px solid #94a3b8;
        }

        /* ===== PERTEMUAN DETAIL LIST ===== */
        .pertemuan-info {
            margin-top: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 10px;
        }

        /* ===== SUMMARY & STATS ===== */
        .summary-cards {
            display: flex;
            gap: 12px;
            margin-top: 14px;
            margin-bottom: 20px;
        }
        .sum-card {
            flex: 1;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: #fafafa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sum-card .label { font-size: 10px; color: #64748b; font-weight: 600; }
        .sum-card .val { font-size: 13px; font-weight: 800; }

        /* ===== SIGNATURE ===== */
        .signature-container {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 230px;
            line-height: 1.5;
        }
        .signature-space {
            height: 60px;
        }
        .signature-name {
            font-weight: 700;
            text-decoration: underline;
            color: #0f172a;
        }

        /* ===== BUTTONS ===== */
        .no-print {
            margin-bottom: 16px;
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print { background: #1d4ed8; color: #fff; }
        .btn-back  { background: #e2e8f0; color: #334155; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
    <a class="btn btn-back" href="javascript:history.back()">← Kembali ke Dashboard</a>
</div>

{{-- KOP SURAT --}}
<div class="kop">
    <h1>{{ config('app.school_name', 'SMKN 1 BERINGIN') }}</h1>
    <h2>REKAPITULASI PRESENSI MATA PELAJARAN</h2>
    <div><span class="mapel-pill">{{ strtoupper($mapel->nama_mapel) }}</span></div>
</div>

{{-- INFO MAPEL & KELAS --}}
<div class="info-grid">
    <div class="info-col">
        <table>
            <tr><td>Mata Pelajaran</td><td>: <strong>{{ $mapel->nama_mapel }}</strong></td></tr>
            <tr><td>Guru Pengampu</td><td>: {{ $guruNama }}</td></tr>
        </table>
    </div>
    <div class="info-col">
        <table>
            <tr><td>Kelas</td><td>: {{ $kelas->nama_kelas }}</td></tr>
            <tr><td>Bulan / Tahun</td><td>: {{ $bulanDate->translatedFormat('F Y') }}</td></tr>
        </table>
    </div>
    <div class="info-col">
        <table>
            <tr><td>Jumlah Siswa</td><td>: {{ $siswaList->count() }} Orang</td></tr>
            <tr><td>Total Pertemuan</td><td>: {{ $sesiList->count() }} Pertemuan</td></tr>
        </table>
    </div>
</div>

@php
    $totalPertemuan = $sesiList->count();
@endphp

<div class="table-wrap">
<table class="data-table">
    <thead>
        <tr>
            <th rowspan="2" class="th-main" style="width: 28px;">No</th>
            <th rowspan="2" class="th-main" style="width: 90px;">NISN</th>
            <th rowspan="2" class="th-main" style="text-align: left; padding-left: 8px; min-width: 170px;">Nama Siswa</th>
            
            @if($totalPertemuan > 0)
                <th colspan="{{ $totalPertemuan }}" class="th-pertemuan">
                    Presensi Pertemuan Tatap Muka
                </th>
            @else
                <th class="th-pertemuan">Pertemuan</th>
            @endif

            <th colspan="4" class="th-main">Total Rekap</th>
            <th rowspan="2" class="th-main" style="width: 48px;">% Hadir</th>
            <th rowspan="2" class="th-main" style="width: 110px;">Status Keaktifan</th>
        </tr>
        <tr>
            @forelse($sesiList as $idx => $sesi)
                <th style="min-width: 45px;" class="th-pertemuan">
                    <div style="font-weight: 800; font-size: 10px;">P.{{ $idx + 1 }}</div>
                    <div style="font-size: 8px; font-weight: normal; color: #475569;">{{ $sesi->tanggal->format('d/m') }}</div>
                    @if($sesi->jam_pelajaran)
                        <div style="font-size: 7.5px; color: #2563eb; font-weight: 600;">{{ $sesi->jam_pelajaran }}</div>
                    @endif
                </th>
            @empty
                <th style="color: #94a3b8; font-weight: normal; font-size: 9px;">Belum Ada Pertemuan</th>
            @endforelse

            <th style="width: 28px; background: #dcfce7; color: #166534;">H</th>
            <th style="width: 28px; background: #ffedd5; color: #9a3412;">S</th>
            <th style="width: 28px; background: #fef9c3; color: #854d0e;">I</th>
            <th style="width: 28px; background: #fee2e2; color: #991b1b;">A</th>
        </tr>
    </thead>
    <tbody>
        @php
            $grandH = 0; $grandS = 0; $grandI = 0; $grandA = 0;
            $perSesiCount = [];
            foreach($sesiList as $sesi) {
                $perSesiCount[$sesi->id] = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];
            }
        @endphp

        @forelse($siswaList as $idx => $siswa)
        @php
            $countH = 0; $countS = 0; $countI = 0; $countA = 0;
        @endphp
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td class="nis">{{ $siswa->nisn ?: '-' }}</td>
            <td class="nama">{{ $siswa->name }}</td>

            @forelse($sesiList as $sesi)
                @php
                    $st = $matrix[$siswa->id][$sesi->id] ?? null;

                    if ($st === 'hadir') {
                        $countH++;
                        $perSesiCount[$sesi->id]['H']++;
                    } elseif ($st === 'sakit') {
                        $countS++;
                        $perSesiCount[$sesi->id]['S']++;
                    } elseif ($st === 'izin') {
                        $countI++;
                        $perSesiCount[$sesi->id]['I']++;
                    } elseif ($st === 'alpa') {
                        $countA++;
                        $perSesiCount[$sesi->id]['A']++;
                    }
                @endphp
                <td>
                    @if($st === 'hadir')
                        <span class="badge-h">H</span>
                    @elseif($st === 'sakit')
                        <span class="badge-s">S</span>
                    @elseif($st === 'izin')
                        <span class="badge-i">I</span>
                    @elseif($st === 'alpa')
                        <span class="badge-a">A</span>
                    @else
                        <span class="badge-off">-</span>
                    @endif
                </td>
            @empty
                <td style="color: #94a3b8;">-</td>
            @endforelse

            @php
                $grandH += $countH;
                $grandS += $countS;
                $grandI += $countI;
                $grandA += $countA;

                $pct = $totalPertemuan > 0 ? round(($countH / $totalPertemuan) * 100) : 0;
            @endphp

            <td class="rekap-h">{{ $countH }}</td>
            <td class="rekap-s">{{ $countS }}</td>
            <td class="rekap-i">{{ $countI }}</td>
            <td class="rekap-a">{{ $countA }}</td>
            <td>
                <span class="{{ $pct >= 85 ? 'pct-good' : ($pct >= 75 ? 'pct-warn' : 'pct-danger') }}">
                    {{ $pct }}%
                </span>
            </td>
            <td style="font-size: 9.5px; font-weight: 700;">
                @if($totalPertemuan == 0)
                    <span style="color: #94a3b8;">Belum Mulai</span>
                @elseif($pct >= 85)
                    <span style="color: #16a34a;">TUNTAS (A)</span>
                @elseif($pct >= 75)
                    <span style="color: #0284c7;">TUNTAS (B)</span>
                @elseif($pct >= 60)
                    <span style="color: #ca8a04;">TUGAS TAMBAHAN</span>
                @else
                    <span style="color: #dc2626;">PERLU REMEDIAL</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="{{ max($totalPertemuan, 1) + 8 }}" style="padding: 20px; color: #94a3b8;">
                Belum ada data siswa di kelas ini.
            </td>
        </tr>
        @endforelse

        {{-- ROW TOTAL --}}
        @if($siswaList->count() > 0 && $totalPertemuan > 0)
        <tr class="total-row">
            <td colspan="3" style="text-align: right; padding-right: 10px; font-weight: 800;">
                TOTAL HADIR PER PERTEMUAN:
            </td>
            @foreach($sesiList as $sesi)
                @php $sCount = $perSesiCount[$sesi->id]; @endphp
                <td style="font-size: 8.5px; line-height: 1.2;">
                    <span style="color: #15803d;">{{ $sCount['H'] }}H</span>
                    @if($sCount['A'] > 0)<br><span style="color: #b91c1c;">{{ $sCount['A'] }}A</span>@endif
                </td>
            @endforeach
            <td class="rekap-h">{{ $grandH }}</td>
            <td class="rekap-s">{{ $grandS }}</td>
            <td class="rekap-i">{{ $grandI }}</td>
            <td class="rekap-a">{{ $grandA }}</td>
            @php
                $grandTotal = $grandH + $grandS + $grandI + $grandA;
                $grandPct = $grandTotal > 0 ? round(($grandH / $grandTotal) * 100) : 0;
            @endphp
            <td class="pct-good" style="font-size: 11px;">{{ $grandPct }}%</td>
            <td style="font-size: 9px; color: #64748b;">Rata-rata Kelas</td>
        </tr>
        @endif
    </tbody>
</table>
</div>

{{-- RINCIAN PERTEMUAN --}}
@if($sesiList->count() > 0)
<div class="pertemuan-info">
    <strong style="color: #1e40af;">Rincian Pertemuan Bulan Ini:</strong>
    <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 4px;">
        @foreach($sesiList as $idx => $sesi)
            <span>
                <strong>P.{{ $idx + 1 }}</strong>: {{ $sesi->tanggal->translatedFormat('d F Y') }} 
                @if($sesi->jam_pelajaran) ({{ $sesi->jam_pelajaran }}) @endif
                @if($sesi->guru) &bull; Pengampu: {{ $sesi->guru->name }} @endif
            </span>
        @endforeach
    </div>
</div>
@endif

{{-- SUMMARY BOXES --}}
<div class="summary-cards">
    <div class="sum-card" style="border-left: 4px solid #16a34a;">
        <span class="label">TOTAL HADIR</span>
        <span class="val" style="color: #16a34a;">{{ $grandH }} <span style="font-size: 9px; font-weight: normal;">siswa</span></span>
    </div>
    <div class="sum-card" style="border-left: 4px solid #ea580c;">
        <span class="label">TOTAL SAKIT</span>
        <span class="val" style="color: #ea580c;">{{ $grandS }} <span style="font-size: 9px; font-weight: normal;">siswa</span></span>
    </div>
    <div class="sum-card" style="border-left: 4px solid #ca8a04;">
        <span class="label">TOTAL IZIN</span>
        <span class="val" style="color: #ca8a04;">{{ $grandI }} <span style="font-size: 9px; font-weight: normal;">siswa</span></span>
    </div>
    <div class="sum-card" style="border-left: 4px solid #dc2626;">
        <span class="label">TOTAL ALPA</span>
        <span class="val" style="color: #dc2626;">{{ $grandA }} <span style="font-size: 9px; font-weight: normal;">siswa</span></span>
    </div>
</div>

<div style="font-size: 10px; color: #64748b; margin-top: 4px;">
    <strong>Keterangan:</strong> 
    <span class="badge-h">H</span> = Hadir &nbsp;|&nbsp; 
    <span class="badge-s">S</span> = Sakit &nbsp;|&nbsp; 
    <span class="badge-i">I</span> = Izin &nbsp;|&nbsp; 
    <span class="badge-a">A</span> = Alpa (Tanpa Keterangan) &nbsp;|&nbsp;
    P.n = Pertemuan Tatap Muka ke-n. Syarat ketuntasan minimal presensi adalah 75%.
</div>

{{-- TANDA TANGAN --}}
<div class="signature-container">
    <div class="signature-box">
        <div>Mengetahui,</div>
        <div>Wali Kelas {{ $kelas->nama_kelas }}</div>
        <div class="signature-space"></div>
        <div class="signature-name">{{ $kelas->wali_kelas?->name ?? '________________________' }}</div>
        <div style="font-size: 9.5px; color: #64748b;">NIP/NIK. {{ $kelas->wali_kelas?->nik ?? '-' }}</div>
    </div>
    <div class="signature-box">
        <div>Beringin, {{ now()->translatedFormat('d F Y') }}</div>
        <div>Guru Mata Pelajaran</div>
        <div class="signature-space"></div>
        <div class="signature-name">{{ $guruNama }}</div>
        <div style="font-size: 9.5px; color: #64748b;">NIP/NIK. {{ $guruNik }}</div>
    </div>
</div>

</body>
</html>
