{{--
    CONFIRM ALERT DIALOG — Linear / Vercel Aesthetic
    Border-first, zero AI-slop, clean SVG indicators
--}}

<div x-data="shadcnConfirmModal()" 
     x-cloak
     @keydown.escape.window="handleCancel()">

    {{-- Backdrop Overlay --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9998] bg-black/60 backdrop-blur-xs"></div>

    {{-- Dialog Container --}}
    <div x-show="open"
         class="fixed inset-0 z-[9999] flex items-center justify-center p-4 overflow-y-auto"
         @click.self="handleCancel()">

        <div x-show="open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95 translate-y-1"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-1"
             class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm max-w-md w-full p-6 relative overflow-hidden my-auto"
             role="alertdialog"
             aria-modal="true">

            {{-- Header with Icon --}}
            <div class="flex items-start gap-3.5">
                {{-- Dynamic Icon Badge --}}
                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 border"
                     :class="{
                         'bg-rose-500/10 border-rose-500/20 text-rose-600 dark:text-rose-400': type === 'danger',
                         'bg-amber-500/10 border-amber-500/20 text-amber-600 dark:text-amber-400': type === 'warning',
                         'bg-zinc-500/10 border-zinc-500/20 text-zinc-700 dark:text-zinc-300': type === 'info'
                     }">
                    <template x-if="type === 'danger'">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18m-2 0v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6m3 0V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </template>
                    <template x-if="type === 'warning'">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </template>
                    <template x-if="type === 'info'">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </template>
                </div>

                {{-- Text Content --}}
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 tracking-tight"
                        x-text="title"></h3>
                    <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1 leading-relaxed"
                       x-text="description"></p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-2.5 mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <button type="button"
                        @click="handleCancel()"
                        class="px-3.5 py-2 text-xs font-medium text-zinc-700 dark:text-zinc-300 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-800 rounded-lg transition"
                        x-text="cancelText">
                </button>
                <button type="button"
                        @click="handleConfirm()"
                        class="px-3.5 py-2 text-xs font-medium rounded-lg text-white transition focus:ring-2 focus:ring-offset-2"
                        :class="{
                            'bg-rose-600 hover:bg-rose-700 focus:ring-rose-500': type === 'danger',
                            'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500': type === 'warning',
                            'bg-zinc-900 dark:bg-zinc-100 dark:text-zinc-900 hover:bg-zinc-800 dark:hover:bg-zinc-200 focus:ring-zinc-500': type === 'info'
                        }"
                        x-text="confirmText">
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

                document.addEventListener('click', (e) => {
                    const btn = e.target.closest('[data-confirm]');
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
