                <div x-show="activeTab === 'teman'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
                    {{-- Header Teman --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <span class="text-[10px] font-mono font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Komunitas Kelas</span>
                            <h2 class="font-heading font-black text-xl text-slate-900 dark:text-white">Teman Sekelas</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar siswa resmi di kelas {{ $user->kelas->nama_kelas ?? 'Anda' }}</p>
                        </div>
                        <div class="bg-indigo-50 dark:bg-indigo-950/50 border border-indigo-200 dark:border-indigo-800 rounded-2xl px-4 py-2.5 text-center">
                            <p class="text-[10px] font-bold text-indigo-700 dark:text-indigo-300 uppercase">Total Siswa</p>
                            <p class="text-2xl font-black font-heading text-indigo-600 dark:text-indigo-400 leading-none">{{ $temanSekelas->count() }}</p>
                        </div>
                    </div>

                    {{-- Search & Classmate Filters --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" x-model="classmateSearch"
                                   placeholder="Cari nama, username @, atau bio teman..."
                                   class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="classmateFilter = 'semua'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition"
                                :class="classmateFilter === 'semua' ? 'bg-blue-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'">
                                Semua
                            </button>
                            <button type="button" @click="classmateFilter = 'hadir'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition"
                                :class="classmateFilter === 'hadir' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300'">
                                Hadir Hari Ini
                            </button>
                            <button type="button" @click="classmateFilter = 'belum'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition"
                                :class="classmateFilter === 'belum' ? 'bg-slate-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'">
                                Belum Hadir
                            </button>
                        </div>
                    </div>

                    {{-- Classmate Cards Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <template x-for="item in filteredClassmates()" :key="item.id">
                            <div @click="openClassmateModal(item)"
                                 class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-all hover:scale-[1.01] cursor-pointer group flex flex-col justify-between">
                                <div>
                                    {{-- Banner Mini --}}
                                    <div class="h-16 bg-slate-800 relative overflow-hidden">
                                        <template x-if="item.banner_url || item.banner">
                                            <img :src="item.banner_url || '/storage/' + item.banner" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!item.banner_url && !item.banner">
                                            <div class="w-full h-full bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 relative">
                                                <div class="absolute inset-0 bg-[radial-gradient(#ffffff15_1px,transparent_1px)] [background-size:12px_12px] opacity-40"></div>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Body Card --}}
                                    <div class="px-4 pb-3 pt-0 relative">
                                        <div class="flex items-end justify-between -mt-7 mb-2">
                                            {{-- Avatar --}}
                                            <div class="relative w-14 h-14 rounded-full border-3 border-white dark:border-slate-900 overflow-hidden shadow-md bg-blue-600 text-white shrink-0">
                                                <template x-if="item.avatar_url || item.avatar">
                                                    <img :src="item.avatar_url || '/storage/' + item.avatar" class="w-full h-full object-cover">
                                                </template>
                                                <template x-if="!item.avatar_url && !item.avatar">
                                                    <div class="w-full h-full flex items-center justify-center font-heading font-extrabold text-sm"
                                                         x-text="item.name.substring(0, 2).toUpperCase()"></div>
                                                </template>
                                            </div>

                                            {{-- Status Badge --}}
                                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full font-bold"
                                                  :class="item.status_hari_ini === 'hadir' ? 'bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'">
                                                <span x-text="item.status_hari_ini === 'hadir' ? 'Hadir' : 'Belum'"></span>
                                            </span>
                                        </div>

                                        <h4 class="font-heading font-extrabold text-sm text-slate-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition" x-text="item.name"></h4>
                                        <p class="text-[11px] font-mono text-slate-400" x-text="'@' + (item.username || 'siswa')"></p>

                                        <template x-if="item.bio">
                                            <p class="text-[11px] text-slate-600 dark:text-slate-300 italic mt-2 line-clamp-2" x-text="'“' + item.bio + '”'"></p>
                                        </template>
                                    </div>
                                </div>

                                <div class="px-4 py-2.5 bg-slate-50/70 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px] text-blue-600 dark:text-blue-400 font-bold">
                                    <span>Lihat Profil Lengkap</span>
                                    <i class="fas fa-arrow-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════ --}}
                {{-- TAB 4: AKUN & PROFIL                       --}}
                {{-- ══════════════════════════════════════════ --}}
