@extends('layouts.dashboard')

@section('title', 'Overview')
@section('page-title', 'Overview')
@section('page-subtitle', auth()->user()->isAdmin() ? 'Ringkasan data sistem presensi' : 'Ringkasan aktivitas Anda')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- GREETING BANNER --}}
<div class="mb-6 bg-gradient-to-r from-slate-900 via-slate-800 to-blue-950 text-white rounded-xl p-5 sm:p-6 border border-slate-800 shadow-sm relative overflow-hidden" data-aos="fade-down">
    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2 py-0.5 rounded bg-blue-500/20 text-blue-300 border border-blue-400/30 text-[11px] font-semibold tracking-wide uppercase">
                    {{ auth()->user()->isAdmin() ? 'Administrator Sekolah' : 'Tenaga Pendidik' }}
                </span>
                <span class="text-slate-400 text-xs">•</span>
                <span class="text-slate-300 text-xs font-mono">T.A. 2026/2027</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-heading font-extrabold text-white tracking-tight">
                Selamat Datang, {{ auth()->user()->name }}
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl leading-relaxed">
                {{ auth()->user()->isAdmin() 
                    ? 'Pantau aktivitas presensi digital, kelola verifikasi wajah siswa, dan audit rekapan akademik SMKN 1 Beringin.' 
                    : 'Kelola sesi kehadiran mata pelajaran dan kelas Anda hari ini dengan sistem verifikasi biometrik terintegrasi.' }}
            </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <a href="{{ route('dashboard.presensi') }}" class="btn-primary py-2.5 px-4 text-xs font-semibold shadow-sm">
                <i class="fas fa-clipboard-list text-xs"></i> Kelola Presensi
            </a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('dashboard.siswa') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-lg text-xs font-semibold bg-white/10 hover:bg-white/15 text-white border border-white/20 transition">
                <i class="fas fa-users text-xs"></i> Data Siswa
            </a>
            @endif
        </div>
    </div>
    {{-- Subtle decorative grid --}}
    <div class="absolute -right-6 -bottom-8 w-48 h-48 bg-blue-600/10 rounded-full blur-2xl pointer-events-none"></div>
</div>

