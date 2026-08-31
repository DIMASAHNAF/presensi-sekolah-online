@extends('layouts.dashboard')

@section('title', 'Log Perubahan Absensi')
@section('page-title', 'Log Perubahan Absensi')
@section('page-subtitle', 'Riwayat perubahan status absensi siswa oleh guru')

@section('content')

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 mb-6">

    {{-- Filter Panel --}}
    <form method="GET" action="{{ route('dashboard.log') }}" class="mb-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-3">

            {{-- Filter Kelas --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Filter Kelas</label>
                <select name="kelas_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Mapel --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Filter Mapel</label>
                <select name="mapel_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">-- Semua Mapel --</option>
                    @foreach($mapel as $m)
                        <option value="{{ $m->id }}" {{ request('mapel_id') == $m->id ? 'selected' : '' }}>
                            {{ $m->nama_mapel }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Guru --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Filter Guru (Pengubah)</label>
                <select name="guru_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">-- Semua Guru --</option>
                    @foreach($guruList as $g)
                        <option value="{{ $g->id }}" {{ request('guru_id') == $g->id ? 'selected' : '' }}>
                            {{ $g->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tanggal --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Filter Tanggal Sesi</label>
                <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
            </div>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <button type="submit" class="bg-slate-800 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-slate-700 transition">
                <i class="fas fa-filter mr-1.5"></i> Terapkan Filter
            </button>
            @if(request('kelas_id') || request('mapel_id') || request('guru_id') || request('tanggal'))
                <a href="{{ route('dashboard.log') }}" class="text-sm text-red-500 hover:underline">
                    <i class="fas fa-times-circle mr-1"></i> Reset Filter
                </a>
                <span class="text-xs bg-blue-50 text-blue-600 font-semibold px-2.5 py-1 rounded-lg">
                    <i class="fas fa-database mr-1"></i> {{ $logs->total() }} data ditemukan
                </span>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="text-xs text-slate-400 bg-slate-50 uppercase font-semibold">
                <tr>
                    <th class="px-4 py-3 rounded-l-xl">Waktu Ubah</th>
                    <th class="px-4 py-3">Siswa</th>
                    <th class="px-4 py-3">Kelas / Mapel / Tanggal</th>
                    <th class="px-4 py-3">Diubah Oleh</th>
                    <th class="px-4 py-3 text-center">Perubahan Status</th>
                    <th class="px-4 py-3 rounded-r-xl">Keterangan & Jam</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-4 py-3 text-xs text-slate-500">
                        {{ $log->created_at->format('d M Y') }}
                        <br><span class="font-semibold text-slate-700 text-sm">{{ $log->created_at->format('H:i') }}</span>
                    </td>
                    <td class="px-4 py-3 font-semibold text-slate-700">
                        {{ optional($log->absensi->siswa)->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        <span class="font-semibold">{{ optional($log->absensi->sesiAbsensi->kelas)->nama_kelas ?? '-' }}</span>
                        @if(optional($log->absensi->sesiAbsensi)->mataPelajaran)
                            <span class="text-xs ml-1 bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">
                                <i class="fas fa-book-open mr-0.5 text-[10px]"></i>{{ $log->absensi->sesiAbsensi->mataPelajaran->nama_mapel }}
                            </span>
                        @else
                            <span class="text-xs ml-1 bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">Sesi Pagi</span>
                        @endif
                        <br>
                        <span class="text-xs text-slate-400">
                            <i class="fas fa-calendar text-[10px] mr-1"></i>{{ optional($log->absensi->sesiAbsensi)->tanggal?->format('d M Y') ?? '-' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-semibold text-blue-600">
                        {{ optional($log->guru)->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="badge badge-{{ $log->status_sebelumnya }}">{{ strtoupper($log->status_sebelumnya) }}</span>
                        <i class="fas fa-arrow-right text-slate-300 mx-2 text-xs"></i>
                        <span class="badge badge-{{ $log->status_baru }}">{{ strtoupper($log->status_baru) }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        {{ $log->keterangan ?: '-' }}
                        @if(optional($log->absensi->sesiAbsensi)->jam_pelajaran)
                            <br><span class="text-xs text-slate-400 font-medium">
                                <i class="fas fa-clock mr-1"></i>{{ $log->absensi->sesiAbsensi->jam_pelajaran }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-magnifying-glass text-3xl text-slate-200"></i>
                            <p class="text-slate-400 text-sm font-medium">Belum ada riwayat perubahan</p>
                            @if(request('kelas_id') || request('mapel_id') || request('guru_id') || request('tanggal'))
                                <p class="text-xs text-slate-400">Coba ubah atau reset filter di atas</p>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>

@endsection
