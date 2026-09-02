@extends('layouts.dashboard')

@section('title', 'Kelola Presensi')
@section('page-title', 'Kelola Presensi')
@section('page-subtitle', 'Manajemen sesi presensi dan kehadiran harian siswa')

@section('content')

{{-- FLASH MESSAGE --}}
@if(session('success'))
<div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-5 py-4 flex items-center justify-between" data-aos="fade-down">
    <div class="flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
        <div>
            <p class="font-bold text-sm">Berhasil!</p>
            <p class="text-sm mt-0.5">{{ session('success') }}</p>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 flex items-center justify-between" data-aos="fade-down">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
        <div>
            <p class="font-bold text-sm">Gagal!</p>
            <p class="text-sm mt-0.5">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<div class="flex flex-col lg:flex-row gap-6 items-start">

    {{-- KIRI: FORM BUAT SESI (HANYA GURU/ADMIN) --}}
    <div class="w-full lg:w-1/3 bg-white rounded-3xl shadow-sm border border-slate-100 p-6 lg:sticky lg:top-24 z-10" data-aos="fade-up">
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl mb-4">
            <i class="fas fa-plus"></i>
        </div>
        <h2 class="text-lg font-extrabold text-slate-800">Buat Sesi Absen</h2>
        <p class="text-sm text-slate-500 mt-1 mb-6">Pilih kelas untuk membuat sesi barcode absen hari ini.</p>

        <form action="{{ route('dashboard.presensi.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Kelas</label>
                <div class="relative">
                    <select name="kelas_id" required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-door-open absolute left-3.5 top-3 text-slate-400"></i>
                    <i class="fas fa-chevron-down absolute right-3.5 top-3 text-slate-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pilih Mata Pelajaran <span class="text-xs font-normal text-slate-400">(Opsional)</span></label>
                <div class="relative">
                    <select name="mapel_id" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none">
                        <option value="">-- Pilih Mapel --</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-book absolute left-3.5 top-3 text-slate-400"></i>
                    <i class="fas fa-chevron-down absolute right-3.5 top-3 text-slate-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jam Pelajaran <span class="text-xs font-normal text-slate-400">(Opsional)</span></label>
                <div class="relative">
                    <input type="text" name="jam_pelajaran" placeholder="Contoh: Les 1 - 2" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <i class="fas fa-clock absolute left-3.5 top-3 text-slate-400"></i>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tanggal</label>
                <div class="relative">
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <i class="fas fa-calendar absolute left-3.5 top-3 text-slate-400"></i>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                <i class="fas fa-qrcode"></i> Buat Barcode Sesi
            </button>
        </form>
    </div>

    {{-- KANAN: LIST SESI HARI INI --}}
    <div class="w-full lg:w-2/3 space-y-4">
        
        {{-- Filter Box --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4" data-aos="fade-up" data-aos-delay="50">
            <form method="GET" action="{{ route('dashboard.presensi') }}" class="flex flex-col sm:flex-row items-end gap-3 flex-wrap">
                <div class="w-full sm:w-auto flex-1 min-w-[140px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Filter Kelas</label>
                    <select name="kelas_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-auto flex-1 min-w-[140px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Filter Mapel</label>
                    <select name="mapel_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                        <option value="">Semua Mapel</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-auto flex-1 min-w-[130px]">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                </div>
                <div class="w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700 transition">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                    @if(request('kelas_id') || request('mapel_id') || request('tanggal') !== today()->toDateString())
                        <a href="{{ route('dashboard.presensi') }}" class="inline-block mt-2 sm:mt-0 text-xs text-red-500 hover:underline sm:ml-2">Reset</a>
                    @endif
                </div>
            </form>
            @if(auth()->user()->isAdmin())
            <div class="mt-4 pt-4 border-t border-slate-100 text-right">
                <form action="{{ route('dashboard.presensi.delete-all') }}" method="POST" class="inline-block">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition" onclick="return confirm('SANGAT BERBAHAYA: Anda yakin ingin menghapus SELURUH riwayat sesi presensi dari database? Tindakan ini tidak bisa dibatalkan.')">
                        <i class="fas fa-trash-can mr-1"></i> Kosongkan Semua Riwayat Presensi
                    </button>
                </form>
            </div>
            @endif
        </div>

        {{-- EXPORT PDF PANEL --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden" data-aos="fade-up" data-aos-delay="80" x-data="{ openExport: false }">
            <button @click="openExport = !openExport"
                class="w-full flex items-center justify-between px-5 py-4 hover:bg-slate-50 transition">
                <span class="font-semibold text-slate-700 text-sm flex items-center gap-2">
                    <i class="fas fa-file-pdf text-rose-500"></i> Generate Rekap PDF
                </span>
                <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="openExport ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="openExport" x-cloak x-transition class="border-t border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0 divide-y md:divide-y-0 md:divide-x divide-slate-100">

                    {{-- REKAP KELAS BULANAN --}}
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-school text-sm"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-sm">Rekap Absen Kelas</p>
                                <p class="text-xs text-slate-400">Kehadiran harian — format matriks bulanan</p>
                            </div>
                        </div>
                        <form action="{{ route('dashboard.presensi.pdf.bulanan.kelas') }}" method="GET" target="_blank" class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Kelas</label>
                                <select name="kelas_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Bulan</label>
                                <input type="month" name="bulan" value="{{ date('Y-m') }}" required
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                            </div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                                <i class="fas fa-print"></i> Cetak Rekap Kelas
                            </button>
                        </form>
                    </div>

                    {{-- REKAP MAPEL BULANAN --}}
                    <div class="p-5">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book-open text-sm"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-800 text-sm">Rekap Absen Mapel</p>
                                <p class="text-xs text-slate-400">Per pertemuan mapel — format matriks bulanan</p>
                            </div>
                        </div>
                        <form action="{{ route('dashboard.presensi.pdf.bulanan.mapel') }}" method="GET" target="_blank" class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Kelas</label>
                                <select name="kelas_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Mata Pelajaran</label>
                                <select name="mapel_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                                    <option value="">-- Pilih Mapel --</option>
                                    @foreach($mapel as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Bulan</label>
                                <input type="month" name="bulan" value="{{ date('Y-m') }}" required
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                            </div>
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                                <i class="fas fa-print"></i> Cetak Rekap Mapel
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        {{-- Sesi List --}}
        @forelse($sesiList as $index => $sesi)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col md:flex-row gap-5 items-center justify-between transition hover:border-blue-200 hover:shadow-md" data-aos="fade-up" data-aos-delay="{{ 100 + ($index * 50) }}">
                
                <div class="flex items-center gap-5 w-full md:w-auto">
                    <div class="w-16 h-16 rounded-2xl {{ $sesi->tanggal->isToday() ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'bg-slate-100 text-slate-500' }} flex flex-col items-center justify-center shrink-0">
                        <span class="text-xs font-bold uppercase">{{ $sesi->tanggal->isoFormat('MMM') }}</span>
                        <span class="text-xl font-extrabold leading-none">{{ $sesi->tanggal->format('d') }}</span>
                    </div>

                    <div>
                        <h3 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                            {{ $sesi->kelas->nama_kelas }}
                            @if($sesi->mataPelajaran)
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-0.5 rounded-md">{{ $sesi->mataPelajaran->nama_mapel }}</span>
                            @endif
                            @if($sesi->jam_pelajaran)
                                <span class="bg-slate-100 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-md"><i class="fas fa-clock mr-1"></i>{{ $sesi->jam_pelajaran }}</span>
                            @endif
                        </h3>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                            <span><i class="fas fa-user text-slate-400 mr-1"></i> {{ $sesi->guru->name }} (Pembuat)</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            <span>{{ $sesi->tanggal->isoFormat('dddd, D MMMM Y') }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between md:justify-end gap-5 w-full md:w-auto mt-4 md:mt-0 pt-4 md:pt-0 border-t border-slate-100 md:border-none">
                    <div class="text-center md:text-right">
                        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider mb-0.5">Kehadiran</p>
                        <p class="text-sm font-bold text-slate-800">
                            <span class="text-emerald-600">{{ $sesi->hadir_count }}</span> / {{ $sesi->total_count }} Siswa
                        </p>
                    </div>

                    <a href="{{ route('dashboard.presensi.detail', $sesi) }}" class="btn-primary shrink-0">
                        Kelola <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-slate-200 border-dashed p-10 text-center" data-aos="fade-in" data-aos-delay="100">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-folder-open text-3xl text-slate-300"></i>
                </div>
                <h3 class="text-slate-700 font-bold mb-1">Belum ada sesi presensi</h3>
                <p class="text-sm text-slate-500">Silakan buat sesi baru untuk kelas yang akan Anda ajar.</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $sesiList->links() }}
        </div>

    </div>
</div>

@endsection
