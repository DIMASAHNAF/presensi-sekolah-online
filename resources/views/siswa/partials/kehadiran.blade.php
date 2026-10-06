                <div x-show="activeTab === 'kehadiran'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
                    {{-- Header Kehadiran --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <span class="text-[10px] font-mono font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Rekapitulasi Presensi</span>
                            <h2 class="font-heading font-black text-xl text-slate-900 dark:text-white">Kehadiran & Riwayat</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Semester Berjalan &bull; {{ $user->kelas->nama_kelas ?? 'Kelas Siswa' }}</p>
                        </div>
                        <div class="text-right flex items-center gap-3 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 rounded-2xl px-4 py-2.5">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-emerald-700 dark:text-emerald-300">Persentase</p>
                                <p class="text-2xl font-black font-heading text-emerald-600 dark:text-emerald-400 leading-none">{{ $persentaseKehadiran }}%</p>
                            </div>
                            <i class="fas fa-circle-check text-2xl text-emerald-500"></i>
                        </div>
                    </div>

                    {{-- STAT CARDS — Colored Pills --}}
                    <div class="grid grid-cols-4 gap-2.5">
                        <div class="stat-pill hadir shadow-sm">
                            <div class="w-8 h-8 bg-emerald-600 text-white rounded-xl flex items-center justify-center mx-auto mb-1.5 shadow-md">
                                <i class="fas fa-user-check text-xs"></i>
                            </div>
                            <p class="text-xl font-heading font-extrabold text-emerald-800 dark:text-emerald-300 leading-none">{{ $stats['hadir'] }}</p>
                            <p class="text-[10px] text-emerald-700 dark:text-emerald-300 font-bold mt-1">Hadir</p>
                        </div>
                        <div class="stat-pill izin shadow-sm">
                            <div class="w-8 h-8 bg-amber-500 text-white rounded-xl flex items-center justify-center mx-auto mb-1.5 shadow-md">
                                <i class="fas fa-envelope-open-text text-xs"></i>
                            </div>
                            <p class="text-xl font-heading font-extrabold text-amber-800 dark:text-amber-300 leading-none">{{ $stats['izin'] }}</p>
                            <p class="text-[10px] text-amber-700 dark:text-amber-300 font-bold mt-1">Izin</p>
                        </div>
                        <div class="stat-pill sakit shadow-sm">
                            <div class="w-8 h-8 bg-sky-500 text-white rounded-xl flex items-center justify-center mx-auto mb-1.5 shadow-md">
                                <i class="fas fa-hospital-user text-xs"></i>
                            </div>
                            <p class="text-xl font-heading font-extrabold text-sky-800 dark:text-sky-300 leading-none">{{ $stats['sakit'] }}</p>
                            <p class="text-[10px] text-sky-700 dark:text-sky-300 font-bold mt-1">Sakit</p>
                        </div>
                        <div class="stat-pill alpa shadow-sm">
                            <div class="w-8 h-8 bg-rose-500 text-white rounded-xl flex items-center justify-center mx-auto mb-1.5 shadow-md">
                                <i class="fas fa-user-xmark text-xs"></i>
                            </div>
                            <p class="text-xl font-heading font-extrabold text-rose-800 dark:text-rose-300 leading-none">{{ $stats['alpa'] }}</p>
                            <p class="text-[10px] text-rose-700 dark:text-rose-300 font-bold mt-1">Alpa</p>
                        </div>
                    </div>

                    {{-- Search & Filter Controls --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" x-model="riwayatSearch"
                                   placeholder="Cari mata pelajaran, guru, atau tanggal..."
                                   class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        </div>

                        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 -mx-1 px-1">
                            <button type="button" @click="riwayatFilter = 'semua'"
                                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shrink-0"
                                :class="riwayatFilter === 'semua' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700'">
                                Semua (<span x-text="riwayatSemuaList.length"></span>)
                            </button>
                            <button type="button" @click="riwayatFilter = 'hadir'"
                                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shrink-0"
                                :class="riwayatFilter === 'hadir' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300'">
                                Hadir
                            </button>
                            <button type="button" @click="riwayatFilter = 'izin'"
                                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shrink-0"
                                :class="riwayatFilter === 'izin' ? 'bg-amber-600 text-white' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300'">
                                Izin
                            </button>
                            <button type="button" @click="riwayatFilter = 'sakit'"
                                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shrink-0"
                                :class="riwayatFilter === 'sakit' ? 'bg-sky-600 text-white' : 'bg-sky-50 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300'">
                                Sakit
                            </button>
                            <button type="button" @click="riwayatFilter = 'alpa'"
                                class="px-3 py-1.5 rounded-xl text-xs font-extrabold transition shrink-0"
                                :class="riwayatFilter === 'alpa' ? 'bg-rose-600 text-white' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300'">
                                Alpa
                            </button>
                        </div>
                    </div>

                    {{-- Riwayat Presensi List --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <h3 class="font-heading font-extrabold text-slate-800 dark:text-white text-sm flex items-center gap-2">
                                <i class="fas fa-list-check text-blue-600 dark:text-blue-400"></i>
                                <span>Daftar Riwayat Presensi</span>
                            </h3>
                            <span class="text-[10px] font-mono text-slate-400" x-text="filteredRiwayat().length + ' catatan'"></span>
                        </div>

                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            <template x-for="(item, idx) in filteredRiwayat()" :key="idx">
                                <div class="px-5 py-4 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3 min-w-0">
                                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 mt-0.5"
                                             :class="{
                                                 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400': item.status === 'hadir',
                                                 'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400': item.status === 'izin',
                                                 'bg-sky-100 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400': item.status === 'sakit',
                                                 'bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400': item.status === 'alpa',
                                             }">
                                            <i class="fas text-sm"
                                               :class="{
                                                   'fa-check': item.status === 'hadir',
                                                   'fa-envelope': item.status === 'izin',
                                                   'fa-hospital': item.status === 'sakit',
                                                   'fa-times': item.status === 'alpa',
                                               }"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="font-heading font-bold text-xs text-slate-900 dark:text-white truncate" x-text="item.mapel"></h4>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1">
                                                <i class="fas fa-chalkboard-user text-[10px] opacity-70"></i>
                                                <span x-text="item.guru"></span>
                                            </p>
                                            <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400 font-mono">
                                                <span x-text="item.tanggal"></span>
                                                <template x-if="item.waktu && item.waktu !== '-'">
                                                    <span>&bull; <span x-text="item.waktu"></span> WIB</span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <span class="badge text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full inline-block"
                                              :class="{
                                                  'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800': item.status === 'hadir',
                                                  'bg-amber-100 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-300 dark:border-amber-800': item.status === 'izin',
                                                  'bg-sky-100 text-sky-700 dark:bg-sky-950/80 dark:text-sky-300 border border-sky-300 dark:border-sky-800': item.status === 'sakit',
                                                  'bg-rose-100 text-rose-700 dark:bg-rose-950/80 dark:text-rose-300 border border-rose-300 dark:border-rose-800': item.status === 'alpa',
                                              }"
                                              x-text="item.status.toUpperCase()"></span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="filteredRiwayat().length === 0">
                                <div class="py-12 text-center">
                                    <div class="w-14 h-14 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-3 text-slate-400">
                                        <i class="fas fa-calendar-xmark text-2xl"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Tidak ada riwayat presensi yang cocok</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci pencarian atau filter status.</p>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════ --}}
                {{-- TAB 3: TEMAN SEKELAS (DIREKTORI SOSIAL)    --}}
                {{-- ══════════════════════════════════════════ --}}
