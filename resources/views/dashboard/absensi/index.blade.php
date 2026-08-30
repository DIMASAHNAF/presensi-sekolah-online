@extends('layouts.dashboard')

@section('title', 'Kelola Absensi')
@section('page-title', 'Kelola Absensi')
@section('page-subtitle', 'Manajemen sesi absensi dan kehadiran harian siswa')

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

        <form action="{{ route('dashboard.absensi.store') }}" method="POST" class="space-y-5">
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
            <form method="GET" action="{{ route('dashboard.absensi') }}" class="flex flex-col sm:flex-row items-end gap-3">
                <div class="w-full sm:w-1/3">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Filter Kelas</label>
                    <select name="kelas_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-1/3">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm">
                </div>
                <div class="w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700 transition">
                        Filter
                    </button>
                    @if(request('kelas_id') || request('tanggal'))
                        <a href="{{ route('dashboard.absensi') }}" class="inline-block mt-2 sm:mt-0 text-xs text-red-500 hover:underline sm:ml-2">Reset</a>
                    @endif
                </div>
            </form>
            @if(auth()->user()->isAdmin())
            <div class="mt-4 pt-4 border-t border-slate-100 text-right">
                <form action="{{ route('dashboard.absensi.delete-all') }}" method="POST" class="inline-block">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition" onclick="return confirm('SANGAT BERBAHAYA: Anda yakin ingin menghapus SELURUH riwayat sesi absensi dari database? Tindakan ini tidak bisa dibatalkan.')">
                        <i class="fas fa-trash-can mr-1"></i> Kosongkan Semua Riwayat Absensi
                    </button>
                </form>
            </div>
            @endif
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
                        <h3 class="text-lg font-extrabold text-slate-800">{{ $sesi->kelas->nama_kelas }}</h3>
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

                    <a href="{{ route('dashboard.absensi.detail', $sesi) }}" class="btn-primary shrink-0">
                        Kelola <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-slate-200 border-dashed p-10 text-center" data-aos="fade-in" data-aos-delay="100">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-folder-open text-3xl text-slate-300"></i>
                </div>
                <h3 class="text-slate-700 font-bold mb-1">Belum ada sesi absensi</h3>
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