{{-- STAT CARDS --}}
<div x-data="{ showSesi: false, showHadir: false, showAlpa: false }">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    @if(auth()->user()->isAdmin())
        {{-- Admin stats --}}
        <div class="stat-card" data-aos="fade-up" data-aos-delay="0">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 bg-blue-50 text-blue-700 rounded-xl flex items-center justify-center border border-blue-100 shadow-2xs">
                    <i class="fas fa-user-graduate text-lg"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded uppercase tracking-wider font-mono">Siswa</span>
            </div>
            <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">{{ $stats['siswa'] }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Total Siswa Terdaftar</p>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="60">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 bg-indigo-50 text-indigo-700 rounded-xl flex items-center justify-center border border-indigo-100 shadow-2xs">
                    <i class="fas fa-chalkboard-user text-lg"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded uppercase tracking-wider font-mono">Guru</span>
            </div>
            <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">{{ $stats['guru'] }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Total Dewan Guru</p>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="120">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 bg-slate-100 text-slate-700 rounded-xl flex items-center justify-center border border-slate-200 shadow-2xs">
                    <i class="fas fa-school text-lg"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2.5 py-0.5 rounded uppercase tracking-wider font-mono">Rombel</span>
            </div>
            <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">{{ $stats['kelas'] }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Total Kelas Aktif</p>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="180">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center border border-emerald-200 shadow-2xs">
                    <i class="fas fa-user-check text-lg"></i>
                </div>
                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded uppercase tracking-wider font-mono">Hari ini</span>
            </div>
            <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">{{ $stats['hadir_hari_ini'] }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Kehadiran Hari Ini</p>
        </div>

    @else
        {{-- Guru stats --}}
        <button @click="showSesi = true" class="stat-card text-left hover:border-blue-300 transition-colors cursor-pointer group" data-aos="fade-up" data-aos-delay="0">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 bg-blue-50 text-blue-700 rounded-xl flex items-center justify-center border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition shadow-2xs">
                    <i class="fas fa-calendar-check text-lg"></i>
                </div>
                <span class="text-[10px] font-bold text-blue-800 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded font-mono">Hari Ini</span>
            </div>
            <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">{{ $stats['sesi_hari_ini'] }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Sesi Presensi Dibuat</p>
            <p class="text-[11px] text-blue-700 mt-2 font-semibold flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                <span>Buka rincian sesi</span> <i class="fas fa-arrow-right text-[10px]"></i>
            </p>
        </button>

        <button @click="showSesi = true" class="stat-card text-left hover:border-amber-300 transition-colors cursor-pointer group" data-aos="fade-up" data-aos-delay="60">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 bg-amber-50 text-amber-700 rounded-xl flex items-center justify-center border border-amber-200 group-hover:bg-amber-600 group-hover:text-white transition shadow-2xs">
                    <i class="fas fa-clock-rotate-left text-lg"></i>
                </div>
                <span class="text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded font-mono">Aktif</span>
            </div>
            <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">{{ $stats['sesi_aktif'] }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Sesi Berjalan Saat Ini</p>
            <p class="text-[11px] text-amber-700 mt-2 font-semibold flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                <span>Periksa sesi aktif</span> <i class="fas fa-arrow-right text-[10px]"></i>
            </p>
        </button>

        <button @click="showHadir = true" class="stat-card text-left hover:border-emerald-300 transition-colors cursor-pointer group" data-aos="fade-up" data-aos-delay="120">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center border border-emerald-200 group-hover:bg-emerald-600 group-hover:text-white transition shadow-2xs">
                    <i class="fas fa-check-double text-lg"></i>
                </div>
                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded font-mono">Hadir</span>
            </div>
            <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">{{ $stats['hadir_hari_ini'] }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Siswa Terverifikasi Hadir</p>
            <p class="text-[11px] text-emerald-700 mt-2 font-semibold flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                <span>Lihat daftar siswa</span> <i class="fas fa-arrow-right text-[10px]"></i>
            </p>
        </button>

        <button @click="showAlpa = true" class="stat-card text-left hover:border-red-300 transition-colors cursor-pointer group" data-aos="fade-up" data-aos-delay="180">
            <div class="flex items-center justify-between mb-3">
                <div class="w-11 h-11 bg-red-50 text-red-700 rounded-xl flex items-center justify-center border border-red-200 group-hover:bg-red-600 group-hover:text-white transition shadow-2xs">
                    <i class="fas fa-user-xmark text-lg"></i>
                </div>
                <span class="text-[10px] font-bold text-red-800 bg-red-50 border border-red-200 px-2 py-0.5 rounded font-mono">Alpa</span>
            </div>
            <p class="text-2xl sm:text-3xl font-heading font-extrabold text-slate-900">{{ $stats['alpa_hari_ini'] }}</p>
            <p class="text-xs text-slate-500 mt-1 font-medium">Siswa Belum / Tidak Hadir</p>
            <p class="text-[11px] text-red-700 mt-2 font-semibold flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                <span>Lihat daftar alpa</span> <i class="fas fa-arrow-right text-[10px]"></i>
            </p>
        </button>

        {{-- Modals for Guru Stats --}}
        {{-- Modal Sesi --}}
        <div x-show="showSesi" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-xs p-4">
            <div @click.away="showSesi = false" class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-2xl overflow-hidden flex flex-col max-h-[80vh]">
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-calendar-days text-blue-700 text-base"></i>
                        <h3 class="font-heading font-bold text-sm text-slate-900">Daftar Sesi Presensi Hari Ini</h3>
                    </div>
                    <button @click="showSesi = false" class="text-slate-400 hover:text-slate-600 text-sm"><i class="fas fa-times"></i></button>
                </div>
                <div class="overflow-y-auto p-4 flex-1">
                    @if($sesiHariIni->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-xmark text-slate-300 text-3xl mb-2"></i>
                            <p class="text-xs text-slate-500">Belum ada sesi presensi yang dibuat hari ini.</p>
                        </div>
                    @else
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="text-slate-500 bg-slate-50 border-b border-slate-200 font-heading uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="p-3 font-bold">Kelas & Mapel</th>
                                    <th class="p-3 font-bold text-center">Status Sesi</th>
                                    <th class="p-3 font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($sesiHariIni as $sesi)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="p-3">
                                            <p class="font-bold text-slate-900 text-sm">{{ $sesi->kelas->nama_kelas }}</p>
                                            <p class="text-[11px] text-slate-500 mt-0.5">
                                                {{ $sesi->mataPelajaran ? $sesi->mataPelajaran->nama_mapel : 'Sesi Kelas (Pagi)' }}
                                                @if($sesi->jam_pelajaran) • <span class="font-mono">{{ $sesi->jam_pelajaran }}</span>@endif
                                            </p>
                                        </td>
                                        <td class="p-3 text-center">
                                            @if($sesi->is_active)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Aktif
                                                </span>
                                            @else
                                                <span class="text-[11px] font-medium text-slate-600 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">Ditutup</span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-right">
                                            <a href="{{ route('dashboard.presensi.detail', $sesi) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 hover:text-blue-800 bg-blue-50 border border-blue-200 px-2.5 py-1 rounded-md">
                                                <span>Buka Detail</span> <i class="fas fa-chevron-right text-[10px]"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Hadir --}}
        <div x-show="showHadir" x-cloak x-data="{ search: '' }" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-xs p-4">
            <div @click.away="showHadir = false" class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-2xl overflow-hidden flex flex-col max-h-[80vh]">
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-emerald-50/70">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-circle-check text-emerald-700 text-base"></i>
                        <h3 class="font-heading font-bold text-sm text-slate-900">Siswa Hadir Hari Ini</h3>
                    </div>
                    <button @click="showHadir = false" class="text-slate-400 hover:text-slate-600 text-sm"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-3.5 border-b border-slate-100 bg-slate-50/50">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" x-model="search" placeholder="Cari nama atau kelas siswa..." class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-600">
                    </div>
                </div>
                <div class="overflow-y-auto p-4 flex-1">
                    @php $listHadir = $presensiHariIni->where('status', 'hadir'); @endphp
                    @if($listHadir->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-user-clock text-slate-300 text-3xl mb-2"></i>
                            <p class="text-xs text-slate-500">Belum ada data kehadiran siswa yang tercatat hari ini.</p>
                        </div>
                    @else
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="text-slate-500 bg-slate-50 border-b border-slate-200 font-heading uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="p-3 font-bold">Nama Siswa</th>
                                    <th class="p-3 font-bold">Kelas</th>
                                    <th class="p-3 font-bold text-right">Waktu Scan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($listHadir as $absen)
                                    <tr class="hover:bg-slate-50/70 transition-colors" x-show="search === '' || '{{ strtolower($absen->siswa->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($absen->sesiPresensi->kelas->nama_kelas) }}'.includes(search.toLowerCase())">
                                        <td class="p-3 font-medium text-slate-900">{{ $absen->siswa->name }}</td>
                                        <td class="p-3 text-slate-600 font-semibold">{{ $absen->sesiPresensi->kelas->nama_kelas }}</td>
                                        <td class="p-3 text-right font-mono text-emerald-700 font-bold">
                                            {{ $absen->waktu_scan ? \Carbon\Carbon::parse($absen->waktu_scan)->format('H:i') . ' WIB' : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Alpa --}}
        <div x-show="showAlpa" x-cloak x-data="{ search: '' }" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-xs p-4">
            <div @click.away="showAlpa = false" class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-2xl overflow-hidden flex flex-col max-h-[80vh]">
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-red-50/70">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-circle-xmark text-red-600 text-base"></i>
                        <h3 class="font-heading font-bold text-sm text-slate-900">Siswa Alpa Hari Ini</h3>
                    </div>
                    <button @click="showAlpa = false" class="text-slate-400 hover:text-slate-600 text-sm"><i class="fas fa-times"></i></button>
                </div>
                <div class="p-3.5 border-b border-slate-100 bg-slate-50/50">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" x-model="search" placeholder="Cari nama atau kelas siswa..." class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-blue-600">
                    </div>
                </div>
                <div class="overflow-y-auto p-4 flex-1">
                    @php $listAlpa = $presensiHariIni->where('status', 'alpa'); @endphp
                    @if($listAlpa->isEmpty())
                        <div class="text-center py-8">
                            <i class="fas fa-face-smile text-emerald-500 text-3xl mb-2"></i>
                            <p class="text-xs font-semibold text-emerald-700">Luar biasa! Tidak ada siswa dengan status alpa hari ini.</p>
                        </div>
                    @else
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="text-slate-500 bg-slate-50 border-b border-slate-200 font-heading uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="p-3 font-bold">Nama Siswa</th>
                                    <th class="p-3 font-bold">Kelas</th>
                                    <th class="p-3 font-bold text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($listAlpa as $absen)
                                    <tr class="hover:bg-slate-50/70 transition-colors" x-show="search === '' || '{{ strtolower($absen->siswa->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($absen->sesiPresensi->kelas->nama_kelas) }}'.includes(search.toLowerCase())">
                                        <td class="p-3 font-medium text-slate-900">{{ $absen->siswa->name }}</td>
                                        <td class="p-3 text-slate-600 font-semibold">{{ $absen->sesiPresensi->kelas->nama_kelas }}</td>
                                        <td class="p-3 text-right">
                                            <span class="badge-alpa">Alpa</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

    @endif
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- CHART (admin only) --}}
    @if(auth()->user()->isAdmin())
    <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-5 sm:p-6" data-aos="fade-up" data-aos-delay="80">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-heading font-bold text-slate-900 text-base">Tingkat Kehadiran 7 Hari Terakhir</h3>
                <p class="text-xs text-slate-500 mt-0.5">Perbandingan siswa hadir vs tidak hadir (alpa)</p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5 font-medium text-slate-700">
                    <span class="w-2.5 h-2.5 rounded-sm bg-blue-700 inline-block"></span> Hadir
                </span>
                <span class="flex items-center gap-1.5 font-medium text-slate-700">
                    <span class="w-2.5 h-2.5 rounded-sm bg-red-600 inline-block"></span> Alpa
                </span>
            </div>
        </div>
        <canvas id="attendanceChart" height="95"></canvas>
    </div>
    @endif

    {{-- RECENT SESI --}}
    <div class="{{ auth()->user()->isAdmin() ? '' : 'xl:col-span-3' }} bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden" data-aos="fade-up" data-aos-delay="140">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 bg-slate-50/60">
            <h3 class="font-heading font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-clock-rotate-left text-blue-700"></i> Sesi Presensi Terbaru
            </h3>
            <a href="{{ route('dashboard.presensi') }}" class="text-xs font-semibold text-blue-700 hover:text-blue-800 hover:underline inline-flex items-center gap-1">
                <span>Lihat semua</span> <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        @if($recentSesi->isEmpty())
            <div class="py-12 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-calendar-xmark text-lg"></i>
                </div>
                <p class="text-xs font-semibold text-slate-700">Belum ada sesi presensi</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Sesi baru yang dibuat oleh guru akan muncul di sini.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($recentSesi as $sesi)
                    <a href="{{ route('dashboard.presensi.detail', $sesi) }}"
                       class="flex items-center justify-between px-5 py-3.5 hover:bg-slate-50/80 transition-colors group">
                        <div>
                            <p class="text-xs font-bold text-slate-900 group-hover:text-blue-700 transition flex items-center gap-2">
                                {{ optional($sesi->kelas)->nama_kelas ?? '-' }}
                                @if($sesi->mataPelajaran)
                                    <span class="bg-blue-50 text-blue-800 text-[10px] font-semibold px-2 py-0.5 rounded border border-blue-200">{{ $sesi->mataPelajaran->nama_mapel }}</span>
                                @endif
                                @if($sesi->jam_pelajaran)
                                    <span class="bg-slate-100 text-slate-700 text-[10px] font-mono font-medium px-1.5 py-0.5 rounded border border-slate-200">{{ $sesi->jam_pelajaran }}</span>
                                @endif
                            </p>
                            <p class="text-[11px] text-slate-500 mt-1 flex items-center gap-1.5">
                                <i class="fas fa-calendar-day text-[10px] text-slate-400"></i>
                                <span>{{ optional($sesi->tanggal)->format('d M Y') ?? '-' }}</span>
                                @if(auth()->user()->isAdmin())
                                    <span class="text-slate-300">•</span>
                                    <span>{{ $sesi->guru->name ?? '-' }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] px-2 py-0.5 rounded font-semibold border
                                {{ $sesi->is_active ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                {{ $sesi->is_active ? 'Aktif' : 'Selesai' }}
                            </span>
                            <i class="fas fa-chevron-right text-slate-300 group-hover:text-slate-600 text-xs group-hover:translate-x-0.5 transition-transform"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
@if(auth()->user()->isAdmin() && count($chartLabels) > 0)
<script>
const ctx = document.getElementById('attendanceChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [
            {
                label: 'Hadir',
                data: @json($chartHadir),
                backgroundColor: '#1d4ed8', // Royal Blue institutional
                borderRadius: 4, 
                borderSkipped: false,
            },
            {
                label: 'Alpa',
                data: @json($chartAlpa),
                backgroundColor: '#e11d48', // Rose Red
                borderRadius: 4, 
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { 
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0d1527',
                titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 'bold' },
                bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                padding: 10,
                cornerRadius: 8
            }
        },
        scales: {
            x: { 
                grid: { display: false }, 
                ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, color: '#64748b' } 
            },
            y: { 
                grid: { color: '#f1f5f9' }, 
                ticks: { font: { family: 'JetBrains Mono', size: 11 }, color: '#64748b', stepSize: 1 }, 
                beginAtZero: true 
            }
        }
    }
});
</script>
@endif
@endpush
