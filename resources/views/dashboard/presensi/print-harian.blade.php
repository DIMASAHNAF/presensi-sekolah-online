<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Harian - {{ $kelas->nama_kelas }}</title>
    <style>
        body { font-family: sans-serif; color: #333; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0 0 5px 0; font-size: 24px; }
        .header p { margin: 0; font-size: 14px; color: #555; }
        .info { margin-bottom: 20px; font-size: 14px; }
        .info table { width: 100%; }
        .info td { padding: 4px 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
        .table th, .table td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        .table th { background: #f9f9f9; }
        .text-left { text-align: left !important; }
        .footer { margin-top: 50px; text-align: right; font-size: 14px; }
        .signature { display: inline-block; text-align: center; width: 250px; }
        .signature .name { margin-top: 70px; font-weight: bold; text-decoration: underline; }
        @media print {
            body { margin: 0; }
            @page { margin: 1cm; size: landscape; }
            .btn-print { display: none; }
        }
        .btn-print {
            padding: 10px 20px; background: #2563eb; color: #fff; text-decoration: none;
            border-radius: 5px; font-weight: bold; border: none; cursor: pointer;
            margin-bottom: 20px; display: inline-block;
        }
    </style>
</head>
<body>

    <button class="btn-print" onclick="window.print()">🖨️ Cetak PDF / Print</button>

    <div class="header">
        <h1>LAPORAN PRESENSI HARIAN KELAS</h1>
        <p>SMKN 1 BERINGIN</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="120"><strong>Kelas</strong></td>
                <td>: {{ $kelas->nama_kelas }}</td>
                <td width="120"><strong>Tanggal</strong></td>
                <td>: {{ $tanggal->format('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Dicetak Oleh</strong></td>
                <td>: {{ auth()->user()->name }}</td>
                <td><strong>Waktu Cetak</strong></td>
                <td>: {{ now()->format('H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="3%" rowspan="2">No</th>
                <th width="20%" rowspan="2" class="text-left">Nama Siswa</th>
                <th colspan="{{ $sesiList->count() }}">Mata Pelajaran / Sesi</th>
            </tr>
            <tr>
                @foreach($sesiList as $sesi)
                    <th>
                        @if(!$sesi->mapel_id)
                            Sesi Pagi (Wali Kelas)
                        @else
                            {{ optional($sesi->mataPelajaran)->nama_mapel }} <br>
                            <small>{{ $sesi->jam_pelajaran }}</small>
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($siswaList as $i => $siswa)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="text-left">{{ $siswa->name }}</td>
                @foreach($sesiList as $sesi)
                    @php
                        $absen = $sesi->presensi->where('siswa_id', $siswa->id)->first();
                        $status = $absen ? strtoupper(substr($absen->status, 0, 1)) : '-';
                    @endphp
                    <td>
                        <strong>{{ $status }}</strong>
                    </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 15px; font-size: 12px; color: #555;">
        <strong>Keterangan:</strong> H (Hadir), S (Sakit), I (Izin), A (Alpa)
    </div>

    <div class="footer">
        <div class="signature">
            <p>Dibuat oleh,</p>
            <p class="name">{{ auth()->user()->name }}</p>
            <p>NIP/NIK: {{ auth()->user()->nik ?? '-' }}</p>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
