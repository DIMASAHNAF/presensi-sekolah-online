@extends('layouts.dashboard')

@section('title', 'Kelola Kelas')
@section('page-title', 'Kelola Kelas')
@section('page-subtitle', 'Manajemen data kelas')

@section('content')

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-circle-check text-emerald-500"></i> {{ session('success') }}
    </div>
@endif

<div x-data="kelolaKelasApp()">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-800">Daftar Kelas</h2>
            <div class="flex gap-2">
                <!-- Reset Kelas (Sesi) button -->
                <form action="{{ route('dashboard.reset-sesi') }}" method="POST"
                      data-confirm="Apakah Anda yakin ingin mereset (menutup) semua sesi kelas hari ini? Riwayat hari ini akan tetap ada namun sesi akan berakhir."
                      data-confirm-title="Reset Semua Sesi Kelas Hari Ini?"
                      data-confirm-type="warning"
                      data-confirm-btn="Ya, Akhiri Sesi">
                    @csrf
                    <button type="submit" class="btn-secondary !text-red-600 !bg-red-50 hover:!bg-red-100">
                        <i class="fas fa-rotate-left"></i> Reset Kelas (Akhiri Sesi)
                    </button>
                </form>

                <button type="button" @click="openAddModal()" class="btn-primary">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </button>
            </div>
        </div>

        {{-- MOBILE CARDS VIEW (md:hidden) --}}
        <div class="md:hidden space-y-3 p-3.5 bg-slate-50/50 rounded-xl mb-4">
            @forelse($kelasList as $i => $kelas)
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs transition-all duration-200 hover:border-blue-300">
                    {{-- Header: Nama Kelas + Jumlah Siswa --}}
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-700 border border-indigo-200 flex items-center justify-center font-bold text-sm shrink-0 shadow-2xs">
                                <i class="fas fa-door-open"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="font-heading font-extrabold text-sm text-slate-900 truncate">{{ $kelas->nama_kelas }}</h4>
                                <p class="text-[11px] font-mono text-slate-500 mt-0.5">Tingkat {{ $kelas->tingkat }}</p>
                            </div>
                        </div>

                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200 shrink-0 flex items-center gap-1">
                            <i class="fas fa-users text-[9px]"></i>
                            <span>{{ $kelas->siswa_count }} Siswa</span>
                        </span>
                    </div>

                    {{-- Metadata Box --}}
                    <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100 grid grid-cols-2 gap-3 text-xs mb-3.5">
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Tingkat Kelas</span>
                            <span class="font-bold text-slate-800 text-xs block">
                                Kelas {{ $kelas->tingkat }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Kompetensi Jurusan</span>
                            <span class="text-slate-800 text-xs font-semibold truncate block" title="{{ $kelas->jurusan }}">
                                {{ $kelas->jurusan ?: '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- Action Footer --}}
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="openEditModal({ id: {{ $kelas->id }}, nama_kelas: '{{ addslashes($kelas->nama_kelas) }}', tingkat: '{{ $kelas->tingkat }}', jurusan: '{{ addslashes($kelas->jurusan ?? '') }}' })" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 transition text-xs font-bold">
                            <i class="fas fa-pen-to-square text-xs"></i>
                            <span>Edit</span>
                        </button>
                        <form action="{{ route('dashboard.kelas.destroy', $kelas) }}" method="POST" class="inline-block"
                              data-confirm="Hapus kelas {{ $kelas->nama_kelas }} secara permanen?"
                              data-confirm-title="Hapus Kelas?"
                              data-confirm-type="danger"
                              data-confirm-btn="Hapus Kelas">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition text-xs font-bold" title="Hapus Kelas">
                                <i class="fas fa-trash-can text-xs"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center bg-white rounded-2xl border border-slate-200">
                    <i class="fas fa-door-closed text-3xl text-slate-300 mb-2"></i>
                    <p class="text-sm font-semibold text-slate-600">Belum ada data kelas</p>
                </div>
            @endforelse
        </div>

        {{-- DESKTOP TABLE VIEW (hidden md:block) --}}
        <div class="hidden md:block overflow-x-auto">
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
                        <td class="px-4 py-3">{{ $kelas->jurusan ?: '-' }}</td>
                        <td class="px-4 py-3">{{ $kelas->siswa_count }} Siswa</td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" @click="openEditModal({ id: {{ $kelas->id }}, nama_kelas: '{{ addslashes($kelas->nama_kelas) }}', tingkat: '{{ $kelas->tingkat }}', jurusan: '{{ addslashes($kelas->jurusan ?? '') }}' })" class="text-blue-500 hover:bg-blue-50 px-2 py-1 rounded transition">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('dashboard.kelas.destroy', $kelas) }}" method="POST" class="inline-block"
                                  data-confirm="Hapus kelas {{ $kelas->nama_kelas }} secara permanen?"
                                  data-confirm-title="Hapus Kelas?"
                                  data-confirm-type="danger"
                                  data-confirm-btn="Hapus Kelas">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:bg-red-50 px-2 py-1 rounded transition">
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
        <div @click.away="if (!isSubmitting) openAdd = false" 
             :class="addShake ? 'animate-shake ring-2 ring-rose-500/50' : ''"
             class="bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-md p-6 max-h-[90vh] overflow-y-auto transition-transform">
            
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <h3 class="font-heading font-bold text-base text-slate-900">Tambah Kelas Baru</h3>
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
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nama Kelas</label>
                    <input type="text" name="nama_kelas" x-model="addForm.nama_kelas"
                           :class="addErrors.nama_kelas ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                           class="w-full border rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 transition" 
                           required placeholder="Contoh: X TJKT 1">
                    <p x-show="addErrors.nama_kelas" x-text="addErrors.nama_kelas ? addErrors.nama_kelas[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Tingkat</label>
                    <select name="tingkat" x-model="addForm.tingkat"
                            :class="addErrors.tingkat ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                            class="w-full border rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 transition" required>
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>
                    <p x-show="addErrors.tingkat" x-text="addErrors.tingkat ? addErrors.tingkat[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Jurusan</label>
                    <input type="text" name="jurusan" x-model="addForm.jurusan"
                           :class="addErrors.jurusan ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                           class="w-full border rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 transition" placeholder="Contoh: TJKT">
                    <p x-show="addErrors.jurusan" x-text="addErrors.jurusan ? addErrors.jurusan[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div class="flex gap-2 justify-end pt-2 border-t border-slate-100">
                    <button type="button" @click="openAdd = false" :disabled="isSubmitting" class="btn-secondary py-2 text-xs">Batal</button>
                    <button type="submit" :disabled="isSubmitting" class="btn-primary py-2 text-xs flex items-center gap-1.5">
                        <i x-show="isSubmitting" class="fas fa-spinner fa-spin text-xs"></i>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Kelas'"></span>
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
                    <h3 class="font-heading font-bold text-base text-slate-900">Edit Data Kelas</h3>
                    <p class="text-xs text-slate-400 mt-0.5" x-text="'Mengubah kelas: ' + (editData.nama_kelas || '')"></p>
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
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Nama Kelas</label>
                    <input type="text" name="nama_kelas" x-model="editData.nama_kelas"
                           :class="editErrors.nama_kelas ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                           class="w-full border rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 transition" required>
                    <p x-show="editErrors.nama_kelas" x-text="editErrors.nama_kelas ? editErrors.nama_kelas[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Tingkat</label>
                    <select name="tingkat" x-model="editData.tingkat"
                            :class="editErrors.tingkat ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                            class="w-full border rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 transition" required>
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>
                    <p x-show="editErrors.tingkat" x-text="editErrors.tingkat ? editErrors.tingkat[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-600 mb-1.5">Jurusan</label>
                    <input type="text" name="jurusan" x-model="editData.jurusan"
                           :class="editErrors.jurusan ? 'border-rose-500 focus:ring-rose-500 bg-rose-50/30' : 'border-slate-300 focus:ring-blue-600 bg-white'"
                           class="w-full border rounded-lg px-3.5 py-2 text-sm focus:outline-none focus:ring-2 transition">
                    <p x-show="editErrors.jurusan" x-text="editErrors.jurusan ? editErrors.jurusan[0] : ''" class="text-[11px] text-rose-600 mt-1 font-medium"></p>
                </div>
                <div class="flex gap-2 justify-end pt-2 border-t border-slate-100">
                    <button type="button" @click="openEdit = false" :disabled="isSubmitting" class="btn-secondary py-2 text-xs">Batal</button>
                    <button type="submit" :disabled="isSubmitting" class="btn-primary py-2 text-xs flex items-center gap-1.5">
                        <i x-show="isSubmitting" class="fas fa-spinner fa-spin text-xs"></i>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Update Data Kelas'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function kelolaKelasApp() {
        return {
            openAdd: false,
            openEdit: false,
            isSubmitting: false,

            // State Form Tambah
            addForm: {
                nama_kelas: '',
                tingkat: 'X',
                jurusan: ''
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
                this.addForm = { nama_kelas: '', tingkat: 'X', jurusan: '' };
                this.addErrors = {};
                this.addErrorMessage = '';
                this.addErrorList = [];
                this.addShake = false;
                this.openAdd = true;
            },

            openEditModal(kelas) {
                this.editData = { ...kelas };
                this.editErrors = {};
                this.editErrorMessage = '';
                this.editErrorList = [];
                this.editShake = false;
                this.openEdit = true;
            },

            // Submit AJAX Tambah Kelas
            async submitAdd(event) {
                this.isSubmitting = true;
                this.addErrors = {};
                this.addErrorMessage = '';
                this.addErrorList = [];

                const form = event.target;
                const formData = new FormData(form);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                try {
                    const res = await fetch("{{ route('dashboard.kelas.store') }}", {
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
                            window.toast.success('Berhasil', data.message || 'Kelas berhasil ditambahkan!');
                        }
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        // Error Validasi: Form tetap terbuka dan bergetar
                        this.addErrorMessage = data.message || 'Terdapat kesalahan pada data kelas.';
                        this.addErrors = data.errors || {};
                        this.addErrorList = Object.values(this.addErrors).flat();
                        
                        this.addShake = true;
                        setTimeout(() => this.addShake = false, 500);
                    }
                } catch (err) {
                    console.error('Submit Add Kelas error:', err);
                    this.addErrorMessage = 'Terjadi gangguan jaringan atau server. Silakan coba lagi.';
                    this.addShake = true;
                    setTimeout(() => this.addShake = false, 500);
                } finally {
                    this.isSubmitting = false;
                }
            },

            // Submit AJAX Edit Kelas
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
                    const updateUrl = "{{ route('dashboard.kelas') }}/" + this.editData.id;
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
                            window.toast.success('Berhasil', data.message || 'Data kelas berhasil diperbarui!');
                        }
                        setTimeout(() => window.location.reload(), 500);
                    } else {
                        // Error Validasi: Form tetap terbuka dan bergetar
                        this.editErrorMessage = data.message || 'Terdapat kesalahan pada data kelas.';
                        this.editErrors = data.errors || {};
                        this.editErrorList = Object.values(this.editErrors).flat();
                        
                        this.editShake = true;
                        setTimeout(() => this.editShake = false, 500);
                    }
                } catch (err) {
                    console.error('Submit Edit Kelas error:', err);
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
</div>
@endsection
