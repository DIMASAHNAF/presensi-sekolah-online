@extends('layouts.dashboard')

@section('title', 'Rekap Nilai Siswa')
@section('page-title', 'Rekap Nilai Siswa')
@section('page-subtitle', 'Kelola rekapitulasi penilaian dan nilai akademik siswa secara komprehensif')

@section('content')

<div x-data="rekapNilaiApp()" class="space-y-6">

    {{-- ═══════════════════════════════════════════════════
         HEADER & ACTION BANNER
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 sm:p-6" data-aos="fade-down">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="flex items-start gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20">
                    <i class="fas fa-graduation-cap text-xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            CRUD Aktif
                        </span>
                        <span class="text-slate-300 dark:text-slate-600 text-xs">•</span>
                        <span class="text-slate-500 dark:text-slate-400 text-xs font-mono font-medium">T.A. 2026/2027</span>
                        <span class="text-slate-300 dark:text-slate-600 text-xs">•</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">Semester Ganjil</span>
                    </div>
                    <h1 class="text-lg sm:text-xl font-heading font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Rekapitulasi &amp; Pengelolaan Nilai Siswa
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 max-w-2xl leading-relaxed">
                        Kelola penilaian akademik (Tugas, Ulangan Harian, PTS, PAS, Praktik, Sikap) per siswa berdasarkan rombel kelas secara langsung dari sistem presensi terpadu.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 w-full sm:w-auto shrink-0">
                <button type="button" @click="openModalAdd()" class="btn-primary text-xs py-2.5 px-4 shadow-sm w-full sm:w-auto justify-center">
                    <i class="fas fa-plus-circle text-xs"></i>
                    <span>Input Nilai Baru</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         RINGKASAN METRIK / STAT CARDS
    ══════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Penilaian --}}
        <div class="stat-card accent-blue" data-aos="fade-up" data-aos-delay="0">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-signature text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-300 uppercase tracking-wider font-mono bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-2 py-0.5 rounded">Penilaian</span>
            </div>
            <p class="text-3xl sm:text-4xl font-heading font-extrabold text-slate-900 dark:text-white leading-none mb-1">{{ number_format($statsNilai['total_input']) }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-300 font-medium">Total Entri Nilai Tersimpan</p>
        </div>

        {{-- Rata-Rata Nilai --}}
        <div class="stat-card accent-indigo" data-aos="fade-up" data-aos-delay="60">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-300 uppercase tracking-wider font-mono bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 px-2 py-0.5 rounded">Skor / 100</span>
            </div>
            <p class="text-3xl sm:text-4xl font-heading font-extrabold text-indigo-600 dark:text-indigo-400 leading-none mb-1">{{ number_format($statsNilai['rata_rata'], 1) }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-300 font-medium">Rata-Rata Nilai Keseluruhan</p>
        </div>

        {{-- Nilai Tertinggi --}}
        <div class="stat-card accent-green" data-aos="fade-up" data-aos-delay="120">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 rounded-lg flex items-center justify-center">
                    <i class="fas fa-award text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider font-mono bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 px-2 py-0.5 rounded">Maksimal</span>
            </div>
            <p class="text-3xl sm:text-4xl font-heading font-extrabold text-emerald-600 dark:text-emerald-400 leading-none mb-1">{{ $statsNilai['tertinggi'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-300 font-medium">Skor Tertinggi Siswa</p>
        </div>

        {{-- Nilai Terendah --}}
        <div class="stat-card accent-amber" data-aos="fade-up" data-aos-delay="180">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 rounded-lg flex items-center justify-center">
                    <i class="fas fa-arrow-down-short-wide text-sm"></i>
                </div>
                <span class="text-[10px] font-bold text-amber-700 dark:text-amber-300 uppercase tracking-wider font-mono bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 px-2 py-0.5 rounded">Minimal</span>
            </div>
            <p class="text-3xl sm:text-4xl font-heading font-extrabold text-amber-600 dark:text-amber-400 leading-none mb-1">{{ $statsNilai['terendah'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-300 font-medium">Skor Terendah Siswa</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         FILTER & PENCARIAN DATA
    ══════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-4 sm:p-5" data-aos="fade-up">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-filter text-blue-600 dark:text-blue-400 text-xs"></i>
                <h2 class="font-heading font-bold text-xs uppercase tracking-wider text-slate-800 dark:text-white">Filter &amp; Pencarian Rekap Nilai</h2>
            </div>
            @if(request()->hasAny(['search', 'nilai_kelas_id', 'nilai_mapel_id', 'nilai_jenis', 'hanya_saya']))
                <span class="text-[10px] font-semibold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/80 px-2.5 py-0.5 rounded-full border border-blue-200 dark:border-blue-800">
                    Filter Aktif
                </span>
            @endif
        </div>

        <form method="GET" action="{{ route('dashboard.nilai') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
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
                    <option value="uts" {{ request('nilai_jenis') == 'uts' ? 'selected' : '' }}>PTS / UTS</option>
                    <option value="uas" {{ request('nilai_jenis') == 'uas' ? 'selected' : '' }}>PAS / UAS</option>
                    <option value="praktik" {{ request('nilai_jenis') == 'praktik' ? 'selected' : '' }}>Praktik / Portofolio</option>
                    <option value="sikap" {{ request('nilai_jenis') == 'sikap' ? 'selected' : '' }}>Sikap &amp; Karakter</option>
                </select>
            </div>

            {{-- Filter Khusus Guru: Input Saya Sendiri --}}
            @if(!auth()->user()->isAdmin())
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Kepemilikan</label>
                <select name="hanya_saya" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <option value="0" {{ request('hanya_saya', '0') == '0' ? 'selected' : '' }}>Semua Penginput</option>
                    <option value="1" {{ request('hanya_saya') == '1' ? 'selected' : '' }}>Hanya Input Saya</option>
                </select>
            </div>
            @endif

            {{-- Tombol Terapkan & Reset --}}
            <div class="flex items-end gap-2 {{ auth()->user()->isAdmin() ? 'lg:col-span-2' : 'lg:col-span-12 xl:col-span-2' }}">
                <button type="submit" class="btn-primary text-xs py-2 px-3.5 flex-1 justify-center">
                    <i class="fas fa-filter text-[11px]"></i> Terapkan
                </button>
                @if(request()->hasAny(['search', 'nilai_kelas_id', 'nilai_mapel_id', 'nilai_jenis', 'hanya_saya']))
                    <a href="{{ route('dashboard.nilai') }}" class="btn-secondary text-xs py-2 px-3 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white" title="Reset Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ═══════════════════════════════════════════════════
         TABEL REKAP NILAI SISWA
    ═══════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden" data-aos="fade-up">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h2 class="font-heading font-bold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                    <i class="fas fa-table-list text-blue-600 dark:text-blue-400"></i>
                    Daftar Rekap Nilai Akademik
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Menampilkan <span class="font-bold text-slate-800 dark:text-slate-200">{{ $listNilai->total() }}</span> entri penilaian siswa
                </p>
            </div>

            <div class="flex items-center gap-2 self-end sm:self-auto">
                <button type="button" @click="openModalAdd()" class="btn-secondary text-xs py-1.5 px-3">
                    <i class="fas fa-plus text-[10px] text-blue-600"></i> Tambah Nilai
                </button>
            </div>
        </div>

        {{-- MOBILE CARDS VIEW (md:hidden) --}}
        <div class="md:hidden space-y-3 p-3.5 bg-slate-50/50 dark:bg-slate-900/50 rounded-xl mb-4">
            @forelse($listNilai as $n)
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/90 dark:border-slate-800 p-4 shadow-xs transition-all duration-200 hover:border-blue-300">
                    {{-- Header: Siswa + Nilai & Predikat --}}
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold text-xs shrink-0 shadow-2xs">
                                {{ strtoupper(substr($n->siswa->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-heading font-extrabold text-sm text-slate-900 dark:text-white truncate">
                                    {{ $n->siswa->name ?? '-' }}
                                </h4>
                                <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                                    NISN: {{ $n->siswa->nisn ?? '-' }} • <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $n->kelas->nama_kelas ?? '-' }}</span>
                                </p>
                            </div>
                        </div>

                        {{-- Nilai & Predikat Badge --}}
                        <div class="flex items-center gap-1.5 shrink-0">
                            <span class="font-mono text-base font-extrabold px-2.5 py-1 rounded-lg border inline-block
                                {{ $n->nilai >= 80
                                    ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'
                                    : ($n->nilai >= 70
                                        ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800'
                                        : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800') }}">
                                {{ number_format($n->nilai, 1) }}
                            </span>
                            <span class="w-7 h-7 inline-flex items-center justify-center font-heading font-extrabold text-xs rounded-full
                                {{ $n->predikat === 'A' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200' :
                                   ($n->predikat === 'B' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200' :
                                   ($n->predikat === 'C' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200')) }}">
                                {{ $n->predikat }}
                            </span>
                        </div>
                    </div>

                    {{-- Metadata Box (Gaya SIM-SARPRAS) --}}
                    <div class="bg-slate-50/80 dark:bg-slate-800/60 rounded-xl p-3 border border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-3 text-xs mb-3">
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Mata Pelajaran</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-xs block truncate" title="{{ $n->mataPelajaran->nama_mapel ?? 'Umum / Wali Kelas' }}">
                                {{ $n->mataPelajaran->nama_mapel ?? 'Umum / Wali Kelas' }}
                            </span>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded inline-block mt-1
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
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Judul Penilaian</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200 text-xs block truncate" title="{{ $n->judul }}">
                                {{ $n->judul }}
                            </span>
                            @if($n->tanggal)
                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono block mt-1">
                                    <i class="far fa-calendar text-[9px] mr-1"></i>{{ $n->tanggal->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($n->catatan)
                        <div class="mb-3">
                            <p class="text-[11px] text-slate-600 dark:text-slate-400 italic bg-slate-50 dark:bg-slate-800/80 p-2 rounded-lg border border-slate-100 dark:border-slate-800">
                                "{{ $n->catatan }}"
                            </p>
                        </div>
                    @endif

                    {{-- Action Footer --}}
                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <i class="fas fa-chalkboard-user text-[10px]"></i>
                            <span>{{ $n->guru->name ?? '-' }}</span>
                        </span>

                        <div>
                            @if(auth()->user()->isAdmin() || $n->guru_id === auth()->id())
                                <div class="flex items-center gap-2">
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
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 text-xs font-bold transition">
                                        <i class="fas fa-pencil text-[10px]"></i> Edit
                                    </button>

                                    <form action="{{ route('dashboard.nilai.destroy', $n) }}" method="POST" class="inline-block"
                                          data-confirm="Apakah Anda yakin ingin menghapus data nilai {{ addslashes($n->judul) }} untuk siswa {{ addslashes($n->siswa->name ?? '') }}?"
                                          data-confirm-title="Hapus Nilai Siswa?"
                                          data-confirm-type="danger"
                                          data-confirm-btn="Ya, Hapus Nilai">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800 text-xs font-bold transition"
                                                title="Hapus Nilai">
                                            <i class="fas fa-trash-can text-[10px]"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-[10px] text-slate-400 italic">Hanya Baca</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-10 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4">
                    <i class="fas fa-file-pen text-3xl text-slate-300 dark:text-slate-600 mb-2"></i>
                    <p class="text-sm font-semibold text-slate-700 dark:text-white">Belum Ada Data Nilai</p>
                </div>
            @endforelse
        </div>

        {{-- DESKTOP TABLE VIEW (hidden md:block) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="text-slate-500 dark:text-slate-400 bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 font-heading uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5 font-bold">Siswa &amp; Kelas</th>
                        <th class="p-3.5 font-bold">Mata Pelajaran</th>
                        <th class="p-3.5 font-bold">Judul &amp; Jenis Penilaian</th>
                        <th class="p-3.5 font-bold text-center">Nilai</th>
                        <th class="p-3.5 font-bold text-center">Predikat</th>
                        <th class="p-3.5 font-bold">Guru Penginput</th>
                        <th class="p-3.5 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($listNilai as $n)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            {{-- Siswa & Kelas --}}
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

                            {{-- Mata Pelajaran --}}
                            <td class="p-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    <i class="fas fa-book-bookmark text-[10px]"></i>
                                    {{ $n->mataPelajaran->nama_mapel ?? 'Umum / Wali Kelas' }}
                                </span>
                            </td>

                            {{-- Judul & Jenis --}}
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

                            {{-- Nilai Angka --}}
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

                            {{-- Predikat Huruf --}}
                            <td class="p-3.5 text-center">
                                <span class="w-7 h-7 inline-flex items-center justify-center font-heading font-extrabold text-xs rounded-full
                                    {{ $n->predikat === 'A' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200' :
                                       ($n->predikat === 'B' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200' :
                                       ($n->predikat === 'C' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200')) }}">
                                    {{ $n->predikat }}
                                </span>
                            </td>

                            {{-- Guru Penginput --}}
                            <td class="p-3.5">
                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                    <i class="fas fa-chalkboard-user text-slate-400 text-[10px]"></i>
                                    {{ $n->guru->name ?? '-' }}
                                </p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">
                                    {{ $n->created_at ? $n->created_at->diffForHumans() : '-' }}
                                </p>
                            </td>

                            {{-- Aksi Edit & Hapus (Sesuai Hak Akses) --}}
                            <td class="p-3.5 text-right">
                                @if(auth()->user()->isAdmin() || $n->guru_id === auth()->id())
                                    <div class="inline-flex items-center gap-1.5 justify-end">
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
                                                class="w-7 h-7 rounded-lg text-blue-600 hover:text-white hover:bg-blue-600 dark:text-blue-400 dark:hover:bg-blue-600 border border-blue-200 dark:border-blue-800 flex items-center justify-center transition"
                                                title="Edit Nilai">
                                            <i class="fas fa-pencil text-[10px]"></i>
                                        </button>

                                        <form action="{{ route('dashboard.nilai.destroy', $n) }}" method="POST" class="inline-block"
                                              data-confirm="Apakah Anda yakin ingin menghapus data nilai {{ addslashes($n->judul) }} untuk siswa {{ addslashes($n->siswa->name ?? '') }}?"
                                              data-confirm-title="Hapus Nilai Siswa?"
                                              data-confirm-type="danger"
                                              data-confirm-btn="Ya, Hapus Nilai">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="w-7 h-7 rounded-lg text-rose-600 hover:text-white hover:bg-rose-600 dark:text-rose-400 dark:hover:bg-rose-600 border border-rose-200 dark:border-rose-800 flex items-center justify-center transition"
                                                    title="Hapus Nilai">
                                                <i class="fas fa-trash-can text-[10px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">Hanya Baca</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-14 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-500 dark:text-blue-400 flex items-center justify-center mx-auto mb-3 border border-blue-100 dark:border-blue-900">
                                    <i class="fas fa-file-pen text-2xl"></i>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Belum Ada Data Nilai</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                                    @if(request()->hasAny(['search', 'nilai_kelas_id', 'nilai_mapel_id', 'nilai_jenis', 'hanya_saya']))
                                        Tidak ada data penilaian yang sesuai dengan filter yang dipilih. Silakan atur ulang filter pencarian Anda.
                                    @else
                                        Pilih tombol "Input Nilai Baru" untuk menambahkan rekapitulasi nilai per siswa berdasarkan rombel kelas.
                                    @endif
                                </p>
                                @if(request()->hasAny(['search', 'nilai_kelas_id', 'nilai_mapel_id', 'nilai_jenis', 'hanya_saya']))
                                    <div class="mt-4">
                                        <a href="{{ route('dashboard.nilai') }}" class="btn-secondary text-xs py-2 px-3">
                                            <i class="fas fa-rotate-left"></i> Reset Filter
                                        </a>
                                    </div>
                                @else
                                    <div class="mt-4">
                                        <button type="button" @click="openModalAdd()" class="btn-primary text-xs py-2 px-3.5">
                                            <i class="fas fa-plus"></i> Input Nilai Sekarang
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        @if($listNilai->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $listNilai->links() }}
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════
         MODAL TAMBAH NILAI (PILIH KELAS → LOAD SISWA)
    ═══════════════════════════════════════════════════ --}}
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs px-4 overflow-y-auto">
        <div @click.away="openAdd = false" class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-xl p-6 my-8 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class="fas fa-file-circle-plus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white">Input Nilai Siswa</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih kelas, lalu tentukan siswa yang akan dinilai</p>
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

                    {{-- 2. Pilih Siswa (Dinamis dari Kelas) --}}
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
                                <option :value="s.id" x-text="s.name + (s.nisn ? ' (NISN: ' + s.nisn + ')' : '')"></option>
                            </template>
                        </select>
                        <p x-show="!selectedKelasId" class="text-[10px] text-slate-400 mt-1">Pilih kelas terlebih dahulu untuk melihat daftar siswa.</p>
                        <p x-show="selectedKelasId && !loadingSiswa && siswaOptions.length === 0" class="text-[10px] text-amber-500 mt-1">Belum ada siswa terdaftar di kelas ini.</p>
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
                            <option value="tugas">Tugas</option>
                            <option value="ulangan_harian">Ulangan Harian (UH)</option>
                            <option value="uts">PTS / UTS</option>
                            <option value="uas">PAS / UAS</option>
                            <option value="praktik">Praktik / Portofolio</option>
                            <option value="sikap">Sikap &amp; Karakter</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    {{-- 5. Judul / Materi Penilaian --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Judul / Deskripsi Singkat <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="judul" placeholder="Contoh: Tugas 1 - Logika Pemrograman" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>

                    {{-- 6. Nilai Angka (0-100) --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Nilai (0 - 100) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="nilai" placeholder="85.5" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-mono font-bold text-blue-600 dark:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- 7. Tanggal Penilaian --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal Penilaian
                        </label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>

                    {{-- 8. Catatan Guru --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Catatan / Feedback (Opsional)
                        </label>
                        <input type="text" name="catatan" placeholder="Contoh: Sangat baik, teliti menyelesaikan studi kasus" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>
                </div>

                <div class="flex gap-2.5 justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="openAdd = false" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary text-xs py-2.5 px-5 shadow-xs">
                        <i class="fas fa-floppy-disk text-xs"></i> Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         MODAL EDIT NILAI
    ═══════════════════════════════════════════════════ --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs px-4 overflow-y-auto">
        <div @click.away="openEdit = false" class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-lg p-6 my-8 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <i class="fas fa-pencil text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white">Perbarui Nilai Siswa</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400" x-text="editData.siswa_name + ' • ' + editData.kelas_nama"></p>
                    </div>
                </div>
                <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form :action="'{{ url('dashboard/nilai') }}/' + editData.id" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- Mapel --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Mata Pelajaran</label>
                        <select name="mapel_id" x-model="editData.mapel_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                            <option value="">-- Umum / Wali Kelas --</option>
                            @foreach($mapelList as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jenis Penilaian --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Jenis Penilaian <span class="text-rose-500">*</span></label>
                        <select name="jenis_penilaian" x-model="editData.jenis_penilaian" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                            <option value="tugas">Tugas</option>
                            <option value="ulangan_harian">Ulangan Harian (UH)</option>
                            <option value="uts">PTS / UTS</option>
                            <option value="uas">PAS / UAS</option>
                            <option value="praktik">Praktik / Portofolio</option>
                            <option value="sikap">Sikap &amp; Karakter</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    {{-- Judul --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Judul Penilaian <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" x-model="editData.judul" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>

                    {{-- Nilai Angka --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Nilai (0 - 100) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.1" min="0" max="100" name="nilai" x-model="editData.nilai" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-mono font-bold text-blue-600 dark:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- Tanggal --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Tanggal</label>
                        <input type="date" name="tanggal" x-model="editData.tanggal" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Catatan / Feedback</label>
                        <input type="text" name="catatan" x-model="editData.catatan" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>
                </div>

                <div class="flex gap-2.5 justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="openEdit = false" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary text-xs py-2.5 px-5 shadow-xs">
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
            openAdd: false,
            openEdit: false,
            selectedKelasId: '',
            selectedSiswaId: '',
            loadingSiswa: false,
            siswaOptions: [],
            editData: {},

            openModalAdd() {
                this.openAdd = true;
                const urlParams = new URLSearchParams(window.location.search);
                const currentKelas = urlParams.get('nilai_kelas_id');
                if (currentKelas && !this.selectedKelasId) {
                    this.selectedKelasId = currentKelas;
                    this.onKelasChange();
                }
            },

            openModalEdit(data) {
                this.editData = { ...data };
                this.openEdit = true;
            },

            onKelasChange() {
                this.siswaOptions = [];
                this.selectedSiswaId = '';
                if (!this.selectedKelasId) return;

                this.loadingSiswa = true;
                fetch(`{{ url('dashboard/kelas') }}/${this.selectedKelasId}/siswa-json`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            this.siswaOptions = res.siswa || [];
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
