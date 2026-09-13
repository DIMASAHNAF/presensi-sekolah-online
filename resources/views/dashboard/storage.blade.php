@extends('layouts.dashboard')

@section('title', 'Manajemen Storage 100GB')
@section('page-title', 'Manajemen Storage')
@section('page-subtitle', 'Monitoring kapasitas mount 100GB, direktori akun, dan pembersih snapshot presensi')

@section('content')

@if(session('success'))
    <div class="mb-5 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-4 py-3 text-xs sm:text-sm flex items-center gap-2.5 shadow-2xs">
        <i class="fas fa-circle-check text-emerald-500 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('info'))
    <div class="mb-5 bg-blue-50 border border-blue-200 text-blue-800 rounded-2xl px-4 py-3 text-xs sm:text-sm flex items-center gap-2.5 shadow-2xs">
        <i class="fas fa-circle-info text-blue-500 text-base"></i>
        <span>{{ session('info') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl px-4 py-3 text-xs sm:text-sm flex items-center gap-2.5 shadow-2xs">
        <i class="fas fa-circle-xmark text-rose-500 text-base"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div x-data="{ previewModal: false, previewUrl: '', previewTitle: '' }">

    {{-- ────────────────────────────────────────────── --}}
    {{-- 1. HERO: MOUNT POINT 100GB & DISK GAUGE        --}}
    {{-- ────────────────────────────────────────────── --}}
    <div class="bg-gradient-to-br from-slate-900 via-blue-950 to-indigo-950 text-white rounded-3xl p-6 sm:p-7 shadow-lg border border-slate-800 mb-6 relative overflow-hidden">
        {{-- Subtle radial background glow --}}
        <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
            {{-- Info Mount --}}
            <div class="space-y-2 max-w-xl">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="px-3 py-1 rounded-full text-[10px] font-extrabold font-mono uppercase bg-blue-500/20 text-blue-300 border border-blue-400/30 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Storage Mounted (100 GB)
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-white/10 text-slate-300 border border-white/15">
                        {{ $diskInfo['is_writable'] ? 'Izin Akses: Read/Write OK' : 'Akses: Read-Only' }}
                    </span>
                </div>

                <h2 class="text-xl sm:text-2xl font-black font-heading tracking-tight text-white">
                    Penyimpanan Lokal Berkecepatan Tinggi
                </h2>
                
                <p class="text-xs text-slate-300 leading-relaxed font-mono flex items-center gap-2">
                    <i class="fas fa-hard-drive text-blue-400"></i>
                    <span>Mount Path: <code class="bg-black/40 px-2 py-0.5 rounded text-blue-200">{{ $diskInfo['mount_path'] }}</code></span>
                </p>
            </div>

            {{-- Metric Box --}}
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/15 w-full lg:w-72 shrink-0">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="font-bold text-slate-300">Penggunaan Disk</span>
                    <span class="font-mono font-black text-white">{{ $diskInfo['used_percent'] }}%</span>
                </div>

                {{-- Progress Bar --}}
                <div class="w-full bg-black/40 rounded-full h-3 overflow-hidden p-0.5 border border-white/10">
                    <div class="h-full rounded-full bg-gradient-to-r from-blue-400 via-indigo-400 to-emerald-400 transition-all duration-500" 
                         style="width: {{ max(1, min(100, $diskInfo['used_percent'])) }}%"></div>
                </div>

                <div class="flex items-center justify-between text-[11px] font-mono mt-2 text-slate-300">
                    <span>Terpakai: <strong class="text-white">{{ $diskInfo['used_gb'] }} GB</strong></span>
                    <span>Sisa: <strong class="text-emerald-300">{{ $diskInfo['free_gb'] }} GB</strong></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ────────────────────────────────────────────── --}}
    {{-- 2. CATEGORY BREAKDOWN CARDS                    --}}
    {{-- ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @foreach($categories as $key => $cat)
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $cat['name'] }}</p>
                <div class="flex items-baseline gap-2 mt-1">
                    <h3 class="text-2xl font-extrabold text-slate-900 font-heading">{{ $cat['count'] }}</h3>
                    <span class="text-xs text-slate-400 font-bold">file</span>
                </div>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full font-bold inline-block mt-1 bg-slate-100 text-slate-700">
                    Ukuran: {{ $cat['mb'] }} MB
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fas {{ $cat['icon'] }}"></i>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ────────────────────────────────────────────── --}}
    {{-- 3. STORAGE TOOLS & CLEANER ACTIONS             --}}
    {{-- ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
        
        {{-- Tool 1: Cleaner Presensi Harian --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-broom"></i>
                </div>
                <div>
                    <h3 class="font-heading font-extrabold text-sm text-slate-900">Pembersih Snapshot Absensi Harian</h3>
                    <p class="text-[11px] text-slate-400">Hapus file snapshot kamera lama di <code class="text-slate-600">face_scans/</code> (Log presensi di database tetap aman).</p>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="text-slate-600">Status Berkas Saat Ini:</span>
                <span class="font-mono font-bold text-slate-800">
                    {{ $categories['scans']['count'] }} file snapshot &bull; {{ $categories['scans']['mb'] }} MB
                </span>
            </div>

            <form action="{{ route('dashboard.storage.cleanup') }}" method="POST" onsubmit="return confirm('Pembersihan akan menghapus foto snapshot presensi harian pada server sesuai batas waktu yang dipilih.\n\nCatatan: Data kehadiran siswa di database tetap tersimpan rapi.\nLanjutkan?')" class="flex flex-col sm:flex-row items-stretch sm:items-end gap-3">
                @csrf
                <div class="flex-1">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Batas Usia Snapshot</label>
                    <select name="days" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="all">Hapus Semua Snapshot (Reset Bersih Total)</option>
                        <option value="1">Lebih dari 1 Hari yang Lalu (Kemarin & sebelumnya)</option>
                        <option value="7">Lebih dari 7 Hari yang Lalu (1 Minggu)</option>
                        <option value="30">Lebih dari 30 Hari yang Lalu (1 Bulan)</option>
                        <option value="60" selected>Lebih dari 60 Hari yang Lalu (2 Bulan)</option>
                        <option value="90">Lebih dari 90 Hari (1 Triwulan)</option>
                        <option value="180">Lebih dari 180 Hari (1 Semester)</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full sm:w-auto bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-xs flex items-center justify-center gap-1.5 shrink-0">
                        <i class="fas fa-trash-arrow-up"></i>
                        <span>Bersihkan Sekarang</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Tool 2: Organize Legacy Folders --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fas fa-folder-tree"></i>
                </div>
                <div>
                    <h3 class="font-heading font-extrabold text-sm text-slate-900">Rapikan Struktur Folder Akun</h3>
                    <p class="text-[11px] text-slate-400">Pindahkan folder angka lama (<code class="text-slate-600">face_enrollments/4/</code> atau <code class="text-slate-600">profiles/</code>) ke format terstruktur <code class="text-blue-600 font-bold">accounts/{nisn}_{username}/</code>.</p>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs bg-slate-50 p-3 rounded-xl border border-slate-100">
                <span class="text-slate-600">Status Organisasi:</span>
                <div>
                    @if($hasLegacyFolders)
                        <span class="inline-flex items-center gap-1.5 text-amber-700 font-bold bg-amber-50 px-2.5 py-0.5 rounded-full border border-amber-200 text-[11px]">
                            <i class="fas fa-circle-exclamation"></i> Ada folder angka legacy
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-emerald-700 font-bold bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200 text-[11px]">
                            <i class="fas fa-circle-check"></i> Seluruh akun sudah terorganisir rapi
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 pt-1">
                <p class="text-[11px] text-slate-400 leading-tight">
                    @if($hasLegacyFolders)
                        Klik tombol untuk memindahkan berkas pendaftaran lama ke folder akun resmi.
                    @else
                        Semua berkas siswa sudah tersimpan rapi di direktori masing-masing.
                    @endif
                </p>

                <form action="{{ route('dashboard.storage.organize') }}" method="POST" onsubmit="return confirm('Pindai dan rapikan seluruh berkas pendaftaran ke struktur folder baru per akun?')">
                    @csrf
                    <button type="submit" 
                            class="font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-xs flex items-center gap-1.5 shrink-0 {{ $hasLegacyFolders ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        <i class="fas fa-arrows-split-up-and-left"></i>
                        <span>{{ $hasLegacyFolders ? 'Rapikan Berkas Lama' : 'Pindai Ulang Folder' }}</span>
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- ────────────────────────────────────────────── --}}
    {{-- 4. STUDENT ACCOUNT STORAGE EXPLORER            --}}
    {{-- ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden mb-6">
        {{-- Header & Search --}}
        <div class="p-5 border-b border-slate-100 flex flex-col lg:flex-row items-center justify-between gap-4">
            <div>
                <h3 class="font-heading font-extrabold text-base text-slate-900">Penjelajah Berkas per Akun Siswa</h3>
                <p class="text-xs text-slate-400 mt-0.5">Lihat berkas foto profil, banner, dan foto biometrik yang disimpan untuk setiap siswa</p>
            </div>

            <form action="{{ route('dashboard.storage') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                <select name="kelas_id" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                <div class="relative min-w-[220px]">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari nama, NISN, atau username..." 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 placeholder-slate-400">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>

                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-xs">
                    Cari
                </button>
            </form>
        </div>

        {{-- Student Cards Grid --}}
        <div class="p-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($students as $st)
            <div class="bg-slate-50/70 border border-slate-200 rounded-2xl p-4 flex flex-col justify-between hover:bg-slate-50 transition">
                <div>
                    {{-- Student Identity --}}
                    <div class="flex items-center gap-3 pb-3 border-b border-slate-200/60">
                        <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 border border-slate-200 bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                            @if($st->avatar_url)
                                <img src="{{ $st->avatar_url }}" class="w-full h-full object-cover">
                            @else
                                <span>{{ strtoupper(mb_substr($st->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-heading font-extrabold text-xs text-slate-900 truncate">{{ $st->name }}</h4>
                            <p class="text-[11px] font-mono text-slate-400 truncate">
                                {{ '@' . $st->username }} &bull; {{ optional($st->kelas)->nama_kelas ?? '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Storage Path & Stats --}}
                    <div class="py-2.5 text-[11px] space-y-1 font-mono">
                        <p class="text-slate-500 truncate text-[10px]" title="{{ $st->storage_folder }}">
                            <i class="fas fa-folder text-blue-500 mr-1"></i> <span class="text-slate-700 font-bold">{{ $st->storage_folder }}</span>
                        </p>
                        <div class="flex items-center justify-between text-slate-600 pt-1">
                            <span>Total File: <strong>{{ $st->storage_total_files }} berkas</strong></span>
                            <span>Ukuran: <strong class="text-blue-600">{{ $st->storage_total_kb }} KB</strong></span>
                        </div>
                    </div>

                    {{-- Files List --}}
                    <div class="pt-2 border-t border-slate-200/60 space-y-1.5">
                        @forelse($st->storage_files as $file)
                        <div class="flex items-center justify-between text-[11px] p-1.5 rounded-lg bg-white border border-slate-200/80">
                            <span class="truncate flex items-center gap-1.5 text-slate-700 max-w-[150px]" title="{{ $file['path'] }}">
                                <i class="fas fa-file-image text-slate-400 text-xs"></i>
                                <span class="font-medium truncate">{{ $file['type'] }}</span>
                            </span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-[10px] font-mono text-slate-400">{{ $file['size_kb'] }} KB</span>
                                {{-- Tombol Preview --}}
                                <button type="button" @click="previewUrl = '{{ $file['url'] }}'; previewTitle = '{{ $st->name }} - {{ $file['type'] }}'; previewModal = true" 
                                        class="text-blue-600 hover:text-blue-800 p-1 font-bold text-xs" title="Lihat Foto">
                                    <i class="fas fa-eye"></i>
                                </button>
                                {{-- Tombol Hapus Berkas Spesifik --}}
                                <form action="{{ route('dashboard.storage.file.delete') }}" method="POST" onsubmit="return confirm('Hapus berkas {{ $file['type'] }} milik {{ $st->name }} secara permanen?')" class="inline-block">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="path" value="{{ $file['path'] }}">
                                    <button type="submit" class="text-rose-500 hover:text-rose-700 p-1 font-bold text-xs transition" title="Hapus Berkas dari Storage">
                                        <i class="fas fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <p class="text-[11px] text-slate-400 italic py-1 text-center">Belum ada berkas tersimpan di storage.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-slate-400">
                <i class="fas fa-folder-open text-3xl mb-2 block"></i>
                <p class="text-xs font-bold text-slate-600">Tidak ada akun siswa yang ditemukan</p>
            </div>
            @endforelse
        </div>

        @if($students->hasPages())
        <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50">
            {{ $students->links() }}
        </div>
        @endif
    </div>

    {{-- ────────────────────────────────────────────── --}}
    {{-- 5. PHOTO PREVIEW MODAL                         --}}
    {{-- ────────────────────────────────────────────── --}}
    <div x-show="previewModal" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-xs p-4 overflow-y-auto">
        <div @click.away="previewModal = false" 
             class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl relative my-auto animate-in fade-in zoom-in-95">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <h4 class="font-heading font-extrabold text-xs sm:text-sm text-slate-800 truncate max-w-xs" x-text="previewTitle"></h4>
                <button type="button" @click="previewModal = false" class="text-slate-400 hover:text-slate-700 p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 bg-slate-950 flex items-center justify-center max-h-[70vh] overflow-hidden">
                <img :src="previewUrl" alt="Preview Foto" class="max-h-[60vh] max-w-full object-contain rounded-xl shadow-md">
            </div>
            <div class="p-3 bg-slate-50 text-right">
                <a :href="previewUrl" target="_blank" download class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-3.5 py-1.5 rounded-xl inline-flex items-center gap-1.5 transition">
                    <i class="fas fa-download"></i> Buka / Download Berkas
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
