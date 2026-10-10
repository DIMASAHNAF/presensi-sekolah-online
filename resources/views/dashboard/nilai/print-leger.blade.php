<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Leger Nilai - {{ $kelas ? $kelas->nama_kelas : 'Semua Kelas' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.webp') }}">
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
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 16px;
            position: relative;
        }
        .kop h1 {
            font-size: 16px;
            font-weight: 800;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .kop h2 {
            font-size: 13px;
            font-weight: 700;
            margin: 0 0 2px 0;
            color: #2563eb;
            text-transform: uppercase;
        }
        .kop p {
            font-size: 10.5px;
            margin: 0;
            color: #64748b;
        }

        /* ===== ACTION BAR (PRINT ONLY HIDDEN) ===== */
        .no-print {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            background: #f1f5f9;
            padding: 10px 16px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }
        .btn {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back {
            background: #64748b;
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
            padding: 2px 8px 2px 0;
        }

        /* ===== TABEL LEGER ===== */
        table.leger {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 16px;
        }
        table.leger th, table.leger td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        table.leger th {
            background: #f1f5f9;
            font-weight: 700;
            text-align: center;
            color: #0f172a;
        }
        table.leger tr:nth-child(even) td {
            background: #fafafa;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }

        /* ===== BADGES ===== */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: 700;
        }
        .badge-tuntas { background: #dcfce7; color: #166534; }
        .badge-remedial { background: #ffe4e6; color: #9f1239; }

        /* ===== STATS ===== */
        .stats-box {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-card {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            background: #f8fafc;
            text-align: center;
        }
        .stat-card .val {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }
        .stat-card .lbl {
            font-size: 9.5px;
            color: #64748b;
            text-transform: uppercase;
        }

        /* ===== TANDA TANGAN ===== */
        .ttd-box {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .ttd-col {
            width: 250px;
            text-align: center;
        }
        .ttd-space { height: 60px; }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            @page { size: A4 landscape; margin: 12mm 15mm; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <div>
            <strong>Cetak Leger Nilai Siswa</strong> • SMKN 1 Beringin
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="{{ route('dashboard.nilai', ['tab' => 'leger', 'nilai_kelas_id' => $kelas ? $kelas->id : '', 'nilai_mapel_id' => $mapel ? $mapel->id : '']) }}" class="btn btn-back">
                &larr; Kembali ke Dashboard
            </a>
            <button onclick="window.print()" class="btn">
                &#128438; Cetak Sekarang (Print / PDF)
            </button>
        </div>
    </div>

    {{-- KOP SURAT --}}
    <div class="kop">
        <h1>SMK NEGERI 1 BERINGIN</h1>
        <h2>LEGER REKAPITULASI PENILAIAN HASIL BELAJAR SISWA</h2>
        <p>Jalan Pendidikan No. 1, Kec. Beringin, Kab. Deli Serdang • Sumatera Utara</p>
    </div>

    {{-- INFO METADATA --}}
    <div class="info-grid">
        <div class="info-col">
            <table>
                <tr>
                    <td><strong>Rombel Kelas</strong></td>
                    <td>:</td>
                    <td><strong>{{ $kelas ? $kelas->nama_kelas : 'Semua Kelas' }}</strong> (Tingkat {{ $kelas ? $kelas->tingkat : '-' }})</td>
                </tr>
                <tr>
                    <td><strong>Mata Pelajaran</strong></td>
                    <td>:</td>
                    <td><strong>{{ $mapel ? $mapel->nama_mapel : 'Seluruh Mata Pelajaran (Umum)' }}</strong></td>
                </tr>
                <tr>
                    <td><strong>Tahun Pelajaran</strong></td>
                    <td>:</td>
                    <td>2026/2027 • Semester Ganjil</td>
                </tr>
            </table>
        </div>
        <div class="info-col">
            <table>
                <tr>
                    <td><strong>Guru Pengajar</strong></td>
                    <td>:</td>
                    <td>{{ $guru ? $guru->name : '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Wali Kelas</strong></td>
                    <td>:</td>
                    <td>{{ $kelas && $kelas->wali_kelas ? $kelas->wali_kelas->name : '-' }}</td>
                </tr>
                <tr>
                    <td><strong>KKM / Batas Tuntas</strong></td>
                    <td>:</td>
                    <td><strong>{{ number_format($statsLeger['kkm'], 1) }}</strong></td>
                </tr>
            </table>
        </div>
    </div>

    {{-- STATS SUMMARY --}}
    <div class="stats-box">
        <div class="stat-card">
            <div class="val">{{ $statsLeger['total_siswa'] }}</div>
            <div class="lbl">Total Siswa</div>
        </div>
        <div class="stat-card">
            <div class="val">{{ number_format($statsLeger['rata_rata'], 1) }}</div>
            <div class="lbl">Rata-Rata Kelas (NA)</div>
        </div>
        <div class="stat-card">
            <div class="val" style="color: #16a34a;">{{ $statsLeger['siswa_tuntas'] }} ({{ $statsLeger['persen_tuntas'] }}%)</div>
            <div class="lbl">Siswa Tuntas (&ge; {{ $statsLeger['kkm'] }})</div>
        </div>
        <div class="stat-card">
            <div class="val" style="color: #dc2626;">{{ $statsLeger['siswa_remedial'] }}</div>
            <div class="lbl">Perlu Remedial (< {{ $statsLeger['kkm'] }})</div>
        </div>
    </div>

    {{-- TABEL LEGER --}}
    <table class="leger">
        <thead>
            <tr>
                <th rowspan="2" style="width: 35px;">No</th>
                <th rowspan="2" style="width: 100px;">NISN</th>
                <th rowspan="2" style="text-align: left;">Nama Siswa</th>
                <th colspan="5">Komponen Penilaian Akademik</th>
                <th rowspan="2" style="width: 70px; background: #e2e8f0;">Nilai Akhir (NA)</th>
                <th rowspan="2" style="width: 55px;">Predikat</th>
                <th rowspan="2" style="width: 80px;">Status KKM</th>
            </tr>
            <tr>
                <th style="width: 65px;">Tugas (20%)</th>
                <th style="width: 65px;">UH (20%)</th>
                <th style="width: 75px;">Praktik (25%)</th>
                <th style="width: 65px;">PTS (15%)</th>
                <th style="width: 65px;">PAS (20%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($legerData as $idx => $row)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center">{{ $row['siswa']->nisn ?: '-' }}</td>
                <td class="text-left font-bold">{{ $row['siswa']->name }}</td>
                <td class="text-center">{{ $row['tugas_avg'] !== null ? number_format($row['tugas_avg'], 1) : '-' }}</td>
                <td class="text-center">{{ $row['uh_avg'] !== null ? number_format($row['uh_avg'], 1) : '-' }}</td>
                <td class="text-center">{{ $row['praktik_avg'] !== null ? number_format($row['praktik_avg'], 1) : '-' }}</td>
                <td class="text-center">{{ $row['uts_avg'] !== null ? number_format($row['uts_avg'], 1) : '-' }}</td>
                <td class="text-center">{{ $row['uas_avg'] !== null ? number_format($row['uas_avg'], 1) : '-' }}</td>
                <td class="text-center font-bold" style="background: #f8fafc; font-size: 11.5px;">
                    {{ $row['nilai_akhir'] !== null ? number_format($row['nilai_akhir'], 1) : '-' }}
                </td>
                <td class="text-center font-bold">{{ $row['predikat'] }}</td>
                <td class="text-center">
                    @if($row['status'] === 'tuntas')
                        <span class="badge badge-tuntas">TUNTAS</span>
                    @elseif($row['status'] === 'remedial')
                        <span class="badge badge-remedial">REMEDIAL</span>
                    @else
                        <span style="color: #94a3b8;">-</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center" style="padding: 16px;">
                    Tidak ada siswa terdaftar dalam rombel ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="font-size: 10px; color: #64748b; margin-bottom: 20px;">
        <em>* Bobot NA SMK: Tugas 20%, Ulangan Harian (UH) 20%, Praktik/Portofolio 25%, PTS 15%, PAS 20%. Dihitung dinamis proporsional sesuai asesmen yang telah dilaksanakan.</em>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="ttd-box">
        <div class="ttd-col">
            <p>Mengetahui,<br>Kepala SMK Negeri 1 Beringin</p>
            <div class="ttd-space"></div>
            <p><strong>( Ilyas, M.Pd )</strong><br><small>NIP. 19750512 200501 1 004</small></p>
        </div>
        <div class="ttd-col">
            <p>Beringin, {{ date('d F Y') }}<br>Guru Mata Pelajaran</p>
            <div class="ttd-space"></div>
            <p><strong>( {{ $guru ? $guru->name : 'Guru Pengajar' }} )</strong><br><small>NIP/ID: {{ $guru ? ($guru->nip ?? $guru->username) : '-' }}</small></p>
        </div>
    </div>

</body>
</html>
