@extends('layouts.dashboard')

@section('title', 'Log Perubahan Absensi')
@section('page-title', 'Log Perubahan Absensi')
@section('page-subtitle', 'Riwayat perubahan status absensi siswa oleh guru')

@section('content')

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 mb-6">
    {{-- Filter Dropdown --}}
    <form method="GET" action="{{ route('dashboard.log') }}" class="mb-4 flex items-center gap-3">
        <label for="kelas_id" class="text-sm font-semibold text-slate-700">Filter Kelas:</label>
        <select name="kelas_id" id="kelas_id" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" onchange="this.form.submit()">
            <option value="">-- Semua Kelas --</option>
            @foreach($kelas as $k)
                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                    {{ $k->nama_kelas }}
                </option>
            @endforeach
        </select>
        @if(request('kelas_id'))
            <a href="{{ route('dashboard.log') }}" class="text-sm text-red-500 hover:underline">Reset</a>
        @endif
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="text-xs text-slate-400 bg-slate-50 uppercase font-semibold">
                <tr>
                    <th class="px-4 py-3 rounded-l-xl">Waktu & Tanggal</th>
                    <th class="px-4 py-3">Siswa</th>
                    <th class="px-4 py-3">Kelas / Sesi</th>
                    <th class="px-4 py-3">Diubah Oleh</th>
                    <th class="px-4 py-3 text-center">Perubahan Status</th>
                    <th class="px-4 py-3 rounded-r-xl">Keterangan / Jam Mapel</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr class="table-row">
                    <td class="px-4 py-3 text-slate-500">{{ $log->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-4 py-3 font-semibold text-slate-700">{{ optional($log->absensi->siswa)->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-slate-500">
                        {{ optional($log->absensi->sesiAbsensi->kelas)->nama_kelas ?? '-' }}
                        <br><span class="text-xs">{{ optional($log->absensi->sesiAbsensi)->tanggal?->format('d M Y') }}</span>
                    </td>
                    <td class="px-4 py-3 font-semibold text-blue-600">{{ optional($log->guru)->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="badge badge-{{ $log->status_sebelumnya }}">{{ strtoupper($log->status_sebelumnya) }}</span>
                        <i class="fas fa-arrow-right text-slate-300 mx-2 text-xs"></i>
                        <span class="badge badge-{{ $log->status_baru }}">{{ strtoupper($log->status_baru) }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        {{ $log->keterangan ?: '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada riwayat perubahan</td>
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
