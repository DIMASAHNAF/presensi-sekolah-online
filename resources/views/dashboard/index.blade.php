@extends('layouts.dashboard')

@section('title', 'Overview')
@section('page-title', 'Overview')
@section('page-subtitle', auth()->user()->isAdmin() ? 'Ringkasan data sistem absensi' : 'Ringkasan aktivitas Anda')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

{{-- STAT CARDS --}}
<div x-data="{ showSesi: false, showHadir: false, showAlpa: false }">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">

    @if(auth()->user()->isAdmin())
        {{-- Admin stats --}}
        <div class="stat-card" data-aos="fade-up" data-aos-delay="0">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">Total</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $stats['siswa'] }}</p>
            <p class="text-sm text-slate-500 mt-1 font-medium">Total Siswa</p>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="60">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-user text-indigo-600 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">Total</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $stats['guru'] }}</p>
            <p class="text-sm text-slate-500 mt-1 font-medium">Total Guru</p>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="120">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-violet-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-door-open text-violet-600 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full">Total</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $stats['kelas'] }}</p>
            <p class="text-sm text-slate-500 mt-1 font-medium">Total Kelas</p>
        </div>

        <div class="stat-card" data-aos="fade-up" data-aos-delay="180">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-circle-check text-emerald-600 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Hari ini</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $stats['hadir_hari_ini'] }}</p>
            <p class="text-sm text-slate-500 mt-1 font-medium">Hadir Hari Ini</p>
        </div>

    @else
        {{-- Guru stats --}}
        <button @click="showSesi = true" class="stat-card text-left hover:scale-[1.02] transition-transform cursor-pointer" data-aos="fade-up" data-aos-delay="0">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-blue-600 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-full">Hari Ini</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $stats['sesi_hari_ini'] }}</p>
            <p class="text-sm text-slate-500 mt-1 font-medium">Sesi Absensi</p>
            <p class="text-xs text-blue-500 mt-2">Klik untuk melihat detail &rarr;</p>
        </button>

        <button @click="showSesi = true" class="stat-card text-left hover:scale-[1.02] transition-transform cursor-pointer" data-aos="fade-up" data-aos-delay="60">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-spinner text-amber-600 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Aktif</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $stats['sesi_aktif'] }}</p>
            <p class="text-sm text-slate-500 mt-1 font-medium">Sesi Masih Aktif</p>
            <p class="text-xs text-amber-600 mt-2">Klik untuk melihat detail &rarr;</p>
        </button>

        <button @click="showHadir = true" class="stat-card text-left hover:scale-[1.02] transition-transform cursor-pointer" data-aos="fade-up" data-aos-delay="120">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-circle-check text-emerald-600 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">Hari ini</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $stats['hadir_hari_ini'] }}</p>
            <p class="text-sm text-slate-500 mt-1 font-medium">Hadir Hari Ini</p>
            <p class="text-xs text-emerald-600 mt-2">Klik untuk melihat daftar &rarr;</p>
        </button>

        <button @click="showAlpa = true" class="stat-card text-left hover:scale-[1.02] transition-transform cursor-pointer" data-aos="fade-up" data-aos-delay="180">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-circle-xmark text-red-500 text-lg"></i>
                </div>
                <span class="text-xs font-semibold text-red-500 bg-red-50 px-2.5 py-1 rounded-full">Hari ini</span>
            </div>
            <p class="text-3xl font-extrabold text-slate-800">{{ $stats['alpa_hari_ini'] }}</p>
            <p class="text-sm text-slate-500 mt-1 font-medium">Alpa Hari Ini</p>
            <p class="text-xs text-red-500 mt-2">Klik untuk melihat daftar &rarr;</p>
        </button>

        {{-- Modals for Guru Stats --}}
        {{-- Modal Sesi --}}
        <div x-show="showSesi" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div @click.away="showSesi = false" class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[80vh]">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-bold text-slate-800"><i class="fas fa-clipboard-list text-blue-600 mr-2"></i>Daftar Sesi Hari Ini</h3>
                    <button @click="showSesi = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>
                </div>
                <div class="overflow-y-auto p-4 flex-1">
                    @if($sesiHariIni->isEmpty())
                        <p class="text-center text-slate-500 py-4">Belum ada sesi yang Anda buat hari ini.</p>
                    @else
                        <table class="w-full text-left text-sm">
                            <thead class="text-slate-400 bg-slate-50 text-xs">
                                <tr>
                                    <th class="p-3">Kelas</th>
                                    <th class="p-3 text-center">Status Sesi</th>
                                    <th class="p-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($sesiHariIni as $sesi)
                                    <tr>
                                        <td class="p-3 font-semibold">{{ $sesi->kelas->nama_kelas }}</td>
                                        <td class="p-3 text-center">
                                            @if($sesi->is_active)
                                                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded">Aktif</span>
                                            @else
                                                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">Ditutup</span>
                                            @endif
                                        </td>
                                        <td class="p-3 text-right">
                                            <a href="{{ route('dashboard.absensi.detail', $sesi) }}" class="text-blue-600 hover:underline">Detail</a>
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
        <div x-show="showHadir" x-cloak x-data="{ search: '' }" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div @click.away="showHadir = false" class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[80vh]">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-emerald-50">
                    <h3 class="font-bold text-slate-800"><i class="fas fa-circle-check text-emerald-600 mr-2"></i>Siswa Hadir Hari Ini</h3>
                    <button @click="showHadir = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>
                </div>
                <div class="p-4 border-b border-slate-100">
                    <input type="text" x-model="search" placeholder="Cari nama atau kelas..." class="w-full px-4 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div class="overflow-y-auto p-4 flex-1">
                    @php $listHadir = $absensiHariIni->where('status', 'hadir'); @endphp
                    @if($listHadir->isEmpty())
                        <p class="text-center text-slate-500 py-4">Belum ada siswa yang hadir hari ini.</p>
                    @else
                        <table class="w-full text-left text-sm">
                            <thead class="text-slate-400 bg-slate-50 text-xs">
                                <tr>
                                    <th class="p-3">Nama Siswa</th>
                                    <th class="p-3">Kelas</th>
                                    <th class="p-3">Waktu Scan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($listHadir as $absen)
                                    <tr x-show="search === '' || '{{ strtolower($absen->siswa->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($absen->sesiAbsensi->kelas->nama_kelas) }}'.includes(search.toLowerCase())">
                                        <td class="p-3 font-semibold">{{ $absen->siswa->name }}</td>
                                        <td class="p-3">{{ $absen->sesiAbsensi->kelas->nama_kelas }}</td>
                                        <td class="p-3">{{ $absen->waktu_scan ? \Carbon\Carbon::parse($absen->waktu_scan)->format('H:i') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal Alpa --}}
        <div x-show="showAlpa" x-cloak x-data="{ search: '' }" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div @click.away="showAlpa = false" class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[80vh]">
                <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-red-50">
                    <h3 class="font-bold text-slate-800"><i class="fas fa-circle-xmark text-red-500 mr-2"></i>Siswa Alpa Hari Ini</h3>
                    <button @click="showAlpa = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>
                </div>
                <div class="p-4 border-b border-slate-100">
                    <input type="text" x-model="search" placeholder="Cari nama atau kelas..." class="w-full px-4 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-red-500">
                </div>
                <div class="overflow-y-auto p-4 flex-1">
                    @php $listAlpa = $absensiHariIni->where('status', 'alpa'); @endphp
                    @if($listAlpa->isEmpty())
                        <p class="text-center text-slate-500 py-4">Tidak ada siswa alpa hari ini!</p>
                    @else
                        <table class="w-full text-left text-sm">
                            <thead class="text-slate-400 bg-slate-50 text-xs">
                                <tr>
                                    <th class="p-3">Nama Siswa</th>
                                    <th class="p-3">Kelas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($listAlpa as $absen)
                                    <tr x-show="search === '' || '{{ strtolower($absen->siswa->name) }}'.includes(search.toLowerCase()) || '{{ strtolower($absen->sesiAbsensi->kelas->nama_kelas) }}'.includes(search.toLowerCase())">
                                        <td class="p-3 font-semibold">{{ $absen->siswa->name }}</td>
                                        <td class="p-3">{{ $absen->sesiAbsensi->kelas->nama_kelas }}</td>
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
    <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6" data-aos="fade-up" data-aos-delay="80">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-bold text-slate-800">Kehadiran 7 Hari Terakhir</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbandingan hadir vs alpa</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>Hadir</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span>Alpa</span>
            </div>
        </div>
        <canvas id="attendanceChart" height="100"></canvas>
    </div>
    @endif

    {{-- RECENT SESI --}}
    <div class="{{ auth()->user()->isAdmin() ? '' : 'xl:col-span-3' }} bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" data-aos="fade-up" data-aos-delay="140">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800 text-sm">
                <i class="fas fa-clock text-blue-500 mr-2"></i>Sesi Absensi Terbaru
            </h3>
            <a href="{{ route('dashboard.absensi') }}" class="text-xs text-blue-600 hover:underline">Lihat semua</a>
        </div>

        @if($recentSesi->isEmpty())
            <div class="py-10 text-center">
                <i class="fas fa-inbox text-slate-300 text-3xl mb-2"></i>
                <p class="text-sm text-slate-500">Belum ada sesi absensi</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($recentSesi as $sesi)
                    <a href="{{ route('dashboard.absensi.detail', $sesi) }}"
                       class="flex items-center justify-between px-5 py-3.5 hover:bg-blue-50 transition group">
                        <div>
                            <p class="text-sm font-semibold text-slate-800 group-hover:text-blue-700">
                                {{ optional($sesi->kelas)->nama_kelas ?? '-' }}
                            </p>
                            <p class="text-xs text-slate-400 mt-0.5">
                                <i class="fas fa-calendar-day mr-1"></i>
                                {{ optional($sesi->tanggal)->format('d M Y') ?? '-' }}
                                @if(auth()->user()->isAdmin())
                                    &nbsp;·&nbsp; {{ $sesi->guru->name ?? '-' }}
                                @endif
                            </p>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold
                            {{ $sesi->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $sesi->is_active ? 'Aktif' : 'Selesai' }}
                        </span>
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
                backgroundColor: 'rgba(37,99,235,0.8)',
                borderRadius: 6, borderSkipped: false,
            },
            {
                label: 'Alpa',
                data: @json($chartAlpa),
                backgroundColor: 'rgba(248,113,113,0.8)',
                borderRadius: 6, borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
            y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, stepSize: 1 }, beginAtZero: true }
        }
    }
});
</script>
@endif
@endpush
