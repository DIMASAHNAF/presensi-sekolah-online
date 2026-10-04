{{--
    TOAST NOTIFICATION SYSTEM
    Adapted from Ark UI / shadcn toast → Vanilla JS + Alpine.js
    Usage:
        window.toast.success('Judul', 'Deskripsi opsional')
        window.toast.error('Judul', 'Deskripsi')
        window.toast.info('Judul', 'Deskripsi')
        window.toast.loading('Judul', 'Deskripsi') → returns id
        window.toast.update(id, { title, description, type })
        window.toast.dismiss(id)
--}}

<div id="toast-portal"
     x-data="toastSystem()"
     x-init="init()"
     class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2.5 items-end"
     style="min-width: 300px; max-width: 380px;"
     aria-live="polite">

    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-180"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-1 scale-95"
            class="bg-white rounded-xl shadow-lg border w-full relative overflow-hidden flex"
            :class="{
                'border-slate-200': toast.type === 'info' || toast.type === 'loading',
                'border-emerald-200': toast.type === 'success',
                'border-red-200': toast.type === 'error',
                'border-amber-200': toast.type === 'warning',
            }">

            {{-- Side accent bar --}}
            <div class="w-1 shrink-0 rounded-l-xl"
                 :class="{
                    'bg-blue-600': toast.type === 'info' || toast.type === 'loading',
                    'bg-emerald-500': toast.type === 'success',
                    'bg-red-500': toast.type === 'error',
                    'bg-amber-500': toast.type === 'warning',
                 }"></div>

            <div class="flex items-start gap-3 px-4 py-3.5 flex-1">
                {{-- Icon --}}
                <div class="shrink-0 mt-0.5">
                    <template x-if="toast.type === 'success'">
                        <div class="w-5 h-5 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <div class="w-5 h-5 bg-red-100 text-red-600 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                    </template>
                    
                    <template x-if="toast.type === 'warning'">
                        <div class="w-5 h-5 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                        </div>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <div class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </template>
                    <template x-if="toast.type === 'loading'">
                        <div class="w-5 h-5 text-blue-600">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                    </template>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-900 leading-tight" x-text="toast.title"></p>
                    <p x-show="toast.description" class="text-xs text-slate-500 mt-0.5 leading-relaxed" x-text="toast.description"></p>

                    {{-- Progress bar for loading --}}
                    <template x-if="toast.type === 'loading'">
                        <div class="mt-2 w-full bg-slate-100 rounded-full h-1">
                            <div class="bg-blue-600 h-1 rounded-full animate-pulse" style="width: 60%"></div>
                        </div>
                    </template>
                </div>

                {{-- Close button --}}
                <button @click="dismiss(toast.id)"
                        class="shrink-0 w-5 h-5 flex items-center justify-center text-slate-300 hover:text-slate-600 transition rounded-md hover:bg-slate-100 mt-0.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </template>
</div>

<script>
    function toastSystem() {
        return {
            toasts: [],
            _counter: 0,

            init() {
                // Expose global API
                window.toast = {
                    success: (title, description = '', duration = 4000) => this.create({ title, description, type: 'success', duration }),
                    error:   (title, description = '', duration = 5000) => this.create({ title, description, type: 'error', duration }),
                    warning: (title, description = '', duration = 4500) => this.create({ title, description, type: 'warning', duration }),
                    info:    (title, description = '', duration = 4000) => this.create({ title, description, type: 'info', duration }),
                    loading: (title, description = '')                  => this.create({ title, description, type: 'loading', duration: Infinity }),
                    update:  (id, data)                                 => this.update(id, data),
                    dismiss: (id)                                       => this.dismiss(id),
                };

                // Auto-show flash messages from Laravel session
                @if(session('success'))
                    this.$nextTick(() => window.toast?.success(@json(session('success'))));
                @endif
                @if(session('error'))
                    this.$nextTick(() => window.toast?.error(@json(session('error'))));
                @endif
                @if(session('info'))
                    this.$nextTick(() => window.toast?.info(@json(session('info'))));
                @endif
                @if(session('warning'))
                    this.$nextTick(() => window.toast?.warning(@json(session('warning'))));
                @endif
            },

            create({ title, description = '', type = 'info', duration = 4000 }) {
                const id = ++this._counter;
                this.toasts.push({ id, title, description, type, visible: true });

                if (duration !== Infinity) {
                    setTimeout(() => this.dismiss(id), duration);
                }
                // Max 5 toasts
                if (this.toasts.length > 5) {
                    this.dismiss(this.toasts[0].id);
                }
                return id;
            },

            update(id, data) {
                const toast = this.toasts.find(t => t.id === id);
                if (!toast) return;
                Object.assign(toast, data);
                if (data.duration && data.duration !== Infinity) {
                    setTimeout(() => this.dismiss(id), data.duration);
                }
            },

            dismiss(id) {
                const toast = this.toasts.find(t => t.id === id);
                if (toast) {
                    toast.visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 200);
                }
            }
        };
    }
</script>
