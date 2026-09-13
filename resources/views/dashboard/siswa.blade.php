@extends('layouts.dashboard')

@section('title', 'Kelola Siswa')
@section('page-title', 'Kelola Siswa')
@section('page-subtitle', 'Manajemen data siswa, foto profil, dan diagnostik biometrik Face ID')

@section('content')

@if(isset($errors) && $errors->any())
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

<div x-data="kelolaSiswaApp()">

    {{-- ────────────────────────────────────────────── --}}
    {{-- 1. TECHNICAL STATS CARDS                       --}}
    {{-- ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Card 1: Total Siswa --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Akun Siswa</p>
                <h3 class="text-2xl font-extrabold text-slate-900 font-heading mt-0.5">{{ $stats['total'] }}</h3>
                <span class="text-[10px] font-mono text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full font-bold">Terdaftar di Sistem</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fas fa-users"></i>
            </div>
        </div>

        {{-- Card 2: Face ID Enrolled --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Biometrik Face ID</p>
                <div class="flex items-baseline gap-2 mt-0.5">
                    <h3 class="text-2xl font-extrabold text-emerald-600 font-heading">{{ $stats['enrolled'] }}</h3>
                    <span class="text-xs font-bold text-slate-400">/ {{ $stats['total'] }}</span>
                </div>
                <span class="text-[10px] font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full font-bold">
                    {{ $stats['enrolled_percent'] }}% Terverifikasi
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fas fa-face-viewfinder"></i>
            </div>
        </div>

        {{-- Card 3: Belum Rekam Wajah --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Belum Rekam Wajah</p>
                <h3 class="text-2xl font-extrabold text-amber-600 font-heading mt-0.5">{{ $stats['not_enrolled'] }}</h3>
                <span class="text-[10px] font-mono text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full font-bold">Perlu Re-Enroll</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
        </div>

        {{-- Card 4: Foto Profil Custom --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Foto Profil Custom</p>
                <h3 class="text-2xl font-extrabold text-indigo-600 font-heading mt-0.5">{{ $stats['custom_avatar'] }}</h3>
                <span class="text-[10px] font-mono text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full font-bold">Avatar Mandiri</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                <i class="fas fa-image"></i>
            </div>
        </div>
    </div>

    {{-- ────────────────────────────────────────────── --}}
    {{-- 2. FILTER & SEARCH CONTROLS                    --}}
    {{-- ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-5 mb-6">
        <div class="flex flex-col lg:flex-row gap-4 items-center justify-between">
            {{-- Form Filter & Search --}}
            <form action="{{ route('dashboard.siswa') }}" method="GET" class="flex flex-wrap gap-2.5 w-full lg:w-auto flex-1">
                {{-- Kelas Filter --}}
                <select name="kelas_id" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                {{-- Status Biometrik Filter --}}
                <select name="wajah" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">Status Biometrik</option>
                    <option value="enrolled" {{ request('wajah') == 'enrolled' ? 'selected' : '' }}>Wajah Terdaftar (Face ID)</option>
                    <option value="not_enrolled" {{ request('wajah') == 'not_enrolled' ? 'selected' : '' }}>Belum Rekam Wajah</option>
                </select>

                {{-- Status Avatar Filter --}}
                <select name="foto" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">Status Foto Profil</option>
                    <option value="has_avatar" {{ request('foto') == 'has_avatar' ? 'selected' : '' }}>Ada Foto Custom</option>
                    <option value="no_avatar" {{ request('foto') == 'no_avatar' ? 'selected' : '' }}>Avatar Default</option>
                </select>

                {{-- Search Input --}}
                <div class="flex flex-1 min-w-[200px] relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari nama, NISN, username, bio..." 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-800 placeholder-slate-400">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                </div>

                <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-xs">
                    Terapkan
                </button>

                @if(request()->hasAny(['kelas_id', 'wajah', 'foto', 'search']))
                <a href="{{ route('dashboard.siswa') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1">
                    <i class="fas fa-rotate-left"></i> Reset
                </a>
                @endif
            </form>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2 w-full lg:w-auto justify-end shrink-0">
                <form action="{{ route('dashboard.siswa.reset-all-faces') }}" method="POST" onsubmit="return confirm('PERINGATAN KRITIS!\n\nTindakan ini akan menghapus seluruh data vektor biometrik wajah SEMUA siswa.\nSemua siswa akan diwajibkan melakukan scan/enroll ulang saat presensi berikutnya.\n\nApakah Anda yakin ingin mereset semua wajah?')" class="inline-block">
                    @csrf
                    <button type="submit" class="bg-rose-50 text-rose-600 hover:bg-rose-100 hover:text-rose-700 px-3.5 py-2 rounded-xl text-xs font-bold transition border border-rose-200 flex items-center gap-1.5">
                        <i class="fas fa-triangle-exclamation"></i>
                        <span class="hidden sm:inline">Reset Semua Wajah</span>
                        <span class="sm:hidden">Reset Wajah</span>
                    </button>
                </form>

                <button @click="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                    <i class="fas fa-plus"></i> Tambah Siswa
                </button>
            </div>
        </div>
    </div>

    {{-- ────────────────────────────────────────────── --}}
    {{-- 3. TECHNICAL STUDENT TABLE                     --}}
    {{-- ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="text-[11px] text-slate-400 bg-slate-50 uppercase font-bold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3.5">Siswa</th>
                        <th class="px-4 py-3.5">NISN & Kelas</th>
                        <th class="px-4 py-3.5 text-center">Status Face ID</th>
                        <th class="px-4 py-3.5 text-center">Profil Sosial</th>
                        <th class="px-4 py-3.5 text-center">Aksi Teknis</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($siswaList as $i => $siswa)
                    @php
                        $hasAvatar = !empty($siswa->avatar) && $siswa->avatar !== '0';
                        $hasBanner = !empty($siswa->banner) && $siswa->banner !== '0';
                        $hasFace = $siswa->isFaceEnrolled();
                        $initials = strtoupper(mb_substr($siswa->name, 0, 2));
                        $faceVector = is_array($siswa->face_descriptor) ? $siswa->face_descriptor : [];
                        $faceDimensions = count($faceVector);
                        $faceSample = $faceDimensions > 0 ? array_slice($faceVector, 0, 5) : [];
                        
                        $siswaJson = [
                            'id' => $siswa->id,
                            'name' => $siswa->name,
                            'username' => $siswa->username,
                            'nisn' => $siswa->nisn,
                            'email' => $siswa->email,
                            'kelas_id' => $siswa->kelas_id,
                            'nama_kelas' => optional($siswa->kelas)->nama_kelas ?? 'Tanpa Kelas',
                            'avatar_url' => $siswa->avatar_url,
                            'banner_url' => $siswa->banner_url,
                            'has_avatar' => $hasAvatar,
                            'has_banner' => $hasBanner,
                            'bio' => $siswa->bio,
                            'website' => $siswa->website,
                            'has_face' => $hasFace,
                            'face_enrolled_at' => $siswa->face_enrolled_at ? $siswa->face_enrolled_at->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : null,
                            'face_dimensions' => $faceDimensions,
                            'face_sample' => $faceSample,
                            'created_at' => $siswa->created_at ? $siswa->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') . ' WIB' : '-',
                        ];
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        {{-- Avatar & Student Info --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="relative w-10 h-10 rounded-full overflow-hidden shrink-0 border border-slate-200 bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-2xs">
                                    @if($siswa->avatar_url)
                                        <img src="{{ $siswa->avatar_url }}" alt="{{ $siswa->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span>{{ $initials }}</span>
                                    @endif
                                    @if($hasAvatar)
                                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border border-white rounded-full" title="Custom Photo Uploaded"></span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <h4 class="font-heading font-extrabold text-xs sm:text-sm text-slate-900 truncate max-w-xs">{{ $siswa->name }}</h4>
                                    </div>
                                    <p class="text-[11px] font-mono text-slate-400">
                                        {{ '@' . ($siswa->username ?? 'siswa') }}
                                        @if($siswa->email)
                                            &bull; <span class="text-slate-400">{{ $siswa->email }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- NISN & Kelas --}}
                        <td class="px-4 py-3.5">
                            <span class="font-mono text-xs font-bold text-slate-800 block">{{ $siswa->nisn }}</span>
                            <span class="text-[11px] font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md inline-block mt-0.5">
                                {{ optional($siswa->kelas)->nama_kelas ?? 'Belum ada kelas' }}
                            </span>
                        </td>

                        {{-- Status Face ID Biometrics --}}
                        <td class="px-4 py-3.5 text-center">
                            @if($hasFace)
                                <div class="inline-flex flex-col items-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold font-mono uppercase bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5 shadow-2xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        128-D Vector Ready
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono mt-0.5">
                                        {{ $siswa->face_enrolled_at ? $siswa->face_enrolled_at->format('d/m/y H:i') : 'Tersimpan' }}
                                    </span>
                                </div>
                            @else
                                <div class="inline-flex flex-col items-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold font-mono uppercase bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1 shadow-2xs">
                                        <i class="fas fa-triangle-exclamation text-[9px]"></i> Belum Terdaftar
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono mt-0.5">Wajib Enroll</span>
                                </div>
                            @endif
                        </td>

                        {{-- Profil Sosial Metadata --}}
                        <td class="px-4 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Avatar Badge --}}
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] {{ $hasAvatar ? 'bg-indigo-50 text-indigo-600 border border-indigo-200' : 'bg-slate-100 text-slate-300' }}" title="{{ $hasAvatar ? 'Foto Avatar Custom' : 'Avatar Default' }}">
                                    <i class="fas fa-user"></i>
                                </span>
                                {{-- Banner Badge --}}
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] {{ $hasBanner ? 'bg-purple-50 text-purple-600 border border-purple-200' : 'bg-slate-100 text-slate-300' }}" title="{{ $hasBanner ? 'Cover Banner Custom' : 'Banner Default' }}">
                                    <i class="fas fa-panorama"></i>
                                </span>
                                {{-- Bio Badge --}}
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] {{ !empty($siswa->bio) ? 'bg-blue-50 text-blue-600 border border-blue-200' : 'bg-slate-100 text-slate-300' }}" title="{{ !empty($siswa->bio) ? 'Bio: ' . $siswa->bio : 'Belum Ada Bio' }}">
                                    <i class="fas fa-quote-left"></i>
                                </span>
                                {{-- Website Badge --}}
                                <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] {{ !empty($siswa->website) ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-slate-100 text-slate-300' }}" title="{{ !empty($siswa->website) ? 'Website: ' . $siswa->website : 'Belum Ada Website' }}">
                                    <i class="fas fa-link"></i>
                                </span>
                            </div>
                        </td>

                        {{-- Technical Actions --}}
                        <td class="px-4 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- 1. INSPECT TEKNIS --}}
                                <button type="button" @click="openInspectModal(@js($siswaJson))" 
                                        class="p-2 rounded-xl bg-slate-100 hover:bg-blue-50 text-slate-700 hover:text-blue-600 border border-slate-200 hover:border-blue-200 transition" 
                                        title="Inspeksi Teknis Akun & Foto">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>

                                {{-- 2. EDIT & RESET PASSWORD --}}
                                <button type="button" @click="openEditModal(@js($siswaJson))" 
                                        class="p-2 rounded-xl bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200 hover:border-indigo-200 transition" 
                                        title="Edit Data & Ganti Password">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </button>

                                {{-- 3. RESET WAJAH --}}
                                @if($hasFace)
                                <form action="{{ route('dashboard.siswa.reset-face', $siswa) }}" method="POST" onsubmit="return confirm('Reset biometrik wajah siswa {{ $siswa->name }}? Vektor 128-d akan dihapus dan siswa harus scan ulang.')" class="inline-block">
                                    @csrf
                                    <button type="submit" class="p-2 rounded-xl bg-slate-100 hover:bg-amber-50 text-slate-700 hover:text-amber-600 border border-slate-200 hover:border-amber-200 transition" title="Reset Wajah Face ID">
                                        <i class="fas fa-face-smile text-xs"></i>
                                    </button>
                                </form>
                                @endif

                                {{-- 4. HAPUS AKUN --}}
                                <form action="{{ route('dashboard.siswa.destroy', $siswa) }}" method="POST" onsubmit="return confirm('Hapus akun siswa {{ $siswa->name }} secara permanen? Data absensi terkait akan terpengaruh.')" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 border border-slate-200 hover:border-rose-200 transition" title="Hapus Siswa">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <p class="font-bold text-slate-600 text-sm">Tidak ada data siswa yang cocok</p>
                            <p class="text-xs text-slate-400 mt-0.5">Silakan sesuaikan filter pencarian atau kelas di atas.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($siswaList->hasPages())
        <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50">
            {{ $siswaList->links() }}
        </div>
        @endif
    </div>

    {{-- ────────────────────────────────────────────── --}}
    {{-- 4. TECHNICAL INSPECTOR MODAL                   --}}
    {{-- ────────────────────────────────────────────── --}}
    <div x-show="showInspect" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4 overflow-y-auto">
        <div @click.away="showInspect = false" 
             class="bg-white rounded-3xl w-full max-w-xl shadow-2xl border border-slate-200 overflow-hidden relative my-auto animate-in fade-in zoom-in-95">
            
            <template x-if="inspectData">
                <div>
                    {{-- Cover Banner Preview --}}
                    <div class="h-32 bg-slate-800 relative overflow-hidden">
                        <template x-if="inspectData.banner_url">
                            <img :src="inspectData.banner_url" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!inspectData.banner_url">
                            <div class="w-full h-full bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 relative">
                                <div class="absolute inset-0 bg-[radial-gradient(#ffffff15_1px,transparent_1px)] [background-size:12px_12px] opacity-40"></div>
                            </div>
                        </template>
                        
                        {{-- Close button --}}
                        <button type="button" @click="showInspect = false" 
                                class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center transition">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>

                    {{-- Profile Header with overlapping Avatar --}}
                    <div class="px-6 pb-4 pt-0 relative border-b border-slate-100">
                        <div class="flex items-end justify-between -mt-12 mb-3">
                            {{-- Avatar --}}
                            <div class="relative w-20 h-20 rounded-full border-4 border-white bg-blue-600 text-white shadow-md overflow-hidden shrink-0 flex items-center justify-center font-heading font-extrabold text-xl">
                                <template x-if="inspectData.avatar_url">
                                    <img :src="inspectData.avatar_url" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!inspectData.avatar_url">
                                    <span x-text="inspectData.name.substring(0, 2).toUpperCase()"></span>
                                </template>
                            </div>

                            {{-- Action Badge --}}
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold font-mono uppercase"
                                      :class="inspectData.has_face ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200'">
                                    <span x-text="inspectData.has_face ? 'Face ID Aktif' : 'Belum Rekam Wajah'"></span>
                                </span>
                            </div>
                        </div>

                        <div>
                            <h3 class="font-heading font-black text-lg text-slate-900 leading-tight" x-text="inspectData.name"></h3>
                            <p class="text-xs font-mono text-slate-400 mt-0.5">
                                <span x-text="'@' + inspectData.username"></span> &bull; 
                                <span x-text="inspectData.nama_kelas"></span> &bull; 
                                NISN: <span x-text="inspectData.nisn"></span>
                            </p>
                        </div>
                    </div>

                    {{-- Body Technical Metadata --}}
                    <div class="p-6 space-y-5 max-h-[60vh] overflow-y-auto">
                        
                        {{-- 1. Identitas Akun & Sosial --}}
                        <div>
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2.5 flex items-center gap-1.5">
                                <i class="fas fa-id-card text-blue-600"></i> Informasi Akun & Sosial
                            </h4>
                            <div class="grid grid-cols-2 gap-2.5 text-xs">
                                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">ID Pengguna (DB)</span>
                                    <span class="font-mono font-bold text-slate-800" x-text="'#' + inspectData.id"></span>
                                </div>
                                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Waktu Daftar</span>
                                    <span class="font-mono font-bold text-slate-800 text-[11px]" x-text="inspectData.created_at"></span>
                                </div>
                                <div class="col-span-2 p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Bio Siswa</span>
                                    <p class="text-slate-700 italic mt-0.5" x-text="inspectData.bio || 'Belum mencantumkan bio.'"></p>
                                </div>
                                <template x-if="inspectData.website">
                                    <div class="col-span-2 p-2.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                        <div>
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Website / Tautan</span>
                                            <span class="font-mono text-blue-600 text-xs truncate max-w-sm block" x-text="inspectData.website"></span>
                                        </div>
                                        <a :href="inspectData.website.startsWith('http') ? inspectData.website : 'https://' + inspectData.website" target="_blank" class="text-blue-600 hover:text-blue-700 text-xs font-bold px-2 py-1 bg-white rounded-lg border border-slate-200">
                                            Kunjungi &rarr;
                                        </a>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- 2. Diagnostik Biometrik Face ID --}}
                        <div>
                            <h4 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-2.5 flex items-center gap-1.5">
                                <i class="fas fa-face-viewfinder text-emerald-600"></i> Diagnostik Vektor Biometrik Face ID
                            </h4>
                            <div class="p-3.5 rounded-2xl border" :class="inspectData.has_face ? 'bg-emerald-50/40 border-emerald-200' : 'bg-slate-50 border-slate-200'">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                        <i class="fas text-sm" :class="inspectData.has_face ? 'fa-circle-check text-emerald-600' : 'fa-circle-xmark text-slate-400'"></i>
                                        <span x-text="inspectData.has_face ? 'Vektor 128-D Tersimpan' : 'Data Biometrik Kosong'"></span>
                                    </span>
                                    <template x-if="inspectData.has_face">
                                        <span class="text-[10px] font-mono text-emerald-700 font-bold" x-text="'Enrolled: ' + inspectData.face_enrolled_at"></span>
                                    </template>
                                </div>

                                <template x-if="inspectData.has_face">
                                    <div class="mt-2 space-y-1.5">
                                        <p class="text-[11px] text-slate-600">Model: <strong>128-dimensional Euclidean Descriptor</strong> (kompatibel face_recognition & dlib).</p>
                                        <div class="bg-slate-900 text-emerald-400 p-2.5 rounded-xl font-mono text-[10px] overflow-x-auto">
                                            <span class="text-slate-400 block">// Cuplikan vektor 5 dimensi pertama:</span>
                                            <span x-text="JSON.stringify(inspectData.face_sample) + ' ... (' + inspectData.face_dimensions + ' floats)'"></span>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!inspectData.has_face">
                                    <p class="text-[11px] text-slate-500 mt-1">Siswa ini belum merekam biometrik wajah. Siswa dapat melakukan perekaman di portal siswa saat login berikutnya.</p>
                                </template>
                            </div>
                        </div>

                        {{-- 3. Cepat Moderasi Akun --}}
                        <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
                            <button type="button" @click="showInspect = false; openEditModal(inspectData)" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-xs flex items-center gap-1.5">
                                <i class="fas fa-key"></i> Edit & Ganti Password
                            </button>

                            <template x-if="inspectData.has_face">
                                <form :action="'{{ route('dashboard.siswa') }}/' + inspectData.id + '/reset-face'" method="POST" onsubmit="return confirm('Reset wajah siswa ini sekarang?')">
                                    @csrf
                                    <button type="submit" class="bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-bold px-3.5 py-2.5 rounded-xl transition flex items-center gap-1.5">
                                        <i class="fas fa-rotate-left"></i> Reset Wajah
                                    </button>
                                </form>
                            </template>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>

    {{-- ────────────────────────────────────────────── --}}
    {{-- 5. MODAL TAMBAH SISWA                          --}}
    {{-- ────────────────────────────────────────────── --}}
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs px-4 overflow-y-auto">
        <div @click.away="openAdd = false" class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-md p-6 my-auto animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <h3 class="font-heading font-extrabold text-base text-slate-900">Tambah Akun Siswa Baru</h3>
                <button type="button" @click="openAdd = false" class="text-slate-400 hover:text-slate-700 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('dashboard.siswa.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Nama Lengkap Siswa</label>
                    <input type="text" name="name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" required placeholder="Contoh: Muhammad Fajar Pratama">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">NISN (10 Digit)</label>
                        <input type="text" name="nisn" maxlength="10" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" required placeholder="0012345678">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Kelas</label>
                        <select name="kelas_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Username Akun</label>
                    <input type="text" name="username" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" required placeholder="fajar.pratama">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Password Awal</label>
                    <input type="password" name="password" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" required placeholder="Minimal 6 karakter">
                </div>
                
                <div class="flex gap-2 justify-end pt-3 border-t border-slate-100">
                    <button type="button" @click="openAdd = false" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">Batal</button>
                    <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white transition shadow-xs">Simpan Siswa</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ────────────────────────────────────────────── --}}
    {{-- 6. MODAL EDIT SISWA & RESET PASSWORD           --}}
    {{-- ────────────────────────────────────────────── --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs px-4 overflow-y-auto">
        <div @click.away="openEdit = false" class="bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-lg p-6 my-auto animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <div>
                    <h3 class="font-heading font-extrabold text-base text-slate-900">Edit Akun & Reset Password</h3>
                    <p class="text-xs text-slate-400 mt-0.5" x-text="'Mengubah akun: ' + (editData.name || '')"></p>
                </div>
                <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-slate-700 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form :action="'{{ route('dashboard.siswa') }}/' + editData.id" method="POST" class="space-y-4">
                @csrf @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Nama Lengkap Siswa</label>
                    <input type="text" name="name" x-model="editData.name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">NISN</label>
                        <input type="text" name="nisn" x-model="editData.nisn" maxlength="10" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Kelas</label>
                        <select name="kelas_id" x-model="editData.kelas_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Username Login</label>
                    <input type="text" name="username" x-model="editData.username" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition" required>
                </div>

                {{-- Password Reset Input --}}
                <div class="p-3.5 bg-blue-50/50 rounded-2xl border border-blue-100">
                    <label class="block text-xs font-bold uppercase tracking-wider text-blue-900 mb-1">
                        <i class="fas fa-key mr-1"></i> Reset Password Siswa
                    </label>
                    <p class="text-[11px] text-blue-700/80 mb-2">Kosongkan jika tidak ingin mengubah password siswa.</p>
                    <input type="password" name="password" placeholder="Ketik password baru siswa..." class="w-full bg-white border border-blue-200 rounded-xl px-3.5 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                </div>

                {{-- Moderasi Foto & Reset Biometrik --}}
                <div class="space-y-2 pt-1 border-t border-slate-100">
                    <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Moderasi & Biometrik</span>
                    
                    <label x-show="editData.has_face" class="flex items-center gap-2.5 cursor-pointer p-2 rounded-xl hover:bg-slate-50">
                        <input type="checkbox" name="reset_wajah" value="1" class="rounded border-slate-300 text-amber-600 focus:ring-amber-600 w-4 h-4">
                        <span class="text-xs text-slate-700 font-medium">Reset Data Wajah Face ID (Siswa wajib rekam ulang)</span>
                    </label>

                    <label x-show="editData.has_avatar" class="flex items-center gap-2.5 cursor-pointer p-2 rounded-xl hover:bg-slate-50">
                        <input type="checkbox" name="remove_avatar" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-600 w-4 h-4">
                        <span class="text-xs text-rose-700 font-medium">Hapus Foto Profil Siswa (Reset ke default inisial)</span>
                    </label>

                    <label x-show="editData.has_banner" class="flex items-center gap-2.5 cursor-pointer p-2 rounded-xl hover:bg-slate-50">
                        <input type="checkbox" name="remove_banner" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-600 w-4 h-4">
                        <span class="text-xs text-rose-700 font-medium">Hapus Banner Cover Siswa (Reset ke default gradient)</span>
                    </label>
                </div>

                <div class="flex gap-2 justify-end pt-3 border-t border-slate-100">
                    <button type="button" @click="openEdit = false" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">Batal</button>
                    <button type="submit" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white transition shadow-xs">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function kelolaSiswaApp() {
        return {
            openAdd: false,
            openEdit: false,
            showInspect: false,
            editData: {},
            inspectData: null,

            openAddModal() {
                this.openAdd = true;
            },

            openEditModal(siswa) {
                this.editData = { ...siswa };
                this.openEdit = true;
            },

            openInspectModal(siswa) {
                this.inspectData = siswa;
                this.showInspect = true;
            }
        };
    }
</script>
@endsection
