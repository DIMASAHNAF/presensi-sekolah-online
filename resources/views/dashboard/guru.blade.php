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
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
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
                            <div class="flex justify-center gap-2">
                                <button @click="openEdit = true; editData = { id: {{ $guru->id }}, nama: '{{ $guru->name }}', nik: '{{ $guru->nik }}', username: '{{ $guru->username }}', email: '{{ $guru->email }}' }" class="text-blue-500 hover:bg-blue-50 px-2 py-1.5 rounded-lg border border-transparent hover:border-blue-100 transition-colors" title="Edit Guru">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('dashboard.guru.destroy', $guru) }}" method="POST" onsubmit="return confirm('Hapus guru {{ $guru->name }} secara permanen?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:bg-red-50 px-2 py-1.5 rounded-lg border border-transparent hover:border-red-100 transition-colors" title="Hapus Guru">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
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
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-xs px-4">
        <div @click.away="openAdd = false" class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="font-heading font-bold text-base mb-4 text-slate-900">Tambah Akun Dewan Guru</h3>
            <form action="{{ route('dashboard.guru.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">NIK (16 Digit)</label>
                    <input type="text" name="nik" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Username Login</label>
                    <input type="text" name="username" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Password Login</label>
                    <input type="password" name="password" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="flex gap-2 justify-end pt-2 border-t border-slate-100">
                    <button type="button" @click="openAdd = false" class="btn-secondary py-2 text-xs">Batal</button>
                    <button type="submit" class="btn-primary py-2 text-xs">Simpan Data Guru</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-xs px-4">
        <div @click.away="openEdit = false" class="bg-white rounded-xl shadow-xl border border-slate-200 w-full max-w-md p-6">
            <h3 class="font-heading font-bold text-base mb-4 text-slate-900">Edit Data Guru</h3>
            <form :action="'{{ route('dashboard.guru') }}/' + editData.id" method="POST">
                @csrf @method('PUT')
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editData.nama" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">NIK</label>
                    <input type="text" name="nik" x-model="editData.nik" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Username Login</label>
                    <input type="text" name="username" x-model="editData.username" class="w-full bg-white border border-slate-300 rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600" required>
                </div>
                <div class="flex gap-2 justify-end mt-6">
                    <button type="button" @click="openEdit = false" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInputs = document.querySelectorAll('input[type="password"]');
        passwordInputs.forEach(input => {
            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);

            const toggleBtn = document.createElement('span');
            toggleBtn.innerHTML = '<i class="far fa-eye text-gray-500"></i>';
            toggleBtn.style.position = 'absolute';
            toggleBtn.style.right = '12px';
            toggleBtn.style.top = '50%';
            toggleBtn.style.transform = 'translateY(-50%)';
            toggleBtn.style.cursor = 'pointer';
            wrapper.appendChild(toggleBtn);

            toggleBtn.addEventListener('click', function() {
                if (input.type === 'password') {
                    input.type = 'text';
                    toggleBtn.innerHTML = '<i class="far fa-eye-slash text-gray-800"></i>';
                } else {
                    input.type = 'password';
                    toggleBtn.innerHTML = '<i class="far fa-eye text-gray-500"></i>';
                }
            });
        });
    });
</script>
@endsection
