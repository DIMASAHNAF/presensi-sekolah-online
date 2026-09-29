{{--
    SHADCN STYLE CONFIRM ALERT DIALOG
    Komponen modal konfirmasi modern pengganti browser window.confirm()
    Didesain persis seperti AlertDialog shadcn/ui.

    Penggunaan:
    1. Otomatis mencegat form yang memiliki attribute data-confirm="Pesan konfirmasi":
       <form action="..." method="POST" data-confirm="Hapus akun siswa Budi secara permanen?" data-confirm-title="Hapus Akun Siswa?" data-confirm-type="danger">
           <button type="submit">Hapus</button>
       </form>

    2. Programmatic JS:
       window.shadcnConfirm({
           title: "Konfirmasi Tindakan",
           description: "Apakah Anda yakin ingin melanjutkan tindakan ini?",
           confirmText: "Ya, Lanjutkan",
           cancelText: "Batal",
           type: "danger" // "danger" | "warning" | "info"
       }).then(confirmed => {
           if (confirmed) { ... }
       });
--}}

<div x-data="shadcnConfirmModal()" 
     x-cloak
     @keydown.escape.window="handleCancel()">

    {{-- Backdrop Overlay --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9998] bg-black/75 backdrop-blur-xs"></div>

    {{-- Dialog Container --}}
    <div x-show="open"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 overflow-y-auto"
         @click.self="handleCancel()">

        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl max-w-md w-full p-6 relative overflow-hidden my-auto"
             role="alertdialog"
             aria-modal="true"
             :aria-labelledby="$id('dialog-title')"
             :aria-describedby="$id('dialog-desc')">

            {{-- Header with Icon --}}
            <div class="flex items-start gap-3.5">
                {{-- Dynamic Icon Badge --}}
                <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 border"
                     :class="{
                         'bg-rose-50 border-rose-200 text-rose-600 dark:bg-rose-950/50 dark:border-rose-800 dark:text-rose-400': type === 'danger',
                         'bg-amber-50 border-amber-200 text-amber-600 dark:bg-amber-950/50 dark:border-amber-800 dark:text-amber-400': type === 'warning',
                         'bg-blue-50 border-blue-200 text-blue-600 dark:bg-blue-950/50 dark:border-blue-800 dark:text-blue-400': type === 'info'
                     }">
                    <template x-if="type === 'danger'">
                        <i class="fas fa-trash-can text-sm"></i>
                    </template>
                    <template x-if="type === 'warning'">
                        <i class="fas fa-triangle-exclamation text-sm"></i>
                    </template>
                    <template x-if="type === 'info'">
                        <i class="fas fa-circle-question text-sm"></i>
                    </template>
                </div>

                {{-- Text Content --}}
                <div class="space-y-1.5 flex-1 min-w-0">
                    <h3 class="font-heading font-extrabold text-base text-slate-900 dark:text-white tracking-tight"
                        x-text="title"></h3>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed whitespace-pre-line"
                       x-text="description"></p>
                </div>
            </div>

            {{-- Footer Buttons (Shadcn style) --}}
            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5 sm:gap-2">
                <button type="button" 
                        @click="handleCancel()" 
                        class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-750 text-slate-700 dark:text-slate-200 text-xs sm:text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-slate-400/20"
                        x-text="cancelText">
                    Batal
                </button>
                <button type="button" 
                        @click="handleConfirm()" 
                        class="px-4 py-2.5 rounded-xl text-white text-xs sm:text-sm font-bold shadow-xs transition focus:outline-none focus:ring-2 focus:ring-offset-2"
                        :class="{
                            'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500': type === 'danger',
                            'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500': type === 'warning',
                            'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500': type === 'info'
                        }"
                        x-text="confirmText">
                    Konfirmasi
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function shadcnConfirmModal() {
        return {
            open: false,
            title: 'Konfirmasi Tindakan',
            description: '',
            confirmText: 'Konfirmasi',
            cancelText: 'Batal',
            type: 'danger',
            _resolve: null,

            init() {
                // Register Global Helper
                window.shadcnConfirm = (options = {}) => {
                    return new Promise((resolve) => {
                        this.title = options.title || 'Konfirmasi Tindakan';
                        this.description = options.description || (typeof options === 'string' ? options : 'Apakah Anda yakin ingin melanjutkan?');
                        this.confirmText = options.confirmText || 'Konfirmasi';
                        this.cancelText = options.cancelText || 'Batal';
                        this.type = options.type || 'danger';
                        this._resolve = resolve;
                        this.open = true;
                    });
                };

                // 1. Intercept forms with data-confirm automatically
                document.addEventListener('submit', (e) => {
                    const form = e.target;
                    if (!form || !form.hasAttribute('data-confirm')) return;

                    if (form._isConfirmed) {
                        form._isConfirmed = false;
                        return;
                    }

                    e.preventDefault();
                    e.stopImmediatePropagation();

                    const message = form.getAttribute('data-confirm');
                    const title = form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                    const type = form.getAttribute('data-confirm-type') || 'danger';
                    const btnText = form.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';

                    window.shadcnConfirm({
                        title: title,
                        description: message,
                        confirmText: btnText,
                        cancelText: 'Batal',
                        type: type
                    }).then(confirmed => {
                        if (confirmed) {
                            form._isConfirmed = true;
                            if (typeof form.requestSubmit === 'function') {
                                form.requestSubmit();
                            } else {
                                form.submit();
                            }
                        }
                    });
                }, true);

                // 2. Intercept any button or link with data-confirm
                document.addEventListener('click', (e) => {
                    const btn = e.target.closest('[data-confirm]');
                    // If button is inside form with data-confirm, form handler will handle it
                    if (!btn || btn.tagName === 'FORM') return;

                    if (btn._isConfirmed) {
                        btn._isConfirmed = false;
                        return;
                    }

                    e.preventDefault();
                    e.stopImmediatePropagation();

                    const message = btn.getAttribute('data-confirm');
                    const title = btn.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                    const type = btn.getAttribute('data-confirm-type') || 'danger';
                    const confirmBtnText = btn.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';

                    window.shadcnConfirm({
                        title: title,
                        description: message,
                        confirmText: confirmBtnText,
                        cancelText: 'Batal',
                        type: type
                    }).then(confirmed => {
                        if (confirmed) {
                            btn._isConfirmed = true;
                            if (btn.tagName === 'A' && btn.href) {
                                window.location.href = btn.href;
                            } else if (btn.type === 'submit' && btn.form) {
                                btn.form._isConfirmed = true;
                                if (typeof btn.form.requestSubmit === 'function') {
                                    btn.form.requestSubmit(btn);
                                } else {
                                    btn.form.submit();
                                }
                            } else {
                                btn.click();
                            }
                        }
                    });
                }, true);
            },

            handleConfirm() {
                this.open = false;
                if (this._resolve) this._resolve(true);
            },

            handleCancel() {
                this.open = false;
                if (this._resolve) this._resolve(false);
            }
        };
    }
</script>
