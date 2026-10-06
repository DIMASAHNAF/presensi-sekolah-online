                <div x-show="activeTab === 'profil'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
                    {{-- Header Profil --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <span class="text-[10px] font-mono font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Identitas & Akun</span>
                            <h2 class="font-heading font-black text-xl text-slate-900 dark:text-white">Pengaturan Akun</h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola foto profil, bio, dan status keamanan biometrik Anda</p>
                        </div>
                        <button type="button" @click="openEditProfileModal()"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs px-4 py-2.5 rounded-xl shadow-xs transition flex items-center gap-2">
                            <i class="fas fa-pen-to-square"></i>
                            <span>Edit Profil</span>
                        </button>
                    </div>

                    {{-- Detail Dapodik (Data Terkunci) --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs">
                                <i class="fas fa-shield-halved"></i>
                            </div>
                            <div>
                                <h3 class="font-heading font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white">Data Resmi Siswa (Dapodik)</h3>
                                <p class="text-[11px] text-slate-400">Data ini tersinkronisasi otomatis dengan server sekolah.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5 font-heading">{{ $user->name }}</p>
                                <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 mt-1"><i class="fas fa-lock text-[9px]"></i> Terkunci</span>
                            </div>

                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NISN</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5 font-mono">{{ $user->nisn ?? '-' }}</p>
                                <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 mt-1"><i class="fas fa-lock text-[9px]"></i> Terkunci</span>
                            </div>

                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas & Rombel</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5 font-heading">{{ $user->kelas->nama_kelas ?? 'Siswa' }}</p>
                                <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 mt-1"><i class="fas fa-lock text-[9px]"></i> Terkunci</span>
                            </div>

                            <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Username Akun</p>
                                <p class="text-sm font-black text-slate-900 dark:text-white mt-0.5 font-mono" x-text="'@' + profileUsername"></p>
                                <span class="inline-flex items-center gap-1 text-[10px] text-blue-600 dark:text-blue-400 mt-1"><i class="fas fa-pen text-[9px]"></i> Dapat diubah di Edit Profil</span>
                            </div>
                        </div>

                        <div class="p-3.5 bg-blue-50/60 dark:bg-blue-950/40 rounded-2xl border border-blue-100 dark:border-blue-900/50 text-xs text-blue-800 dark:text-blue-200 flex items-start gap-2.5">
                            <i class="fas fa-circle-info text-blue-500 mt-0.5 shrink-0"></i>
                            <span>Untuk perubahan nama resmi, NISN, atau kelas, silakan menghubungi operator kurikulum sekolah.</span>
                        </div>
                    </div>

                    {{-- Biometrik Wajah Card --}}
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                                    <i class="fas fa-face-viewfinder"></i>
                                </div>
                                <div>
                                    <h3 class="font-heading font-extrabold text-xs sm:text-sm text-slate-900 dark:text-white">Biometrik Face ID</h3>
                                    <p class="text-[11px] text-slate-400">Perekaman wajah untuk presensi real-time</p>
                                </div>
                            </div>
                            @if($user->isFaceEnrolled())
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold font-mono uppercase bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 pulse-dot"></span>
                                    Face ID Aktif
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold font-mono uppercase bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                    Belum Rekam Wajah
                                </span>
                            @endif
                        </div>

                        <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-center justify-between flex-wrap gap-3">
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Perekaman Ulang Wajah (Re-Enroll)</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Jika wajah Anda sering gagal terdeteksi atau berubah penampilan.</p>
                            </div>
                            <a href="{{ route('siswa.enroll') }}"
                               class="bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-700 text-xs font-bold px-3.5 py-2 rounded-xl transition shadow-2xs">
                                Rekam Ulang &rarr;
                            </a>
                        </div>
                    </div>
                </div>

            </main>

            {{-- ────────────────────────────────────────────── --}}
            {{-- 3. MOBILE FLOATING BOTTOM BAR (lg:hidden)       --}}
            {{-- ────────────────────────────────────────────── --}}
            <nav x-init="initPill()" class="lg:hidden fixed bottom-4 left-4 right-4 max-w-md mx-auto z-40 select-none">
                <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-2xl border border-white/50 dark:border-slate-700/50 rounded-[28px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] px-2 py-2 flex items-center justify-around relative overflow-hidden">
                    {{-- Inner Highlight for Glass --}}
                    <div class="absolute inset-0 bg-gradient-to-b from-white/40 to-transparent dark:from-white/5 rounded-[28px] pointer-events-none"></div>

                    {{-- Sliding Pill --}}
                    <div class="absolute bg-blue-600 shadow-[0_4px_12px_rgba(37,99,235,0.4)] rounded-[20px] transition-all duration-300 ease-out z-0"
                         :style="`left: ${pillLeft}px; width: ${pillWidth}px; top: ${pillTop}px; height: ${pillHeight}px;`"></div>

