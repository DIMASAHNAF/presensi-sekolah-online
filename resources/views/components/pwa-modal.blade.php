{{-- resources/views/components/pwa-modal.blade.php --}}
<div x-data="pwaInstallManager()"
     @open-pwa-install.window="openModal()"
     x-cloak>

    {{-- Backdrop & Modal --}}
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/70 backdrop-blur-sm">

        {{-- Modal Dialog --}}
        <div x-show="showModal"
             @click.away="closeModal()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-12 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-12 sm:scale-95"
             class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-3xl shadow-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden flex flex-col max-h-[90vh]">

            {{-- Header --}}
            <div class="relative bg-gradient-to-br from-blue-600 via-indigo-600 to-blue-700 text-white p-5 sm:p-6 shrink-0">
                <button type="button" @click="closeModal()"
                        class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition"
                        title="Tutup">
                    <i class="fas fa-times text-sm"></i>
                </button>

                <div class="flex items-center gap-3.5">
                    <div class="w-13 h-13 sm:w-14 sm:h-14 bg-white/10 rounded-2xl p-2 backdrop-blur-md border border-white/20 shadow-md shrink-0 flex items-center justify-center">
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="w-10 h-10 object-contain drop-shadow">
                    </div>
                    <div class="min-w-0 pr-8">
                        <div class="flex items-center gap-2">
                            <h3 class="font-heading font-black text-lg sm:text-xl text-white tracking-tight">Presensi SMKN 1 Beringin</h3>
                        </div>
                        <p class="text-xs text-blue-100 font-medium mt-0.5">Versi Aplikasi Mobile (PWA / Seperti APK)</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-400 text-slate-900">
                                <i class="fas fa-bolt text-[9px]"></i> Sangat Cepat &amp; Ringan
                            </span>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/20 text-white">
                                <i class="fas fa-mobile-screen text-[9px]"></i> Full Screen
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Body Content --}}
            <div class="p-5 sm:p-6 overflow-y-auto space-y-4">

                {{-- Direct 1-Click Install Button (Shown on Android / Chrome when deferredPrompt is ready) --}}
                <div x-show="canInstallDirectly && !isStandalone" class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800 text-center space-y-2.5">
                    <p class="text-xs font-bold text-blue-900 dark:text-blue-200">
                        Browser Anda mendukung pemasangan instan 1-klik:
                    </p>
                    <button type="button" @click="triggerInstall()"
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 active:scale-[0.98] text-white font-black text-sm shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2.5 transition">
                        <i class="fas fa-download text-base"></i>
                        <span>PASANG APLIKASI SEKARANG</span>
                    </button>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Aplikasi akan langsung terpasang di menu aplikasi &amp; layar utama HP Anda.
                    </p>
                </div>

                {{-- Standalone Status Notification --}}
                <div x-show="isStandalone" class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 flex items-center gap-3 text-emerald-800 dark:text-emerald-200 text-xs">
                    <i class="fas fa-circle-check text-xl text-emerald-500 shrink-0"></i>
                    <div>
                        <p class="font-bold">Aplikasi Sudah Terpasang!</p>
                        <p class="text-[11px] opacity-90 mt-0.5">Anda saat ini sedang membuka Presensi langsung dari aplikasi di layar utama.</p>
                    </div>
                </div>

                {{-- Platform Instructions Selector --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                        Pilih Jenis Perangkat Anda:
                    </p>
                    <div class="grid grid-cols-3 gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl border border-slate-200/60 dark:border-slate-700">
                        <button type="button" @click="activeDeviceTab = 'android'"
                                class="py-2.5 px-2 rounded-xl text-xs font-extrabold flex flex-col items-center gap-1 transition"
                                :class="activeDeviceTab === 'android' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                            <i class="fab fa-android text-base"></i>
                            <span>Android</span>
                        </button>
                        <button type="button" @click="activeDeviceTab = 'ios'"
                                class="py-2.5 px-2 rounded-xl text-xs font-extrabold flex flex-col items-center gap-1 transition"
                                :class="activeDeviceTab === 'ios' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                            <i class="fab fa-apple text-base"></i>
                            <span>iPhone / iPad</span>
                        </button>
                        <button type="button" @click="activeDeviceTab = 'desktop'"
                                class="py-2.5 px-2 rounded-xl text-xs font-extrabold flex flex-col items-center gap-1 transition"
                                :class="activeDeviceTab === 'desktop' ? 'bg-white dark:bg-slate-700 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900'">
                            <i class="fas fa-laptop text-base"></i>
                            <span>Laptop / PC</span>
                        </button>
                    </div>
                </div>

                {{-- TAB: ANDROID --}}
                <div x-show="activeDeviceTab === 'android'" class="space-y-3 pt-1">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-white">Buka di Browser Google Chrome</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Pastikan link <span class="font-mono text-blue-600">presensi.tiksmkn1beringin.my.id</span> dibuka di Google Chrome.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-white">Ketuk Menu Titik Tiga (⋮) di Kanan Atas</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Ketuk ikon titik tiga di sudut kanan atas layar Chrome.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">3</span>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-white">Pilih "Instal aplikasi" atau "Tambahkan ke Layar Utama"</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Ketuk pilihan tersebut lalu konfirmasi tombol <strong class="text-blue-600 dark:text-blue-400">"Instal"</strong>.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">✓</span>
                            <div>
                                <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Selesai!</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Ikon aplikasi Presensi akan muncul di menu HP Anda seperti aplikasi APK biasa.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: IPHONE / IPAD --}}
                <div x-show="activeDeviceTab === 'ios'" class="space-y-3 pt-1">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-white">Buka di Browser Safari Bawaan iPhone</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Pemasangan di iOS hanya bisa dilakukan melalui browser <strong>Safari</strong> (bukan Chrome iOS).</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-white">Ketuk Tombol Bagikan (Share)</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Ikon kotak dengan panah menghadap ke atas (<i class="fas fa-arrow-up-from-bracket text-blue-600"></i>) di bilah bawah Safari.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">3</span>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-white">Pilih "Tambah ke Layar Utama" (Add to Home Screen)</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Gulir ke bawah pada menu pop-up Safari dan pilih opsi berlambang tambah (<i class="far fa-square-plus text-blue-600"></i>).</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">✓</span>
                            <div>
                                <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Ketuk "Tambah" di Pojok Kanan Atas</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Ikon Presensi langsung muncul di Home Screen iPhone tanpa App Store!</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: LAPTOP / PC --}}
                <div x-show="activeDeviceTab === 'desktop'" class="space-y-3 pt-1">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-white">Buka di Chrome atau Microsoft Edge</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Gunakan browser modern berbasis Chromium di Windows atau Mac.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <div>
                                <p class="text-xs font-bold text-slate-800 dark:text-white">Klik Ikon Instal di Kolom URL</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Lihat di sebelah kanan kolom alamat web (URL bar), klik ikon komputer dengan tanda panah ke bawah (<i class="fas fa-circle-down text-blue-600"></i>).</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">✓</span>
                            <div>
                                <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Klik "Instal"</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Aplikasi akan terbuka di jendela tersendiri tanpa gangguan tab lain.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Quick Link to Guides --}}
                <div class="pt-2 flex items-center justify-between border-t border-slate-100 dark:border-slate-800 text-xs">
                    <a href="{{ route('panduan') }}" target="_blank"
                       class="font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1.5">
                        <i class="fas fa-book-open"></i>
                        <span>Lihat Buku Panduan Lengkap</span>
                    </a>
                    <button type="button" @click="closeModal()"
                            class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold transition">
                        Tutup
                    </button>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
    function pwaInstallManager() {
        return {
            showModal: false,
            canInstallDirectly: false,
            isStandalone: false,
            activeDeviceTab: 'android',

            init() {
                // Detect standalone mode
                this.isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

                // Detect device
                const ua = navigator.userAgent || '';
                if (/iPad|iPhone|iPod/.test(ua) && !window.MSStream) {
                    this.activeDeviceTab = 'ios';
                } else if (/Android/.test(ua)) {
                    this.activeDeviceTab = 'android';
                } else {
                    this.activeDeviceTab = 'desktop';
                }

                // Listen to deferredPrompt if captured globally
                if (window.deferredPrompt) {
                    this.canInstallDirectly = true;
                }

                window.addEventListener('beforeinstallprompt', (e) => {
                    e.preventDefault();
                    window.deferredPrompt = e;
                    this.canInstallDirectly = true;
                });

                window.addEventListener('appinstalled', () => {
                    this.canInstallDirectly = false;
                    this.isStandalone = true;
                    this.showModal = false;
                    window.deferredPrompt = null;
                });
            },

            openModal() {
                if (window.deferredPrompt) {
                    this.canInstallDirectly = true;
                }
                this.showModal = true;
            },

            closeModal() {
                this.showModal = false;
            },

            triggerInstall() {
                if (window.deferredPrompt) {
                    window.deferredPrompt.prompt();
                    window.deferredPrompt.userChoice.then((choiceResult) => {
                        if (choiceResult.outcome === 'accepted') {
                            this.showModal = false;
                            this.canInstallDirectly = false;
                        }
                        window.deferredPrompt = null;
                    });
                } else {
                    // Fallback to instruction tab
                    const ua = navigator.userAgent || '';
                    if (/iPad|iPhone|iPod/.test(ua) && !window.MSStream) {
                        this.activeDeviceTab = 'ios';
                    } else {
                        this.activeDeviceTab = 'android';
                    }
                }
            }
        };
    }

    // Global helper for opening PWA install
    window.openPwaInstallPrompt = function() {
        if (window.deferredPrompt) {
            window.deferredPrompt.prompt();
            window.deferredPrompt.userChoice.then((res) => {
                if (res.outcome === 'accepted') {
                    window.deferredPrompt = null;
                }
            }).catch(() => {
                window.dispatchEvent(new CustomEvent('open-pwa-install'));
            });
        } else {
            window.dispatchEvent(new CustomEvent('open-pwa-install'));
        }
    };
</script>
