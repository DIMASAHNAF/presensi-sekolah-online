<table border="1">
    <thead>
        <tr>
            <th colspan="{{ 2 + count($sesiList) }}" style="text-align: center; font-weight: bold; font-size: 14px;">
                LAPORAN PRESENSI HARIAN KELAS
            </th>
        </tr>
        <tr>
            <th colspan="{{ 2 + count($sesiList) }}" style="text-align: center; font-weight: bold; font-size: 12px;">
                {{ config('app.school_name', 'SMKN 1 BERINGIN') }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ 2 + count($sesiList) }}"></th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Kelas: {{ $kelas->nama_kelas }}</th>
            <th colspan="{{ count($sesiList) }}" style="font-weight: bold;">Tanggal: {{ $tanggal->format('d F Y') }}</th>
        </tr>
        <tr>
            <th colspan="2" style="font-weight: bold;">Dicetak Oleh: {{ auth()->user()->name }}</th>
            <th colspan="{{ count($sesiList) }}" style="font-weight: bold;">Waktu Cetak: {{ now()->format('H:i') }} WIB</th>
        </tr>
        <tr>
            <th colspan="{{ 2 + count($sesiList) }}"></th>
        </tr>
        <tr>
            <th rowspan="2" style="text-align: center; font-weight: bold;">No</th>
            <th rowspan="2" style="text-align: left; font-weight: bold;">Nama Siswa</th>
            <th colspan="{{ count($sesiList) }}" style="text-align: center; font-weight: bold;">Mata Pelajaran / Sesi</th>
        </tr>
        <tr>
            @foreach($sesiList as $sesi)
                <th style="text-align: center; font-weight: bold;">
                    @if(!$sesi->mapel_id)
                        Sesi Pagi (Wali Kelas)
                    @else
                        {{ optional($sesi->mataPelajaran)->nama_mapel }} <br>
                        {{ $sesi->jam_pelajaran }}
                    @endif
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($siswaList as $i => $siswa)
        <tr>
            <td style="text-align: center;">{{ $i + 1 }}</td>
            <td style="text-align: left;">{{ $siswa->name }}</td>
            @foreach($sesiList as $sesi)
                @php
                    $absen = $sesi->presensi->where('siswa_id', $siswa->id)->first();
                    $status = $absen ? strtoupper(substr($absen->status, 0, 1)) : '-';
                @endphp
                <td style="text-align: center; font-weight: bold;">
                    {{ $status }}
                </td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>
