@extends('layouts.dashboard')

@section('title', 'Log Perubahan Presensi')
@section('page-title', 'Log Perubahan Presensi')
@section('page-subtitle', 'Riwayat perubahan status presensi siswa oleh guru')

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
                        </option>no 2 ga jadi, gw mau bahas dulu alur  gw

gw  jelasin dulu  alur  project  presensi gw, 

di awali dengan  form login yang ada guru dan siswa, akun guru + admin hanya bisa di buat oleh backdoor, tidak bisa public teruntuk siswa bisa di  public

nah setelah siswa register, dia akan di arahkan ke  dahshboard scan (untuk sekrang kan masih barcode) , dan setiap guru di mapel pertama pagi hari, itu membuat sesi kelas, dan ada barcode nya  ntar, barcode tersebut  memiliki timeout 30 menit bawaan sistem dan bisa di matikan langsung,

nah untuk kondisi sekolah, hape itu  di kumpul dari pagi sampe sore (sampe balik)

nah sesuai presensi gw di project itu ada 2, yaitu presensi kelas (wali kelas) , u know lah, yang hanya  di lakukan di pagi hari doang pake buku

nah kedua ada presensi mapel, yaitu presensi permapel, setiap guru kan punya presensi permapel tersendiri, untuk  kebutuhan nilai


nah gw jelasin alur sekarang, setelah siswa scan barcode, total perkelas itu 36 murid, anggap sudah scan semua, ntar di  table sesi kelas  itu, semua murid akan hadir 36 nya, untuk sesi kelas baru dan murid yang baru register itu akan baru semua maksud nya adalah, semisal yang dateng 35, 1 orang tersebut ga masuk  di table, alias belum terdaftar, belum ada riwayat kelas lah bisa di bilang

kecuali 36 sekarang besok nya jadi 35, nah sesi  besok hari, kelas langusng terisi siswa siswa dari kelas tersebut, tapi masih kondisi absen, ntar pas di scan baru hadir, yang tak hadir ini akan tetep absen, dan namanya udah ada di table karna sebelum nya dia  scan di hari sebelum nya kan

oke lanjut, gw kan buat 2 rekap pdf tuh, ada rekap presensi perbulan, sama mapel nah untuk per bulan udah bagus, kan sesi nya 1 hari hanya  1 gw buat alur sistem nya

jadi dia bakal rekap tuh semua sesi di satu bulan, contoh 30x (sesuai rata rata hari bulan), nah teruntuk mapel ini ada yang jaanggal

kan waktu guru mau buat sesi kelas disitu tertera input mapel dan les nya, nah oke lah clear di 3 les itu habbis, ntar di  les berikut nya gimana dong, setelah les 3 mapel b indo habis, nah create mapel website nya gimana, sedangkan hape di kumpul dan kalau di create juga percuma ga  ada yang  scan, dan juga bisa ngeganggu rekap karna 1 hari ada 2 sesi

nah gw mikir, di bedain aja ya form nya, ada  presensi kelas sama mapel, nah kalau kelas kan ada input mapel, itu tetep di adakan aja, karna dari pada capek, buat 2 sesi yang berbeda, contoh sesi kelas 10 pplg 3, sesi yang kelas sama mapel, kan ribet 2x, nah makanya tetep pertahankan input mapel dan les di sesi kelas, lagian di rekap pdf kelas juga ga kebawa  mapel nya

nah di form baru itu khusus mapel, tapiii data nya di ambil dari table kelas hari itu,  contoh sesi pertama matematika 2 les dan 36 hadir, lalu mapel ke 2 itu print design 3 les satu siswa izin sakit balik, nah pas after mapel 1 kan hape di kumpul jadi ga bisa scan, nah mapel after pertama tinggal buat sesi kelas tapi untuk mapel dia dan les nya, jadi data di ambil dari siswa yang hadir/tidak di  hari itu dari sesi mapel pertama, ntar tinggal di edit mereka siapa yang masih hadir atau sakit, dan mapel seterus nya, jadi lebih gampang untuk rekap mapel

nah itu untuk jam jam yang memungkin kan pergantian mapel, masuk  kan ke database ga papa biar singkron, dan biar  enak  juga, bebas kamu  lah, untuk cara baca nya,  hanya liat jam nya aja per les di hari senin/selasa/rabu/kamis/jumat itu habis di jam berapa aja
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
                        {{ optional($log->presensi->siswa)->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3 text-slate-600">
                        <span class="font-semibold">{{ optional($log->presensi->sesiPresensi->kelas)->nama_kelas ?? '-' }}</span>
                        @if(optional($log->presensi->sesiPresensi)->mataPelajaran)
                            <span class="text-xs ml-1 bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded">
                                <i class="fas fa-book-open mr-0.5 text-[10px]"></i>{{ $log->presensi->sesiPresensi->mataPelajaran->nama_mapel }}
                            </span>
                        @else
                            <span class="text-xs ml-1 bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded">Sesi Pagi</span>
                        @endif
                        <br>
                        <span class="text-xs text-slate-400">
                            <i class="fas fa-calendar text-[10px] mr-1"></i>{{ optional($log->presensi->sesiPresensi)->tanggal?->format('d M Y') ?? '-' }}
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
                        @if(optional($log->presensi->sesiPresensi)->jam_pelajaran)
                            <br><span class="text-xs text-slate-400 font-medium">
                                <i class="fas fa-clock mr-1"></i>{{ $log->presensi->sesiPresensi->jam_pelajaran }}
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
