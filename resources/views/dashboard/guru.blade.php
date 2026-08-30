@extends('layouts.dashboard')

@section('title', 'Kelola Guru')
@section('page-title', 'Kelola Guru')
@section('page-subtitle', 'Manajemen data guru dan akun login')

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
            <h2 class="font-bold text-slate-800">Daftar Guru</h2>
            
            <div class="flex gap-2 w-full sm:w-auto">
                <form action="{{ route('dashboard.guru') }}" method="GET" class="flex flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/NIK..." class="w-full sm:w-48 bg-slate-50 border border-slate-200 rounded-l-xl px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <button type="submit" class="bg-slate-200 text-slate-600 px-3 rounded-r-xl border border-l-0 border-slate-200"><i class="fas fa-search"></i></button>
                </form>
                <button @click="openAdd = true" class="btn-primary shrink-0">
                    <i class="fas fa-plus"></i> Tambah Guru
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="text-xs text-slate-400 bg-slate-50 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 rounded-l-xl">No</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">NIK</th>
                        <th class="px-4 py-3">Username (Login)</th>
                        <th class="px-4 py-3 text-center rounded-r-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($guruList as $i => $guru)
                    <tr class="table-row">
                        <td class="px-4 py-3 text-slate-500">{{ $guruList->firstItem() + $i }}</td>
                        <td class="px-4 py-3 font-semibold text-slate-700">{{ $guru->name }}</td>
                        <td class="px-4 py-3">{{ $guru->nik }}</td>
                        <td class="px-4 py-3">{{ $guru->username }}</td>
                        <td class="px-4 py-3 text-center">
                            <button @click="openEdit = true; editData = { id: {{ $guru->id }}, nama: '{{ $guru->name }}', nik: '{{ $guru->nik }}', username: '{{ $guru->username }}', email: '{{ $guru->email }}' }" class="text-blue-500 hover:bg-blue-50 px-2 py-1 rounded">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('dashboard.guru.destroy', $guru) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus guru ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:bg-red-50 px-2 py-1 rounded">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada data guru</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $guruList->links() }}
        </div>
    </div>

    {{-- MODAL ADD --}}
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 px-4">
        <div @click.away="openAdd = false" class="bg-white rounded-3xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="font-bold text-lg mb-4">Tambah Akun Guru</h3>
            <form action="{{ route('dashboard.guru.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">NIK</label>
                    <input type="text" name="nik" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
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
        <div @click.away="openEdit = false" class="bg-white rounded-3xl w-full max-w-md p-6">
            <h3 class="font-bold text-lg mb-4">Edit Guru</h3>
            <form :action="'{{ route('dashboard.guru') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editData.nama" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">NIK</label>
                    <input type="text" name="nik" x-model="editData.nik" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2" required>
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
