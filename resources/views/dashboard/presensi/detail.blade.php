@extends('layouts.dashboard')

@section('title', 'Detail Presensi - ' . $sesiPresensi->kelas->nama_kelas)
@section('page-title', 'Detail Presensi: ' . $sesiPresensi->kelas->nama_kelas)
@section('page-subtitle', 'Tanggal: ' . $sesiPresensi->tanggal->isoFormat('dddd, D MMMM Y'))

@section('content')

    {{-- FLASH MESSAGE --}}
    @if(session('success'))
        <div class="mb-5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-2xl px-5 py-4 flex items-center justify-between"
            data-aos="fade-down">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
                <div>
                    <p class="font-bold text-sm">Berhasil!</p>
                    <p class="text-sm mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-5 bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 rounded-2xl px-5 py-4 flex items-center justify-between"
            data-aos="fade-down">
            <div class="flex items-center gap-3">
                <i class="fas fa-circle-info text-blue-500 text-xl"></i>
                <div>
                    <p class="font-bold text-sm">Informasi</p>
                    <p class="text-sm mt-0.5">{{ session('info') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-2xl px-5 py-4 flex items-center justify-between"
            data-aos="fade-down">
            <div class="flex items-center gap-3">
                <i class="fas fa-triangle-exclamation text-red-500 text-xl"></i>
                <div>
                    <p class="font-bold text-sm">Perhatian!</p>
                    <p class="text-sm mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div x-data="presensiDetailApp()" class="space-y-6">

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

            {{-- KIRI: INFO & AKSI SESI --}}
            <div class="xl:col-span-1 space-y-5">
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5 text-center">
                    <div
                        class="w-14 h-14 bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800 rounded-2xl flex items-center justify-center text-xl mx-auto mb-3.5 shadow-sm">
                        <i class="fas fa-camera-viewfinder text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <h3 class="font-heading font-bold text-slate-900 dark:text-white text-base">Presensi Face Recognition</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-4 leading-relaxed">
                        Siswa melakukan verifikasi biometrik wajah mandiri melalui portal siswa.
                    </p>

                    <div
                        class="bg-blue-50/70 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-800/60 rounded-xl px-3.5 py-2.5 text-xs text-blue-800 dark:text-blue-300 text-left mb-4 flex items-start gap-2">
                        <i class="fas fa-circle-info text-blue-600 dark:text-blue-400 text-sm mt-0.5 shrink-0"></i>
                        <span class="leading-relaxed">Verifikasi berjalan otomatis dan terenkripsi. Kehadiran akan ter-update
                            otomatis pada tabel secara <em>real-time</em>.</span>
                    </div>

                    <a href="{{ route('dashboard.presensi.pdf', $sesiPresensi) }}" target="_blank"
                        class="w-full btn-secondary justify-center py-2.5 text-xs font-semibold flex items-center gap-1.5 mb-2">
                        <i class="fas fa-file-pdf text-red-600"></i> Unduh / Cetak Laporan PDF
                    </a>
                    <a href="{{ route('dashboard.presensi.excel', $sesiPresensi) }}" target="_blank"
                        class="w-full bg-green-50 hover:bg-green-100 dark:bg-green-950/30 dark:hover:bg-green-900/40 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 flex justify-center py-2.5 rounded-xl text-xs font-semibold transition items-center gap-1.5 shadow-sm">
                        <i class="fas fa-file-excel text-green-600"></i> Unduh Laporan Excel
                    </a>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 p-5">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-3.5">
                        <h3 class="font-heading font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider">Informasi Sesi</h3>
                        <span
                            class="text-[10px] font-mono px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded border border-slate-200 dark:border-slate-700">#{{ $sesiPresensi->id }}</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between items-center py-1 border-b border-slate-50 dark:border-slate-800/60">
                            <span class="text-slate-500 dark:text-slate-400">Kelas</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $sesiPresensi->kelas->nama_kelas }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-50 dark:border-slate-800/60">
                            <span class="text-slate-500 dark:text-slate-400">Tipe / Mapel</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">
                                @if($sesiPresensi->tipe === 'kelas')
                                    <span class="inline-flex items-center gap-1 text-blue-600 dark:text-blue-400 font-bold">
                                        <i class="fas fa-sun text-amber-500"></i> Sesi Kelas (Pagi)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                                        <i class="fas fa-book-open text-emerald-500"></i> {{ $sesiPresensi->mataPelajaran ? $sesiPresensi->mataPelajaran->nama_mapel : 'Sesi Mapel' }}
                                    </span>
                                @endif
                            </span>
                        </div>
                        @if($sesiPresensi->jam_pelajaran)
                        <div class="flex justify-between items-center py-1 border-b border-slate-50 dark:border-slate-800/60">
                            <span class="text-slate-500 dark:text-slate-400">Jam Pelajaran</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300 font-medium">{{ $sesiPresensi->jam_pelajaran }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center py-1 border-b border-slate-50 dark:border-slate-800/60">
                            <span class="text-slate-500 dark:text-slate-400">Guru Pengampu</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200">{{ $sesiPresensi->guru->name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-50 dark:border-slate-800/60">
                            <span class="text-slate-500 dark:text-slate-400">Siswa di Sesi</span>
                            <span class="font-bold text-slate-900 dark:text-white font-mono">
                                {{ $sesiPresensi->presensi->count() }} /
                                {{ $totalSiswaKelas ?? $sesiPresensi->presensi->count() }}
                                @if(isset($missingCount) && $missingCount > 0)
                                    <span class="text-amber-700 dark:text-amber-400 text-[11px] font-bold ml-1 font-sans">({{ $missingCount }} susulan)</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-slate-500 dark:text-slate-400">Status Sesi</span>
                            @if($sesiPresensi->is_active)
                                @php
                                    $minutesDiff = now()->diffInMinutes($sesiPresensi->created_at);
                                    $isExpired = $minutesDiff >= 30;
                                @endphp
                                @if($isExpired)
                                    <span
                                        class="text-amber-800 dark:text-amber-300 font-semibold bg-amber-50 dark:bg-amber-950/50 border border-amber-300 dark:border-amber-700 px-2 py-0.5 rounded text-[11px]">
                                        Expired (>30m)
                                    </span>
                                @else
                                    <span
                                        class="text-emerald-800 dark:text-emerald-300 font-semibold bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-300 dark:border-emerald-700 px-2 py-0.5 rounded text-[11px] flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                        Aktif ({{ 30 - $minutesDiff }}m)
                                    </span>
                                @endif
                            @else
                                <span
                                    class="text-slate-600 dark:text-slate-300 font-medium bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-2 py-0.5 rounded text-[11px]">
                                    Selesai / Ditutup
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- SINKRONISASI SISWA SUSULAN (RETROACTIVE SYNC) --}}
                    <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800">
                        <form action="{{ route('dashboard.presensi.sync-siswa', $sesiPresensi) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full bg-slate-50 hover:bg-blue-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 hover:text-blue-800 dark:text-slate-200 dark:hover:text-blue-300 border border-slate-200 dark:border-slate-700 font-medium py-2 px-3 rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-2xs">
                                <i class="fas fa-arrows-rotate text-blue-600 dark:text-blue-400"></i>
                                <span>Sinkronkan Siswa Susulan</span>
                            </button>
                        </form>
                        <p class="text-[10px] text-slate-400 mt-1.5 text-center leading-normal">Tarik siswa yang baru
                            diverifikasi atau update kehadiran dari sesi pagi.</p>
                    </div>

                    {{-- Tutup Sesi: HANYA tampil untuk sesi tipe kelas (pagi) --}}
                    @if($sesiPresensi->tipe === 'kelas')
                        <div class="mt-3.5 pt-3.5 border-t border-slate-100 dark:border-slate-800">
                            <form action="{{ route('dashboard.presensi.close', $sesiPresensi) }}" method="POST"
                                  @if($sesiPresensi->is_active)
                                      data-confirm="Tutup sesi presensi ini? Siswa tidak bisa scan wajah lagi."
                                      data-confirm-title="Kunci / Tutup Sesi Presensi?"
                                      data-confirm-type="warning"
                                      data-confirm-btn="Ya, Tutup Sesi"
                                  @endif>
                                @csrf @method('PATCH')
                                @if($sesiPresensi->is_active)
                                    <button type="submit" class="w-full btn-danger justify-center py-2 text-xs font-semibold">
                                        <i class="fas fa-lock text-xs"></i> Kunci / Tutup Sesi Presensi
                                    </button>
                                @else
                                    <button type="submit"
                                        class="w-full bg-emerald-700 hover:bg-emerald-800 text-white justify-center py-2 text-xs font-semibold rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                                        <i class="fas fa-unlock text-xs"></i> Buka Kembali Sesi
                                    </button>
                                @endif
                            </form>
                        </div>
                    @else
                        <div class="mt-3.5 pt-3.5 border-t border-slate-100 dark:border-slate-800">
                            <div
                                class="bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-2 text-[11px] text-slate-600 dark:text-slate-300 flex items-center gap-2">
                                <i class="fas fa-circle-info text-blue-600 dark:text-blue-400 text-xs"></i>
                                <span>Sesi Mapel: Klik baris siswa atau switch tombol H/I/S/A untuk mengubah kehadiran langsung.</span>
                            </div>
                        </div>
                    @endif

                    @if(auth()->user()?->isAdmin())
                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <form action="{{ route('dashboard.presensi.reset', $sesiPresensi) }}" method="POST"
                                  data-confirm="BAHAYA: Yakin ingin MERESET seluruh kehadiran kelas ini? Semua siswa akan dikembalikan ke status Alpa dan log riwayat akan dihapus."
                                  data-confirm-title="PERINGATAN: Reset Kehadiran Kelas?"
                                  data-confirm-type="danger"
                                  data-confirm-btn="Reset Semua Kehadiran">
                                @csrf
                                <button type="submit"
                                    class="w-full bg-red-50 hover:bg-red-100 dark:bg-red-950/30 dark:hover:bg-red-900/40 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800 font-semibold justify-center py-2 rounded-xl text-xs transition flex items-center gap-1.5">
                                    <i class="fas fa-trash-can text-xs"></i> Reset Presensi Kelas
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- KANAN: DAFTAR SISWA (TABLE ALAMI FULL WIDTH - TIDAK TERPOTONG & SCROLL WEBSITE ASLI) --}}
            <div class="xl:col-span-3 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col">

                {{-- HEADER CARD: STATISTIK & LIVE SYNC BADGE --}}
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/70 dark:bg-slate-800/50 rounded-t-2xl">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-heading font-extrabold text-slate-900 dark:text-white">Daftar Kehadiran Siswa</h2>
                            <span class="text-xs text-slate-400 font-mono">({{ $sesiPresensi->presensi->count() }} Total)</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            <i class="fas fa-hand-pointer text-blue-500 mr-1"></i> Klik baris siswa atau switch tombol H/I/S/A untuk langsung ubah kehadiran.
                        </p>
                    </div>

                    {{-- LIVE SYNC BADGE & ACTION --}}
                    <div class="flex items-center gap-2">
                        <form action="{{ route('dashboard.presensi.sync-siswa', $sesiPresensi) }}" method="POST">
                            @csrf
                            <button type="submit" title="Sinkronkan siswa susulan atau perbarui dari sesi kelas pagi"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-2xs">
                                <i class="fas fa-arrows-rotate text-blue-600 dark:text-blue-400"></i> Sync Siswa
                            </button>
                        </form>

                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-[11px] font-semibold bg-emerald-50 dark:bg-emerald-950/50 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span> Realtime Live
                        </span>
                    </div>
                </div>

                {{-- FILTER TABS & SEARCH INPUT --}}
                <div class="p-4 border-b border-slate-100 dark:border-slate-800 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-white dark:bg-slate-900">
                    
                    {{-- Filter Status Tabs --}}
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" @click="statusFilter = 'all'"
                            :class="statusFilter === 'all' ? 'bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 shadow-xs' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <span>Semua</span>
                            <span class="font-mono text-[11px] opacity-80" x-text="stats.total"></span>
                        </button>

                        <button type="button" @click="statusFilter = 'hadir'"
                            :class="statusFilter === 'hadir' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/80 hover:bg-emerald-100'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400" :class="statusFilter === 'hadir' ? 'bg-white' : ''"></span>
                            <span>Hadir</span>
                            <span class="font-mono text-[11px]" id="count-hadir" x-text="stats.hadir"></span>
                        </button>

                        <button type="button" @click="statusFilter = 'izin'"
                            :class="statusFilter === 'izin' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/80 hover:bg-amber-100'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-400" :class="statusFilter === 'izin' ? 'bg-white' : ''"></span>
                            <span>Izin</span>
                            <span class="font-mono text-[11px]" id="count-izin" x-text="stats.izin"></span>
                        </button>

                        <button type="button" @click="statusFilter = 'sakit'"
                            :class="statusFilter === 'sakit' ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 dark:bg-blue-950/40 text-blue-800 dark:text-blue-300 border border-blue-200/80 dark:border-blue-800/80 hover:bg-blue-100'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-400" :class="statusFilter === 'sakit' ? 'bg-white' : ''"></span>
                            <span>Sakit</span>
                            <span class="font-mono text-[11px]" id="count-sakit" x-text="stats.sakit"></span>
                        </button>

                        <button type="button" @click="statusFilter = 'alpa'"
                            :class="statusFilter === 'alpa' ? 'bg-rose-600 text-white shadow-xs' : 'bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800/80 hover:bg-rose-100'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-400" :class="statusFilter === 'alpa' ? 'bg-white' : ''"></span>
                            <span>Alpa</span>
                            <span class="font-mono text-[11px]" id="count-alpa" x-text="stats.alpa"></span>
                        </button>
                    </div>

                    {{-- Search Input Box --}}
                    <div class="relative min-w-[220px]">
                        <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                        <input type="text" x-model="search" placeholder="Cari nama atau NISN siswa..."
                            class="w-full pl-9 pr-8 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                        <button x-show="search.length > 0" @click="search = ''" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600 text-xs">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                </div>

                {{-- ALERT BANNER: JIKA ADA SISWA BELUM TERDAFTAR DI SESI INI --}}
                @if(isset($missingCount) && $missingCount > 0)
                    <div class="p-4 bg-amber-50/80 dark:bg-amber-950/40 border-b border-amber-200 dark:border-amber-800/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-200 border border-amber-200 dark:border-amber-700 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fas fa-user-plus text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-amber-900 dark:text-amber-200">
                                    {{ $missingCount }} Siswa Belum Terdaftar di Sesi Ini
                                </p>
                                <p class="text-[11px] text-amber-800 dark:text-amber-300 mt-0.5 leading-normal">
                                    Siswa susulan:
                                    <span class="font-semibold">{{ $missingStudents->pluck('name')->take(3)->implode(', ') }}{{ $missingCount > 3 ? ' +' . ($missingCount - 3) . ' lainnya' : '' }}</span>.
                                </p>
                            </div>
                        </div>
                        <form action="{{ route('dashboard.presensi.sync-siswa', $sesiPresensi) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs shadow-2xs transition whitespace-nowrap">
                                <i class="fas fa-arrows-rotate text-xs"></i> Sinkronkan {{ $missingCount }} Siswa
                            </button>
                        </form>
                    </div>
                @endif

                {{-- DESKTOP VIEW: TABEL FULL & NATURAL (TIDAK ADA INNER SCROLLBAR, SCROLL WEBSITE ASLI) --}}
                <div class="hidden md:block w-full">
                    <table class="w-full text-left border-collapse table-auto">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 text-[11px] uppercase tracking-wider text-slate-500 dark:text-slate-400 font-heading">
                                <th class="w-10 text-center px-2 py-3 font-bold text-slate-400">#</th>
                                <th class="px-3 py-3 font-bold">Nama Siswa</th>
                                <th class="w-24 text-center px-2 py-3 font-bold whitespace-nowrap">Status</th>
                                <th class="px-2.5 py-3 font-bold">Keterangan</th>
                                <th class="w-20 text-center px-2 py-3 font-bold whitespace-nowrap">Waktu</th>
                                <th class="w-52 text-right px-3 py-3 font-bold whitespace-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                            @php $rowNumber = 1; @endphp
                            @foreach($sesiPresensi->presensi->sortBy('siswa.name') as $absen)
                                @php
                                    $words = explode(' ', trim($absen->siswa->name));
                                    $initials = count($words) >= 2
                                        ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1)
                                        : mb_substr($words[0], 0, 2);
                                    $avatarColors = [
                                        'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
                                        'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                                        'bg-indigo-100 text-indigo-800 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800',
                                        'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                                        'bg-teal-100 text-teal-800 border-teal-200 dark:bg-teal-950/60 dark:text-teal-300 dark:border-teal-800',
                                    ];
                                    $colorIndex = abs(crc32($absen->siswa->name)) % count($avatarColors);
                                    $avatarColor = $avatarColors[$colorIndex];

                                    $absenJson = [
                                        'id' => $absen->id,
                                        'siswa_id' => $absen->siswa_id,
                                        'status' => $absen->status,
                                        'label' => $absen->labelStatus(),
                                        'keterangan' => $absen->keterangan ?: '',
                                        'waktu' => $absen->updated_at ? $absen->updated_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB' : '-',
                                        'update_url' => route('dashboard.presensi.record.update', $absen),
                                        'siswa' => [
                                            'id' => $absen->siswa->id,
                                            'name' => $absen->siswa->name,
                                            'nisn' => $absen->siswa->nisn ?: '-',
                                            'username' => $absen->siswa->username,
                                            'avatar_url' => $absen->siswa->avatar_url,
                                            'initials' => strtoupper($initials),
                                            'avatar_color' => $avatarColor,
                                            'kelas_nama' => $sesiPresensi->kelas->nama_kelas,
                                        ],
                                        'logs' => $absen->logPresensi->map(function($l) {
                                            return [
                                                'id' => $l->id,
                                                'waktu' => $l->created_at->format('d/m/Y, H:i') . ' WIB',
                                                'guru' => $l->guru->name ?? 'Guru',
                                                'status_sebelumnya' => $l->labelStatusSebelumnya(),
                                                'status_baru' => $l->labelStatusBaru(),
                                                'keterangan' => $l->keterangan ?: '-',
                                            ];
                                        }),
                                    ];
                                @endphp

                                <tr id="row-{{ $absen->id }}"
                                    x-data="{
                                        currentStatus: '{{ $absen->status }}',
                                        currentKet: '{{ addslashes($absen->keterangan ?: '-') }}',
                                        currentWaktu: '{{ $absen->updated_at ? $absen->updated_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB' : '-' }}'
                                    }"
                                    x-show="matchesItem('{{ strtolower(addslashes($absen->siswa->name)) }}', '{{ $absen->siswa->nisn }}', currentStatus)"
                                    @status-changed-{{ $absen->id }}.window="currentStatus = $event.detail.status; if($event.detail.keterangan) currentKet = $event.detail.keterangan; if($event.detail.waktu) currentWaktu = $event.detail.waktu;"
                                    @click="openEditModalFromRow(@js($absenJson), currentStatus, currentKet)"
                                    class="hover:bg-blue-50/50 dark:hover:bg-slate-800/60 transition-colors duration-150 group cursor-pointer">
                                    
                                    {{-- Kolom No --}}
                                    <td class="w-10 text-center px-2 py-2.5 text-xs font-mono text-slate-400">
                                        {{ $rowNumber++ }}
                                    </td>

                                    {{-- Kolom Profil Siswa --}}
                                    <td class="px-3 py-2.5 min-w-0">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-8 h-8 rounded-xl {{ $avatarColor }} border flex items-center justify-center text-xs font-extrabold font-heading shrink-0 shadow-2xs">
                                                {{ strtoupper($initials) }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition truncate"
                                                   title="{{ $absen->siswa->name }}">
                                                    {{ $absen->siswa->name }}
                                                </p>
                                                <p class="text-[11px] font-mono text-slate-400 dark:text-slate-500 mt-0.5 truncate">
                                                    NISN: {{ $absen->siswa->nisn ?: '-' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kolom Status --}}
                                    <td class="w-24 text-center px-2 py-2.5 whitespace-nowrap">
                                        <span id="badge-{{ $absen->id }}"
                                            :data-status="currentStatus"
                                            :class="'badge badge-' + currentStatus"
                                            class="shadow-2xs cursor-pointer hover:scale-105 transition-all text-[10px] font-extrabold inline-block px-2.5 py-0.5"
                                            x-text="getStatusLabel(currentStatus)"
                                            title="Klik untuk ubah status">
                                            {{ $absen->labelStatus() }}
                                        </span>
                                    </td>

                                    {{-- Kolom Keterangan --}}
                                    <td class="px-2.5 py-2.5 text-slate-600 dark:text-slate-300 text-xs">
                                        <span id="ket-{{ $absen->id }}" class="truncate block max-w-[110px] xl:max-w-[150px]"
                                            :title="currentKet"
                                            x-text="currentKet">
                                            {{ $absen->keterangan ?: '-' }}
                                        </span>
                                    </td>

                                    {{-- Kolom Waktu Presensi --}}
                                    <td class="w-20 text-center px-2 py-2.5 text-slate-400 dark:text-slate-400 text-[11px] font-mono whitespace-nowrap">
                                        <span id="waktu-{{ $absen->id }}" x-text="currentWaktu">
                                            {{ $absen->updated_at ? $absen->updated_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB' : '-' }}
                                        </span>
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="w-52 text-right px-3 py-2.5 whitespace-nowrap" @click.stop>
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            
                                            {{-- Quick 1-Click Status Pills (SWITCH PINDAH H I S A SECARA REAKTIF) --}}
                                            <div class="inline-flex items-center bg-slate-100 dark:bg-slate-800 p-0.5 rounded-lg border border-slate-200/90 dark:border-slate-700 select-none shadow-2xs shrink-0" title="Aksi Cepat 1-Klik">
                                                {{-- Button H --}}
                                                <button type="button"
                                                    @click="
                                                        if (currentStatus !== 'hadir') {
                                                            const oldStatus = currentStatus;
                                                            currentStatus = 'hadir';
                                                            quickSetStatusRemote({{ $absen->id }}, 'hadir', oldStatus, '{{ route('dashboard.presensi.record.update', $absen) }}');
                                                        }
                                                    "
                                                    :class="currentStatus === 'hadir'
                                                        ? 'bg-emerald-600 text-white font-black shadow-xs ring-1 ring-emerald-500/50'
                                                        : 'text-slate-500 dark:text-slate-400 hover:text-emerald-700 hover:bg-emerald-100/60 dark:hover:bg-emerald-950/40'"
                                                    class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold transition-all duration-150"
                                                    title="Set Hadir">
                                                    H
                                                </button>

                                                {{-- Button I --}}
                                                <button type="button"
                                                    @click="
                                                        if (currentStatus !== 'izin') {
                                                            const oldStatus = currentStatus;
                                                            currentStatus = 'izin';
                                                            quickSetStatusRemote({{ $absen->id }}, 'izin', oldStatus, '{{ route('dashboard.presensi.record.update', $absen) }}');
                                                        }
                                                    "
                                                    :class="currentStatus === 'izin'
                                                        ? 'bg-amber-500 text-white font-black shadow-xs ring-1 ring-amber-500/50'
                                                        : 'text-slate-500 dark:text-slate-400 hover:text-amber-700 hover:bg-amber-100/60 dark:hover:bg-amber-950/40'"
                                                    class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold transition-all duration-150"
                                                    title="Set Izin">
                                                    I
                                                </button>

                                                {{-- Button S --}}
                                                <button type="button"
                                                    @click="
                                                        if (currentStatus !== 'sakit') {
                                                            const oldStatus = currentStatus;
                                                            currentStatus = 'sakit';
                                                            quickSetStatusRemote({{ $absen->id }}, 'sakit', oldStatus, '{{ route('dashboard.presensi.record.update', $absen) }}');
                                                        }
                                                    "
                                                    :class="currentStatus === 'sakit'
                                                        ? 'bg-blue-600 text-white font-black shadow-xs ring-1 ring-blue-500/50'
                                                        : 'text-slate-500 dark:text-slate-400 hover:text-blue-700 hover:bg-blue-100/60 dark:hover:bg-blue-950/40'"
                                                    class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold transition-all duration-150"
                                                    title="Set Sakit">
                                                    S
                                                </button>

                                                {{-- Button A --}}
                                                <button type="button"
                                                    @click="
                                                        if (currentStatus !== 'alpa') {
                                                            const oldStatus = currentStatus;
                                                            currentStatus = 'alpa';
                                                            quickSetStatusRemote({{ $absen->id }}, 'alpa', oldStatus, '{{ route('dashboard.presensi.record.update', $absen) }}');
                                                        }
                                                    "
                                                    :class="currentStatus === 'alpa'
                                                        ? 'bg-rose-600 text-white font-black shadow-xs ring-1 ring-rose-500/50'
                                                        : 'text-slate-500 dark:text-slate-400 hover:text-rose-700 hover:bg-rose-100/60 dark:hover:bg-rose-950/40'"
                                                    class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold transition-all duration-150"
                                                    title="Set Alpa">
                                                    A
                                                </button>
                                            </div>

                                            {{-- Tombol Ubah / Edit --}}
                                            <button type="button" @click="openEditModalFromRow(@js($absenJson), currentStatus, currentKet)"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-bold bg-blue-50 hover:bg-blue-100 dark:bg-blue-950/60 dark:hover:bg-blue-900/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 transition shadow-2xs shrink-0">
                                                <i class="fas fa-pen text-[9px]"></i> Ubah
                                            </button>

                                            {{-- Tombol Log --}}
                                            @if($absen->logPresensi->count() > 0)
                                                <button type="button" @click="openLogModal(@js($absenJson))"
                                                    class="p-1.5 text-xs text-slate-400 hover:text-blue-700 dark:hover:text-blue-300 transition rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 shrink-0"
                                                    title="Lihat Riwayat Perubahan">
                                                    <i class="fas fa-clock-rotate-left"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE VIEW: KARTU SISWA (md:hidden) --}}
                <div class="md:hidden p-3.5 space-y-3 bg-slate-50/50 dark:bg-slate-950/20">
                    @forelse($sesiPresensi->presensi->sortBy('siswa.name') as $absen)
                        @php
                            $words = explode(' ', trim($absen->siswa->name));
                            $initials = count($words) >= 2
                                ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1)
                                : mb_substr($words[0], 0, 2);
                            $avatarColors = [
                                'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800',
                                'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800',
                                'bg-indigo-100 text-indigo-800 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 dark:border-indigo-800',
                                'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                                'bg-teal-100 text-teal-800 border-teal-200 dark:bg-teal-950/60 dark:text-teal-300 dark:border-teal-800',
                            ];
                            $colorIndex = abs(crc32($absen->siswa->name)) % count($avatarColors);
                            $avatarColor = $avatarColors[$colorIndex];

                            $absenJson = [
                                'id' => $absen->id,
                                'siswa_id' => $absen->siswa_id,
                                'status' => $absen->status,
                                'label' => $absen->labelStatus(),
                                'keterangan' => $absen->keterangan ?: '',
                                'waktu' => $absen->updated_at ? $absen->updated_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB' : '-',
                                'update_url' => route('dashboard.presensi.record.update', $absen),
                                'siswa' => [
                                    'id' => $absen->siswa->id,
                                    'name' => $absen->siswa->name,
                                    'nisn' => $absen->siswa->nisn ?: '-',
                                    'username' => $absen->siswa->username,
                                    'avatar_url' => $absen->siswa->avatar_url,
                                    'initials' => strtoupper($initials),
                                    'avatar_color' => $avatarColor,
                                    'kelas_nama' => $sesiPresensi->kelas->nama_kelas,
                                ],
                                'logs' => $absen->logPresensi->map(function($l) {
                                    return [
                                        'id' => $l->id,
                                        'waktu' => $l->created_at->format('d/m/Y, H:i') . ' WIB',
                                        'guru' => $l->guru->name ?? 'Guru',
                                        'status_sebelumnya' => $l->labelStatusSebelumnya(),
                                        'status_baru' => $l->labelStatusBaru(),
                                        'keterangan' => $l->keterangan ?: '-',
                                    ];
                                }),
                            ];
                        @endphp
                        
                        <div id="card-{{ $absen->id }}"
                            x-data="{
                                currentStatus: '{{ $absen->status }}',
                                currentKet: '{{ addslashes($absen->keterangan ?: '-') }}',
                                currentWaktu: '{{ $absen->updated_at ? $absen->updated_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB' : '-' }}'
                            }"
                            x-show="matchesItem('{{ strtolower(addslashes($absen->siswa->name)) }}', '{{ $absen->siswa->nisn }}', currentStatus)"
                            @status-changed-{{ $absen->id }}.window="currentStatus = $event.detail.status; if($event.detail.keterangan) currentKet = $event.detail.keterangan; if($event.detail.waktu) currentWaktu = $event.detail.waktu;"
                            @click="openEditModalFromRow(@js($absenJson), currentStatus, currentKet)"
                            class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-xs transition-all duration-200 hover:border-blue-400 cursor-pointer">
                            
                            {{-- Top: Avatar + Nama + Status Badge --}}
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl {{ $avatarColor }} border flex items-center justify-center text-xs font-bold font-heading shrink-0 shadow-2xs">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="font-heading font-bold text-slate-900 dark:text-white text-sm leading-tight truncate">
                                            {{ $absen->siswa->name }}
                                        </h3>
                                        <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                                            NISN: {{ $absen->siswa->nisn ?: '-' }}
                                        </p>
                                    </div>
                                </div>

                                <span id="badge-m-{{ $absen->id }}"
                                    :data-status="currentStatus"
                                    :class="'badge badge-' + currentStatus"
                                    class="shrink-0 text-xs font-bold"
                                    x-text="getStatusLabel(currentStatus)">
                                    {{ $absen->labelStatus() }}
                                </span>
                            </div>

                            {{-- Metadata Box --}}
                            <div class="bg-slate-50/80 dark:bg-slate-800/60 rounded-xl p-3 border border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-3 text-xs mb-3.5">
                                <div>
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Waktu Presensi</span>
                                    <span class="font-mono text-slate-800 dark:text-slate-200 text-xs font-semibold flex items-center gap-1">
                                        <i class="far fa-clock text-slate-400 text-[10px]"></i>
                                        <span id="waktu-m-{{ $absen->id }}" x-text="currentWaktu">
                                            {{ $absen->updated_at ? $absen->updated_at->timezone('Asia/Jakarta')->format('H:i') . ' WIB' : '-' }}
                                        </span>
                                    </span>
                                </div>
                                <div>
                                    <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Keterangan</span>
                                    <span id="ket-m-{{ $absen->id }}" class="text-slate-800 dark:text-slate-200 text-xs truncate block font-medium"
                                        :title="currentKet"
                                        x-text="currentKet">
                                        {{ $absen->keterangan ?: '-' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Action Footer --}}
                            <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800" @click.stop>
                                {{-- Mobile Quick Pills --}}
                                <div class="inline-flex items-center bg-slate-100 dark:bg-slate-800 p-0.5 rounded-lg border border-slate-200/90 dark:border-slate-700 select-none shadow-2xs">
                                    <button type="button"
                                        @click="
                                            if (currentStatus !== 'hadir') {
                                                const oldStatus = currentStatus;
                                                currentStatus = 'hadir';
                                                quickSetStatusRemote({{ $absen->id }}, 'hadir', oldStatus, '{{ route('dashboard.presensi.record.update', $absen) }}');
                                            }
                                        "
                                        :class="currentStatus === 'hadir'
                                            ? 'bg-emerald-600 text-white font-black shadow-xs ring-1 ring-emerald-500/50'
                                            : 'text-slate-500 dark:text-slate-400 hover:text-emerald-700'"
                                        class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold transition-all">
                                        H
                                    </button>
                                    <button type="button"
                                        @click="
                                            if (currentStatus !== 'izin') {
                                                const oldStatus = currentStatus;
                                                currentStatus = 'izin';
                                                quickSetStatusRemote({{ $absen->id }}, 'izin', oldStatus, '{{ route('dashboard.presensi.record.update', $absen) }}');
                                            }
                                        "
                                        :class="currentStatus === 'izin'
                                            ? 'bg-amber-500 text-white font-black shadow-xs ring-1 ring-amber-500/50'
                                            : 'text-slate-500 dark:text-slate-400 hover:text-amber-700'"
                                        class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold transition-all">
                                        I
                                    </button>
                                    <button type="button"
                                        @click="
                                            if (currentStatus !== 'sakit') {
                                                const oldStatus = currentStatus;
                                                currentStatus = 'sakit';
                                                quickSetStatusRemote({{ $absen->id }}, 'sakit', oldStatus, '{{ route('dashboard.presensi.record.update', $absen) }}');
                                            }
                                        "
                                        :class="currentStatus === 'sakit'
                                            ? 'bg-blue-600 text-white font-black shadow-xs ring-1 ring-blue-500/50'
                                            : 'text-slate-500 dark:text-slate-400 hover:text-blue-700'"
                                        class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold transition-all">
                                        S
                                    </button>
                                    <button type="button"
                                        @click="
                                            if (currentStatus !== 'alpa') {
                                                const oldStatus = currentStatus;
                                                currentStatus = 'alpa';
                                                quickSetStatusRemote({{ $absen->id }}, 'alpa', oldStatus, '{{ route('dashboard.presensi.record.update', $absen) }}');
                                            }
                                        "
                                        :class="currentStatus === 'alpa'
                                            ? 'bg-rose-600 text-white font-black shadow-xs ring-1 ring-rose-500/50'
                                            : 'text-slate-500 dark:text-slate-400 hover:text-rose-700'"
                                        class="w-6 h-6 rounded flex items-center justify-center text-[10px] font-bold transition-all">
                                        A
                                    </button>
                                </div>

                                <div class="flex items-center gap-2">
                                    @if($absen->logPresensi->count() > 0)
                                        <button type="button" @click="openLogModal(@js($absenJson))"
                                            class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-blue-700 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 transition"
                                            title="Lihat Riwayat Perubahan">
                                            <i class="fas fa-clock-rotate-left text-xs text-blue-600 dark:text-blue-400"></i>
                                            <span>Log</span>
                                        </button>
                                    @endif

                                    <button type="button" @click="openEditModalFromRow(@js($absenJson), currentStatus, currentKet)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 transition shadow-2xs">
                                        <i class="fas fa-pen text-[9px]"></i>
                                        <span>Ubah</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800">
                            <i class="fas fa-users-slash text-3xl text-slate-300 mb-2"></i>
                            <p class="text-sm font-semibold text-slate-600 dark:text-slate-400">Belum ada siswa terdaftar</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

        {{-- ───────────────────────────────────────────────────────────── --}}
        {{-- MODAL EDIT STATUS KEHADIRAN SISWA (SINGLE ROOT MODAL)          --}}
        {{-- ───────────────────────────────────────────────────────────── --}}
        <template x-teleport="body">
            <div x-show="openEdit" x-cloak
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 overflow-y-auto bg-slate-950/60 backdrop-blur-xs"
                @click.self="openEdit = false"
                @keydown.escape.window="openEdit = false">
                
                <div x-show="openEdit"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative my-auto p-6 sm:p-7">
                    
                    {{-- Modal Header --}}
                    <div class="flex items-start justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-5">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-sm font-extrabold font-heading shrink-0 shadow-sm"
                                x-text="activeAbsen ? activeAbsen.siswa.initials : 'SW'">
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white truncate"
                                    x-text="activeAbsen ? activeAbsen.siswa.name : 'Ubah Kehadiran Siswa'"></h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    NISN: <span class="font-mono font-bold text-slate-700 dark:text-slate-200" x-text="activeAbsen ? activeAbsen.siswa.nisn : '-'"></span>
                                    <span class="mx-1">&bull;</span>
                                    <span class="text-blue-600 dark:text-blue-400 font-semibold" x-text="activeAbsen ? activeAbsen.siswa.kelas_nama : ''"></span>
                                </p>
                            </div>
                        </div>

                        <button type="button" @click="openEdit = false"
                            class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center transition">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    {{-- Form Ubah Status --}}
                    <form @submit.prevent="submitEditForm()" class="space-y-5">
                        
                        {{-- 4 Big Status Buttons --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2.5">
                                Pilih Status Kehadiran <span class="text-rose-500">*</span>
                            </label>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                {{-- Hadir --}}
                                <button type="button" @click="editStatus = 'hadir'"
                                    :class="editStatus === 'hadir'
                                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-md ring-2 ring-emerald-500/30'
                                        : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-emerald-300'"
                                    class="py-3 px-2 rounded-2xl border flex flex-col items-center justify-center gap-1.5 transition">
                                    <i class="fas fa-circle-check text-base" :class="editStatus === 'hadir' ? 'text-white' : 'text-emerald-500'"></i>
                                    <span class="text-xs font-extrabold uppercase tracking-wide">Hadir</span>
                                </button>

                                {{-- Izin --}}
                                <button type="button" @click="editStatus = 'izin'"
                                    :class="editStatus === 'izin'
                                        ? 'bg-amber-600 text-white border-amber-600 shadow-md ring-2 ring-amber-500/30'
                                        : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-amber-300'"
                                    class="py-3 px-2 rounded-2xl border flex flex-col items-center justify-center gap-1.5 transition">
                                    <i class="fas fa-clock text-base" :class="editStatus === 'izin' ? 'text-white' : 'text-amber-500'"></i>
                                    <span class="text-xs font-extrabold uppercase tracking-wide">Izin</span>
                                </button>

                                {{-- Sakit --}}
                                <button type="button" @click="editStatus = 'sakit'"
                                    :class="editStatus === 'sakit'
                                        ? 'bg-blue-600 text-white border-blue-600 shadow-md ring-2 ring-blue-500/30'
                                        : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-blue-300'"
                                    class="py-3 px-2 rounded-2xl border flex flex-col items-center justify-center gap-1.5 transition">
                                    <i class="fas fa-notes-medical text-base" :class="editStatus === 'sakit' ? 'text-white' : 'text-blue-500'"></i>
                                    <span class="text-xs font-extrabold uppercase tracking-wide">Sakit</span>
                                </button>

                                {{-- Alpa --}}
                                <button type="button" @click="editStatus = 'alpa'"
                                    :class="editStatus === 'alpa'
                                        ? 'bg-rose-600 text-white border-rose-600 shadow-md ring-2 ring-rose-500/30'
                                        : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-rose-300'"
                                    class="py-3 px-2 rounded-2xl border flex flex-col items-center justify-center gap-1.5 transition">
                                    <i class="fas fa-times-circle text-base" :class="editStatus === 'alpa' ? 'text-white' : 'text-rose-500'"></i>
                                    <span class="text-xs font-extrabold uppercase tracking-wide">Alpa</span>
                                </button>
                            </div>
                        </div>

                        {{-- Keterangan / Alasan --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                    Keterangan / Alasan
                                </label>
                                <span class="text-[11px] text-slate-400 font-normal">(opsional)</span>
                            </div>

                            {{-- Quick Chips Presets --}}
                            <div class="flex items-center gap-1.5 flex-wrap mb-2">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Cepat:</span>
                                <button type="button" @click="editKeterangan = 'Sakit demam/flu'" class="text-[11px] px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                                    Sakit Demam
                                </button>
                                <button type="button" @click="editKeterangan = 'Dispen LKS / Lomba Sekolah'" class="text-[11px] px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                                    Dispen LKS
                                </button>
                                <button type="button" @click="editKeterangan = 'Izin keperluan keluarga'" class="text-[11px] px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                                    Izin Keluarga
                                </button>
                                <button type="button" @click="editKeterangan = 'Terlambat hadir'" class="text-[11px] px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                                    Terlambat
                                </button>
                            </div>

                            <input type="text" x-model="editKeterangan"
                                placeholder="Contoh: Dispen lomba LKS / Sakit demam"
                                class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                        </div>

                        {{-- Shortcut Master Siswa (Jika Admin) --}}
                        @if(auth()->user()?->isAdmin())
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                            <span class="text-slate-400">Perlu ganti akun atau password siswa?</span>
                            <a :href="'{{ route('dashboard.siswa') }}?search=' + (activeAbsen ? activeAbsen.siswa.nisn : '')"
                                target="_blank"
                                class="inline-flex items-center gap-1 font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline">
                                <span>Master Siswa</span>
                                <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        </div>
                        @endif

                        {{-- Footer Buttons --}}
                        <div class="flex gap-2.5 justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="openEdit = false"
                                class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSaving"
                                class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-blue-600 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 transition shadow-xs flex items-center gap-2 disabled:opacity-50">
                                <span x-show="isSaving" class="fas fa-circle-notch fa-spin"></span>
                                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ───────────────────────────────────────────────────────────── --}}
        {{-- MODAL RIWAYAT / LOG PERUBAHAN KEHADIRAN (SINGLE ROOT MODAL)    --}}
        {{-- ───────────────────────────────────────────────────────────── --}}
        <template x-teleport="body">
            <div x-show="openLog" x-cloak
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4 overflow-y-auto bg-slate-950/60 backdrop-blur-xs"
                @click.self="openLog = false"
                @keydown.escape.window="openLog = false">
                
                <div x-show="openLog"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                    class="bg-white dark:bg-slate-900 rounded-3xl w-full max-w-lg shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative my-auto p-6 sm:p-7">
                    
                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-300 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-sm">
                                <i class="fas fa-clock-rotate-left"></i>
                            </div>
                            <div>
                                <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white">Riwayat Perubahan Status</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    Siswa: <strong class="text-slate-800 dark:text-slate-200" x-text="activeAbsen ? activeAbsen.siswa.name : ''"></strong>
                                </p>
                            </div>
                        </div>

                        <button type="button" @click="openLog = false"
                            class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-white flex items-center justify-center transition">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    {{-- Timeline List --}}
                    <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                        <template x-if="activeAbsen && activeAbsen.logs && activeAbsen.logs.length > 0">
                            <div>
                                <template x-for="log in activeAbsen.logs" :key="log.id">
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-3.5 border border-slate-200/80 dark:border-slate-700/80 mb-2.5">
                                        <div class="flex justify-between items-start mb-1.5">
                                            <span class="text-[11px] font-mono text-slate-500 dark:text-slate-400" x-text="log.waktu"></span>
                                            <span class="text-[11px] font-semibold text-blue-700 dark:text-blue-300 flex items-center gap-1">
                                                <i class="fas fa-user-tie text-[10px]"></i>
                                                <span x-text="log.guru"></span>
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-800 dark:text-slate-200">
                                            Status diubah dari <span class="font-semibold line-through text-slate-400" x-text="log.status_sebelumnya"></span>
                                            menjadi <span class="font-bold text-slate-900 dark:text-white" x-text="log.status_baru"></span>
                                        </p>
                                        <template x-if="log.keterangan && log.keterangan !== '-'">
                                            <p class="text-[11px] text-slate-600 dark:text-slate-300 mt-1.5 bg-white dark:bg-slate-900 px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 italic"
                                                x-text="'“' + log.keterangan + '”'"></p>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <template x-if="!activeAbsen || !activeAbsen.logs || activeAbsen.logs.length === 0">
                            <div class="p-8 text-center text-slate-400">
                                <i class="fas fa-clock text-2xl mb-2"></i>
                                <p class="text-xs">Belum ada riwayat perubahan manual untuk siswa ini.</p>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 text-right">
                        <button type="button" @click="openLog = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </template>

    </div>

    @push('scripts')
        <script>
            function presensiDetailApp() {
                return {
                    search: '',
                    statusFilter: 'all',
                    openEdit: false,
                    openLog: false,
                    activeAbsen: null,
                    editStatus: 'hadir',
                    editKeterangan: '',
                    isSaving: false,

                    stats: {
                        hadir: {{ $sesiPresensi->presensi->where('status', 'hadir')->count() }},
                        izin: {{ $sesiPresensi->presensi->where('status', 'izin')->count() }},
                        sakit: {{ $sesiPresensi->presensi->where('status', 'sakit')->count() }},
                        alpa: {{ $sesiPresensi->presensi->where('status', 'alpa')->count() }},
                        total: {{ $sesiPresensi->presensi->count() }}
                    },

                    init() {
                        window.presensiApp = this;
                    },

                    getStatusLabel(status) {
                        switch (status) {
                            case 'hadir': return 'Hadir';
                            case 'izin': return 'Izin';
                            case 'sakit': return 'Sakit';
                            case 'alpa': return 'Alpa';
                            default: return 'Alpa';
                        }
                    },

                    matchesItem(name, nisn, currentStatus) {
                        const q = this.search.trim().toLowerCase();
                        const matchesSearch = !q || name.includes(q) || (nisn && nisn.includes(q));
                        const matchesFilter = this.statusFilter === 'all' || currentStatus === this.statusFilter;
                        return matchesSearch && matchesFilter;
                    },

                    openEditModalFromRow(absen, currentStatus, currentKet) {
                        this.activeAbsen = absen;
                        this.editStatus = currentStatus || absen.status;
                        this.editKeterangan = (currentKet && currentKet !== '-') ? currentKet : (absen.keterangan || '');
                        this.openEdit = true;
                        this.openLog = false;
                    },

                    openLogModal(absen) {
                        this.activeAbsen = absen;
                        this.openLog = true;
                        this.openEdit = false;
                    },

                    // Quick Set Status 1-Klik dari baris tabel (Pindah tombol reaktif H I S A)
                    async quickSetStatusRemote(absenId, newStatus, oldStatus, updateUrl) {
                        // 1. Update stats counter di bar atas
                        if (this.stats[oldStatus] > 0) this.stats[oldStatus]--;
                        if (this.stats[newStatus] !== undefined) this.stats[newStatus]++;

                        // 2. Highlight visual baris
                        const row = document.getElementById('row-' + absenId);
                        if (row) {
                            row.classList.add('bg-blue-50/70', 'dark:bg-blue-950/40');
                            setTimeout(() => row.classList.remove('bg-blue-50/70', 'dark:bg-blue-950/40'), 800);
                        }

                        // 3. Kirim request background ke backend
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                        try {
                            const res = await fetch(updateUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    _token: csrfToken,
                                    _method: 'PATCH',
                                    status: newStatus
                                })
                            });

                            const data = await res.json();
                            if (res.ok && data.success) {
                                if (data.updated_at) {
                                    window.dispatchEvent(new CustomEvent('status-changed-' + absenId, {
                                        detail: { status: newStatus, waktu: data.updated_at }
                                    }));
                                }
                            } else {
                                // Rollback jika error
                                if (this.stats[newStatus] > 0) this.stats[newStatus]--;
                                if (this.stats[oldStatus] !== undefined) this.stats[oldStatus]++;
                                window.dispatchEvent(new CustomEvent('status-changed-' + absenId, {
                                    detail: { status: oldStatus }
                                }));
                                alert(data.message || 'Gagal mengubah status');
                            }
                        } catch (err) {
                            console.error('Quick set status error:', err);
                            if (this.stats[newStatus] > 0) this.stats[newStatus]--;
                            if (this.stats[oldStatus] !== undefined) this.stats[oldStatus]++;
                            window.dispatchEvent(new CustomEvent('status-changed-' + absenId, {
                                detail: { status: oldStatus }
                            }));
                        }
                    },

                    // Submit form modal edit
                    async submitEditForm() {
                        if (!this.activeAbsen) return;
                        this.isSaving = true;

                        const absen = this.activeAbsen;
                        const newStatus = this.editStatus;
                        const newKet = this.editKeterangan;
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                        try {
                            const res = await fetch(absen.update_url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: JSON.stringify({
                                    _token: csrfToken,
                                    _method: 'PATCH',
                                    status: newStatus,
                                    keterangan: newKet
                                })
                            });

                            const data = await res.json();
                            if (res.ok && data.success) {
                                window.dispatchEvent(new CustomEvent('status-changed-' + absen.id, {
                                    detail: {
                                        status: newStatus,
                                        keterangan: newKet || '-',
                                        waktu: data.updated_at
                                    }
                                }));

                                absen.status = newStatus;
                                absen.keterangan = newKet;

                                this.openEdit = false;
                            } else {
                                alert(data.message || 'Gagal menyimpan perubahan status.');
                            }
                        } catch (err) {
                            console.error('Submit edit form error:', err);
                        } finally {
                            this.isSaving = false;
                        }
                    },

                    // Sinkronisasi dari Polling Real-time
                    syncFromLive(id, status, label, ket) {
                        window.dispatchEvent(new CustomEvent('status-changed-' + id, {
                            detail: { status: status, keterangan: ket }
                        }));
                    }
                };
            }

            // REALTIME LIVE AUTO-SYNC (POLLING SETIAP 3 DETIK)
            function pollLiveStatus() {
                fetch("{{ route('dashboard.presensi.live', $sesiPresensi) }}")
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.stats) {
                            const elHadir = document.getElementById('count-hadir');
                            const elIzin = document.getElementById('count-izin');
                            const elSakit = document.getElementById('count-sakit');
                            const elAlpa = document.getElementById('count-alpa');

                            if (elHadir) elHadir.innerText = data.stats.hadir;
                            if (elIzin) elIzin.innerText = data.stats.izin;
                            if (elSakit) elSakit.innerText = data.stats.sakit;
                            if (elAlpa) elAlpa.innerText = data.stats.alpa;

                            if (data.items && window.presensiApp) {
                                data.items.forEach(item => {
                                    window.presensiApp.syncFromLive(item.id, item.status, item.label, item.keterangan);
                                });
                            }
                        }
                    })
                    .catch(err => console.debug('Live sync poll:', err));
            }

            let liveSyncTimer = setInterval(pollLiveStatus, 3000);
        </script>
    @endpush

@endsection