<table border="1">
    <thead>
        <tr>
            <th colspan="11" style="text-align: center; font-weight: bold; font-size: 15px;">LEGER REKAPITULASI NILAI SISWA</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center; font-weight: bold; font-size: 13px;">SMK NEGERI 1 BERINGIN</th>
        </tr>
        <tr>
            <th colspan="11" style="text-align: center; font-size: 10px; color: #555555;">Tahun Ajaran 2026/2027 • Sistem Presensi &amp; Akademik Terpadu</th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr>
            <th colspan="3" style="font-weight: bold;">Rombel Kelas: {{ $kelas ? $kelas->nama_kelas : 'Semua Kelas' }}</th>
            <th colspan="4" style="font-weight: bold;">Mata Pelajaran: {{ $mapel ? $mapel->nama_mapel : 'Seluruh Mata Pelajaran' }}</th>
            <th colspan="4" style="font-weight: bold; text-align: right;">KKM: {{ number_format($statsLeger['kkm'], 1) }}</th>
        </tr>
        <tr>
            <th colspan="3" style="font-weight: bold;">Wali Kelas: {{ $kelas && $kelas->wali_kelas ? $kelas->wali_kelas->name : '-' }}</th>
            <th colspan="4" style="font-weight: bold;">Guru Pengajar: {{ $guru ? $guru->name : '-' }}</th>
            <th colspan="4" style="font-weight: bold; text-align: right;">Tanggal Unduh: {{ date('d F Y') }}</th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr style="background-color: #f1f5f9;">
            <th style="font-weight: bold; text-align: center;">No</th>
            <th style="font-weight: bold; text-align: center;">NISN</th>
            <th style="font-weight: bold; text-align: left;">Nama Siswa</th>
            <th style="font-weight: bold; text-align: center;">Tugas (20%)</th>
            <th style="font-weight: bold; text-align: center;">UH (20%)</th>
            <th style="font-weight: bold; text-align: center;">Praktik (25%)</th>
            <th style="font-weight: bold; text-align: center;">PTS (15%)</th>
            <th style="font-weight: bold; text-align: center;">PAS (20%)</th>
            <th style="font-weight: bold; text-align: center; background-color: #e2e8f0;">Nilai Akhir</th>
            <th style="font-weight: bold; text-align: center;">Predikat</th>
            <th style="font-weight: bold; text-align: center;">Status KKM</th>
        </tr>
    </thead>
    <tbody>
        @forelse($legerData as $idx => $row)
        <tr>
            <td style="text-align: center;">{{ $idx + 1 }}</td>
            <td style="text-align: center;">{{ $row['siswa']->nisn ?: '-' }}</td>
            <td style="text-align: left; font-weight: bold;">{{ $row['siswa']->name }}</td>
            <td style="text-align: center;">{{ $row['tugas_avg'] !== null ? number_format($row['tugas_avg'], 1) : '-' }}</td>
            <td style="text-align: center;">{{ $row['uh_avg'] !== null ? number_format($row['uh_avg'], 1) : '-' }}</td>
            <td style="text-align: center;">{{ $row['praktik_avg'] !== null ? number_format($row['praktik_avg'], 1) : '-' }}</td>
            <td style="text-align: center;">{{ $row['uts_avg'] !== null ? number_format($row['uts_avg'], 1) : '-' }}</td>
            <td style="text-align: center;">{{ $row['uas_avg'] !== null ? number_format($row['uas_avg'], 1) : '-' }}</td>
            <td style="text-align: center; font-weight: bold; background-color: #f8fafc;">
                {{ $row['nilai_akhir'] !== null ? number_format($row['nilai_akhir'], 1) : '-' }}
            </td>
            <td style="text-align: center; font-weight: bold;">{{ $row['predikat'] }}</td>
            <td style="text-align: center; font-weight: bold; color: {{ $row['status'] === 'tuntas' ? '#166534' : ($row['status'] === 'remedial' ? '#991b1b' : '#64748b') }};">
                {{ strtoupper($row['status']) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="11" style="text-align: center;">Tidak ada data siswa dalam rombel ini.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr style="background-color: #f8fafc;">
            <th colspan="3" style="font-weight: bold; text-align: left;">RINGKASAN KELAS</th>
            <th colspan="2" style="font-weight: bold; text-align: center;">Rata-Rata: {{ number_format($statsLeger['rata_rata'], 1) }}</th>
            <th colspan="2" style="font-weight: bold; text-align: center;">Tertinggi: {{ $statsLeger['tertinggi'] }}</th>
            <th colspan="2" style="font-weight: bold; text-align: center;">Terendah: {{ $statsLeger['terendah'] }}</th>
            <th colspan="2" style="font-weight: bold; text-align: center;">Tuntas: {{ $statsLeger['siswa_tuntas'] }} / {{ $statsLeger['total_siswa'] }} ({{ $statsLeger['persen_tuntas'] }}%)</th>
        </tr>
        <tr>
            <th colspan="11"></th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center;">Mengetahui,<br>Kepala Sekolah</th>
            <th colspan="3"></th>
            <th colspan="4" style="text-align: center;">Beringin, {{ date('d F Y') }}<br>Guru Mata Pelajaran</th>
        </tr>
        <tr>
            <th colspan="11" style="height: 50px;"></th>
        </tr>
        <tr>
            <th colspan="4" style="text-align: center; font-weight: bold;">(_________________________)</th>
            <th colspan="3"></th>
            <th colspan="4" style="text-align: center; font-weight: bold;">({{ $guru ? $guru->name : '_________________________' }})</th>
        </tr>
    </tfoot>
</table>
