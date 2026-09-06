@extends('layouts.dashboard')

@section('title', 'Kelola Presensi')
@section('page-title', 'Kelola Presensi')
@section('page-subtitle', 'Manajemen sesi presensi harian dan mata pelajaran SMKN 1 Beringin')

@section('content')

<div class="flex flex-col lg:flex-row gap-6 items-start">

    {{-- KIRI: FORM BUAT SESI (HANYA GURU/ADMIN) --}}
    <div class="w-full lg:w-1/3 bg-white rounded-xl shadow-xs border border-slate-200 p-5 lg:sticky lg:top-20 z-10" data-aos="fade-up" x-data="{ tab: 'kelas' }">
        <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
            <div class="w-10 h-10 bg-blue-50 text-blue-700 rounded-lg flex items-center justify-center text-lg shrink-0 border border-blue-100">
                <i class="fas fa-plus"></i>
            </div>
            <div>
                <h2 class="font-heading text-base font-bold text-slate-900">Buat Sesi Baru</h2>
                <p class="text-xs text-slate-500">Pilih sesi kelas atau sesi mata pelajaran.</p>
            </div>
        </div>

        <!-- Mode Switcher Tabs -->
        <div class="flex bg-slate-100/90 p-1 rounded-lg mb-5 border border-slate-200/60">
            <button @click="tab = 'kelas'" 
                    :class="tab === 'kelas' ? 'bg-white shadow-xs text-blue-700 font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'" 
                    class="flex-1 py-1.5 rounded-md text-xs transition-all">
                <i class="fas fa-sun text-amber-500 mr-1"></i> Sesi Kelas (Pagi)
            </button>
            <button @click="tab = 'mapel'" 
                    :class="tab === 'mapel' ? 'bg-white shadow-xs text-emerald-700 font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'" 
                    class="flex-1 py-1.5 rounded-md text-xs transition-all">
                <i class="fas fa-book-open text-emerald-600 mr-1"></i> Sesi Mapel
            </button>
        </div>

        <!-- FORM SESI KELAS -->
        <form x-show="tab === 'kelas'" action="{{ route('dashboard.presensi.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="tipe" value="kelas">
            
            <div class="bg-blue-50/70 p-3 rounded-lg border border-blue-200/70 text-xs text-blue-800 leading-relaxed">
                <i class="fas fa-info-circle text-blue-600 mr-1"></i> <strong>Sesi Kelas</strong>: Digunakan oleh Wali Kelas di pagi hari. Siswa wajib <strong>scan wajah biometrik</strong> dari perangkat masing-masing.
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Kelas <span class="text-rose-600">*</span></label>
                <div class="relative">
                    <select name="kelas_id" required class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition appearance-none">
                        <option value="">-- Pilih Rombel / Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-door-open absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-2.5 text-slate-400 text-[10px] pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mata Pelajaran <span class="text-[11px] font-normal text-slate-400 lowercase">(opsional)</span></label>
                <div class="relative">
                    <select name="mapel_id" class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition appearance-none">
                        <option value="">-- Kosongkan untuk absensi harian umum --</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-book absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-2.5 text-slate-400 text-[10px] pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Sesi <span class="text-rose-600">*</span></label>
                <div class="relative">
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition">
                    <i class="far fa-calendar absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full py-2.5 justify-center text-xs font-bold">
                <i class="fas fa-camera"></i> Buat Sesi Presensi Kelas
            </button>
        </form>

        <!-- FORM SESI MAPEL -->
        <form x-cloak x-show="tab === 'mapel'" action="{{ route('dashboard.presensi.store') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="tipe" value="mapel">

            <div class="bg-emerald-50/70 p-3 rounded-lg border border-emerald-200/70 text-xs text-emerald-900 leading-relaxed">
                <i class="fas fa-circle-check text-emerald-600 mr-1"></i> <strong>Sesi Mapel</strong>: Data kehadiran akan di-copy otomatis dari Sesi Kelas hari ini. Guru cukup mengedit siswa yang izin/sakit.
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Kelas <span class="text-rose-600">*</span></label>
                <div class="relative">
                    <select name="kelas_id" required class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition appearance-none">
                        <option value="">-- Pilih Rombel / Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-door-open absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-2.5 text-slate-400 text-[10px] pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Mata Pelajaran <span class="text-rose-600">*</span></label>
                <div class="relative">
                    <select name="mapel_id" required class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition appearance-none">
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-book-open absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-2.5 text-slate-400 text-[10px] pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jam Pelajaran (Les) <span class="text-rose-600">*</span></label>
                <div class="relative">
                    <select name="jam_pelajaran_id" required class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition appearance-none">
                        <option value="">-- Pilih Jam Pelajaran --</option>
                        @foreach($jamPelajarans as $jp)
                            <option value="{{ $jp->id }}">{{ $jp->label }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-clock absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-2.5 text-slate-400 text-[10px] pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Sesi <span class="text-rose-600">*</span></label>
                <div class="relative">
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium text-slate-800 focus:ring-2 focus:ring-emerald-600 focus:border-emerald-600 transition">
                    <i class="far fa-calendar absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>
            </div>

            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 rounded-lg text-xs transition flex items-center justify-center gap-2 shadow-xs">
                <i class="fas fa-floppy-disk"></i> Buat Sesi Pembelajaran Mapel
            </button>
        </form>
    </div>

    {{-- KANAN: LIST SESI & FILTER --}}
    <div class="w-full lg:w-2/3 space-y-4">
        
        {{-- Filter Box --}}
        <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-4 sm:p-5" data-aos="fade-up" data-aos-delay="50">
            {{-- Quick Filter Pills --}}
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100 flex-wrap">
                <span class="text-xs font-bold text-slate-500 mr-1 flex items-center gap-1.5 uppercase tracking-wider">
                    <i class="fas fa-calendar-week text-blue-600"></i> Periode:
                </span>
                
                <a href="{{ route('dashboard.presensi', array_filter(request()->except(['tanggal', 'bulan', 'periode', 'page']))) }}"
                   class="px-3 py-1 rounded-md text-xs font-bold transition {{ !request('tanggal') && !request('bulan') && !request('periode') ? 'bg-blue-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua Sesi
                </a>
                
                <a href="{{ route('dashboard.presensi', array_merge(request()->except(['tanggal', 'bulan', 'periode', 'page']), ['periode' => 'hari_ini'])) }}"
                   class="px-3 py-1 rounded-md text-xs font-bold transition {{ request('periode') == 'hari_ini' || (request('tanggal') == today()->toDateString() && !request('bulan')) ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Hari Ini
                </a>

                <a href="{{ route('dashboard.presensi', array_merge(request()->except(['tanggal', 'bulan', 'periode', 'page']), ['bulan' => date('Y-m')])) }}"
                   class="px-3 py-1 rounded-md text-xs font-bold transition {{ request('bulan') == date('Y-m') && !request('tanggal') ? 'bg-emerald-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Bulan Ini ({{ \Carbon\Carbon::now()->isoFormat('MMMM') }})
                </a>
            </div>

            <form method="GET" action="{{ route('dashboard.presensi') }}" class="flex flex-col sm:flex-row items-end gap-3 flex-wrap">
                <div class="w-full sm:w-auto flex-1 min-w-[140px]">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Filter Kelas</label>
                    <select name="kelas_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-auto flex-1 min-w-[140px]">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Filter Mapel</label>
                    <select name="mapel_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium">
                        <option value="">Semua Mapel</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-auto flex-1 min-w-[130px]">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Filter Bulan (Opsional)</label>
                    <input type="month" name="bulan" value="{{ request('bulan') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium">
                </div>
                <div class="w-full sm:w-auto flex-1 min-w-[130px]">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Tanggal Tertentu</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" placeholder="Semua Tanggal" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium">
                </div>
                <div class="w-full sm:w-auto flex items-center gap-2">
                    <button type="submit" class="w-full sm:w-auto bg-slate-900 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-slate-800 transition flex items-center justify-center gap-1.5 shadow-xs">
                        <i class="fas fa-filter text-xs"></i> Filter
                    </button>
                    @if(request('kelas_id') || request('mapel_id') || request('tanggal') || request('bulan') || request('periode'))
                        <a href="{{ route('dashboard.presensi') }}" class="w-full sm:w-auto text-xs font-semibold text-rose-600 hover:text-rose-800 hover:underline px-2 py-2 text-center whitespace-nowrap">
                            <i class="fas fa-xmark"></i> Reset
                        </a>
                    @endif
                </div>
            </form>

            @if(auth()->user()->isAdmin())
            <div class="mt-4 pt-3 border-t border-slate-100 text-right">
                <form action="{{ route('dashboard.presensi.delete-all') }}" method="POST" class="inline-block">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-3 py-1.5 rounded-md transition" onclick="return confirm('SANGAT BERBAHAYA: Anda yakin ingin menghapus SELURUH riwayat sesi presensi dari database? Tindakan ini tidak bisa dibatalkan.')">
                        <i class="fas fa-trash-can mr-1"></i> Kosongkan Semua Riwayat Presensi
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- EXPORT PDF ACCORDION --}}
        <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden" data-aos="fade-up" data-aos-delay="80" x-data="{ openExport: false }">
            <button @click="openExport = !openExport"
                class="w-full flex items-center justify-between px-5 py-3.5 hover:bg-slate-50 transition border-b border-transparent"
                :class="openExport ? 'border-slate-200 bg-slate-50/50' : ''">
                <span class="font-heading font-bold text-slate-800 text-xs flex items-center gap-2">
                    <i class="fas fa-file-pdf text-rose-600 text-sm"></i> Generator Laporan &amp; Rekap Absensi PDF
                </span>
                <span class="text-xs text-slate-500 font-semibold flex items-center gap-1.5">
                    <span x-text="openExport ? 'Tutup Panel' : 'Buka Opsi Cetak'"></span>
                    <i class="fas fa-chevron-down text-[10px] transition-transform" :class="openExport ? 'rotate-180' : ''"></i>
                </span>
            </button>

            <div x-show="openExport" x-cloak class="border-t border-slate-100 divide-y divide-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                    
                    {{-- REKAP KELAS BULANAN --}}
                    <div class="p-5">
                        <div class="flex items-center gap-2.5 mb-3">
                            <div class="w-8 h-8 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-check text-xs"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900 text-xs">Rekap Bulanan Wali Kelas</p>
                                <p class="text-[11px] text-slate-500">Format matriks tanggal aktif (H, S, I, A)</p>
                            </div>
                        </div>
                        <form action="{{ route('dashboard.presensi.pdf.bulanan.kelas') }}" method="GET" target="_blank" class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Pilih Kelas</label>
                                <select name="kelas_id" required class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Pilih Bulan</label>
                                <input type="month" name="bulan" value="{{ date('Y-m') }}" required
                                    class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium">
                            </div>
                            <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold py-2 rounded-lg transition flex items-center justify-center gap-1.5 shadow-xs">
                                <i class="fas fa-print"></i> Cetak Rekap Bulanan Kelas
                            </button>
                        </form>
                    </div>

                    {{-- REKAP MAPEL BULANAN --}}
                    <div class="p-5">
                        <div class="flex items-center gap-2.5 mb-3">
                            <div class="w-8 h-8 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book-bookmark text-xs"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900 text-xs">Rekap Pertemuan Guru Mapel</p>
                                <p class="text-[11px] text-slate-500">Format P.1, P.2... lengkap jam pelajaran</p>
                            </div>
                        </div>
                        <form action="{{ route('dashboard.presensi.pdf.bulanan.mapel') }}" method="GET" target="_blank" class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Pilih Kelas</label>
                                <select name="kelas_id" required class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Mata Pelajaran</label>
                                <select name="mapel_id" required class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium">
                                    <option value="">-- Pilih Mapel --</option>
                                    @foreach($mapel as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1">Pilih Bulan</label>
                                <input type="month" name="bulan" value="{{ date('Y-m') }}" required
                                    class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium">
                            </div>
                            <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white text-xs font-bold py-2 rounded-lg transition flex items-center justify-center gap-1.5 shadow-xs">
                                <i class="fas fa-print"></i> Cetak Rekap Pertemuan Mapel
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        {{-- Sesi List --}}
        @forelse($sesiList as $index => $sesi)
            <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-4 sm:p-5 flex flex-col md:flex-row gap-4 items-center justify-between transition-all duration-200 hover:border-blue-400 hover:shadow-md" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 30) }}">
                
                <div class="flex items-center gap-4 w-full md:w-auto">
                    {{-- Calendar Block Badge --}}
                    <div class="w-14 h-14 rounded-lg {{ $sesi->tanggal->isToday() ? 'bg-blue-700 text-white shadow-xs' : 'bg-slate-100 border border-slate-200 text-slate-700' }} flex flex-col items-center justify-center shrink-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider">{{ $sesi->tanggal->isoFormat('MMM') }}</span>
                        <span class="font-heading text-lg font-extrabold leading-none mt-0.5">{{ $sesi->tanggal->format('d') }}</span>
                        @if($sesi->tanggal->isToday())
                            <span class="text-[8px] font-bold uppercase tracking-widest text-blue-200 mt-0.5">Hari Ini</span>
                        @endif
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-heading text-base font-extrabold text-slate-900 leading-tight">
                                {{ $sesi->kelas->nama_kelas }}
                            </h3>

                            @if($sesi->tipe == 'kelas')
                                <span class="badge bg-blue-50 text-blue-700 border border-blue-200 font-bold">
                                    <i class="fas fa-sun text-amber-500"></i> Sesi Kelas (Pagi)
                                </span>
                            @else
                                <span class="badge bg-emerald-50 text-emerald-800 border border-emerald-200 font-bold">
                                    <i class="fas fa-book-open text-emerald-600"></i> Sesi Mapel
                                </span>
                            @endif

                            @if($sesi->mataPelajaran)
                                <span class="badge bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $sesi->mataPelajaran->nama_mapel }}
                                </span>
                            @endif

                            @if($sesi->jam_pelajaran)
                                <span class="badge bg-slate-50 text-slate-600 border border-slate-200 font-mono">
                                    <i class="fas fa-clock text-slate-400"></i> {{ $sesi->jam_pelajaran }}
                                </span>
                            @endif
                        </div>

                        <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-2 flex-wrap">
                            <span><i class="fas fa-user-tie text-slate-400 mr-1"></i>{{ $sesi->guru->name }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            <span><i class="far fa-calendar text-slate-400 mr-1"></i>{{ $sesi->tanggal->isoFormat('dddd, D MMMM Y') }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between md:justify-end gap-5 w-full md:w-auto mt-3 md:mt-0 pt-3 md:pt-0 border-t border-slate-100 md:border-none">
                    <div class="text-left md:text-right">
                        <p class="text-[10.5px] text-slate-500 font-bold uppercase tracking-wider mb-0.5">Kehadiran</p>
                        <p class="text-xs font-extrabold text-slate-800">
                            <span class="text-emerald-700 text-sm font-black">{{ $sesi->hadir_count }}</span>
                            <span class="text-slate-400">/</span> {{ $sesi->total_count }} Siswa
                        </p>
                    </div>

                    <a href="{{ route('dashboard.presensi.detail', $sesi) }}" class="btn-primary text-xs shrink-0 py-2 px-3.5">
                        <span>Kelola Presensi</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-slate-200 border-dashed p-10 text-center" data-aos="fade-in" data-aos-delay="100">
                <div class="w-14 h-14 bg-slate-50 border border-slate-200 rounded-xl flex items-center justify-center mx-auto mb-3.5 text-slate-400">
                    <i class="fas fa-calendar-xmark text-2xl"></i>
                </div>
                @if(request('kelas_id') || request('mapel_id') || request('tanggal') || request('bulan') || request('periode'))
                    <h3 class="font-heading text-slate-800 font-bold text-sm mb-1">Tidak ada sesi presensi yang sesuai filter</h3>
                    <p class="text-xs text-slate-500 mb-4 max-w-md mx-auto">Tidak ditemukan pertemuan presensi untuk kriteria yang dipilih. Coba ganti kelas, bulan, atau reset filter.</p>
                    <a href="{{ route('dashboard.presensi') }}" class="btn-secondary text-xs inline-flex items-center gap-1.5 py-2 px-3.5">
                        <i class="fas fa-rotate-left"></i> Tampilkan Semua Sesi
                    </a>
                @else
                    <h3 class="font-heading text-slate-800 font-bold text-sm mb-1">Belum Ada Sesi Presensi Tercatat</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Silakan pilih rombel kelas dan tanggal di panel sebelah kiri untuk membuat sesi absensi baru.</p>
                @endif
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($sesiList->hasPages())
        <div class="mt-4 bg-white rounded-xl border border-slate-200 p-3">
            {{ $sesiList->links() }}
        </div>
        @endif

    </div>
</div>

@endsection
