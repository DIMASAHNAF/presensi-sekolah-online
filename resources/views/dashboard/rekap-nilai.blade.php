{{-- ══════════════════════════════════════════════════════════════════════════════
     REKAP & CRUD NILAI SISWA (LENGKAP DENGAN FILTER PER KELAS & INPUT PER SISWA)
══════════════════════════════════════════════════════════════════════════════ --}}
<div id="rekap-nilai-section" class="mt-8 mb-6" x-data="rekapNilaiApp()" data-aos="fade-up">

    {{-- Header Section & Summary Metrics --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden mb-5">
        <div class="p-5 sm:p-6 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20">
                    <i class="fas fa-graduation-cap text-lg"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-base sm:text-lg font-heading font-extrabold text-slate-900 dark:text-white tracking-tight">
                            Rekapitulasi &amp; Pengelolaan Nilai Siswa
                        </h2>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            CRUD Aktif
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        Kelola penilaian akademik (Tugas, Ulangan Harian, PTS, PAS) per siswa berdasarkan rombel kelas secara langsung dari dashboard.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2.5 w-full sm:w-auto">
                <button type="button" @click="openModalAdd()" class="btn-primary text-xs py-2.5 px-4 shadow-sm w-full sm:w-auto justify-center">
                    <i class="fas fa-plus-circle text-xs"></i>
                    <span>Input Nilai Baru</span>
                </button>
            </div>
        </div>

        {{-- Mini Stat Baris Nilai --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y sm:divide-y-0 divide-slate-100 dark:divide-slate-800 bg-slate-50/60 dark:bg-slate-800/40">
            <div class="p-4 sm:p-5">
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Penilaian</p>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-xl sm:text-2xl font-heading font-extrabold text-slate-900 dark:text-white">{{ $statsNilai['total_input'] }}</span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">entri</span>
                </div>
            </div>
            <div class="p-4 sm:p-5">
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rata-Rata Nilai</p>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-xl sm:text-2xl font-heading font-extrabold text-blue-600 dark:text-blue-400">{{ $statsNilai['rata_rata'] }}</span>
                    <span class="text-[11px] text-slate-400 dark:text-slate-500">/ 100</span>
                </div>
            </div>
            <div class="p-4 sm:p-5">
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nilai Tertinggi</p>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-xl sm:text-2xl font-heading font-extrabold text-emerald-600 dark:text-emerald-400">{{ $statsNilai['tertinggi'] }}</span>
                    <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-950/60 px-1.5 py-0.5 rounded">Maks</span>
                </div>
            </div>
            <div class="p-4 sm:p-5">
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nilai Terendah</p>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-xl sm:text-2xl font-heading font-extrabold text-amber-600 dark:text-amber-400">{{ $statsNilai['terendah'] }}</span>
                    <span class="text-[10px] font-bold text-amber-700 dark:text-amber-400 bg-amber-100 dark:bg-amber-950/60 px-1.5 py-0.5 rounded">Min</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar & Table Rekap Nilai --}}
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        
        {{-- Form Filter --}}
        <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
            <form method="GET" action="{{ route('dashboard') }}#rekap-nilai-section" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                {{-- Filter Kelas --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Pilih Kelas</label>
                    <select name="nilai_kelas_id" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}" {{ request('nilai_kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }} ({{ $k->tingkat }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Mapel --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Mata Pelajaran</label>
                    <select name="nilai_mapel_id" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="">-- Semua Mapel --</option>
                        @foreach($mapelList as $m)
                            <option value="{{ $m->id }}" {{ request('nilai_mapel_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Jenis Penilaian --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Jenis Penilaian</label>
                    <select name="nilai_jenis" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="">-- Semua Jenis --</option>
                        <option value="tugas" {{ request('nilai_jenis') == 'tugas' ? 'selected' : '' }}>Tugas</option>
                        <option value="ulangan_harian" {{ request('nilai_jenis') == 'ulangan_harian' ? 'selected' : '' }}>Ulangan Harian (UH)</option>
                        <option value="uts" {{ request('nilai_jenis') == 'uts' ? 'selected' : '' }}>PTS / UTS</option>
                        <option value="uas" {{ request('nilai_jenis') == 'uas' ? 'selected' : '' }}>PAS / UAS</option>
                        <option value="praktik" {{ request('nilai_jenis') == 'praktik' ? 'selected' : '' }}>Praktik / Portofolio</option>
                        <option value="sikap" {{ request('nilai_jenis') == 'sikap' ? 'selected' : '' }}>Sikap & Karakter</option>
                    </select>
                </div>

                {{-- Filter Khusus Guru: Input Saya Sendiri --}}
                @if(!auth()->user()->isAdmin())
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1">Kepemilikan</label>
                    <select name="hanya_saya" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        <option value="0" {{ request('hanya_saya', '0') == '0' ? 'selected' : '' }}>Semua Nilai</option>
                        <option value="1" {{ request('hanya_saya') == '1' ? 'selected' : '' }}>Hanya Yang Saya Input</option>
                    </select>
                </div>
                @endif

                {{-- Tombol Terapkan --}}
                <div class="flex items-end gap-2 {{ auth()->user()->isAdmin() ? 'lg:col-span-2' : '' }}">
                    <button type="submit" class="btn-primary text-xs py-2 px-3.5 flex-1 justify-center">
                        <i class="fas fa-filter text-[11px]"></i> Filter Rekap
                    </button>
                    @if(request()->hasAny(['nilai_kelas_id', 'nilai_mapel_id', 'nilai_jenis', 'hanya_saya']))
                        <a href="{{ route('dashboard') }}#rekap-nilai-section" class="btn-secondary text-xs py-2 px-3 text-slate-500 dark:text-slate-400 hover:text-slate-800" title="Reset Filter">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Rekap Nilai --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="text-slate-500 dark:text-slate-400 bg-slate-50/80 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 font-heading uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="p-3.5 font-bold">Siswa &amp; Kelas</th>
                        <th class="p-3.5 font-bold">Mata Pelajaran</th>
                        <th class="p-3.5 font-bold">Judul &amp; Jenis Penilaian</th>
                        <th class="p-3.5 font-bold text-center">Nilai</th>
                        <th class="p-3.5 font-bold text-center">Predikat</th>
                        <th class="p-3.5 font-bold">Guru Penginput</th>
                        <th class="p-3.5 font-bold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($listNilai as $n)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            {{-- Siswa & Kelas --}}
                            <td class="p-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($n->siswa->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900 dark:text-white text-xs">{{ $n->siswa->name ?? '-' }}</p>
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                            NISN: {{ $n->siswa->nisn ?? '-' }} • <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $n->kelas->nama_kelas ?? '-' }}</span>
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Mata Pelajaran --}}
                            <td class="p-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/70 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    <i class="fas fa-book-bookmark text-[10px]"></i>
                                    {{ $n->mataPelajaran->nama_mapel ?? 'Umum / Wali Kelas' }}
                                </span>
                            </td>

                            {{-- Judul & Jenis --}}
                            <td class="p-3.5">
                                <p class="font-semibold text-slate-900 dark:text-white text-xs">{{ $n->judul }}</p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded
                                        {{ $n->jenis_penilaian === 'uas' || $n->jenis_penilaian === 'uts'
                                            ? 'bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800'
                                            : ($n->jenis_penilaian === 'ulangan_harian'
                                                ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800'
                                                : 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700') }}">
                                        {{ $n->jenis_label }}
                                    </span>
                                    @if($n->tanggal)
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">
                                            {{ $n->tanggal->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                                @if($n->catatan)
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 italic mt-1 bg-slate-50 dark:bg-slate-800/60 p-1.5 rounded border border-slate-100 dark:border-slate-800 max-w-xs">
                                        "{{ $n->catatan }}"
                                    </p>
                                @endif
                            </td>

                            {{-- Nilai Angka --}}
                            <td class="p-3.5 text-center">
                                <span class="font-mono text-base font-extrabold px-2.5 py-1 rounded-lg border
                                    {{ $n->nilai >= 80
                                        ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'
                                        : ($n->nilai >= 70
                                            ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border-blue-200 dark:border-blue-800'
                                            : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border-rose-200 dark:border-rose-800') }}">
                                    {{ number_format($n->nilai, 1) }}
                                </span>
                            </td>

                            {{-- Predikat Huruf --}}
                            <td class="p-3.5 text-center">
                                <span class="w-6 h-6 inline-flex items-center justify-center font-heading font-extrabold text-xs rounded-full
                                    {{ $n->predikat === 'A' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200' :
                                       ($n->predikat === 'B' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200' :
                                       ($n->predikat === 'C' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-200')) }}">
                                    {{ $n->predikat }}
                                </span>
                            </td>

                            {{-- Guru Penginput --}}
                            <td class="p-3.5">
                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                    <i class="fas fa-chalkboard-user text-slate-400 text-[10px]"></i>
                                    {{ $n->guru->name ?? '-' }}
                                </p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5">
                                    {{ $n->created_at ? $n->created_at->diffForHumans() : '-' }}
                                </p>
                            </td>

                            {{-- Aksi Edit & Hapus (Sesuai Hak Akses) --}}
                            <td class="p-3.5 text-right">
                                @if(auth()->user()->isAdmin() || $n->guru_id === auth()->id())
                                    <div class="inline-flex items-center gap-1">
                                        <button type="button"
                                                @click="openModalEdit({
                                                    id: {{ $n->id }},
                                                    siswa_name: '{{ addslashes($n->siswa->name ?? '') }}',
                                                    kelas_nama: '{{ addslashes($n->kelas->nama_kelas ?? '') }}',
                                                    mapel_id: '{{ $n->mapel_id ?? '' }}',
                                                    jenis_penilaian: '{{ $n->jenis_penilaian }}',
                                                    judul: '{{ addslashes($n->judul) }}',
                                                    nilai: '{{ $n->nilai }}',
                                                    tanggal: '{{ $n->tanggal ? $n->tanggal->format('Y-m-d') : '' }}',
                                                    catatan: '{{ addslashes($n->catatan ?? '') }}'
                                                })"
                                                class="w-7 h-7 rounded-lg text-blue-600 hover:text-white hover:bg-blue-600 dark:text-blue-400 dark:hover:bg-blue-600 border border-blue-200 dark:border-blue-800 flex items-center justify-center transition"
                                                title="Edit Nilai">
                                            <i class="fas fa-pencil text-[10px]"></i>
                                        </button>

                                        <form action="{{ route('dashboard.nilai.destroy', $n) }}" method="POST" class="inline-block"
                                              data-confirm="Apakah Anda yakin ingin menghapus data nilai {{ addslashes($n->judul) }} untuk siswa {{ addslashes($n->siswa->name ?? '') }}?"
                                              data-confirm-title="Hapus Nilai Siswa?"
                                              data-confirm-type="danger"
                                              data-confirm-btn="Ya, Hapus Nilai">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="w-7 h-7 rounded-lg text-rose-600 hover:text-white hover:bg-rose-600 dark:text-rose-400 dark:hover:bg-rose-600 border border-rose-200 dark:border-rose-800 flex items-center justify-center transition"
                                                    title="Hapus Nilai">
                                                <i class="fas fa-trash-can text-[10px]"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 dark:text-slate-500 italic">Hanya Baca</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-500 dark:text-blue-400 flex items-center justify-center mx-auto mb-3 border border-blue-100 dark:border-blue-900">
                                    <i class="fas fa-file-pen text-xl"></i>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800 dark:text-white">Belum Ada Data Nilai</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                                    Pilih tombol "Input Nilai Baru" di atas untuk menambahkan rekap nilai per siswa berdasarkan rombel kelas.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        @if($listNilai->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $listNilai->links() }}
            </div>
        @endif
    </div>

    {{-- ──────────────────────────────────────────────────────────── --}}
    {{-- MODAL TAMBAH NILAI (PILIH KELAS → LOAD SISWA OTOMATIS)        --}}
    {{-- ──────────────────────────────────────────────────────────── --}}
    <div x-show="openAdd" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs px-4 overflow-y-auto">
        <div @click.away="openAdd = false" class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-xl p-6 my-8 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <i class="fas fa-file-circle-plus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white">Input Nilai Siswa</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih kelas, lalu tentukan siswa yang akan dinilai</p>
                    </div>
                </div>
                <button type="button" @click="openAdd = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form action="{{ route('dashboard.nilai.store') }}" method="POST" class="space-y-4">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- 1. Pilih Kelas --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Kelas Siswa <span class="text-rose-500">*</span>
                        </label>
                        <select name="kelas_id" x-model="selectedKelasId" @change="onKelasChange()" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }} ({{ $k->tingkat }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 2. Pilih Siswa (Dinamis dari Kelas) --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">Nama Siswa <span class="text-rose-500">*</span></span>
                            <span x-show="loadingSiswa" class="text-[10px] text-blue-600 dark:text-blue-400 font-normal">
                                <i class="fas fa-spinner fa-spin"></i> Memuat...
                            </span>
                        </div>
                        <select name="siswa_id" x-model="selectedSiswaId" :disabled="loadingSiswa || siswaOptions.length === 0" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition disabled:opacity-60" required>
                            <option value="">-- Pilih Siswa --</option>
                            <template x-for="s in siswaOptions" :key="s.id">
                                <option :value="s.id" x-text="s.name + (s.nisn ? ' (NISN: ' + s.nisn + ')' : '')"></option>
                            </template>
                        </select>
                        <p x-show="!selectedKelasId" class="text-[10px] text-slate-400 mt-1">Pilih kelas terlebih dahulu untuk melihat daftar siswa.</p>
                        <p x-show="selectedKelasId && !loadingSiswa && siswaOptions.length === 0" class="text-[10px] text-amber-500 mt-1">Belum ada siswa terdaftar di kelas ini.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- 3. Mata Pelajaran --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Mata Pelajaran
                        </label>
                        <select name="mapel_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                            <option value="">-- Umum / Wali Kelas --</option>
                            @foreach($mapelList as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 4. Jenis Penilaian --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Jenis Penilaian <span class="text-rose-500">*</span>
                        </label>
                        <select name="jenis_penilaian" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                            <option value="tugas">Tugas</option>
                            <option value="ulangan_harian">Ulangan Harian (UH)</option>
                            <option value="uts">PTS / UTS</option>
                            <option value="uas">PAS / UAS</option>
                            <option value="praktik">Praktik / Portofolio</option>
                            <option value="sikap">Sikap & Karakter</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    {{-- 5. Judul / Materi Penilaian --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Judul / Deskripsi Singkat <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="judul" placeholder="Contoh: Tugas 1 - Logika Pemrograman" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>

                    {{-- 6. Nilai Angka (0-100) --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Nilai (0 - 100) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="0.1" min="0" max="100" name="nilai" placeholder="85.5" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-mono font-bold text-blue-600 dark:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- 7. Tanggal Penilaian --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal Penilaian
                        </label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>

                    {{-- 8. Catatan Guru --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Catatan / Feedback (Opsional)
                        </label>
                        <input type="text" name="catatan" placeholder="Contoh: Sangat baik, teliti menyelesaikan studi kasus" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>
                </div>

                <div class="flex gap-2.5 justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="openAdd = false" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary text-xs py-2.5 px-5 shadow-xs">
                        <i class="fas fa-floppy-disk text-xs"></i> Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ──────────────────────────────────────────────────────────── --}}
    {{-- MODAL EDIT NILAI                                             --}}
    {{-- ──────────────────────────────────────────────────────────── --}}
    <div x-show="openEdit" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs px-4 overflow-y-auto">
        <div @click.away="openEdit = false" class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 w-full max-w-lg p-6 my-8 animate-in fade-in zoom-in-95">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <i class="fas fa-pencil text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white">Perbarui Nilai Siswa</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400" x-text="editData.siswa_name + ' • ' + editData.kelas_nama"></p>
                    </div>
                </div>
                <button type="button" @click="openEdit = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-white">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <form :action="'{{ route('dashboard') }}/nilai/' + editData.id" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- Mapel --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Mata Pelajaran</label>
                        <select name="mapel_id" x-model="editData.mapel_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                            <option value="">-- Umum / Wali Kelas --</option>
                            @foreach($mapelList as $m)
                                <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Jenis Penilaian --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Jenis Penilaian <span class="text-rose-500">*</span></label>
                        <select name="jenis_penilaian" x-model="editData.jenis_penilaian" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                            <option value="tugas">Tugas</option>
                            <option value="ulangan_harian">Ulangan Harian (UH)</option>
                            <option value="uts">PTS / UTS</option>
                            <option value="uas">PAS / UAS</option>
                            <option value="praktik">Praktik / Portofolio</option>
                            <option value="sikap">Sikap & Karakter</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    {{-- Judul --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Judul Penilaian <span class="text-rose-500">*</span></label>
                        <input type="text" name="judul" x-model="editData.judul" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>

                    {{-- Nilai Angka --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Nilai (0 - 100) <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.1" min="0" max="100" name="nilai" x-model="editData.nilai" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-mono font-bold text-blue-600 dark:text-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-600 transition" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    {{-- Tanggal --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Tanggal</label>
                        <input type="date" name="tanggal" x-model="editData.tanggal" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">Catatan / Feedback</label>
                        <input type="text" name="catatan" x-model="editData.catatan" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 transition">
                    </div>
                </div>

                <div class="flex gap-2.5 justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="openEdit = false" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="btn-primary text-xs py-2.5 px-5 shadow-xs">
                        <i class="fas fa-check text-xs"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- Script Alpine.js untuk Rekap Nilai & AJAX load siswa per kelas --}}
<script>
    function rekapNilaiApp() {
        return {
            openAdd: false,
            openEdit: false,
            selectedKelasId: '',
            selectedSiswaId: '',
            loadingSiswa: false,
            siswaOptions: [],
            editData: {},

            openModalAdd() {
                this.openAdd = true;
                // Jika sudah ada filter kelas di URL, otomatis set
                const urlParams = new URLSearchParams(window.location.search);
                const currentKelas = urlParams.get('nilai_kelas_id');
                if (currentKelas && !this.selectedKelasId) {
                    this.selectedKelasId = currentKelas;
                    this.onKelasChange();
                }
            },

            openModalEdit(data) {
                this.editData = { ...data };
                this.openEdit = true;
            },

            onKelasChange() {
                this.siswaOptions = [];
                this.selectedSiswaId = '';
                if (!this.selectedKelasId) return;

                this.loadingSiswa = true;
                fetch(`{{ url('dashboard/kelas') }}/${this.selectedKelasId}/siswa-json`)
                    .then(res => res.json())
                    .then(res => {
                        if (res.status === 'success') {
                            this.siswaOptions = res.siswa || [];
                        } else {
                            this.siswaOptions = [];
                        }
                    })
                    .catch(() => {
                        this.siswaOptions = [];
                    })
                    .finally(() => {
                        this.loadingSiswa = false;
                    });
            }
        };
    }
</script>
