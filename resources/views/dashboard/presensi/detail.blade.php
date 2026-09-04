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

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

    {{-- KIRI: INFO & BARCODE --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 text-center" data-aos="fade-up">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mx-auto mb-4">
                <i class="fas fa-face-viewfinder"></i>
            </div>
            <h3 class="font-bold text-slate-800 text-lg">Presensi Wajah</h3>
            <p class="text-xs text-slate-500 mt-1 mb-5">Siswa melakukan absen dengan scan wajah dari dashboard mereka.</p>

            <div class="bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 text-sm text-blue-700 text-left mb-4">
                <i class="fas fa-info-circle mr-1.5"></i>
                Absensi berjalan otomatis saat siswa scan wajah. Data hadir tampil secara real-time di bawah.
            </div>

            <a href="{{ route('dashboard.presensi.pdf', $sesiPresensi) }}" target="_blank"
               class="w-full btn-secondary justify-center py-3 text-sm">
                <i class="fas fa-print"></i> Cetak Laporan
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6" data-aos="fade-up" data-aos-delay="50">
            <h3 class="font-bold text-slate-800 text-sm mb-4 border-b border-slate-100 pb-3">Informasi Sesi</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Kelas</span>
                    <span class="font-semibold text-slate-800">{{ $sesiPresensi->kelas->nama_kelas }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Mata Pelajaran</span>
                    <span class="font-semibold text-slate-800">{{ $sesiPresensi->mataPelajaran ? $sesiPresensi->mataPelajaran->nama_mapel : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Jam Pelajaran</span>
                    <span class="font-semibold text-slate-800">{{ $sesiPresensi->jam_pelajaran ?: '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Pembuat Sesi</span>
                    <span class="font-semibold text-slate-800">{{ $sesiPresensi->guru->name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500">Status Sesi</span>
                    @if($sesiPresensi->is_active)
                        @php
                            $minutesDiff = now()->diffInMinutes($sesiPresensi->created_at);
                            $isExpired = $minutesDiff >= 30;
                        @endphp
                        @if($isExpired)
                            <span class="text-orange-600 font-bold bg-orange-50 px-2 py-0.5 rounded text-xs">Expired (Lewat 30m)</span>
                        @else
                            <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded text-xs">Aktif ({{ 30 - $minutesDiff }}m tersisa)</span>
                        @endif
                    @else
                        <span class="text-slate-500 font-bold bg-slate-100 px-2 py-0.5 rounded text-xs">Selesai / Ditutup</span>
                    @endif
                </div>
            </div>

            {{-- Tutup Sesi: HANYA tampil untuk sesi tipe kelas (pagi) --}}
            @if($sesiPresensi->tipe === 'kelas')
            <div class="mt-5 pt-5 border-t border-slate-100">
                <form action="{{ route('dashboard.presensi.close', $sesiPresensi) }}" method="POST">
                    @csrf @method('PATCH')
                    @if($sesiPresensi->is_active)
                        <button type="submit" class="w-full btn-danger justify-center py-2 text-xs font-semibold" onclick="return confirm('Tutup sesi presensi ini? Siswa tidak bisa scan wajah lagi.')">
                            <i class="fas fa-lock mr-1"></i> Tutup Sesi (Nonaktifkan Absensi)
                        </button>
                    @else
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white justify-center py-2 text-xs font-semibold rounded-xl transition flex items-center gap-1">
                            <i class="fas fa-unlock mr-1"></i> Buka Sesi (Aktifkan Kembali)
                        </button>
                    @endif
                </form>
            </div>
            @else
            <div class="mt-5 pt-5 border-t border-slate-100">
                <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs text-slate-500 flex items-center gap-2">
                    <i class="fas fa-info-circle text-blue-400"></i>
                    Sesi Mapel tidak memiliki tombol tutup. Edit kehadiran langsung dari tabel siswa.
                </div>
            </div>
            @endif

            @if(auth()->user()->isAdmin())
            <div class="mt-3 pt-3 border-t border-slate-100">
                <form action="{{ route('dashboard.presensi.reset', $sesiPresensi) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-red-100 text-red-600 hover:bg-red-200 font-bold justify-center py-2 rounded-xl text-sm transition" onclick="return confirm('BAHAYA: Yakin ingin MERESET seluruh kehadiran kelas ini? Semua siswa akan dikembalikan ke status Alpa dan log riwayat akan dihapus.')">
                        <i class="fas fa-trash-can"></i> Reset Presensi Kelas
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>

    {{-- KANAN: DAFTAR SISWA --}}
    <div class="lg:col-span-3 bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden flex flex-col" data-aos="fade-up" data-aos-delay="100">
        <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h2 class="text-lg font-extrabold text-slate-800">Daftar Kehadiran Siswa</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Hadir: <span id="count-hadir" class="font-bold text-emerald-600">{{ $sesiPresensi->presensi->where('status', 'hadir')->count() }}</span> |
                    Izin: <span id="count-izin" class="font-bold text-amber-500">{{ $sesiPresensi->presensi->where('status', 'izin')->count() }}</span> |
                    Sakit: <span id="count-sakit" class="font-bold text-orange-500">{{ $sesiPresensi->presensi->where('status', 'sakit')->count() }}</span> |
                    Alpa: <span id="count-alpa" class="font-bold text-red-500">{{ $sesiPresensi->presensi->where('status', 'alpa')->count() }}</span>
                </p>
            </div>

            {{-- LIVE SYNC BADGE --}}
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Realtime Live Auto-Sync
                </span>
            </div>
        </div>

        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500">
                        <th class="px-6 py-4 font-semibold">Nama Siswa</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Keterangan</th>
                        <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($sesiPresensi->presensi->sortBy('siswa.name') as $absen)
                        <tr id="row-{{ $absen->id }}" class="hover:bg-slate-50 transition-colors duration-300 group" x-data="{ openLog: false, openEdit: false }">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $absen->siswa->name }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">NISN: {{ $absen->siswa->nisn ?: '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span id="badge-{{ $absen->id }}" data-status="{{ $absen->status }}" class="badge badge-{{ $absen->status }}">
                                    {{ $absen->labelStatus() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 italic text-xs">
                                <span id="ket-{{ $absen->id }}">{{ $absen->keterangan ?: '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                @if($absen->logPresensi->count() > 0)
                                    <button @click="openLog = true" class="text-xs text-slate-400 hover:text-blue-600 transition" title="Lihat Riwayat Perubahan">
                                        <i class="fas fa-history"></i> Log ({{ $absen->logPresensi->count() }})
                                    </button>
                                @endif
                                <button @click="openEdit = true" class="btn-secondary py-1.5 px-3 text-xs">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>

                            {{-- MODAL EDIT STATUS --}}
                            <td class="p-0 border-0 m-0 h-0 w-0">
                                <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4">
                                    <div @click.away="openEdit = false" class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden p-6 relative">
                                        <h3 class="font-bold text-lg text-slate-800 mb-1">Edit Kehadiran</h3>
                                        <p class="text-sm text-slate-500 mb-5">Siswa: <strong>{{ $absen->siswa->name }}</strong></p>
                                        
                                        <form method="POST" action="{{ route('dashboard.presensi.record.update', $absen) }}">
                                            @csrf @method('PATCH')
                                            <div class="mb-4">
                                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Status Baru</label>
                                                <select name="status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                                                    <option value="hadir" {{ $absen->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                                    <option value="izin" {{ $absen->status == 'izin' ? 'selected' : '' }}>Izin</option>
                                                    <option value="sakit" {{ $absen->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                                    <option value="alpa" {{ $absen->status == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                                </select>
                                            </div>
                                            <div class="mb-6">
                                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Keterangan / Alasan</label>
                                                <input type="text" name="keterangan" value="{{ $absen->keterangan }}" placeholder="Contoh: Pulang awal karena sakit" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500">
                                            </div>
                                            <div class="flex gap-3 justify-end">
                                                <button type="button" @click="openEdit = false" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition">Batal</button>
                                                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 transition">Simpan & Log</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>

                            {{-- MODAL LOG PERUBAHAN --}}
                            <td class="p-0 border-0 m-0 h-0 w-0">
                                <div x-show="openLog" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4">
                                    <div @click.away="openLog = false" class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden p-6 relative">
                                        <div class="flex justify-between items-center mb-5">
                                            <h3 class="font-bold text-lg text-slate-800">Riwayat Perubahan Status</h3>
                                            <button @click="openLog = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-xl"></i></button>
                                        </div>
                                        <p class="text-sm text-slate-500 mb-4 border-b border-slate-100 pb-4">Siswa: <strong>{{ $absen->siswa->name }}</strong></p>
                                        
                                        <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                                            @foreach($absen->logPresensi as $log)
                                                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                                    <div class="flex justify-between items-start mb-2">
                                                        <span class="text-xs font-semibold text-slate-400">{{ $log->created_at->format('d M Y, H:i') }}</span>
                                                        <span class="text-xs font-bold text-blue-600"><i class="fas fa-user-edit mr-1"></i> {{ $log->guru->name }}</span>
                                                    </div>
                                                    <p class="text-sm text-slate-700">
                                                        Status diubah dari <span class="font-bold line-through text-slate-400">{{ $log->labelStatusSebelumnya() }}</span> menjadi <span class="font-bold text-slate-800">{{ $log->labelStatusBaru() }}</span>
                                                    </p>
                                                    @if($log->keterangan)
                                                        <p class="text-xs text-slate-500 mt-2 bg-white px-3 py-2 rounded-lg border border-slate-100 italic">"{{ $log->keterangan }}"</p>
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
