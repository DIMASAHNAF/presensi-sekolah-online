@extends('layouts.dashboard')

@section('title', 'Detail Presensi - ' . $sesiPresensi->kelas->nama_kelas)
@section('page-title', 'Detail Presensi: ' . $sesiPresensi->kelas->nama_kelas)
@section('page-subtitle', 'Tanggal: ' . $sesiPresensi->tanggal->isoFormat('dddd, D MMMM Y'))

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

@if(session('info'))
<div class="mb-5 bg-blue-50 border border-blue-200 text-blue-700 rounded-2xl px-5 py-4 flex items-center justify-between" data-aos="fade-down">
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
<div class="mb-5 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 flex items-center justify-between" data-aos="fade-down">
    <div class="flex items-center gap-3">
        <i class="fas fa-triangle-exclamation text-red-500 text-xl"></i>
        <div>
            <p class="font-bold text-sm">Perhatian!</p>
            <p class="text-sm mt-0.5">{{ session('error') }}</p>
        </div>
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

    {{-- KIRI: INFO & AKSI SESI --}}
    <div class="lg:col-span-1 space-y-5">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 text-center" data-aos="fade-up">
            <div class="w-14 h-14 bg-blue-50 text-blue-700 border border-blue-100 rounded-xl flex items-center justify-center text-xl mx-auto mb-3.5 shadow-sm">
                <i class="fas fa-camera-viewfinder text-blue-600"></i>
            </div>
            <h3 class="font-heading font-bold text-slate-900 text-base">Presensi Face Recognition</h3>
            <p class="text-xs text-slate-500 mt-1 mb-4 leading-relaxed">Siswa melakukan verifikasi biometrik wajah mandiri melalui portal siswa.</p>

            <div class="bg-blue-50/70 border border-blue-200/80 rounded-lg px-3.5 py-2.5 text-xs text-blue-800 text-left mb-4 flex items-start gap-2">
                <i class="fas fa-circle-info text-blue-600 text-sm mt-0.5 shrink-0"></i>
                <span class="leading-relaxed">Verifikasi berjalan otomatis dan terenkripsi. Kehadiran akan ter-update otomatis pada tabel secara <em>real-time</em>.</span>
            </div>

            <a href="{{ route('dashboard.presensi.pdf', $sesiPresensi) }}" target="_blank"
               class="w-full btn-secondary justify-center py-2.5 text-xs font-semibold">
                <i class="fas fa-file-pdf text-red-600"></i> Unduh / Cetak Laporan PDF
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5" data-aos="fade-up" data-aos-delay="50">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3.5">
                <h3 class="font-heading font-bold text-slate-900 text-xs uppercase tracking-wider">Informasi Sesi</h3>
                <span class="text-[10px] font-mono px-2 py-0.5 bg-slate-100 text-slate-600 rounded border border-slate-200">#{{ $sesiPresensi->id }}</span>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Kelas</span>
                    <span class="font-bold text-slate-900">{{ $sesiPresensi->kelas->nama_kelas }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Mata Pelajaran</span>
                    <span class="font-semibold text-slate-800">{{ $sesiPresensi->mataPelajaran ? $sesiPresensi->mataPelajaran->nama_mapel : 'Sesi Umum (Pagi)' }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Jam Pelajaran</span>
                    <span class="font-mono text-slate-700 font-medium">{{ $sesiPresensi->jam_pelajaran ?: '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Guru Pengampu</span>
                    <span class="font-medium text-slate-800">{{ $sesiPresensi->guru->name }}</span>
                </div>
                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                    <span class="text-slate-500">Siswa di Sesi</span>
                    <span class="font-bold text-slate-900 font-mono">
                        {{ $sesiPresensi->presensi->count() }} / {{ $totalSiswaKelas ?? $sesiPresensi->presensi->count() }}
                        @if(isset($missingCount) && $missingCount > 0)
                            <span class="text-amber-700 text-[11px] font-bold ml-1 font-sans">({{ $missingCount }} susulan)</span>
                        @endif
                    </span>
                </div>
                <div class="flex justify-between items-center py-1">
                    <span class="text-slate-500">Status Sesi</span>
                    @if($sesiPresensi->is_active)
                        @php
                            $minutesDiff = now()->diffInMinutes($sesiPresensi->created_at);
                            $isExpired = $minutesDiff >= 30;
                        @endphp
                        @if($isExpired)
                            <span class="text-amber-800 font-semibold bg-amber-50 border border-amber-300 px-2 py-0.5 rounded text-[11px]">Expired (>30m)</span>
                        @else
                            <span class="text-emerald-800 font-semibold bg-emerald-50 border border-emerald-300 px-2 py-0.5 rounded text-[11px] flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                Aktif ({{ 30 - $minutesDiff }}m tersisa)
                            </span>
                        @endif
                    @else
                        <span class="text-slate-600 font-medium bg-slate-100 border border-slate-200 px-2 py-0.5 rounded text-[11px]">Selesai / Ditutup</span>
                    @endif
                </div>
            </div>

            {{-- SINKRONISASI SISWA SUSULAN (RETROACTIVE SYNC) --}}
            <div class="mt-4 pt-3.5 border-t border-slate-100">
                <form action="{{ route('dashboard.presensi.sync-siswa', $sesiPresensi) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-slate-50 hover:bg-blue-50 text-slate-700 hover:text-blue-800 border border-slate-200 hover:border-blue-200 font-medium py-2 px-3 rounded-lg text-xs transition flex items-center justify-center gap-2 shadow-2xs">
                        <i class="fas fa-arrows-rotate text-blue-600"></i>
                        <span>Sinkronkan Siswa Susulan</span>
                    </button>
                </form>
                <p class="text-[10px] text-slate-400 mt-1.5 text-center leading-normal">Tarik siswa yang baru diverifikasi atau update kehadiran dari sesi pagi.</p>
            </div>

            {{-- Tutup Sesi: HANYA tampil untuk sesi tipe kelas (pagi) --}}
            @if($sesiPresensi->tipe === 'kelas')
            <div class="mt-3.5 pt-3.5 border-t border-slate-100">
                <form action="{{ route('dashboard.presensi.close', $sesiPresensi) }}" method="POST">
                    @csrf @method('PATCH')
                    @if($sesiPresensi->is_active)
                        <button type="submit" class="w-full btn-danger justify-center py-2 text-xs font-semibold" onclick="return confirm('Tutup sesi presensi ini? Siswa tidak bisa scan wajah lagi.')">
                            <i class="fas fa-lock text-xs"></i> Kunci / Tutup Sesi Presensi
                        </button>
                    @else
                        <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white justify-center py-2 text-xs font-semibold rounded-lg transition flex items-center gap-1.5 shadow-2xs">
                            <i class="fas fa-unlock text-xs"></i> Buka Kembali Sesi
                        </button>
                    @endif
                </form>
            </div>
            @else
            <div class="mt-3.5 pt-3.5 border-t border-slate-100">
                <div class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-[11px] text-slate-600 flex items-center gap-2">
                    <i class="fas fa-circle-info text-blue-600 text-xs"></i>
                    <span>Sesi Mapel diedit langsung pada tabel siswa di samping.</span>
                </div>
            </div>
            @endif

            @if(auth()->user()->isAdmin())
            <div class="mt-3 pt-3 border-t border-slate-100">
                <form action="{{ route('dashboard.presensi.reset', $sesiPresensi) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-semibold justify-center py-2 rounded-lg text-xs transition flex items-center gap-1.5" onclick="return confirm('BAHAYA: Yakin ingin MERESET seluruh kehadiran kelas ini? Semua siswa akan dikembalikan ke status Alpa dan log riwayat akan dihapus.')">
                        <i class="fas fa-trash-can text-xs"></i> Reset Presensi Kelas
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    {{-- KANAN: DAFTAR SISWA --}}
    <div class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col" data-aos="fade-up" data-aos-delay="100">
        <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/60">
            <div>
                <h2 class="text-base font-heading font-bold text-slate-900">Daftar Kehadiran Siswa</h2>
                <div class="flex items-center flex-wrap gap-2 text-xs text-slate-600 mt-1">
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Hadir: <span id="count-hadir" class="font-bold font-mono">{{ $sesiPresensi->presensi->where('status', 'hadir')->count() }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 rounded font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Izin: <span id="count-izin" class="font-bold font-mono">{{ $sesiPresensi->presensi->where('status', 'izin')->count() }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 rounded font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Sakit: <span id="count-sakit" class="font-bold font-mono">{{ $sesiPresensi->presensi->where('status', 'sakit')->count() }}</span>
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 bg-red-50 text-red-800 border border-red-200 rounded font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Alpa: <span id="count-alpa" class="font-bold font-mono">{{ $sesiPresensi->presensi->where('status', 'alpa')->count() }}</span>
                    </span>
                </div>
            </div>

            {{-- LIVE SYNC BADGE & ACTION --}}
            <div class="flex items-center gap-2">
                <form action="{{ route('dashboard.presensi.sync-siswa', $sesiPresensi) }}" method="POST">
                    @csrf
                    <button type="submit" title="Sinkronkan siswa susulan atau perbarui dari sesi kelas pagi" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                        <i class="fas fa-arrows-rotate text-blue-600"></i> Sync Siswa
                    </button>
                </form>

                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span> Realtime Live
                </span>
            </div>
        </div>

        {{-- ALERT BANNER: JIKA ADA SISWA BELUM TERDAFTAR DI SESI INI --}}
        @if(isset($missingCount) && $missingCount > 0)
        <div class="p-4 bg-amber-50/80 border-b border-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 border border-amber-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas fa-user-plus text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-amber-900">
                        {{ $missingCount }} Siswa Belum Terdaftar di Sesi Ini
                    </p>
                    <p class="text-[11px] text-amber-800 mt-0.5 leading-normal">
                        Siswa susulan (mendaftar/baru hadir setelah sesi dibuka):
                        <span class="font-semibold">{{ $missingStudents->pluck('name')->take(3)->implode(', ') }}{{ $missingCount > 3 ? ' +' . ($missingCount - 3) . ' lainnya' : '' }}</span>.
                    </p>
                </div>
            </div>
            <form action="{{ route('dashboard.presensi.sync-siswa', $sesiPresensi) }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs shadow-2xs transition whitespace-nowrap">
                    <i class="fas fa-arrows-rotate text-xs"></i> Sinkronkan {{ $missingCount }} Siswa
                </button>
            </form>
        </div>
        @endif

        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-500 font-heading">
                        <th class="px-5 py-3.5 font-bold">Nama Siswa</th>
                        <th class="px-5 py-3.5 font-bold">Status Kehadiran</th>
                        <th class="px-5 py-3.5 font-bold">Keterangan</th>
                        <th class="px-5 py-3.5 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($sesiPresensi->presensi->sortBy('siswa.name') as $absen)
                        @php
                            $words = explode(' ', trim($absen->siswa->name));
                            $initials = count($words) >= 2 
                                ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1) 
                                : mb_substr($words[0], 0, 2);
                            $avatarColors = [
                                'bg-blue-100 text-blue-800 border-blue-200',
                                'bg-emerald-100 text-emerald-800 border-emerald-200',
                                'bg-indigo-100 text-indigo-800 border-indigo-200',
                                'bg-slate-100 text-slate-800 border-slate-200',
                                'bg-teal-100 text-teal-800 border-teal-200',
                            ];
                            $colorIndex = abs(crc32($absen->siswa->name)) % count($avatarColors);
                            $avatarColor = $avatarColors[$colorIndex];
                        @endphp
                        <tr id="row-{{ $absen->id }}" class="hover:bg-slate-50/80 transition-colors duration-200 group" x-data="{ openLog: false, openEdit: false }">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg {{ $avatarColor }} border flex items-center justify-center text-xs font-bold font-heading shrink-0 shadow-2xs">
                                        {{ strtoupper($initials) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900 text-sm leading-tight">{{ $absen->siswa->name }}</p>
                                        <p class="text-[11px] font-mono text-slate-500 mt-0.5">NISN: {{ $absen->siswa->nisn ?: '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span id="badge-{{ $absen->id }}" data-status="{{ $absen->status }}" class="badge badge-{{ $absen->status }}">
                                    {{ $absen->labelStatus() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 text-xs">
                                <span id="ket-{{ $absen->id }}">{{ $absen->keterangan ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                @if($absen->logPresensi->count() > 0)
                                    <button @click="openLog = true" class="inline-flex items-center gap-1 text-xs text-slate-500 hover:text-blue-700 transition px-2 py-1 rounded hover:bg-slate-100" title="Lihat Riwayat Perubahan">
                                        <i class="fas fa-clock-rotate-left text-xs"></i> Log ({{ $absen->logPresensi->count() }})
                                    </button>
                                @endif
                                <button @click="openEdit = true" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-700 border border-slate-200 transition">
                                    <i class="fas fa-pen text-[10px]"></i> Ubah
                                </button>
                            </td>

                            {{-- MODAL EDIT STATUS --}}
                            <td class="p-0 border-0 m-0 h-0 w-0">
                                <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-xs px-4">
                                    <div @click.away="openEdit = false" class="bg-white rounded-xl w-full max-w-md shadow-xl border border-slate-200 overflow-hidden p-6 relative">
                                        <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                                            <h3 class="font-heading font-bold text-base text-slate-900">Ubah Status Kehadiran</h3>
                                            <button @click="openEdit = false" class="text-slate-400 hover:text-slate-600 text-sm"><i class="fas fa-times"></i></button>
                                        </div>
                                        <p class="text-xs text-slate-500 mb-4">Siswa: <strong class="text-slate-800">{{ $absen->siswa->name }}</strong> (NISN: {{ $absen->siswa->nisn ?: '-' }})</p>
                                        
                                        <form method="POST" action="{{ route('dashboard.presensi.record.update', $absen) }}">
                                            @csrf @method('PATCH')
                                            <div class="mb-4">
                                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Status Baru</label>
                                                <select name="status" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600">
                                                    <option value="hadir" {{ $absen->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                    <option value="izin" {{ $absen->status == 'izin' ? 'selected' : '' }}>Izin</option>
                                                    <option value="sakit" {{ $absen->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                    <option value="alpa" {{ $absen->status == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                                </select>
                                            </div>
                                            <div class="mb-5">
                                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Keterangan / Alasan</label>
                                                <input type="text" name="keterangan" value="{{ $absen->keterangan }}" placeholder="Contoh: Dispen lomba LKS / Sakit demam" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-600">
                                            </div>
                                            <div class="flex gap-2.5 justify-end pt-2 border-t border-slate-100">
                                                <button type="button" @click="openEdit = false" class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                                                <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold text-white bg-blue-700 hover:bg-blue-800 transition">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>

                            {{-- MODAL LOG PERUBAHAN --}}
                            <td class="p-0 border-0 m-0 h-0 w-0">
                                <div x-show="openLog" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-xs px-4">
                                    <div @click.away="openLog = false" class="bg-white rounded-xl w-full max-w-lg shadow-xl border border-slate-200 overflow-hidden p-6 relative">
                                        <div class="flex justify-between items-center border-b border-slate-100 pb-3 mb-4">
                                            <h3 class="font-heading font-bold text-base text-slate-900">Riwayat Perubahan Kehadiran</h3>
                                            <button @click="openLog = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-sm"></i></button>
                                        </div>
                                        <p class="text-xs text-slate-500 mb-4">Siswa: <strong class="text-slate-800">{{ $absen->siswa->name }}</strong></p>
                                        
                                        <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                                            @foreach($absen->logPresensi as $log)
                                                <div class="bg-slate-50/80 rounded-lg p-3.5 border border-slate-200">
                                                    <div class="flex justify-between items-start mb-1.5">
                                                        <span class="text-[11px] font-mono text-slate-500">{{ $log->created_at->format('d/m/Y, H:i') }} WIB</span>
                                                        <span class="text-[11px] font-semibold text-blue-700"><i class="fas fa-user-tie text-[10px] mr-1"></i> {{ $log->guru->name }}</span>
                                                    </div>
                                                    <p class="text-xs text-slate-800">
                                                        Status diubah dari <span class="font-semibold line-through text-slate-400">{{ $log->labelStatusSebelumnya() }}</span> menjadi <span class="font-bold text-slate-900">{{ $log->labelStatusBaru() }}</span>
                                                    </p>
                                                    @if($log->keterangan)
                                                        <p class="text-[11px] text-slate-600 mt-1.5 bg-white px-2.5 py-1.5 rounded border border-slate-200 italic">"{{ $log->keterangan }}"</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // REALTIME LIVE AUTO-SYNC (POLLING SETIAP 2 DETIK)
    function pollLiveStatus() {
        fetch("{{ route('dashboard.presensi.live', $sesiPresensi) }}")
            .then(res => res.json())
            .then(data => {
                if(data.success && data.stats) {
                    const elHadir = document.getElementById('count-hadir');
                    const elIzin  = document.getElementById('count-izin');
                    const elSakit = document.getElementById('count-sakit');
                    const elAlpa  = document.getElementById('count-alpa');

                    if(elHadir) elHadir.innerText = data.stats.hadir;
                    if(elIzin)  elIzin.innerText  = data.stats.izin;
                    if(elSakit) elSakit.innerText = data.stats.sakit;
                    if(elAlpa)  elAlpa.innerText  = data.stats.alpa;

                    if(data.items) {
                        data.items.forEach(item => {
                            const badge = document.getElementById('badge-' + item.id);
                            if(badge && badge.getAttribute('data-status') !== item.status) {
                                badge.setAttribute('data-status', item.status);
                                badge.className = 'badge badge-' + item.status + ' transition-transform duration-300 transform scale-110';
                                badge.innerText = item.label;

                                setTimeout(() => {
                                    badge.className = 'badge badge-' + item.status + ' transition-transform duration-300';
                                }, 600);

                                const row = document.getElementById('row-' + item.id);
                                if(row && item.status === 'hadir') {
                                    row.classList.add('bg-emerald-50');
                                    setTimeout(() => row.classList.remove('bg-emerald-50'), 2500);
                                }
                            }

                            const ketEl = document.getElementById('ket-' + item.id);
                            if(ketEl && item.keterangan) {
                                ketEl.innerText = item.keterangan;
                            }
                        });
                    }
                }
            })
            .catch(err => console.debug('Live sync poll:', err));
    }

    let liveSyncTimer = setInterval(pollLiveStatus, 2000);
</script>
@endpush

@endsection
