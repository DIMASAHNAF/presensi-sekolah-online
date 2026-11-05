@extends('layouts.dashboard')

@section('title', 'Buku Izin & Sakit Siswa')
@section('page-title', 'Buku Izin & Sakit Siswa')
@section('page-subtitle', 'Pencatatan surat izin, sakit, dan dispensasi resmi siswa SMKN 1 Beringin')

@section('content')
<div class="space-y-6" x-data="bukuIzinApp()">

    {{-- 1. KPI SUMMARY (BENTO GRID) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        {{-- Card 1: Sakit Hari Ini --}}
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sakit Hari Ini</p>
                <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900 dark:text-white mt-1 tabular-nums">{{ $stats['total_sakit'] }}</p>
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-0.5">Dispensasi kesehatan</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-notes-medical"></i>
            </div>
        </div>

        {{-- Card 2: Izin Hari Ini --}}
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Izin Hari Ini</p>
                <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900 dark:text-white mt-1 tabular-nums">{{ $stats['total_izin'] }}</p>
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-medium mt-0.5">Keperluan keluarga/dinas</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-envelope-open-text"></i>
            </div>
        </div>

        {{-- Card 3: Surat Fisik Terlampir --}}
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Surat Terlampir</p>
                <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900 dark:text-white mt-1 tabular-nums">{{ $stats['surat_terlampir'] }}</p>
                <p class="text-[11px] text-blue-600 dark:text-blue-400 font-medium mt-0.5">Bukti foto / dokumen PDF</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-file-shield"></i>
            </div>
        </div>

        {{-- Card 4: Total Arsip Izin --}}
        <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Riwayat</p>
                <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900 dark:text-white mt-1 tabular-nums">{{ $stats['total_riwayat'] }}</p>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium mt-0.5">Semua catatan perizinan</p>
            </div>
            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>
    </div>

    {{-- 2. ACTION BAR & FILTER CONTAINER --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-600 dark:text-blue-400"></i>
                    <span>Daftar Surat Izin &amp; Sakit Siswa</span>
                    @if($kelasBinaan)
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            Kelas Binaan: {{ $kelasBinaan->nama_kelas }}
                        </span>
                    @endif
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Siswa yang tercatat di sini otomatis disinkronkan ke sesi presensi harian.</p>
            </div>

            <button type="button" @click="openCreateModal()"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-lg text-xs font-bold transition shadow-xs">
                <i class="fas fa-plus"></i>
                <span>Catat Izin / Sakit Baru</span>
            </button>
        </div>

        {{-- Filter Row --}}
        <form method="GET" action="{{ route('dashboard.izin') }}" class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Search Nama Siswa / NISN --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Cari Siswa</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama / NISN..."
                        class="w-full pl-8 pr-3 py-1.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    <i class="fas fa-search absolute left-2.5 top-2.5 text-slate-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            {{-- Filter Kelas --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Rombel / Kelas</label>
                <select name="kelas_id" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    <option value="">Semua Rombel</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Jenis --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Jenis Dispensasi</label>
                <select name="jenis" class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    <option value="">Semua Jenis</option>
                    <option value="sakit" {{ request('jenis') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ request('jenis') === 'izin' ? 'selected' : '' }}>Izin Resmi</option>
                </select>
            </div>

            {{-- Filter Tanggal Aktif --}}
            <div>
                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-1">Tanggal Berlaku</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
            </div>

            {{-- Tombol Aksi Filter --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold py-1.5 px-3 rounded-lg text-xs transition flex items-center justify-center gap-1.5 shadow-xs">
                    <i class="fas fa-filter text-[10px]"></i>
                    <span>Terapkan</span>
                </button>
                @if(request()->hasAny(['search', 'kelas_id', 'jenis', 'tanggal', 'bulan']))
                    <a href="{{ route('dashboard.izin') }}" class="px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-semibold transition" title="Reset filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- 3. TABEL DATA DAFTAR PERIZINAN --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        @if($perizinanList->isEmpty())
            <div class="py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 flex items-center justify-center mx-auto mb-3 text-xl">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">Belum Ada Catatan Surat Izin / Sakit</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                    Surat izin atau dispensasi sakit siswa yang dicatat akan muncul di sini dan otomatis tersinkron ke sesi presensi.
                </p>
                <button type="button" @click="openCreateModal()" class="mt-4 inline-flex items-center gap-2 px-3.5 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus"></i> Catat Surat Pertama
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-800/60 border-b border-slate-200/80 dark:border-slate-800 text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[10.5px] font-bold">
                            <th class="py-3 px-4">Siswa &amp; Kelas</th>
                            <th class="py-3 px-4">Jenis &amp; Status</th>
                            <th class="py-3 px-4">Periode Tanggal</th>
                            <th class="py-3 px-4">Alasan / Keterangan</th>
                            <th class="py-3 px-4 text-center">Berkas Surat</th>
                            <th class="py-3 px-4">Diverifikasi Oleh</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($perizinanList as $izin)
                            @php
                                $isToday = today()->between($izin->tanggal_mulai, $izin->tanggal_selesai);
                                $daysDiff = $izin->tanggal_mulai->diffInDays($izin->tanggal_selesai) + 1;
                            @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                                {{-- Siswa & Kelas --}}
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($izin->siswa->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 dark:text-white truncate">{{ $izin->siswa->name ?? '-' }}</p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1.5 mt-0.5">
                                                <span class="font-semibold text-blue-700 dark:text-blue-300">{{ $izin->kelas->nama_kelas ?? '-' }}</span>
                                                <span class="text-slate-300 dark:text-slate-700">•</span>
                                                <span class="tabular-nums">NISN: {{ $izin->siswa->nisn ?? '-' }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Jenis & Status --}}
                                <td class="py-3 px-4">
                                    @if($izin->jenis === 'sakit')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            <i class="fas fa-notes-medical text-[10px]"></i> Sakit
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-50 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            <i class="fas fa-envelope-open-text text-[10px]"></i> Izin Resmi
                                        </span>
                                    @endif
                                    @if($isToday)
                                        <span class="inline-block mt-1 text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-1.5 py-0.2 rounded border border-blue-200 dark:border-blue-800">
                                            Aktif Hari Ini
                                        </span>
                                    @endif
                                </td>

                                {{-- Periode Tanggal --}}
                                <td class="py-3 px-4">
                                    <div class="text-xs text-slate-800 dark:text-slate-200 font-medium">
                                        @if($izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                                            <span>{{ $izin->tanggal_mulai->isoFormat('D MMM Y') }}</span>
                                        @else
                                            <span>{{ $izin->tanggal_mulai->format('d M') }} - {{ $izin->tanggal_selesai->isoFormat('D MMM Y') }}</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-semibold mt-0.5 inline-block">
                                        Durasi: {{ $daysDiff }} Hari
                                    </span>
                                </td>

                                {{-- Alasan / Keterangan --}}
                                <td class="py-3 px-4 max-w-xs">
                                    <p class="text-xs text-slate-700 dark:text-slate-300 line-clamp-2 leading-relaxed">
                                        {{ $izin->keterangan ?: 'Tanpa keterangan khusus.' }}
                                    </p>
                                </td>

                                {{-- Berkas Surat --}}
                                <td class="py-3 px-4 text-center">
                                    @if($izin->surat_file)
                                        <button type="button" @click="previewSurat('{{ $izin->surat_url }}', '{{ $izin->is_pdf ? 'pdf' : 'image' }}', '{{ addslashes($izin->siswa->name ?? 'Surat Izin') }}', '{{ $izin->tanggal_mulai->format('d M Y') }}')"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/70 border border-blue-200 dark:border-blue-800 hover:bg-blue-100 transition shadow-2xs">
                                            <i class="fas {{ $izin->is_pdf ? 'fa-file-pdf text-rose-500' : 'fa-image text-blue-500' }}"></i>
                                            <span>Lihat Surat</span>
                                        </button>
                                    @else
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500 italic">Tanpa Lampiran</span>
                                    @endif
                                </td>

                                {{-- Diverifikasi Oleh --}}
                                <td class="py-3 px-4">
                                    <p class="font-medium text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                        <i class="fas fa-user-check text-[10px] text-emerald-500"></i>
                                        <span>{{ $izin->diajukanOleh->name ?? 'Sistem' }}</span>
                                    </p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">
                                        {{ $izin->created_at->format('d M Y, H:i') }}
                                    </p>
                                </td>

                                {{-- Aksi Hapus --}}
                                <td class="py-3 px-4 text-right">
                                    <form action="{{ route('dashboard.izin.destroy', $izin) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Hapus catatan surat izin untuk {{ addslashes($izin->siswa->name ?? 'siswa ini') }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition" title="Hapus catatan izin">
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($perizinanList->hasPages())
                <div class="px-5 py-3.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
                    {{ $perizinanList->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- 4. MODAL INPUT PERIZINAN BARU --}}
    <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/50 backdrop-blur-xs">
        <div @click.away="showCreateModal = false" class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden flex flex-col max-h-[90vh]">
            {{-- Modal Header --}}
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-file-pen text-blue-600 dark:text-blue-400"></i> Catat Surat Izin / Sakit Baru
                    </h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Dispensasi resmi akan langsung disinkronkan ke sesi presensi.</p>
                </div>
                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Modal Body Form --}}
            <form action="{{ route('dashboard.izin.store') }}" method="POST" enctype="multipart/form-data" class="overflow-y-auto p-5 space-y-4 flex-1">
                @csrf

                {{-- Pilih Siswa --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Pilih Siswa <span class="text-rose-500">*</span>
                    </label>
                    <select name="siswa_id" required class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                        <option value="">-- Cari &amp; Pilih Siswa --</option>
                        @foreach($siswaList as $s)
                            <option value="{{ $s->id }}">
                                {{ $s->name }} ({{ $s->kelas->nama_kelas ?? 'Kelas -' }} • NISN: {{ $s->nisn ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Switcher Jenis: Sakit vs Izin --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Jenis Keterangan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                            :class="formJenis === 'sakit' ? 'border-emerald-500 bg-emerald-50/60 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-200 font-bold' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-600 dark:text-slate-400'">
                            <input type="radio" name="jenis" value="sakit" x-model="formJenis" class="text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <p class="text-xs">Sakit</p>
                                <p class="text-[10px] font-normal opacity-80">Surat dokter / keterangan ortu</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                            :class="formJenis === 'izin' ? 'border-amber-500 bg-amber-50/60 dark:bg-amber-950/40 text-amber-800 dark:text-amber-200 font-bold' : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/60 text-slate-600 dark:text-slate-400'">
                            <input type="radio" name="jenis" value="izin" x-model="formJenis" class="text-amber-600 focus:ring-amber-500">
                            <div>
                                <p class="text-xs">Izin Resmi</p>
                                <p class="text-[10px] font-normal opacity-80">Keperluan keluarga / dinas</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Rentang Tanggal --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Tanggal Mulai <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Tanggal Selesai <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_selesai" value="{{ date('Y-m-d') }}" required
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-600 focus:border-blue-600">
                    </div>
                </div>

                {{-- Keterangan / Alasan --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Alasan / Diagnosis Singkat
                    </label>
                    <textarea name="keterangan" rows="2" placeholder="Contoh: Demam tinggi, rawat jalan di rumah / Ada acara keluarga di luar kota"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-xs text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-blue-600 focus:border-blue-600"></textarea>
                </div>

                {{-- Upload Berkas Surat Bukti --}}
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Unggah Foto / Berkas Surat Bukti <span class="text-slate-400 font-normal lowercase">(opsional, maks 5 MB)</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl p-4 text-center hover:border-blue-400 dark:hover:border-blue-500 transition cursor-pointer relative bg-slate-50/50 dark:bg-slate-800/40">
                        <input type="file" name="surat_file" accept=".jpg,.jpeg,.png,.webp,.pdf"
                            @change="handleFileSelect($event)"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <i class="fas fa-cloud-arrow-up text-slate-400 text-2xl mb-1.5"></i>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-300" x-text="fileName ? fileName : 'Klik atau seret foto surat ke sini'"></p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Format didukung: JPG, PNG, WEBP, atau PDF</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                    <button type="button" @click="showCreateModal = false"
                        class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 rounded-lg text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white transition shadow-xs flex items-center gap-1.5">
                        <i class="fas fa-floppy-disk"></i>
                        <span>Simpan &amp; Sinkronkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 5. MODAL LIGHTBOX PREVIEW SURAT --}}
    <div x-show="showPreviewModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
        <div @click.away="showPreviewModal = false" class="bg-white dark:bg-slate-900 w-full max-w-2xl rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
            <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-bold text-xs sm:text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fas fa-file-invoice text-blue-600"></i>
                        <span x-text="'Lampiran Surat: ' + previewTitle"></span>
                    </h3>
                    <p class="text-[11px] text-slate-400" x-text="'Tanggal Berlaku: ' + previewDate"></p>
                </div>
                <div class="flex items-center gap-2">
                    <a :href="previewUrl" target="_blank" download class="p-1.5 text-slate-400 hover:text-blue-600 text-xs transition" title="Buka di Tab Baru / Download">
                        <i class="fas fa-arrow-up-right-from-square"></i>
                    </a>
                    <button type="button" @click="showPreviewModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-sm">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="p-4 overflow-y-auto flex-1 flex items-center justify-center bg-slate-100/60 dark:bg-slate-950/60 min-h-[300px]">
                <template x-if="previewType === 'image'">
                    <img :src="previewUrl" alt="Berkas Surat" class="max-h-[70vh] max-w-full rounded-lg shadow-xs object-contain border border-slate-200 dark:border-slate-800">
                </template>
                <template x-if="previewType === 'pdf'">
                    <iframe :src="previewUrl" class="w-full h-[65vh] rounded-lg border border-slate-200 dark:border-slate-800"></iframe>
                </template>
            </div>
        </div>
    </div>

</div>

<script>
function bukuIzinApp() {
    return {
        showCreateModal: false,
        showPreviewModal: false,
        formJenis: 'sakit',
        fileName: '',
        previewUrl: '',
        previewType: 'image',
        previewTitle: '',
        previewDate: '',

        openCreateModal() {
            this.formJenis = 'sakit';
            this.fileName = '';
            this.showCreateModal = true;
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.fileName = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            } else {
                this.fileName = '';
            }
        },

        previewSurat(url, type, title, date) {
            this.previewUrl = url;
            this.previewType = type;
            this.previewTitle = title;
            this.previewDate = date;
            this.showPreviewModal = true;
        }
    };
}
</script>
@endsection
