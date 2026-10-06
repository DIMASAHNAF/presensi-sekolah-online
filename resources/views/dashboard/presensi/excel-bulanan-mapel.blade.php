<table border="1">
    <thead>
        <tr>
            <th colspan="{{ 8 + count($sesiList) }}" style="text-align: center; font-weight: bold; font-size: 14px;">
                REKAPITULASI PRESENSI MATA PELAJARAN
            </th>
        </tr>
        <tr>
            <th colspan="{{ 8 + count($sesiList) }}" style="text-align: center; font-weight: bold; font-size: 12px;">
                {{ config('app.school_name', 'SMKN 1 BERINGIN') }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ 8 + count($sesiList) }}"></th>
        </tr>
        <tr>
            <th colspan="3" style="font-weight: bold;">Mata Pelajaran: {{ $mapel->nama_mapel }}</th>
            <th colspan="{{ 5 + count($sesiList) }}" style="font-weight: bold;">Kelas: {{ $kelas->nama_kelas }}</th>
        </tr>
        <tr>
            <th colspan="3" style="font-weight: bold;">Guru Pengampu: {{ $guruNama }}</th>
            <th colspan="{{ 5 + count($sesiList) }}" style="font-weight: bold;">Bulan / Tahun: {{ $bulanDate->translatedFormat('F Y') }}</th>
        </tr>
        <tr>
            <th colspan="{{ 8 + count($sesiList) }}"></th>
        </tr>
        <tr>
            <th rowspan="2" style="text-align: center; font-weight: bold;">No</th>
            <th rowspan="2" style="text-align: left; font-weight: bold;">NISN</th>
            <th rowspan="2" style="text-align: left; font-weight: bold;">Nama Siswa</th>
            
            @if(count($sesiList) > 0)
                <th colspan="{{ count($sesiList) }}" style="text-align: center; font-weight: bold;">
                    Presensi Pertemuan Tatap Muka
                </th>
            @else
                <th style="text-align: center; font-weight: bold;">Pertemuan</th>
            @endif

            <th colspan="4" style="text-align: center; font-weight: bold;">Total Rekap</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">% Hadir</th>
            <th rowspan="2" style="text-align: center; font-weight: bold;">Status Keaktifan</th>
        </tr>
        <tr>
            @forelse($sesiList as $idx => $sesi)
                <th style="text-align: center; font-weight: bold;">
                    P.{{ $idx + 1 }}<br/>
                    {{ $sesi->tanggal->format('d/m') }}
                </th>
            @empty
                <th style="text-align: center;">-</th>
            @endforelse

            <th style="text-align: center; font-weight: bold; background-color: #dcfce7;">H</th>
            <th style="text-align: center; font-weight: bold; background-color: #ffedd5;">S</th>
            <th style="text-align: center; font-weight: bold; background-color: #fef9c3;">I</th>
            <th style="text-align: center; font-weight: bold; background-color: #fee2e2;">A</th>
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
            <td style="text-align: center;">{{ $idx + 1 }}</td>
            <td>{{ $siswa->nisn ?: '-' }}</td>
            <td>{{ $siswa->name }}</td>

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
                <td style="text-align: center; font-weight: bold;">
                    @if($st === 'hadir') H
                    @elseif($st === 'sakit') S
                    @elseif($st === 'izin') I
                    @elseif($st === 'alpa') A
                    @else -
                    @endif
                </td>
            @empty
                <td style="text-align: center;">-</td>
            @endforelse

            @php
                $totalSesiThisSiswa = count($sesiList);
                $persenHadir = 0;
                if ($totalSesiThisSiswa > 0) {
                    $persenHadir = round(($countH / $totalSesiThisSiswa) * 100);
                }
            @endphp
            <td style="text-align: center; font-weight: bold; background-color: #dcfce7;">{{ $countH }}</td>
            <td style="text-align: center; font-weight: bold; background-color: #ffedd5;">{{ $countS }}</td>
            <td style="text-align: center; font-weight: bold; background-color: #fef9c3;">{{ $countI }}</td>
            <td style="text-align: center; font-weight: bold; background-color: #fee2e2;">{{ $countA }}</td>
            
            <td style="text-align: center;">{{ $persenHadir }}%</td>
            <td style="text-align: center; font-weight: bold;">
                @if($persenHadir >= 75)
                    TUNTAS (A)
                @elseif($persenHadir >= 50)
                    KURANG (B)
                @else
                    PERLU REMEDIAL
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="{{ 8 + count($sesiList) }}" style="text-align: center;">Belum ada siswa terdaftar.</td>
        </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" style="text-align: right; font-weight: bold;">Total Akumulasi</th>
            @forelse($sesiList as $sesi)
                @php
                    $tot = $perSesiCount[$sesi->id];
                    $totalHadir = $tot['H'] + $tot['S'] + $tot['I'] + $tot['A']; // total murid yg ada status
                    $hPercent = $totalHadir > 0 ? round(($tot['H'] / $totalHadir)*100) : 0;
                @endphp
                <th style="text-align: center; font-weight: bold;">{{ $hPercent }}%</th>
            @empty
                <th>-</th>
            @endforelse
            <th colspan="6"></th>
        </tr>
    </tfoot>
</table>
