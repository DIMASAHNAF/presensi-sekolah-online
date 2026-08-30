@extends('layouts.dashboard')

@section('title', 'Kelola Siswa')
@section('page-title', 'Kelola Siswa')
@section('page-subtitle', 'Manajemen data siswa dan kelas')

@section('content')

@if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>Ada error input: {{ $errors->first() }}</span>
    </div>
@endif

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-circle-check text-emerald-500"></i> {{ session('success') }}
    </div>
@endif

<div x-data="{ openAdd: false, openEdit: false, editData: {} }">
    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between mb-4">
            <h2 class="font-bold text-slate-800">Daftar Siswa</h2>
            
            <div class="flex gap-2 w-full sm:w-auto">
                <form action="{{ route('dashboard.siswa') }}" method="GET" class="flex flex-1 gap-2">
                    <select name="kelas_id" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 max-w-[150px]">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <div class="flex">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/NISN..." class="w-full sm:w-48 bg-slate-50 border border-slate-200 rounded-l-xl px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <button type="submit" class="bg-slate-200 text-slate-600 px-3 rounded-r-xl border border-l-0 border-slate-200"><i class="fas fa-search"></i></button>
                    </div>
                </form>
                <button @click="openAdd = true" class="btn-primary shrink-0">
                    <i class="fas fa-plus"></i> Tambah Siswa
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="text-xs text-slate-400 bg-slate-50 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 rounded-l-xl">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">NISN</th>
                        <th class="px-4 py-3">Kelas</th>
                        <th class="px-4 py-3">Username (Login)</th>
                        <th class="px-4 py-3 text-center rounded-r-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($siswaList as $i => $siswa)
                    <tr class="table-row">
                        <td class="px-4 py-3 text-slate-500">{{ $siswaList->firstItem() + $i }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-700">{{ $siswa->name }}</td>
                        <td class="px-4 py-3">{{ $siswa->nisn }}</td>
                        <td class="px-4 py-3">{{ optional($siswa->kelas)->nama_kelas ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $siswa->username }}</td>
                        <td class="px-4 py-3 text-center">
                            <button @click="openEdit = true; editData = { id: {{ $siswa->id }}, nama: '{{ $siswa->name }}', nisn: '{{ $siswa->nisn }}', username: '{{ $siswa->username }}', kelas_id: '{{ $siswa->kelas_id }}' }" class="text-blue-500 hover:bg-blue-50 px-2 py-1 rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('dashboard.siswa.destroy', $siswa) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus siswa ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:bg-red-50 px-2 py-1 rounded">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada data siswa</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $siswaList->links() }}
        </div>
    </div>

    {{-- MODAL ADD --}}
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 px-4">
        <div @click.away="openAdd = false" class="bg-white rounded-3xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="font-bold text-lg mb-4">Tambah Siswa</h3>
            <form action="{{ route('dashboard.siswa.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">NISN</label>
                    <input type="text" name="nisn" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
                        <option value="">Pilih Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Username Login</label>
                    <input type="text" name="username" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Password Login</label>
                    <input type="password" name="password" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
                </div>
                <div class="flex gap-2 justify-end mt-6">
                    <button type="button" @click="openAdd = false" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 px-4">
        <div @click.away="openEdit = false" class="bg-white rounded-3xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="font-bold text-lg mb-4">Edit Siswa</h3>
            <form :action="'{{ route('dashboard.siswa') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editData.nama" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">NISN</label>
                    <input type="text" name="nisn" x-model="editData.nisn" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Kelas</label>
                    <select name="kelas_id" x-model="editData.kelas_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
                        <option value="">Pilih Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Username Login</label>
                    <input type="text" name="username" x-model="editData.username" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
                </div>
                <div class="flex gap-2 justify-end mt-6">
                    <button type="button" @click="openEdit = false" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
