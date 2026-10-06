<table border="1">
    <thead>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold; font-size: 14px;">LAPORAN PRESENSI KELAS</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; font-weight: bold; font-size: 12px;">SMKN 1 BERINGIN</th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Kelas: {{ $sesiPresensi->kelas->nama_kelas }}</th>
            <th colspan="3" style="font-weight: bold;">Tanggal: {{ $sesiPresensi->tanggal->format('d F Y') }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Guru Piket/Mapel: {{ $sesiPresensi->guru->name }}</th>
            <th colspan="3" style="font-weight: bold;">Mata Pelajaran: {{ $sesiPresensi->mataPelajaran ? $sesiPresensi->mataPelajaran->nama_mapel : '-' }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Status Sesi: {{ $sesiPresensi->is_active ? 'Aktif (Sedang Berjalan)' : 'Selesai (Ditutup)' }}</th>
            <th colspan="3" style="font-weight: bold;">Jam Pelajaran: {{ $sesiPresensi->jam_pelajaran ?: '-' }}</th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center;">No</th>
            <th style="font-weight: bold; text-align: left;">Nama Siswa</th>
            <th style="font-weight: bold; text-align: left;">NISN</th>
            <th style="font-weight: bold; text-align: center;">Status</th>
            <th style="font-weight: bold; text-align: left;">Keterangan Tambahan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sesiPresensi->presensi->sortBy('siswa.name') as $i => $abs)
        <tr>
            <td style="text-align: center;">{{ $i + 1 }}</td>
            <td>{{ $abs->siswa->name }}</td>
            <td>{{ $abs->siswa->nisn ?? '-' }}</td>
            <td style="text-align: center; font-weight: bold;">{{ strtoupper($abs->status) }}</td>
            <td>{{ $abs->keterangan ?: '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
