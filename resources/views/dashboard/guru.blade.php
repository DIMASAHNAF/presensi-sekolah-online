@extends('layouts.dashboard')

@section('title', 'Kelola Guru')
@section('page-title', 'Kelola Guru')
@section('page-subtitle', 'Manajemen data guru dan akun login')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-circle-check text-emerald-500"></i> {{ session('success') }}
    </div>
@endif

<div x-data="kelolaGuruApp()">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between mb-4">
            <h2 class="font-bold text-slate-800">Daftar Guru</h2>
            
            <div class="flex gap-2 w-full sm:w-auto">
                <form action="{{ route('dashboard.guru') }}" method="GET" class="flex flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama/NIK..." class="w-full sm:w-48 bg-slate-50 border border-slate-200 rounded-l-xl px-4 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <button type="submit" class="bg-slate-200 text-slate-600 px-3 rounded-r-xl border border-l-0 border-slate-200"><i class="fas fa-search"></i></button>
                </form>
                <button type="button" @click="openAddModal()" class="btn-primary shrink-0">
                    <i class="fas fa-plus"></i> Tambah Guru
                </button>
            </div>
        </div>

        {{-- MOBILE CARDS VIEW (md:hidden) --}}
        <div class="md:hidden space-y-3 p-3.5 bg-slate-50/50 rounded-xl mb-4">
            @forelse($guruList as $i => $guru)
                @php
                    $words = explode(' ', trim($guru->name));
                    $initials = count($words) >= 2
                        ? mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1)
                        : mb_substr($words[0], 0, 2);
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs transition-all duration-200 hover:border-blue-300">
                    {{-- Header: Avatar, Nama, No Urut --}}
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 border border-blue-200 flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs font-heading">
                                {{ strtoupper($initials) }}
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-heading font-bold text-sm text-slate-900 truncate">{{ $guru->name }}</h4>
                                <p class="text-[11px] font-mono text-slate-500 mt-0.5">NIK: {{ $guru->nik ?: '-' }}</p>
                            </div>
                        </div>

                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold font-mono bg-slate-100 text-slate-600 border border-slate-200 shrink-0">
                            #{{ $guruList->firstItem() + $i }}
                        </span>
                    </div>

                    {{-- Metadata Box --}}
                    <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100 grid grid-cols-2 gap-3 text-xs mb-3.5">
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Username Login</span>
                            <span class="font-mono text-slate-800 text-xs font-semibold flex items-center gap-1 truncate">
                                <i class="fas fa-user-tag text-slate-400 text-[10px]"></i>
                                {{ $guru->username }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Email</span>
                            <span class="text-slate-700 text-xs truncate block font-medium" title="{{ $guru->email }}">
                                {{ $guru->email ?: '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- Action Footer --}}
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="openEditModal({ id: {{ $guru->id }}, name: '{{ addslashes($guru->name) }}', nik: '{{ addslashes($guru->nik) }}', username: '{{ addslashes($guru->username) }}', email: '{{ addslashes($guru->email) }}' })" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition text-xs font-bold">
                            <i class="fas fa-pen-to-square text-xs"></i>
                            <span>Edit</span>
                        </button>
                        <form action="{{ route('dashboard.guru.destroy', $guru) }}" method="POST"
                              data-confirm="Hapus guru {{ $guru->name }} secara permanen?"
                              data-confirm-title="Hapus Akun Guru?"
                              data-confirm-type="danger"
                              data-confirm-btn="Hapus Guru"
                              class="inline-block">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition text-xs font-bold" title="Hapus Guru">
                                <i class="fas fa-trash-can text-xs"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white rounded-2xl border border-slate-200">
                    <i class="fas fa-user-slash text-3xl text-slate-300 mb-2"></i>
                    <p class="text-sm font-semibold text-slate-600">Belum ada data guru</p>
                </div>
            @endforelse
        </div>

        {{-- DESKTOP TABLE VIEW (hidden md:block) --}}
        <div class="hidden md:block overflow-x-auto">
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
                                <button type="button" @click="openEditModal({ id: {{ $guru->id }}, name: '{{ addslashes($guru->name) }}', nik: '{{ addslashes($guru->nik) }}', username: '{{ addslashes($guru->username) }}', email: '{{ addslashes($guru->email) }}' })" class="text-blue-500 hover:bg-blue-50 px-2 py-1.5 rounded-lg border border-transparent hover:border-blue-100 transition-colors" title="Edit Guru">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('dashboard.guru.destroy', $guru) }}" method="POST"
                                      data-confirm="Hapus guru {{ $guru->name }} secara permanen?"
                                      data-confirm-title="Hapus Akun Guru?"
                                      data-confirm-type="danger"
                                      data-confirm-btn="Hapus Guru">
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
        <div @click.away="if (!isSubmitting) openAdd = false" 
             :class="addShake ? 'animate-shake ring-2 ring-rose-500/50' : ''"
             class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md p-6 max-h-[90vh] overflow-y-auto transition-transform">
            
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <h3 class="font-heading font-bold text-base text-slate-900">Tambah Akun Dewan Guru</h3>
                <button type="button" @click="openAdd = false" :disabled="isSubmitting" class="text-slate-400 hover:text-slate-700 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- NOTIFIKASI ERROR DI DALAM KOTAK MODAL --}}
            <div x-show="addErrorMessage" x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs flex items-start gap-2.5">
                <i class="fas fa-triangle-exclamation text-rose-500 mt-0.5 text-sm shrink-0"></i>
                <div class="flex-1">
                    <p class="font-bold mb-0.5" x-text="addErrorMessage"></p>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-700 mt-1" x-show="addErrorList.length > 0">
                        <template x-for="(err, idx) in addErrorList" :key="idx">
                            <li x-text="err"></li>
                        </template>
                    </ul>
                </div>
            </div>

            <form @submit.prevent="submitAdd($event)" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nama Lengkap Guru</label>
                    <input type="text" name="name" x-model="addForm.name"
                           :class="addErrors.name ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                           class="w-full border rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 transition" 
                           required placeholder="Contoh: Budi Santoso, S.Kom">
                    <p x-show="addErrors.name" x-text="addErrors.name ? addErrors.name[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">NIK (16 Digit)</label>
                    <input type="text" name="nik" x-model="addForm.nik" maxlength="20"
                           :class="addErrors.nik ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                           class="w-full border rounded-lg px-3.5 py-2 text-sm font-mono focus:outline-none focus:ring-2 transition" 
                           required placeholder="1201xxxxxxxxxxxx">
                    <p x-show="addErrors.nik" x-text="addErrors.nik ? addErrors.nik[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Username Login</label>
                    <input type="text" name="username" x-model="addForm.username"
                           :class="addErrors.username ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                           class="w-full border rounded-lg px-3.5 py-2 text-sm font-mono focus:outline-none focus:ring-2 transition" 
                           required placeholder="budi.santoso">
                    <p x-show="addErrors.username" x-text="addErrors.username ? addErrors.username[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Password Login</label>
                    <div class="relative">
                        <input :type="showAddPassword ? 'text' : 'password'" name="password" x-model="addForm.password"
                               :class="addErrors.password ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                               class="w-full border rounded-lg px-3.5 py-2 pr-10 text-sm focus:outline-none focus:ring-2 transition" 
                               required placeholder="Minimal 6 karakter">
                        <button type="button" @click="showAddPassword = !showAddPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="far" :class="showAddPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <p x-show="addErrors.password" x-text="addErrors.password ? addErrors.password[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div class="flex gap-2 justify-end pt-2 border-t border-slate-100">
                    <button type="button" @click="openAdd = false" :disabled="isSubmitting" class="btn-secondary py-2 text-xs">Batal</button>
                    <button type="submit" :disabled="isSubmitting" class="btn-primary py-2 text-xs flex items-center gap-1.5">
                        <i x-show="isSubmitting" class="fas fa-spinner fa-spin text-xs"></i>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Data Guru'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-xs px-4">
        <div @click.away="if (!isSubmitting) openEdit = false" 
             :class="editShake ? 'animate-shake ring-2 ring-rose-500/50' : ''"
             class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md p-6 max-h-[90vh] overflow-y-auto transition-transform">
            
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-heading font-bold text-base text-slate-900">Edit Data Guru</h3>
                    <p class="text-xs text-slate-400 mt-0.5" x-text="'Mengubah akun: ' + (editData.name || '')"></p>
                </div>
                <button type="button" @click="openEdit = false" :disabled="isSubmitting" class="text-slate-400 hover:text-slate-700 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- NOTIFIKASI ERROR DI DALAM KOTAK MODAL --}}
            <div x-show="editErrorMessage" x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mb-4 p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-xs flex items-start gap-2.5">
                <i class="fas fa-triangle-exclamation text-rose-500 mt-0.5 text-sm shrink-0"></i>
                <div class="flex-1">
                    <p class="font-bold mb-0.5" x-text="editErrorMessage"></p>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-700 mt-1" x-show="editErrorList.length > 0">
                        <template x-for="(err, idx) in editErrorList" :key="idx">
                            <li x-text="err"></li>
                        </template>
                    </ul>
                </div>
            </div>

            <form @submit.prevent="submitEdit($event)" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editData.name"
                           :class="editErrors.name ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                           class="w-full border rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 transition" required>
                    <p x-show="editErrors.name" x-text="editErrors.name ? editErrors.name[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">NIK</label>
                    <input type="text" name="nik" x-model="editData.nik"
                           :class="editErrors.nik ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                           class="w-full border rounded-lg px-3.5 py-2 text-sm font-mono focus:outline-none focus:ring-2 transition" required>
                    <p x-show="editErrors.nik" x-text="editErrors.nik ? editErrors.nik[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Username Login</label>
                    <input type="text" name="username" x-model="editData.username"
                           :class="editErrors.username ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                           class="w-full border rounded-lg px-3.5 py-2 text-sm font-mono focus:outline-none focus:ring-2 transition" required>
                    <p x-show="editErrors.username" x-text="editErrors.username ? editErrors.username[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>

                {{-- Password Reset Input (Opsional) --}}
                <div class="p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-blue-900 mb-1">
                        <i class="fas fa-key mr-1"></i> Ganti Password Guru (Opsional)
                    </label>
                    <p class="text-[11px] text-blue-700/80 mb-2">Kosongkan jika tidak ingin mengubah password.</p>
                    <div class="relative">
                        <input :type="showEditPassword ? 'text' : 'password'" name="password" x-model="editData.password" placeholder="Ketik password baru..." 
                               :class="editErrors.password ? 'border-rose-500 focus:ring-rose-500' : 'border-blue-200 focus:ring-blue-600'"
                               class="w-full bg-white border rounded-lg px-3.5 py-2 pr-10 text-xs text-slate-900 focus:outline-none focus:ring-2 transition">
                        <button type="button" @click="showEditPassword = !showEditPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="far" :class="showEditPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <p x-show="editErrors.password" x-text="editErrors.password ? editErrors.password[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>

                <div class="flex gap-2 justify-end pt-2 border-t border-slate-100">
                    <button type="button" @click="openEdit = false" :disabled="isSubmitting" class="btn-secondary py-2 text-xs">Batal</button>
                    <button type="submit" :disabled="isSubmitting" class="btn-primary py-2 text-xs flex items-center gap-1.5">
                        <i x-show="isSubmitting" class="fas fa-spinner fa-spin text-xs"></i>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Update Data Guru'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function kelolaGuruApp() {
        return {
            openAdd: false,
            openEdit: false,
            isSubmitting: false,
            showAddPassword: false,
            showEditPassword: false,

            // State Form Tambah
            addForm: {
                name: '',
                nik: '',
                username: '',
                password: ''
            },
            addErrors: {},
            addErrorMessage: '',
            addErrorList: [],
            addShake: false,

            // State Form Edit
            editData: {},
            editErrors: {},
            editErrorMessage: '',
            editErrorList: [],
            editShake: false,

            openAddModal() {
                this.addForm = { name: '', nik: '', username: '', password: '' };
                this.addErrors = {};
                this.addErrorMessage = '';
                this.addErrorList = [];
                this.addShake = false;
                this.showAddPassword = false;
                this.openAdd = true;
            },

            openEditModal(guru) {
                this.editData = { ...guru, password: '' };
                this.editErrors = {};
                this.editErrorMessage = '';
                this.editErrorList = [];
                this.editShake = false;
                this.showEditPassword = false;
                this.openEdit = true;
            },

            // Submit AJAX Tambah Guru
            async submitAdd(event) {
                this.isSubmitting = true;
                this.addErrors = {};
                this.addErrorMessage = '';
                this.addErrorList = [];

                const form = event.target;
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                try {
                    const res = await fetch("{{ route('dashboard.guru.store') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.ok && data.success) {
                        this.openAdd = false;
                        if (window.toast) {
                            window.toast.success('Berhasil', data.message || 'Guru berhasil ditambahkan!');
                        }
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        // Error Validasi: Form tetap terbuka dan bergetar
                        this.addErrorMessage = data.message || 'Terdapat kesalahan pada data guru.';
                        this.addErrors = data.errors || {};
                        this.addErrorList = Object.values(this.addErrors).flat();
                        
                        this.addShake = true;
                        setTimeout(() => this.addShake = false, 500);
                    }
                } catch (err) {
                    console.error('Submit Add Guru error:', err);
                    this.addErrorMessage = 'Terjadi gangguan jaringan atau server. Silakan coba lagi.';
                    this.addShake = true;
                    setTimeout(() => this.addShake = false, 500);
                } finally {
                    this.isSubmitting = false;
                }
            },

            // Submit AJAX Edit Guru
            async submitEdit(event) {
                this.isSubmitting = true;
                this.editErrors = {};
                this.editErrorMessage = '';
                this.editErrorList = [];

                const form = event.target;
                const formData = new FormData(form);
                formData.append('_method', 'PUT');
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                try {
                    const updateUrl = "{{ route('dashboard.guru') }}/" + this.editData.id;
                    const res = await fetch(updateUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await res.json().catch(() => ({}));

                    if (res.ok && data.success) {
                        this.openEdit = false;
                        if (window.toast) {
                            window.toast.success('Berhasil', data.message || 'Data guru berhasil diperbarui!');
                        }
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        // Error Validasi: Form tetap terbuka dan bergetar
                        this.editErrorMessage = data.message || 'Terdapat kesalahan pada data guru.';
                        this.editErrors = data.errors || {};
                        this.editErrorList = Object.values(this.editErrors).flat();
                        
                        this.editShake = true;
                        setTimeout(() => this.editShake = false, 500);
                    }
                } catch (err) {
                    console.error('Submit Edit Guru error:', err);
                    this.editErrorMessage = 'Terjadi gangguan jaringan atau server. Silakan coba lagi.';
                    this.editShake = true;
                    setTimeout(() => this.editShake = false, 500);
                } finally {
                    this.isSubmitting = false;
                }
            }
        };
    }
</script>
@endsection
