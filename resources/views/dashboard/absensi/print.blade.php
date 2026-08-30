<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Absensi - {{ $sesiAbsensi->kelas->nama_kelas }}</title>
    <style>
        body { font-family: sans-serif; color: #333; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .header h1 { margin: 0 0 5px 0; font-size: 24px; }
        .header p { margin: 0; font-size: 14px; color: #555; }
        .info { margin-bottom: 20px; font-size: 14px; }
        .info table { width: 100%; }
        .info td { padding: 4px 0; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 13px; }
        .table th, .table td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        .table th { background: #f9f9f9; text-transform: uppercase; }
        .text-center { text-align: center !important; }
        .footer { margin-top: 50px; text-align: right; font-size: 14px; }
        .signature { display: inline-block; text-align: center; width: 250px; }
        .signature .name { margin-top: 70px; font-weight: bold; text-decoration: underline; }
        @media print {
            body { margin: 0; }
            @page { margin: 1cm; }
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
        <h1>LAPORAN ABSENSI KELAS</h1>
        <p>SMKN 1 BERINGIN</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="120"><strong>Kelas</strong></td>
                <td>: {{ $sesiAbsensi->kelas->nama_kelas }}</td>
                <td width="120"><strong>Tanggal</strong></td>
                <td>: {{ $sesiAbsensi->tanggal->format('d F Y') }}</td>
            </tr>
            <tr>
                <td><strong>Guru Piket/Mapel</strong></td>
                <td>: {{ $sesiAbsensi->guru->name }}</td>
                <td><strong>Status Sesi</strong></td>
                <td>: {{ $sesiAbsensi->is_active ? 'Aktif (Sedang Berjalan)' : 'Selesai (Ditutup)' }}</td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="30%">Nama Siswa</th>
                <th width="20%">NISN</th>
                <th width="15%" class="text-center">Status</th>
                <th width="30%">Keterangan Tambahan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sesiAbsensi->absensi->sortBy('siswa.name') as $i => $abs)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $abs->siswa->name }}</td>
                <td>{{ $abs->siswa->nisn ?? '-' }}</td>
                <td class="text-center">
                    <strong>{{ strtoupper($abs->status) }}</strong>
                </td>
                <td>{{ $abs->keterangan ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

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
