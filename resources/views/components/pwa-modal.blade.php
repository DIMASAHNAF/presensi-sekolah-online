{{-- resources/views/components/pwa-modal.blade.php --}}
<div x-data="pwaInstallManager()"
     @open-pwa-install.window="openModal()"
     x-cloak>

    {{-- Backdrop & Modal --}}
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/60 backdrop-blur-xs">

        {{-- Modal Dialog --}}
        <div x-show="showModal"
             @click.away="closeModal()"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
             class="w-full max-w-lg bg-white dark:bg-zinc-900 rounded-t-xl sm:rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-800 overflow-hidden flex flex-col max-h-[90vh]">

            {{-- Header (Border-First, No Gradient) --}}
            <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between bg-zinc-50 dark:bg-zinc-900/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center p-1.5 shadow-xs">
                        <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm text-zinc-900 dark:text-zinc-100 tracking-tight">Pasang Aplikasi Presensi</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">Akses cepat melalui layar utama tanpa peramban</p>
                    </div>
                </div>
                <button type="button" @click="closeModal()"
                        class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 p-1 rounded-lg transition"
                        title="Tutup">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            {{-- Body Content --}}
            <div class="p-5 overflow-y-auto space-y-4">

                {{-- Direct 1-Click Install Button --}}
                <div x-show="canInstallDirectly && !isStandalone" class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 text-center space-y-2.5">
                    <p class="text-xs text-zinc-700 dark:text-zinc-300 font-medium">
                        Peramban Anda mendukung pemasangan langsung:
                    </p>
                    <button type="button" @click="triggerInstall()"
                            class="w-full py-2.5 px-4 rounded-lg bg-zinc-900 text-zinc-50 hover:bg-zinc-800 dark:bg-zinc-100 dark:text-zinc-900 dark:hover:bg-zinc-200 font-medium text-xs transition shadow-xs flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Pasang Aplikasi Sekarang</span>
                    </button>
                </div>

                {{-- Standalone Status Notification --}}
                <div x-show="isStandalone" class="p-3.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20 flex items-center gap-2.5 text-emerald-600 dark:text-emerald-400 text-xs">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <p class="font-medium">Aplikasi sudah terpasang dan berjalan dalam mode aplikasi penuh.</p>
                </div>

                {{-- Platform Instructions Selector --}}
                <div>
                    <div class="grid grid-cols-3 gap-1.5 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <button type="button" @click="activeDeviceTab = 'android'"
                                class="py-2 px-2 rounded-md text-xs font-medium transition"
                                :class="activeDeviceTab === 'android' ? 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900'">
                            Android
                        </button>
                        <button type="button" @click="activeDeviceTab = 'ios'"
                                class="py-2 px-2 rounded-md text-xs font-medium transition"
                                :class="activeDeviceTab === 'ios' ? 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900'">
                            iOS / iPhone
                        </button>
                        <button type="button" @click="activeDeviceTab = 'desktop'"
                                class="py-2 px-2 rounded-md text-xs font-medium transition"
                                :class="activeDeviceTab === 'desktop' ? 'bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-xs' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900'">
                            Laptop / PC
                        </button>
                    </div>
                </div>

                {{-- TAB: ANDROID --}}
                <div x-show="activeDeviceTab === 'android'" class="space-y-2.5 pt-1">
                    <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/40 rounded-lg border border-zinc-200 dark:border-zinc-800 space-y-2.5 text-xs">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-mono text-[11px] flex items-center justify-center shrink-0">1</span>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Buka di Google Chrome</p>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Buka alamat <span class="font-mono text-zinc-800 dark:text-zinc-200">{{ request()->getHost() }}</span> di Chrome Android.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-mono text-[11px] flex items-center justify-center shrink-0">2</span>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Buka Menu Peramban</p>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Ketuk ikon titik tiga di sudut kanan atas layar.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-mono text-[11px] flex items-center justify-center shrink-0">3</span>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Pilih Instal Aplikasi</p>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Pilih opsi "Instal aplikasi" atau "Tambahkan ke Layar Utama".</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: IPHONE / IPAD --}}
                <div x-show="activeDeviceTab === 'ios'" class="space-y-2.5 pt-1">
                    <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/40 rounded-lg border border-zinc-200 dark:border-zinc-800 space-y-2.5 text-xs">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-mono text-[11px] flex items-center justify-center shrink-0">1</span>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Gunakan Browser Safari</p>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Buka alamat website menggunakan aplikasi Safari bawaan iPhone.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-mono text-[11px] flex items-center justify-center shrink-0">2</span>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Ketuk Tombol Bagikan</p>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Ketuk ikon kotak dengan panah ke atas di bagian bawah Safari.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-mono text-[11px] flex items-center justify-center shrink-0">3</span>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Pilih Tambah ke Layar Utama</p>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Gulir ke bawah dan ketuk "Add to Home Screen" (Tambah ke Layar Utama).</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB: LAPTOP / PC --}}
                <div x-show="activeDeviceTab === 'desktop'" class="space-y-2.5 pt-1">
                    <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/40 rounded-lg border border-zinc-200 dark:border-zinc-800 space-y-2.5 text-xs">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-mono text-[11px] flex items-center justify-center shrink-0">1</span>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Gunakan Chrome atau Edge</p>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Akses melalui peramban Google Chrome atau Microsoft Edge.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 font-mono text-[11px] flex items-center justify-center shrink-0">2</span>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-zinc-100">Klik Ikon Instal di Address Bar</p>
                                <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5">Klik ikon komputer kecil di sebelah kanan kolom alamat URL.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bottom Actions --}}
                <div class="pt-2 flex items-center justify-between border-t border-zinc-200 dark:border-zinc-800 text-xs">
                    <a href="{{ route('panduan') }}" target="_blank"
                       class="font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 flex items-center gap-1.5 transition">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
                            <path d="M6 6h10"></path>
                            <path d="M6 10h10"></path>
                        </svg>
                        <span>Buku Panduan Penggunaan</span>
                    </a>
                    <button type="button" @click="closeModal()"
                            class="px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium hover:bg-zinc-50 dark:hover:bg-zinc-700 transition">
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
                this.isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

                const ua = navigator.userAgent || '';
                if (/iPad|iPhone|iPod/.test(ua) && !window.MSStream) {
                    this.activeDeviceTab = 'ios';
                } else if (/Android/.test(ua)) {
                    this.activeDeviceTab = 'android';
                } else {
                    this.activeDeviceTab = 'desktop';
                }

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
