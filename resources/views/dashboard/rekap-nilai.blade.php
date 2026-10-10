@extends('layouts.dashboard')

@section('title', 'Rekapitulasi Nilai Siswa')
@section('page-title', 'Rekap Nilai Siswa')
@section('page-subtitle', 'Kelola buku nilai kelas, leger akademik, dan rekapitulasi penilaian siswa SMKN 1 Beringin')

@section('content')

<div x-data="rekapNilaiApp()" class="space-y-6">

    {{-- ═══════════════════════════════════════════════════
         1. HEADER & DUAL MODE TAB SWITCHER
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 sm:p-6" data-aos="fade-down">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-500 text-white flex items-center justify-center shrink-0 shadow-lg shadow-blue-500/25">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            Kurikulum Merdeka SMK
                        </span>
                        <span class="text-slate-300 dark:text-slate-600 text-xs">•</span>
                        <span class="text-slate-600 dark:text-slate-400 text-xs font-mono font-medium">T.A. 2026/2027</span>
                        <span class="text-slate-300 dark:text-slate-600 text-xs">•</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Semester Ganjil</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-heading font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Buku Nilai &amp; Rekapitulasi Siswa
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-2xl leading-relaxed">
                        Pilih rombel kelas dan mata pelajaran untuk melihat seluruh murid, mengelola leger nilai terpadu, serta melakukan penilaian sekelas sekaligus.
                    </p>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2.5 w-full sm:w-auto flex-wrap lg:flex-nowrap shrink-0">
                @if($selectedKelas)
                    <button type="button" @click="openModalBatch()" class="btn-primary text-xs py-2.5 px-4 shadow-sm w-full sm:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white font-bold flex items-center gap-2 rounded-xl">
                        <i class="fas fa-users-line text-xs"></i>
                        <span>Input Nilai Sekelas</span>
                    </button>
                @endif

                <button type="button" @click="openModalAdd()" class="btn-secondary text-xs py-2.5 px-3.5 shadow-xs w-full sm:w-auto justify-center rounded-xl">
                    <i class="fas fa-plus text-[11px] text-blue-600 dark:text-blue-400"></i>
                    <span>Input Satuan</span>
                </button>

                @if($selectedKelas)
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <a href="{{ route('dashboard.nilai.excel.leger', ['nilai_kelas_id' => $selectedKelasId, 'nilai_mapel_id' => $selectedMapelId]) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 transition"
                           title="Unduh Leger Nilai ke format Excel">
                            <i class="fas fa-file-excel text-xs"></i>
                            <span class="hidden sm:inline">Excel</span>
                        </a>

                        <a href="{{ route('dashboard.nilai.print.leger', ['nilai_kelas_id' => $selectedKelasId, 'nilai_mapel_id' => $selectedMapelId]) }}"
                           target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 transition"
                           title="Cetak / Unduh PDF Leger Nilai">
                            <i class="fas fa-print text-xs"></i>
                            <span class="hidden sm:inline">Cetak</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Dual Mode Navigation Tabs --}}
        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 bg-slate-100/90 dark:bg-slate-800/80 p-1.5 rounded-xl border border-slate-200/80 dark:border-slate-700/80 text-xs font-bold">
                <button type="button" @click="setTab('leger')"
                        class="px-4 py-2 rounded-lg transition-all flex items-center gap-2"
                        :class="currentTab === 'leger'
                            ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs'
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                    <i class="fas fa-table-cells-large text-xs"></i>
                    <span>Matriks Leger Nilai Siswa</span>
                    @if($selectedKelas)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 font-mono">
                            {{ count($legerData) }}
                        </span>
                    @endif
                </button>

                <button type="button" @click="setTab('log')"
                        class="px-4 py-2 rounded-lg transition-all flex items-center gap-2"
                        :class="currentTab === 'log'
                            ? 'bg-white dark:bg-slate-900 text-blue-600 dark:text-blue-400 shadow-xs'
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'">
                    <i class="fas fa-clock-rotate-left text-xs"></i>
                    <span>Riwayat Log Entri Penilaian</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-mono">
                        {{ $listNilai->total() }}
                    </span>
                </button>
            </div>

            {{-- Info Badge Kelas Terpilih --}}
            @if($selectedKelas)
                <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Rombel Aktif: <strong class="text-slate-900 dark:text-white">{{ $selectedKelas->nama_kelas }}</strong></span>
                    @if($selectedMapel)
                        <span>• Mapel: <strong class="text-blue-600 dark:text-blue-400">{{ $selectedMapel->nama_mapel }}</strong></span>
                    @endif
                </div>
            @endif
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════
         2. TAB 1: MATRIKS LEGER NILAI SEKELAS (OPSI A)
    ══════════════════════════════════════════════════════ --}}
    <div x-show="currentTab === 'leger'" class="space-y-6">

        {{-- Filter Cepat Rombel Kelas & Mapel --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-5" data-aos="fade-up">
            <div class="flex items-center justify-between mb-3.5">
                <div class="flex items-center gap-2">
                    <i class="fas fa-sliders text-blue-600 dark:text-blue-400 text-xs"></i>
                    <h2 class="font-heading font-bold text-xs uppercase tracking-wider text-slate-800 dark:text-white">
                        Pilih Kelas &amp; Mata Pelajaran
                    </h2>
                </div>
                <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">
                    Ganti kelas untuk langsung menampilkan seluruh siswa rombel
                </span>
            </div>

            <form method="GET" action="{{ route('dashboard.nilai') }}" id="formFilterLeger" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" value="leger">

                {{-- 1. Pilihan Kelas --}}
                <div class="lg:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Rombel Kelas <span class="text-rose-500">*</span>
                    </label>
                    <select name="nilai_kelas_id" onchange="document.getElementById('formFilterLeger').submit()"
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ $selectedKelasId == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }} ({{ $k->tingkat }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 2. Pilihan Mapel --}}
                <div class="lg:col-span-4">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Mata Pelajaran (Opsional)
                    </label>
                    <select name="nilai_mapel_id" onchange="document.getElementById('formFilterLeger').submit()"
                            class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                        <option value="">-- Seluruh Mapel (Rata-rata Gabungan) --</option>
                        @foreach($mapelList as $m)
                            <option value="{{ $m->id }}" {{ $selectedMapelId == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. Cari Siswa --}}
                <div class="lg:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Cari Siswa di Kelas Ini
                    </label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search_siswa" value="{{ request('search_siswa') }}" placeholder="Ketik nama / NISN..."
                               class="w-full pl-9 pr-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>
                </div>

                {{-- Tombol Filter --}}
                <div class="flex items-center gap-1.5 lg:col-span-1">
                    <button type="submit" class="btn-primary text-xs py-2.5 px-3.5 w-full justify-center rounded-xl" title="Terapkan Filter">
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                    @if(request()->hasAny(['search_siswa', 'nilai_mapel_id']))
                        <a href="{{ route('dashboard.nilai', ['tab' => 'leger', 'nilai_kelas_id' => $selectedKelasId]) }}" class="btn-secondary text-xs py-2.5 px-3 rounded-xl text-slate-500 hover:text-slate-900" title="Reset Mapel/Pencarian">
                            <i class="fas fa-rotate-left text-xs"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Ringkasan Statistik Kelas Leger (4 Metrik) --}}
        @if($selectedKelas)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" data-aos="fade-up">
            {{-- Total Siswa --}}
            <div class="stat-card accent-blue">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-sm"></i>
                    </div>
                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider font-mono bg-slate-50 dark:bg-slate-800 px-2 py-0.5 rounded">Rombel</span>
                </div>
                <p class="text-3xl font-heading font-extrabold text-slate-900 dark:text-white leading-none mb-1">
                    {{ $statsLeger['total_siswa'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Total Siswa {{ $selectedKelas->nama_kelas }}
                </p>
            </div>

            {{-- Rata-Rata NA Kelas --}}
            <div class="stat-card accent-indigo">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-chart-simple text-sm"></i>
                    </div>
                    <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider font-mono bg-indigo-50 dark:bg-indigo-950/60 px-2 py-0.5 rounded">Skor NA</span>
                </div>
                <p class="text-3xl font-heading font-extrabold text-indigo-600 dark:text-indigo-400 leading-none mb-1">
                    {{ number_format($statsLeger['rata_rata'], 1) }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Rata-Rata Nilai Akhir Kelas
                </p>
            </div>

            {{-- Siswa Tuntas KKM --}}
            <div class="stat-card accent-green">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-circle-check text-sm"></i>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider font-mono bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded">
                        KKM {{ $statsLeger['kkm'] }}
                    </span>
                </div>
                <p class="text-3xl font-heading font-extrabold text-emerald-600 dark:text-emerald-400 leading-none mb-1">
                    {{ $statsLeger['siswa_tuntas'] }} <span class="text-base font-normal text-slate-500">/ {{ $statsLeger['total_siswa'] }}</span>
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Siswa Tuntas ({{ $statsLeger['persen_tuntas'] }}%)
                </p>
            </div>

            {{-- Perlu Remedial --}}
            <div class="stat-card accent-amber">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-rose-600 dark:text-rose-400 rounded-xl flex items-center justify-center">
                        <i class="fas fa-triangle-exclamation text-sm"></i>
                    </div>
                    <span class="text-[10px] font-bold text-rose-700 dark:text-rose-300 uppercase tracking-wider font-mono bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded">Perlu Bimbingan</span>
                </div>
                <p class="text-3xl font-heading font-extrabold text-rose-600 dark:text-rose-400 leading-none mb-1">
                    {{ $statsLeger['siswa_remedial'] }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    Siswa Nilai di Bawah KKM
                </p>
            </div>
        </div>
        @endif

        {{-- TABEL MATRIKS LEGER NILAI SEKELAS --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden" data-aos="fade-up">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
                <div>
                    <h2 class="font-heading font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                        <i class="fas fa-table-list text-blue-600 dark:text-blue-400"></i>
                        <span>Daftar Nilai Siswa: {{ $selectedKelas ? $selectedKelas->nama_kelas : 'Pilih Kelas' }}</span>
                        @if($selectedMapel)
                            <span class="text-slate-400 font-normal">({{ $selectedMapel->nama_mapel }})</span>
                        @endif
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Menampilkan seluruh <strong class="text-slate-800 dark:text-slate-200">{{ count($legerData) }} murid</strong> dalam rombel ini
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-slate-500 dark:text-slate-400">
                        Bobot: <strong>T:20%</strong> • <strong>UH:20%</strong> • <strong>P:25%</strong> • <strong>PTS:15%</strong> • <strong>PAS:20%</strong>
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="text-slate-600 dark:text-slate-300 bg-slate-50/90 dark:bg-slate-800/90 border-b border-slate-200 dark:border-slate-700 font-heading uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="p-3.5 font-bold text-center w-12">No</th>
                            <th class="p-3.5 font-bold min-w-[210px]">Nama Siswa &amp; NISN</th>
                            <th class="p-3.5 font-bold text-center min-w-[90px]" title="Rata-rata Tugas (Bobot 20%)">
                                Tugas (20%)
                            </th>
                            <th class="p-3.5 font-bold text-center min-w-[90px]" title="Rata-rata Ulangan Harian (Bobot 20%)">
                                UH (20%)
                            </th>
                            <th class="p-3.5 font-bold text-center min-w-[95px]" title="Rata-rata Praktik / Portofolio Kejuruan (Bobot 25%)">
                                Praktik (25%)
                            </th>
                            <th class="p-3.5 font-bold text-center min-w-[85px]" title="Penilaian Tengah Semester (Bobot 15%)">
                                PTS (15%)
                            </th>
                            <th class="p-3.5 font-bold text-center min-w-[85px]" title="Penilaian Akhir Semester (Bobot 20%)">
                                PAS (20%)
                            </th>
                            <th class="p-3.5 font-bold text-center min-w-[105px] bg-blue-50/50 dark:bg-blue-950/30 text-blue-800 dark:text-blue-300" title="Nilai Akhir Rata-rata Berbobot">
                                Nilai Akhir (NA)
                            </th>
                            <th class="p-3.5 font-bold text-center min-w-[70px]">Predikat</th>
                            <th class="p-3.5 font-bold text-center min-w-[95px]">Status KKM</th>
                            <th class="p-3.5 font-bold text-right min-w-[80px]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($legerData as $idx => $row)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                {{-- No --}}
                                <td class="p-3.5 text-center font-mono text-slate-500 dark:text-slate-400 font-semibold">
                                    {{ $idx + 1 }}
                                </td>

                                {{-- Siswa --}}
                                <td class="p-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold text-xs shrink-0 shadow-2xs">
                                            {{ strtoupper(substr($row['siswa']->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-slate-900 dark:text-white text-xs leading-snug">
                                                {{ $row['siswa']->name }}
                                            </p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                                NISN: {{ $row['siswa']->nisn ?: '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Tugas --}}
                                <td class="p-3.5 text-center">
                                    @if($row['tugas_avg'] !== null)
                                        <span class="font-mono text-xs font-bold text-slate-800 dark:text-slate-200">
                                            {{ number_format($row['tugas_avg'], 1) }}
                                        </span>
                                        <span class="block text-[9px] text-slate-400 font-mono">({{ $row['tugas_count'] }}x)</span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-600">-</span>
                                    @endif
                                </td>

                                {{-- UH --}}
                                <td class="p-3.5 text-center">
                                    @if($row['uh_avg'] !== null)
                                        <span class="font-mono text-xs font-bold text-slate-800 dark:text-slate-200">
                                            {{ number_format($row['uh_avg'], 1) }}
                                        </span>
                                        <span class="block text-[9px] text-slate-400 font-mono">({{ $row['uh_count'] }}x)</span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-600">-</span>
                                    @endif
                                </td>

                                {{-- Praktik --}}
                                <td class="p-3.5 text-center">
                                    @if($row['praktik_avg'] !== null)
                                        <span class="font-mono text-xs font-bold text-teal-700 dark:text-teal-300">
                                            {{ number_format($row['praktik_avg'], 1) }}
                                        </span>
                                        <span class="block text-[9px] text-teal-500 font-mono">({{ $row['praktik_count'] }}x)</span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-600">-</span>
                                    @endif
                                </td>

                                {{-- PTS --}}
                                <td class="p-3.5 text-center">
                                    @if($row['uts_avg'] !== null)
                                        <span class="font-mono text-xs font-bold text-slate-800 dark:text-slate-200">
                                            {{ number_format($row['uts_avg'], 1) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-600">-</span>
                                    @endif
                                </td>

                                {{-- PAS --}}
                                <td class="p-3.5 text-center">
                                    @if($row['uas_avg'] !== null)
                                        <span class="font-mono text-xs font-bold text-slate-800 dark:text-slate-200">
                                            {{ number_format($row['uas_avg'], 1) }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 dark:text-slate-600">-</span>
                                    @endif
                                </td>

                                {{-- Nilai Akhir (NA) --}}
                                <td class="p-3.5 text-center bg-blue-50/30 dark:bg-blue-950/20">
                                    @if($row['nilai_akhir'] !== null)
                                        <span class="font-mono text-sm font-extrabold px-2.5 py-1 rounded-lg border inline-block
                                            {{ $row['nilai_akhir'] >= 80
                                                ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'
                                                : ($row['nilai_akhir'] >= 75
                                                    ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800'
                                                    : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800') }}">
                                            {{ number_format($row['nilai_akhir'], 1) }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Belum Ada</span>
                                    @endif
                                </td>

                                {{-- Predikat --}}
                                <td class="p-3.5 text-center">
                                    @if($row['predikat'] !== '-')
                                        <span class="w-7 h-7 inline-flex items-center justify-center font-heading font-extrabold text-xs rounded-full shadow-2xs
                                            {{ $row['predikat'] === 'A' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200' :
                                               ($row['predikat'] === 'B' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200' :
                                               ($row['predikat'] === 'C' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200')) }}">
                                            {{ $row['predikat'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                {{-- Status KKM --}}
                                <td class="p-3.5 text-center">
                                    @if($row['status'] === 'tuntas')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                            TUNTAS
                                        </span>
                                    @elseif($row['status'] === 'remedial')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 border border-rose-300 dark:border-rose-800">
                                            REMEDIAL
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">Belum Dinilai</span>
                                    @endif
                                </td>

                                {{-- Aksi Cepat Siswa --}}
                                <td class="p-3.5 text-right">
                                    <button type="button" @click="openModalAddForStudent({{ $row['siswa']->id }}, '{{ addslashes($row['siswa']->name) }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 text-xs font-bold transition"
                                            title="Tambah nilai khusus untuk siswa ini">
                                        <i class="fas fa-plus text-[10px]"></i> Nilai
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <i class="fas fa-users-slash text-3xl text-slate-300 dark:text-slate-700"></i>
                                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Belum ada data siswa dalam rombel kelas ini</p>
                                        <p class="text-[11px] text-slate-400">Silakan pilih kelas lain atau tambahkan data siswa terlebih dahulu.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Kartu Keterangan Rumus & Bobot Nilai SMK --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
            <h3 class="font-heading font-extrabold text-xs uppercase tracking-wider text-slate-800 dark:text-white mb-2 flex items-center gap-2">
                <i class="fas fa-circle-info text-blue-600 dark:text-blue-400"></i>
                Panduan Perhitungan Nilai Akhir (NA) &amp; Predikat
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-slate-600 dark:text-slate-400 leading-relaxed">
                <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-xl border border-slate-100 dark:border-slate-800">
                    <p class="font-bold text-slate-800 dark:text-slate-200 mb-1">Rumus Bobot Standar SMK:</p>
                    <p class="font-mono text-[11px] text-blue-600 dark:text-blue-400">
                        NA = (Tugas×20% + UH×20% + Praktik×25% + PTS×15% + PAS×20%) / Total Bobot Terisi
                    </p>
                    <p class="text-[10px] text-slate-500 mt-1">Dihitung proporsional dinamis jika ada komponen penilaian yang belum diselenggarakan.</p>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-xl border border-slate-100 dark:border-slate-800">
                    <p class="font-bold text-slate-800 dark:text-slate-200 mb-1">Skala Predikat:</p>
                    <ul class="text-[11px] space-y-0.5">
                        <li><strong class="text-emerald-600">A (88.0 - 100.0)</strong>: Sangat Baik / Mahir</li>
                        <li><strong class="text-blue-600">B (75.0 - 87.9)</strong>: Baik / Cakap</li>
                        <li><strong class="text-amber-600">C (65.0 - 74.9)</strong>: Cukup / Layak</li>
                        <li><strong class="text-rose-600">D (&lt; 65.0)</strong>: Kurang / Perlu Bimbingan</li>
                    </ul>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/60 p-3.5 rounded-xl border border-slate-100 dark:border-slate-800">
                    <p class="font-bold text-slate-800 dark:text-slate-200 mb-1">Kriteria Ketuntasan Minimal (KKM):</p>
                    <p class="text-[11px]">Batas KKM akademik standar SMK adalah <strong>75.0</strong>. Siswa dengan nilai di bawah KKM ditandai status <strong>REMEDIAL</strong> untuk tindak lanjut guru mata pelajaran.</p>
                </div>
            </div>
        </div>

    </div>


    {{-- ═══════════════════════════════════════════════════
         3. TAB 2: RIWAYAT LOG ENTRI PENILAIAN
    ══════════════════════════════════════════════════════ --}}
    <div x-show="currentTab === 'log'" class="space-y-6">

        {{-- Filter Log --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-5">
            <div class="flex items-center justify-between mb-3.5">
                <div class="flex items-center gap-2">
                    <i class="fas fa-filter text-blue-600 dark:text-blue-400 text-xs"></i>
                    <h2 class="font-heading font-bold text-xs uppercase tracking-wider text-slate-800 dark:text-white">Filter &amp; Pencarian Riwayat Log</h2>
                </div>
            </div>

            <form method="GET" action="{{ route('dashboard.nilai') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <input type="hidden" name="tab" value="log">

                {{-- Search Keyword --}}
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Cari Siswa / Judul</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama, NISN, atau judul..."
                               class="w-full pl-8 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>

                {{-- Filter Kelas --}}
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Rombel Kelas</label>
                    <select name="nilai_kelas_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ request('nilai_kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }} ({{ $k->tingkat }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Mapel --}}
                <div class="lg:col-span-3">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Mata Pelajaran</label>
                    <select name="nilai_mapel_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="">-- Semua Mata Pelajaran --</option>
                        @foreach($mapelList as $m)
                            <option value="{{ $m->id }}" {{ request('nilai_mapel_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Jenis Penilaian --}}
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Jenis Penilaian</label>
                    <select name="nilai_jenis" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="">-- Semua Jenis --</option>
                        <option value="tugas" {{ request('nilai_jenis') == 'tugas' ? 'selected' : '' }}>Tugas</option>
                        <option value="ulangan_harian" {{ request('nilai_jenis') == 'ulangan_harian' ? 'selected' : '' }}>Ulangan Harian (UH)</option>
                        <option value="praktik" {{ request('nilai_jenis') == 'praktik' ? 'selected' : '' }}>Praktik / Portofolio</option>
                        <option value="uts" {{ request('nilai_jenis') == 'uts' ? 'selected' : '' }}>PTS / UTS</option>
                        <option value="uas" {{ request('nilai_jenis') == 'uas' ? 'selected' : '' }}>PAS / UAS</option>
                        <option value="sikap" {{ request('nilai_jenis') == 'sikap' ? 'selected' : '' }}>Sikap &amp; Karakter</option>
                    </select>
                </div>

                {{-- Tombol Terapkan & Reset --}}
                <div class="flex items-end gap-2 lg:col-span-2">
                    <button type="submit" class="btn-primary text-xs py-2 px-3.5 flex-1 justify-center rounded-xl">
                        <i class="fas fa-filter text-[11px]"></i> Terapkan
                    </button>
                    @if(request()->hasAny(['search', 'nilai_kelas_id', 'nilai_mapel_id', 'nilai_jenis', 'hanya_saya']))
                        <a href="{{ route('dashboard.nilai', ['tab' => 'log']) }}" class="btn-secondary text-xs py-2 px-3 text-slate-500 hover:text-slate-900 rounded-xl" title="Reset Filter">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Log Penilaian Individual --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="text-slate-500 dark:text-slate-400 bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 font-heading uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="p-3.5 font-bold">Siswa &amp; Rombel</th>
                            <th class="p-3.5 font-bold">Mata Pelajaran</th>
                            <th class="p-3.5 font-bold">Judul &amp; Jenis Penilaian</th>
                            <th class="p-3.5 font-bold text-center">Nilai</th>
                            <th class="p-3.5 font-bold text-center">Predikat</th>
                            <th class="p-3.5 font-bold">Penginput</th>
                            <th class="p-3.5 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($listNilai as $n)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="p-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($n->siswa->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 dark:text-white text-xs">{{ $n->siswa->name ?? '-' }}</p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                                NISN: {{ $n->siswa->nisn ?? '-' }} • <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $n->kelas->nama_kelas ?? '-' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                        <i class="fas fa-book-bookmark text-[10px]"></i>
                                        {{ $n->mataPelajaran->nama_mapel ?? 'Umum / Wali Kelas' }}
                                    </span>
                                </td>

                                <td class="p-3.5">
                                    <p class="font-semibold text-slate-900 dark:text-white text-xs">{{ $n->judul }}</p>
                                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded
                                            {{ $n->jenis_penilaian === 'uas' || $n->jenis_penilaian === 'uts'
                                                ? 'bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800'
                                                : ($n->jenis_penilaian === 'ulangan_harian'
                                                    ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800'
                                                    : ($n->jenis_penilaian === 'praktik'
                                                        ? 'bg-teal-100 dark:bg-teal-950/80 text-teal-700 dark:text-teal-300 border border-teal-200 dark:border-teal-800'
                                                        : ($n->jenis_penilaian === 'sikap'
                                                            ? 'bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'
                                                            : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700'))) }}">
                                            {{ $n->jenis_label }}
                                        </span>
                                        @if($n->tanggal)
                                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">
                                                {{ $n->tanggal->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($n->catatan)
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 italic mt-1 bg-slate-50 dark:bg-slate-800/60 p-1.5 rounded border border-slate-100 dark:border-slate-800 max-w-sm">
                                            "{{ $n->catatan }}"
                                        </p>
                                    @endif
                                </td>

                                <td class="p-3.5 text-center">
                                    <span class="font-mono text-base font-extrabold px-2.5 py-1 rounded-lg border inline-block
                                        {{ $n->nilai >= 80
                                            ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'
                                            : ($n->nilai >= 70
                                                ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800'
                                                : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800') }}">
                                        {{ number_format($n->nilai, 1) }}
                                    </span>
                                </td>

                                <td class="p-3.5 text-center">
                                    <span class="w-7 h-7 inline-flex items-center justify-center font-heading font-extrabold text-xs rounded-full
                                        {{ $n->predikat === 'A' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200' :
                                           ($n->predikat === 'B' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200' :
                                           ($n->predikat === 'C' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200')) }}">
                                        {{ $n->predikat }}
                                    </span>
                                </td>

                                <td class="p-3.5">
                                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                        <i class="fas fa-chalkboard-user text-slate-400 text-[10px]"></i>
                                        {{ $n->guru->name ?? '-' }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">
                                        {{ $n->created_at ? $n->created_at->diffForHumans() : '-' }}
                                    </p>
                                </td>

                                <td class="p-3.5 text-right">
                                    @if(auth()->user()->isAdmin() || $n->guru_id === auth()->id())
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button"
                                                    @click="openModalEdit({
                                                        id: {{ $n->id }},
                                                        siswa_name: '{{ addslashes($n->siswa->name ?? '') }}',
                                                        kelas_nama: '{{ addslashes($n->kelas->nama_kelas ?? '') }}',
                                                        mapel_id: '{{ $n->mapel_id ?? '' }}',
                                                        jenis_penilaian: '{{ $n->jenis_penilaian }}',
                                                        judul: '{{ addslashes($n->judul) }}',
                                                        nilai: '{{ $n->nilai }}',
                                                        tanggal: '{{ $n->tanggal ? $n->tanggal->format('Y-m-d') : '' }}',
                                                        catatan: '{{ addslashes($n->catatan ?? '') }}'
                                                    })"
                                                    class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition"
                                                    title="Edit Nilai">
                                                <i class="fas fa-pencil text-xs"></i>
                                            </button>

                                            <form action="{{ route('dashboard.nilai.destroy', $n) }}" method="POST" class="inline-block"
                                                  data-confirm="Hapus data nilai {{ addslashes($n->judul) }} untuk siswa {{ addslashes($n->siswa->name ?? '') }}?"
                                                  data-confirm-title="Hapus Nilai Siswa?"
                                                  data-confirm-type="danger"
                                                  data-confirm-btn="Ya, Hapus Nilai">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition" title="Hapus Nilai">
                                                    <i class="fas fa-trash-can text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-slate-400 italic">Read-only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400">
                                    <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Belum ada riwayat entri nilai yang sesuai filter</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($listNilai->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $listNilai->links() }}
                </div>
            @endif
        </div>

    </div>


    {{-- ═══════════════════════════════════════════════════
         4. MODAL INPUT NILAI SEKELAS (BULK / SHEET INPUT)
    ══════════════════════════════════════════════════════ --}}
    <div x-show="openBatch" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs px-4 overflow-y-auto">
        <div @click.away="openBatch = false" class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-4xl p-6 my-8 animate-in fade-in zoom-in-95 max-h-[90vh] flex flex-col">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class="fas fa-layer-group text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white">
                            Input Nilai Sekelas Sekaligus
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Kelas: <strong class="text-slate-800 dark:text-slate-200">{{ $selectedKelas ? $selectedKelas->nama_kelas : '-' }}</strong> • Isi nilai angka untuk seluruh siswa sekaligus
                        </p>
                    </div>
                </div>
                <button type="button" @click="openBatch = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white p-1 rounded-lg">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            {{-- Form Batch --}}
            <form action="{{ route('dashboard.nilai.batch-store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden space-y-4">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $selectedKelasId }}">

                {{-- Parameter Nilai --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 shrink-0 bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Mata Pelajaran
                        </label>
                        <select name="mapel_id" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                            <option value="">-- Umum / Wali Kelas --</option>
                            @foreach($mapelList as $m)
                                <option value="{{ $m->id }}" {{ $selectedMapelId == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Jenis Penilaian <span class="text-rose-500">*</span>
                        </label>
                        <select name="jenis_penilaian" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                            <option value="tugas">Tugas / Asesmen Formatif (20%)</option>
                            <option value="ulangan_harian">Ulangan Harian (UH) (20%)</option>
                            <option value="praktik">Praktik / Portofolio Kejuruan (25%)</option>
                            <option value="uts">PTS / UTS (15%)</option>
                            <option value="uas">PAS / UAS (20%)</option>
                            <option value="sikap">Sikap &amp; Karakter</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Tanggal Pelaksanaan
                        </label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Judul / Materi Penilaian <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="judul" placeholder="Contoh: Tugas 1 - Sintaks Pemrograman Dasar" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            Catatan / Feedback Tambahan (Opsional)
                        </label>
                        <input type="text" name="catatan_umum" placeholder="Opsional untuk semua siswa..." class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>
                </div>

                {{-- Toolbar Autofill --}}
                <div class="flex items-center justify-between bg-blue-50/50 dark:bg-blue-950/30 px-4 py-2.5 rounded-xl border border-blue-100 dark:border-blue-900/50 shrink-0 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-700 dark:text-slate-300">Isi Cepat (Autofill):</span>
                        <input type="number" step="1" min="0" max="100" x-model="quickVal" placeholder="80" class="w-16 px-2.5 py-1 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs font-mono font-bold text-center">
                        <button type="button" @click="applyAutofill()" class="px-2.5 py-1 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 transition">
                            Terapkan ke Semua
                        </button>
                        <button type="button" @click="clearAllBatch()" class="px-2.5 py-1 rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 transition">
                            Kosongkan
                        </button>
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-slate-400">
                        * Siswa yang dikosongkan tidak akan tersimpan nilainya
                    </div>
                </div>

                {{-- Tabel Daftar Siswa Roster --}}
                <div class="flex-1 overflow-y-auto border border-slate-200 dark:border-slate-800 rounded-2xl">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="sticky top-0 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-heading uppercase tracking-wider text-[10px] z-10 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="p-3 text-center w-12">No</th>
                                <th class="p-3">Nama Siswa</th>
                                <th class="p-3 w-32 font-mono">NISN</th>
                                <th class="p-3 text-center w-40">Nilai (0 - 100)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($legerData as $idx => $row)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                    <td class="p-3 text-center font-mono text-slate-400 font-semibold">
                                        {{ $idx + 1 }}
                                    </td>
                                    <td class="p-3">
                                        <div class="font-extrabold text-slate-900 dark:text-white">
                                            {{ $row['siswa']->name }}
                                        </div>
                                    </td>
                                    <td class="p-3 font-mono text-slate-500 dark:text-slate-400">
                                        {{ $row['siswa']->nisn ?: '-' }}
                                    </td>
                                    <td class="p-3 text-center">
                                        <input type="number" step="0.1" min="0" max="100"
                                               name="nilai[{{ $row['siswa']->id }}]"
                                               x-model="batchScores[{{ $row['siswa']->id }}]"
                                               placeholder="0 - 100"
                                               class="w-28 text-center px-3 py-1.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl font-mono font-bold text-sm text-blue-600 dark:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800 shrink-0">
                    <span class="text-xs text-slate-500 dark:text-slate-400">
                        Total Siswa: <strong>{{ count($legerData) }} murid</strong>
                    </span>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="openBatch = false" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                            Batal
                        </button>
                        <button type="submit" class="btn-primary text-xs py-2.5 px-5 font-bold shadow-sm">
                            <i class="fas fa-check-double text-xs"></i> Simpan Nilai Sekelas
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════
         5. MODAL INPUT NILAI SATUAN (SINGLE ADD)
    ═══════════════════════════════════════════════════ --}}
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs px-4 overflow-y-auto">
        <div @click.away="openAdd = false" class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-xl p-6 my-8 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class="fas fa-file-circle-plus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white">Input Nilai Satuan</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Tambahkan nilai perorangan siswa</p>
                    </div>
                </div>
                <button type="button" @click="openAdd = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form action="{{ route('dashboard.nilai.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- 1. Pilih Kelas --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Kelas Siswa <span class="text-rose-500">*</span>
                        </label>
                        <select name="kelas_id" x-model="selectedKelasId" @change="onKelasChange()" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->tingkat }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Pilih Siswa --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Nama Siswa <span class="text-rose-500">*</span></span>
                            <span x-show="loadingSiswa" class="text-[10px] text-blue-600 dark:text-blue-400 font-normal">
                                <i class="fas fa-spinner fa-spin"></i> Memuat...
                            </span>
                        </div>
                        <select name="siswa_id" x-model="selectedSiswaId" :disabled="loadingSiswa || siswaOptions.length === 0" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition disabled:opacity-60" required>
                            <option value="">-- Pilih Siswa --</option>
                            <template x-for="s in siswaOptions" :key="s.id">
                                <option :value="s.id" x-text="s.name + (s.nisn ? ' (' + s.nisn + ')' : '')"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- 3. Mata Pelajaran --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Mata Pelajaran
                        </label>
                        <select name="mapel_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                            <option value="">-- Umum / Wali Kelas --</option>
                            @foreach($mapelList as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 4. Jenis Penilaian --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Jenis Penilaian <span class="text-rose-500">*</span>
                        </label>
                        <select name="jenis_penilaian" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                            <option value="tugas">Tugas (20%)</option>
                            <option value="ulangan_harian">Ulangan Harian (20%)</option>
                            <option value="praktik">Praktik / Portofolio (25%)</option>
                            <option value="uts">PTS / UTS (15%)</option>
                            <option value="uas">PAS / UAS (20%)</option>
                            <option value="sikap">Sikap &amp; Karakter</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Judul Penilaian <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="judul" placeholder="Contoh: Tugas 1 - Sintaks Pemrograman" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Nilai (0 - 100) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="nilai" placeholder="85.5" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-mono font-bold text-blue-600 dark:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal Penilaian
                        </label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Catatan / Feedback
                        </label>
                        <input type="text" name="catatan" placeholder="Opsional..." class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>
                </div>

                <div class="flex gap-2.5 justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="openAdd = false" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary text-xs py-2.5 px-5 font-bold shadow-xs">
                        <i class="fas fa-check text-xs"></i> Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════
         6. MODAL EDIT NILAI SATUAN
    ═══════════════════════════════════════════════════ --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs px-4 overflow-y-auto">
        <div @click.away="openEdit = false" class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-xl p-6 my-8 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class="fas fa-pencil text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white">Edit Nilai Siswa</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            Siswa: <span class="font-bold text-slate-800 dark:text-white" x-text="editData.siswa_name"></span> (<span x-text="editData.kelas_nama"></span>)
                        </p>
                    </div>
                </div>
                <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form :action="'{{ url('dashboard/nilai') }}/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Mata Pelajaran
                        </label>
                        <select name="mapel_id" x-model="editData.mapel_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                            <option value="">-- Umum / Wali Kelas --</option>
                            @foreach($mapelList as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Jenis Penilaian <span class="text-rose-500">*</span>
                        </label>
                        <select name="jenis_penilaian" x-model="editData.jenis_penilaian" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                            <option value="tugas">Tugas (20%)</option>
                            <option value="ulangan_harian">Ulangan Harian (20%)</option>
                            <option value="praktik">Praktik / Portofolio (25%)</option>
                            <option value="uts">PTS / UTS (15%)</option>
                            <option value="uas">PAS / UAS (20%)</option>
                            <option value="sikap">Sikap &amp; Karakter</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Judul Penilaian <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="judul" x-model="editData.judul" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Nilai (0 - 100) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="nilai" x-model="editData.nilai" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-mono font-bold text-blue-600 dark:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal Penilaian
                        </label>
                        <input type="date" name="tanggal" x-model="editData.tanggal" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Catatan / Feedback
                        </label>
                        <input type="text" name="catatan" x-model="editData.catatan" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>
                </div>

                <div class="flex gap-2.5 justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="openEdit = false" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary text-xs py-2.5 px-5 font-bold shadow-xs">
                        <i class="fas fa-check text-xs"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function rekapNilaiApp() {
        return {
            currentTab: '{{ $activeTab }}',
            openBatch: false,
            openAdd: false,
            openEdit: false,
            selectedKelasId: '{{ $selectedKelasId }}',
            selectedSiswaId: '',
            loadingSiswa: false,
            siswaOptions: [],
            editData: {},
            quickVal: '',
            batchScores: {},

            setTab(tab) {
                this.currentTab = tab;
                const url = new URL(window.location);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url);
            },

            openModalBatch() {
                this.openBatch = true;
            },

            openModalAdd() {
                this.openAdd = true;
                if (this.selectedKelasId && this.siswaOptions.length === 0) {
                    this.onKelasChange();
                }
            },

            openModalAddForStudent(siswaId, siswaName) {
                this.openAdd = true;
                this.selectedSiswaId = siswaId;
                if (this.siswaOptions.length === 0) {
                    this.onKelasChange(() => {
                        this.selectedSiswaId = siswaId;
                    });
                }
            },

            openModalEdit(data) {
                this.editData = { ...data };
                this.openEdit = true;
            },

            applyAutofill() {
                if (this.quickVal === '' || isNaN(this.quickVal)) return;
                const val = parseFloat(this.quickVal);
                @foreach($legerData as $row)
                    this.batchScores[{{ $row['siswa']->id }}] = val;
                @endforeach
            },

            clearAllBatch() {
                this.batchScores = {};
                this.quickVal = '';
            },

            onKelasChange(cb) {
                this.siswaOptions = [];
                if (!this.selectedKelasId) return;

                this.loadingSiswa = true;
                fetch(`{{ url('dashboard/kelas') }}/${this.selectedKelasId}/siswa-json`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            this.siswaOptions = res.siswa || [];
                            if (cb) cb();
                        } else {
                            this.siswaOptions = [];
                        }
                    })
                    .catch(() => {
                        this.siswaOptions = [];
                    })
                    .finally(() => {
                        this.loadingSiswa = false;
                    });
            }
        };
    }

    document.addEventListener('alpine:init', () => {
        if (typeof Alpine !== 'undefined' && Alpine.data) {
            Alpine.data('rekapNilaiApp', rekapNilaiApp);
        }
    });
</script>

@endsection
