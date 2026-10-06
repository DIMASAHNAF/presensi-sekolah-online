                <div x-show="activeTab === 'beranda'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
                    {{-- KARTU PROFIL SISWA — Cover & Avatar Style --}}
                    <div class="gsap-stagger-item bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                        {{-- 1. Cover Banner Image --}}
                        <div class="relative h-28 sm:h-36 w-full bg-slate-800 overflow-hidden group">
                            <template x-if="profileBannerUrl">
                                <img :src="profileBannerUrl" 
                                     alt="Cover Profil" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            </template>
                            <template x-if="!profileBannerUrl">
                                <div class="w-full h-full bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 flex items-center justify-center relative overflow-hidden">
                                    <div class="absolute inset-0 bg-[radial-gradient(#ffffff15_1px,transparent_1px)] [background-size:16px_16px] opacity-40"></div>
                                </div>
                            </template>
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-black/20 to-transparent"></div>

                            {{-- Status Bar on Banner --}}
                            <div class="absolute top-3 left-3 flex items-center gap-1.5 bg-black/60 backdrop-blur-md px-2.5 py-1 rounded-full text-[10px] sm:text-xs text-white border border-white/20 shadow-sm">
                                @if($user->isFaceEnrolled())
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 pulse-dot"></span>
                                    <span class="font-semibold text-white">Face ID Aktif</span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                    <span class="font-semibold text-amber-300">Belum Rekam Wajah</span>
                                @endif
                                <span class="text-white/40">&bull;</span>
                                <span id="siswa-clock" class="font-mono">--:--</span>
                            </div>

                            {{-- Tombol Edit Profile di Pojok Kanan Banner --}}
                            <button type="button" @click="openEditProfileModal()" 
                                    class="absolute top-3 right-3 bg-black/60 hover:bg-black/85 backdrop-blur-md text-white text-xs font-bold px-3 py-1.5 rounded-xl border border-white/25 transition-all shadow-md flex items-center gap-1.5 hover:scale-105 active:scale-95">
                                <i class="fas fa-pen-to-square text-xs"></i>
                                <span class="hidden sm:inline">Edit Profil</span>
                            </button>
                        </div>

                        {{-- 2. Floating Avatar & Profile Details --}}
                        <div class="px-4 sm:px-5 pb-5 pt-0 relative">
                            <div class="flex items-end justify-between -mt-10 mb-3 flex-wrap gap-2">
                                {{-- Avatar --}}
                                <div class="relative">
                                    <div class="w-20 h-20 sm:w-22 sm:h-22 rounded-full border-4 border-white dark:border-slate-900 overflow-hidden shadow-xl bg-blue-700 text-white shrink-0">
                                        <template x-if="profileAvatarUrl">
                                            <img :src="profileAvatarUrl" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!profileAvatarUrl">
                                            <div class="w-full h-full flex items-center justify-center font-heading font-extrabold text-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
                                                {{ strtoupper($initials) }}
                                            </div>
                                        </template>
                                    </div>
                                    @if($user->isFaceEnrolled())
                                        <span class="absolute bottom-0 right-0 w-6 h-6 bg-emerald-500 rounded-full border-2 border-white dark:border-slate-900 flex items-center justify-center shadow-sm" title="Biometrik Wajah Terdaftar">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    @else
                                        <span class="absolute bottom-0 right-0 w-6 h-6 bg-amber-500 rounded-full border-2 border-white dark:border-slate-900 flex items-center justify-center shadow-sm" title="Wajah Belum Terdaftar / Direset">
                                            <i class="fas fa-triangle-exclamation text-[10px] text-white"></i>
                                        </span>
                                    @endif
                                </div>

                                {{-- Persentase Kehadiran Badge (Klik untuk beralih ke Tab Kehadiran) --}}
                                <button type="button" @click="switchTab('kehadiran')" 
                                        class="text-right bg-slate-50 dark:bg-slate-800/80 hover:bg-blue-50 dark:hover:bg-blue-950/40 border border-slate-200 dark:border-slate-700 rounded-2xl px-3 py-2 transition-all shadow-xs group"
                                        title="Buka Rekap Kehadiran">
                                    <div class="flex items-center gap-1.5 justify-end">
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">Kehadiran</span>
                                        <i class="fas fa-chart-pie text-[11px] text-emerald-500"></i>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <p class="text-xl font-black font-heading text-emerald-600 dark:text-emerald-400 leading-tight">
                                            {{ $persentaseKehadiran }}%
                                        </p>
                                        <i class="fas fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                                    </div>
                                </button>
                            </div>

                            {{-- Student Name & Class Info --}}
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h1 class="font-heading font-black text-xl text-slate-900 dark:text-white tracking-tight leading-snug">
                                        {{ $user->name }}
                                    </h1>
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-500 text-white text-[10px]" title="Siswa Terverifikasi">
                                        <i class="fas fa-check text-[10px]"></i>
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 mt-1 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                                    <span class="font-bold text-slate-700 dark:text-slate-300 font-mono" x-text="'@' + profileUsername">
                                        {{ '@' . ($user->username ?? 'siswa') }}
                                    </span>
                                    <span>&bull;</span>
                                    <span>SMKN 1 Beringin</span>
                                </div>

                                {{-- Bio Siswa --}}
                                <div class="mt-3">
                                    <template x-if="profileBio">
                                        <p class="text-xs text-slate-600 dark:text-slate-300 italic leading-relaxed bg-slate-50 dark:bg-slate-800/40 p-2.5 rounded-xl border border-slate-100 dark:border-slate-800"
                                           x-text="'“' + profileBio + '”'"></p>
                                    </template>
                                </div>

                                {{-- Website / Social Media Link --}}
                                <template x-if="profileWebsite">
                                    <div class="mt-2.5">
                                        <a :href="profileWebsite.startsWith('http') ? profileWebsite : 'https://' + profileWebsite"
                                           target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                                            <i class="fas fa-link text-[10px]"></i>
                                            <span x-text="profileWebsite.replace(/^https?:\/\//, '')"></span>
                                        </a>
                                    </div>
                                </template>

                                {{-- Kelas & NISN Pills --}}
                                <div class="grid grid-cols-2 gap-2.5 mt-3.5">
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-2.5 border border-slate-100 dark:border-slate-800 flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                            <i class="fas fa-school text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400 dark:text-slate-500">Kelas</p>
                                            <p class="text-xs font-black text-slate-800 dark:text-slate-100 truncate font-heading">{{ $user->kelas->nama_kelas ?? 'X TJKT' }}</p>
                                        </div>
                                    </div>
                                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-xl p-2.5 border border-slate-100 dark:border-slate-800 flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                                            <i class="fas fa-id-card text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-[9px] uppercase tracking-wider font-bold text-slate-400 dark:text-slate-500">NISN</p>
                                            <p class="text-xs font-black text-slate-800 dark:text-slate-100 truncate font-mono">{{ $user->nisn ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Bottom Action/Notice Bar on Profile Card --}}
                        @if(!$user->isFaceEnrolled())
                            <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-orange-600 px-4 sm:px-5 py-2.5 sm:py-3 flex items-center justify-between gap-3 text-white border-t border-amber-400/30">
                                <div class="flex items-center gap-2.5 text-xs font-bold min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-white/20 backdrop-blur-xs flex items-center justify-center shrink-0">
                                        <i class="fas fa-triangle-exclamation text-sm text-white"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="leading-tight font-extrabold text-white truncate sm:overflow-visible">Wajah Belum Terdaftar / Direset</p>
                                        <p class="text-[10px] text-amber-100 font-medium hidden sm:block">Wajib rekam biometrik wajah untuk verifikasi presensi</p>
                                    </div>
                                </div>
                                <a href="{{ route('siswa.enroll') }}" class="shrink-0 bg-white hover:bg-amber-50 text-amber-800 hover:text-amber-900 text-xs font-black px-3.5 py-1.5 rounded-xl transition-all shadow-sm hover:shadow active:scale-95 flex items-center gap-1.5">
                                    <i class="fas fa-camera text-xs text-amber-600"></i>
                                    <span>Rekam Wajah</span>
                                </a>
                            </div>
                        @elseif(!$user->bio)
                            <div class="bg-blue-50/70 dark:bg-blue-950/40 border-t border-blue-100 dark:border-blue-900/50 px-4 sm:px-5 py-2.5 flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs text-blue-800 dark:text-blue-200">
                                    <i class="fas fa-sparkles text-blue-500"></i>
                                    <span>Lengkapi bio profil & link sosmed kamu!</span>
                                </div>
                                <button type="button" @click="openEditProfileModal()" class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-bold">
                                    Atur &rarr;
                                </button>
                            </div>
                        @else
                            <div class="bg-blue-700 dark:bg-blue-950/80 px-4 sm:px-5 py-2 flex items-center gap-2 text-white text-xs font-semibold border-t border-blue-600/40">
                                <i class="fas fa-shield-check text-blue-200 text-xs"></i>
                                <span class="text-[11px] text-blue-100">Sistem biometrik Face ID aktif & terverifikasi</span>
                            </div>
                        @endif
                    </div>

                    {{-- SESI PRESENSI AKTIF CARD (PRIORITAS SCAN UTAMA) --}}
                    <div>
                        {{-- Loading State --}}
                        <div x-show="sesiLoading"
                            class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-8 flex flex-col items-center justify-center gap-3">
                            <div class="w-12 h-12 rounded-full border-4 border-blue-100 dark:border-slate-800 border-t-blue-600 animate-spin"></div>
                            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Menghubungkan ke server presensi...</span>
                        </div>

                        {{-- 1. Ada sesi aktif, SUDAH HADIR --}}
                        <div x-show="!sesiLoading && sesiData && sudahHadir" x-cloak
                            class="gsap-stagger-item bg-blue-600 rounded-[24px] shadow-[0_0_40px_-10px_rgba(59,130,246,0.6)] border-2 border-blue-400/50 overflow-hidden text-white relative group">
                            <div class="px-5 py-4 flex items-start justify-between">
                                <div>
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/20 text-white text-[10px] font-extrabold font-mono uppercase tracking-wider mb-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 pulse-dot"></div>
                                        <span>SESI SEDANG BERLANGSUNG</span>
                                    </div>
                                    <h2 x-text="sesiData?.kelas" class="font-heading font-black text-xl text-white tracking-tight"></h2>
                                    <p x-text="'Guru: ' + sesiData?.guru" class="text-blue-100 text-xs mt-0.5 font-medium"></p>
                                </div>
                                <div class="bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 rounded-xl px-3.5 py-2.5 text-center shadow-sm shrink-0 border border-white/60 dark:border-slate-700">
                                    <i class="fas fa-circle-check text-xl text-emerald-600 dark:text-emerald-400"></i>
                                    <p class="text-[10px] font-extrabold mt-0.5 font-mono text-emerald-600 dark:text-emerald-400">HADIR</p>
                                </div>
                            </div>
                            <div class="bg-emerald-50 dark:bg-emerald-950/70 border-t border-emerald-100 dark:border-emerald-900/60 px-5 py-3 flex items-center gap-2.5 text-emerald-900 dark:text-emerald-200">
                                <i class="fas fa-shield-halved text-emerald-600 dark:text-emerald-400 text-base shrink-0"></i>
                                <span class="text-xs font-semibold">Kehadiran Anda telah terverifikasi biometrik. Selamat belajar!</span>
                            </div>
                        </div>

                        {{-- 2. Ada sesi aktif, BELUM HADIR --}}
                        <div x-show="!sesiLoading && sesiData && !sudahHadir" x-cloak
                            class="gsap-stagger-item bg-blue-600 rounded-[24px] shadow-[0_0_40px_-10px_rgba(59,130,246,0.6)] border-2 border-blue-400/50 overflow-hidden text-white relative group">
                            <div class="px-5 py-4 flex items-start justify-between">
                                <div>
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/20 text-white text-[10px] font-extrabold font-mono uppercase tracking-wider mb-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 pulse-dot"></div>
                                        <span>SESI PRESENSI DIBUKA</span>
                                    </div>
                                    <h2 x-text="sesiData?.kelas" class="font-heading font-black text-xl text-white tracking-tight"></h2>
                                    <p x-text="sesiData?.tanggal" class="text-blue-100 text-xs mt-0.5 font-mono font-medium"></p>
                                    <p x-text="'Pengampu: ' + sesiData?.guru" class="text-blue-200 text-xs mt-0.5 font-medium"></p>
                                </div>
                                <div class="bg-white dark:bg-slate-900 text-amber-600 dark:text-amber-400 rounded-xl px-3.5 py-2.5 text-center shadow-sm shrink-0 border border-white/60 dark:border-slate-700">
                                    <i class="far fa-clock text-xl text-amber-500 dark:text-amber-400"></i>
                                    <p class="text-[10px] font-extrabold mt-0.5 font-mono text-amber-600 dark:text-amber-400">BELUM</p>
                                </div>
                            </div>

                            <div class="bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 p-5 border-t border-blue-500/20 dark:border-slate-800">
                                {{-- WiFi Whitelist Radar --}}
                                <div x-show="ipWhitelistActive" class="mb-4 pb-3.5 border-b border-slate-100 dark:border-slate-800">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs text-slate-700 dark:text-slate-200 font-bold flex items-center gap-1.5">
                                            <i class="fas fa-wifi text-blue-600 dark:text-blue-400"></i> WiFi Sekolah:
                                        </span>
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded font-bold"
                                              :class="isIpAllowed ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800'">
                                            IP: <span x-text="clientIp"></span>
                                        </span>
                                    </div>
                                    <div x-show="isIpAllowed"
                                        class="bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-300 dark:border-emerald-800/80 rounded-xl px-3.5 py-2.5 text-xs text-emerald-800 dark:text-emerald-200 flex items-center gap-2">
                                        <i class="fas fa-circle-check text-emerald-600 dark:text-emerald-400 text-base"></i>
                                        <span>Terhubung ke WiFi Resmi SMKN 1 Beringin.</span>
                                    </div>
                                    <div x-show="!isIpAllowed"
                                        class="bg-rose-50 dark:bg-rose-950/50 border border-rose-300 dark:border-rose-800/80 rounded-xl px-3.5 py-2.5 text-xs text-rose-800 dark:text-rose-200 space-y-1">
                                        <div class="flex items-center gap-2 font-bold text-rose-900 dark:text-rose-100">
                                            <i class="fas fa-triangle-exclamation text-rose-500"></i>
                                            <span>Bukan Jaringan WiFi Sekolah</span>
                                        </div>
                                        <p class="text-[11px] text-rose-700 dark:text-rose-300 pl-6">Silakan sambungkan perangkat ke WiFi SMKN 1 Beringin untuk scan presensi.</p>
                                    </div>
                                </div>

                                {{-- Tombol Mulai Scan Wajah --}}
                                @if(!$user->isFaceEnrolled())
                                    <div class="space-y-3">
                                        <div class="bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800/80 rounded-xl p-3.5 text-xs text-amber-900 dark:text-amber-200 flex items-start gap-2.5">
                                            <i class="fas fa-triangle-exclamation text-amber-500 text-base mt-0.5 shrink-0"></i>
                                            <div>
                                                <p class="font-extrabold text-amber-900 dark:text-amber-100">Wajib Rekam Wajah Terlebih Dahulu</p>
                                                <p class="text-[11px] text-amber-800 dark:text-amber-300 mt-0.5 leading-relaxed">
                                                    Data biometrik wajah Anda belum terdaftar atau baru saja direset oleh pihak sekolah. Anda wajib merekam foto wajah biometrik terlebih dahulu sebelum dapat presensi.
                                                </p>
                                            </div>
                                        </div>

                                        <a href="{{ route('siswa.enroll') }}"
                                            class="magic-btn w-full font-extrabold py-3.5 rounded-xl flex items-center justify-center gap-2.5 text-sm bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white shadow-[0_0_20px_rgba(245,158,11,0.5)] hover:shadow-[0_0_30px_rgba(245,158,11,0.7)] border border-amber-400 transition-all active:scale-[0.99]">
                                            <i class="fas fa-camera text-base"></i>
                                            <span>Rekam Wajah Sekarang &rarr;</span>
                                        </a>
                                    </div>
                                @else
                                    <button @click="openFaceScanner()"
                                        :disabled="(geofencingActive && geoStatus === 'outside') || (ipWhitelistActive && !isIpAllowed)"
                                        :class="(geofencingActive && geoStatus === 'outside') || (ipWhitelistActive && !isIpAllowed) ? 'opacity-70 cursor-not-allowed bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-transparent dark:border-slate-700' : 'magic-btn bg-blue-600 hover:bg-blue-700 text-white shadow-[0_0_25px_rgba(37,99,235,0.6)] border border-blue-400 hover:shadow-[0_0_35px_rgba(37,99,235,0.8)] active:scale-[0.99]'"
                                        class="w-full font-extrabold py-3.5 rounded-xl flex items-center justify-center gap-2.5 text-sm transition-all">
                                        <i class="fas text-base" :class="(geofencingActive && geoStatus === 'outside') || (ipWhitelistActive && !isIpAllowed) ? 'fa-lock' : 'fa-camera'"></i>
                                        <span x-text="ipWhitelistActive && !isIpAllowed ? 'Terkunci: Harus Pakai WiFi Sekolah' : (geofencingActive && geoStatus === 'outside' ? 'Terkunci: Di Luar Sekolah' : 'Mulai Verifikasi Wajah')"></span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- 3. Tidak ada sesi aktif --}}
                        <div x-show="!sesiLoading && !sesiData" x-cloak
                            class="gsap-stagger-item bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                            <div class="bg-slate-50 dark:bg-slate-800/60 px-6 py-8 text-center border-b border-slate-100 dark:border-slate-800">
                                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-950/60 text-blue-500 dark:text-blue-400 border border-blue-200/60 dark:border-blue-800/60 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-hourglass-half text-2xl"></i>
                                </div>
                                <h2 class="text-base font-heading font-extrabold text-slate-800 dark:text-white mb-1">Belum Ada Sesi Aktif</h2>
                                <p class="text-xs text-slate-500 dark:text-slate-300 leading-relaxed max-w-xs mx-auto">Menunggu guru pengampu membuka sesi presensi kelas hari ini.</p>
                            </div>
                            <div class="border-t border-slate-100 dark:border-slate-800 px-6 py-3 flex items-center justify-center gap-2">
                                <i class="fas fa-arrows-rotate text-blue-400 text-[10px] fa-spin"></i>
                                <span class="text-[11px] text-slate-400 dark:text-slate-400 font-mono">Auto-sinkron setiap 5 detik</span>
                            </div>
                        </div>
                    </div>

                    {{-- TEMAN SEKELAS HARI INI (HORIZONTAL STORY ROW) --}}
                    @if($temanSekelas->isNotEmpty())
                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-200 dark:border-slate-800 shadow-sm" data-aos="fade-up">
                        <div class="flex items-center justify-between mb-3 px-1">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs">
                                    <i class="fas fa-users text-xs"></i>
                                </div>
                                <h3 class="font-heading font-extrabold text-xs sm:text-sm text-slate-800 dark:text-white">
                                    Teman Sekelas <span class="text-slate-400 dark:text-slate-500 font-mono font-normal">({{ $temanSekelas->count() }})</span>
                                </h3>
                            </div>
                            <button type="button" @click="switchTab('teman')" class="text-[11px] text-blue-600 dark:text-blue-400 hover:underline font-bold">
                                Lihat Semua &rarr;
                            </button>
                        </div>

                        <div class="flex items-center gap-3.5 overflow-x-auto pb-2 pt-1 -mx-1 px-1">
                            <div @click="openEditProfileModal()" class="flex flex-col items-center gap-1.5 shrink-0 cursor-pointer group snap-start">
                                <div class="w-14 h-14 rounded-full border-2 border-dashed border-blue-400 dark:border-blue-500 flex items-center justify-center text-blue-600 dark:text-blue-400 hover:scale-105 transition-transform bg-blue-50/50 dark:bg-blue-950/40">
                                    <i class="fas fa-plus text-sm"></i>
                                </div>
                                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 truncate max-w-[64px]">Profilku</span>
                            </div>

                            @foreach($temanSekelas as $teman)
                            <div @click="openClassmateModal(@js($teman))"
                                 class="flex flex-col items-center gap-1.5 shrink-0 cursor-pointer group snap-start transition-transform hover:scale-105">
                                <div class="relative w-14 h-14 rounded-full p-0.5 border-2 {{ $teman->status_hari_ini === 'hadir' ? 'border-emerald-500 dark:border-emerald-400 ring-2 ring-emerald-500/20' : 'border-slate-200 dark:border-slate-700' }}">
                                    @if($teman->avatar_url)
                                        <img src="{{ $teman->avatar_url }}" alt="{{ $teman->name }}" class="w-full h-full rounded-full object-cover">
                                    @else
                                        <div class="w-full h-full rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-700 dark:text-slate-200 font-heading font-bold text-xs">
                                            {{ strtoupper(mb_substr($teman->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <span class="absolute bottom-0 right-0 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-slate-900 {{ $teman->status_hari_ini === 'hadir' ? 'bg-emerald-500' : ($teman->status_hari_ini === 'izin' ? 'bg-amber-500' : ($teman->status_hari_ini === 'sakit' ? 'bg-sky-500' : 'bg-slate-300 dark:bg-slate-600')) }}"></span>
                                </div>
                                <span class="text-[11px] font-medium text-slate-700 dark:text-slate-200 truncate max-w-[68px] text-center leading-tight">
                                    {{ explode(' ', trim($teman->name))[0] }}
                                </span>
                                <span class="text-[9px] font-mono px-1.5 py-0.2 rounded-full font-bold {{ $teman->status_hari_ini === 'hadir' ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60' : 'text-slate-400 dark:text-slate-400 bg-slate-100 dark:bg-slate-800' }}">
                                    {{ $teman->status_hari_ini === 'hadir' ? 'Hadir' : 'Belum' }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- PWA Install Banner --}}
                    <div x-show="showInstallPrompt" x-cloak
                        class="bg-gradient-to-r from-blue-50 to-emerald-50 dark:from-slate-800/90 dark:to-slate-900 rounded-2xl p-4 text-slate-800 dark:text-slate-100 shadow-sm flex items-center justify-between gap-3 border border-blue-200/80 dark:border-slate-700"
                        data-aos="fade-down">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center shrink-0 shadow-md shadow-blue-500/20">
                                <i class="fas fa-mobile-screen-button text-lg"></i>
                            </div>
                            <div>
                                <p class="font-extrabold text-xs text-slate-900 dark:text-white font-heading">Pasang Aplikasi Presensi (PWA)</p>
                                <p class="text-[11px] text-slate-600 dark:text-slate-300">Akses instan dari layar utama HP Anda</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button @click="installApp()"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl shadow-xs transition">
                                Pasang
                            </button>
                            <button @click="showInstallPrompt = false" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 text-xs p-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════ --}}
                {{-- TAB 2: KEHADIRAN & RIWAYAT                 --}}
                {{-- ══════════════════════════════════════════ --}}
