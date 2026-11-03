{{--
    TOAST NOTIFICATION SYSTEM — Linear / Vercel Aesthetic
    Border-first, zero AI-slop, clean SVG indicators
    Usage:
        window.toast.success('Presensi Tersimpan', 'Data presensi berhasil dicatat')
        window.toast.error('Presensi Ditolak', 'Radius di luar jangkauan')
        window.toast.info('Info', 'Sesi telah dibuka')
        window.toast.loading('Memproses...')
--}}

<div id="toast-portal"
     x-data="toastSystem()"
     x-init="init()"
     class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2 items-end w-full max-w-sm pointer-events-none px-4"
     aria-live="polite">

    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
            class="bg-white dark:bg-zinc-900 border rounded-lg shadow-xs w-full relative overflow-hidden flex pointer-events-auto transition-colors"
            :class="{
                'border-zinc-200 dark:border-zinc-800': toast.type === 'info' || toast.type === 'loading',
                'border-emerald-500/30 dark:border-emerald-500/20': toast.type === 'success',
                'border-rose-500/30 dark:border-rose-500/20': toast.type === 'error',
                'border-amber-500/30 dark:border-amber-500/20': toast.type === 'warning',
            }">

            {{-- Subtle Left Accent Indicator --}}
            <div class="w-1 shrink-0"
                 :class="{
                    'bg-zinc-900 dark:bg-zinc-100': toast.type === 'info',
                    'bg-emerald-600 dark:bg-emerald-400': toast.type === 'success',
                    'bg-rose-600 dark:bg-rose-400': toast.type === 'error',
                    'bg-amber-600 dark:bg-amber-400': toast.type === 'warning',
                    'bg-blue-600 dark:bg-blue-400': toast.type === 'loading',
                 }"></div>

            <div class="flex items-start gap-3 p-3.5 flex-1 min-w-0">
                {{-- Clean Lucide-style SVG Icon --}}
                <div class="shrink-0 mt-0.5">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="15" y1="9" x2="9" y2="15"></line>
                            <line x1="9" y1="9" x2="15" y2="15"></line>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <svg class="w-4 h-4 text-zinc-700 dark:text-zinc-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </template>
                    <template x-if="toast.type === 'loading'">
                        <svg class="w-4 h-4 text-zinc-900 dark:text-zinc-100 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </template>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 tracking-tight"
                       x-text="toast.title"></p>
                    <p x-show="toast.description"
                       class="text-[11px] text-zinc-600 dark:text-zinc-400 mt-0.5 leading-normal"
                       x-text="toast.description"></p>
                </div>

                {{-- Dismiss Button --}}
                <button
                    x-show="toast.type !== 'loading'"
                    @click="dismiss(toast.id)"
                    class="shrink-0 text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200 p-0.5 transition"
                    aria-label="Tutup notifikasi">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
    function toastSystem() {
        return {
            toasts: [],
            counter: 0,

            init() {
                window.toast = {
                    success: (title, description) => this.add({ title, description, type: 'success' }),
                    error:   (title, description) => this.add({ title, description, type: 'error' }),
                    warning: (title, description) => this.add({ title, description, type: 'warning' }),
                    info:    (title, description) => this.add({ title, description, type: 'info' }),
                    loading: (title, description) => this.add({ title, description, type: 'loading', duration: 0 }),
                    update:  (id, options)        => this.update(id, options),
                    dismiss: (id)                 => this.dismiss(id),
                };
            },

            add({ title, description = '', type = 'info', duration = 4000 }) {
                const id = ++this.counter;
                const toast = { id, title, description, type, visible: true };
                this.toasts.push(toast);

                if (duration > 0) {
                    setTimeout(() => {
                        this.dismiss(id);
                    }, duration);
                }
                return id;
            },

            update(id, options) {
                const index = this.toasts.findIndex(t => t.id === id);
                if (index === -1) return;
                Object.assign(this.toasts[index], options);

                const duration = options.duration !== undefined ? options.duration : 4000;
                if (duration > 0) {
                    setTimeout(() => {
                        this.dismiss(id);
                    }, duration);
                }
            },

            dismiss(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (!toast) return;
                toast.visible = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 150);
            }
        };
    }
</script>
