                <div x-show="activeTab === 'teman'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
                    {{-- Header Teman --}}
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <span class="text-[10px] font-mono font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Komunitas Kelas</span>
                            <h2 class="font-heading font-black text-xl text-slate-900 dark:text-white">Teman Sekelas</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Daftar siswa resmi di kelas {{ $user->kelas->nama_kelas ?? 'Anda' }}</p>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-950/50 border border-blue-200/80 dark:border-blue-800 rounded-xl px-4 py-2 text-center">
                            <p class="text-[10px] font-bold text-blue-700 dark:text-blue-300 uppercase">Total Siswa</p>
                            <p class="text-2xl font-black font-heading text-blue-600 dark:text-blue-400 leading-none">{{ $temanSekelas->count() }}</p>
                        </div>
                    </div>

                    {{-- Search & Classmate Filters --}}
                    <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-3">
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <template x-for="item in filteredClassmates()" :key="item.id">
                            <div @click="openClassmateModal(item)"
                                 class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-3.5 shadow-xs hover:shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-all hover:scale-[1.01] cursor-pointer group flex items-center justify-between gap-3">
                                
                                {{-- Kiri: Avatar (Foto / Inisial Pastel) --}}
                                <div class="relative shrink-0">
                                    <div class="w-12 h-12 rounded-full overflow-hidden border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-center font-heading font-extrabold text-sm shadow-2xs"
                                         :class="item.avatar_url || item.avatar ? 'bg-slate-100 dark:bg-slate-800' : getAvatarColor(item.id)">
                                        <template x-if="item.avatar_url || item.avatar">
                                            <img :src="item.avatar_url || '/storage/' + item.avatar" class="w-full h-full object-cover" :alt="item.name">
                                        </template>
                                        <template x-if="!item.avatar_url && !item.avatar">
                                            <span x-text="item.name ? item.name.substring(0, 2).toUpperCase() : '?'"></span>
                                        </template>
                                    </div>
                                    {{-- Micro status dot on avatar --}}
                                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-slate-900"
                                          :class="item.status_hari_ini === 'hadir' ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-600'"></span>
                                </div>

                                {{-- Tengah: Nama Siswa & @username --}}
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-heading font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition"
                                        :title="formatTitleCase(item.name)"
                                        x-text="formatTitleCase(item.name)"></h4>
                                    <p class="text-[11px] font-sans text-slate-400 dark:text-slate-500 truncate mt-0.5"
                                       x-text="'@' + (item.username || 'siswa')"></p>
                                    <template x-if="item.bio">
                                        <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate mt-1 italic"
                                           x-text="'“' + item.bio + '”'"></p>
                                    </template>
                                </div>

                                {{-- Kanan Atas: Badge Status ("Hadir" / "Belum") --}}
                                <div class="shrink-0 self-start pt-0.5">
                                    <span class="text-[10px] font-sans px-2.5 py-1 rounded-full font-bold inline-flex items-center gap-1.5 shadow-2xs"
                                          :class="item.status_hari_ini === 'hadir' ? 'bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200/60 dark:border-slate-700'">
                                        <span class="w-1.5 h-1.5 rounded-full"
                                              :class="item.status_hari_ini === 'hadir' ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                        <span x-text="item.status_hari_ini === 'hadir' ? 'Hadir' : 'Belum'"></span>
                                    </span>
                                </div>
                            </div>
                        </template>

                        {{-- Empty State jika tidak ada hasil pencarian / filter --}}
                        <div x-show="filteredClassmates().length === 0" class="col-span-full py-12 text-center bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-6">
                            <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mx-auto mb-2 text-base">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <p class="font-heading font-extrabold text-sm text-slate-700 dark:text-slate-300">Tidak ada teman sekelas yang cocok</p>
                            <p class="text-xs text-slate-400 mt-0.5">Coba gunakan kata kunci pencarian atau filter yang berbeda.</p>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════ --}}
                {{-- TAB 4: AKUN & PROFIL                       --}}
                {{-- ══════════════════════════════════════════ --}}
