@extends('layouts.dashboard')

@section('title', 'Kelola Kelas')
@section('page-title', 'Kelola Kelas')
@section('page-subtitle', 'Manajemen data kelas')

@section('content')

@if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>Terjadi kesalahan saat menyimpan data.</span>
    </div>
@endif

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-circle-check text-emerald-500"></i> {{ session('success') }}
    </div>
@endif

<div x-data="{ openAdd: false, openEdit: false, editData: {} }">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-800">Daftar Kelas</h2>
            <div class="flex gap-2">
                <!-- Reset Kelas (Sesi) button -->
                <form action="{{ route('dashboard.reset-sesi') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset (menutup) semua sesi kelas hari ini? Riwayat hari ini akan tetap ada namun sesi akan berakhir.')">
                    @csrf
                    <button type="submit" class="btn-secondary !text-red-600 !bg-red-50 hover:!bg-red-100">
                        <i class="fas fa-rotate-left"></i> Reset Kelas (Akhiri Sesi)
                    </button>
                </form>

                <button @click="openAdd = true" class="btn-primary">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="text-xs text-slate-400 bg-slate-50 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 rounded-l-xl">No</th>
                        <th class="px-4 py-3">Nama Kelas</th>
                        <th class="px-4 py-3">Tingkat</th>
                        <th class="px-4 py-3">Jurusan</th>
                        <th class="px-4 py-3">Jml Siswa</th>
                        <th class="px-4 py-3 text-center rounded-r-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kelasList as $i => $kelas)
                    <tr class="table-row">
                        <td class="px-4 py-3 text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-700">{{ $kelas->nama_kelas }}</td>
                        <td class="px-4 py-3">{{ $kelas->tingkat }}</td>
                        <td class="px-4 py-3">{{ $kelas->jurusan }}</td>
                        <td class="px-4 py-3">{{ $kelas->siswa_count }} Siswa</td>
                        <td class="px-4 py-3 text-center">
                            <button @click="openEdit = true; editData = { id: {{ $kelas->id }}, nama: '{{ $kelas->nama_kelas }}', tingkat: '{{ $kelas->tingkat }}', jurusan: '{{ $kelas->jurusan }}' }" class="text-blue-500 hover:bg-blue-50 px-2 py-1 rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('dashboard.kelas.destroy', $kelas) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus kelas ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:bg-red-50 px-2 py-1 rounded">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada data kelas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL ADD --}}
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-xs px-4">
        <div @click.away="openAdd = false" class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-md p-6">
            <h3 class="font-heading font-bold text-base mb-4 text-slate-900">Tambah Kelas Baru</h3>
            <form action="{{ route('dashboard.kelas.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" required placeholder="Contoh: X TJKT 1">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Tingkat</label>
                    <select name="tingkat" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Jurusan</label>
                    <input type="text" name="jurusan" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" placeholder="TJKT">
                </div>
                <div class="flex gap-2 justify-end pt-2 border-t border-slate-100">
                    <button type="button" @click="openAdd = false" class="btn-secondary py-2 text-xs">Batal</button>
                    <button type="submit" class="btn-primary py-2 text-xs">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-xs px-4">
        <div @click.away="openEdit = false" class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-md p-6">
            <h3 class="font-heading font-bold text-base mb-4 text-slate-900">Edit Data Kelas</h3>
            <form :action="'{{ route('dashboard.kelas') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nama Kelas</label>
                    <input type="text" name="nama_kelas" x-model="editData.nama" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Tingkat</label>
                    <select name="tingkat" x-model="editData.tingkat" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Jurusan</label>
                    <input type="text" name="jurusan" x-model="editData.jurusan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2">
                </div>
                <div class="flex gap-2 justify-end">
                    <button type="button" @click="openEdit = false" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
