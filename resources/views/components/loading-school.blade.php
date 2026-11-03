{{--
    Component: loading-school.blade.php — Linear / Vercel Aesthetic
    Minimalist page transition loader, zero gradients, zero AI slop
--}}

@props([
    'iconSrc' => asset('images/logo.webp'),
    'texts' => [
        'Memverifikasi...',
        'Menghubungkan ke Server...',
        'Memuat Data...',
    ],
    'interval' => 1500,
    'id' => 'loading-school-' . uniqid(),
])

<div id="{{ $id }}" class="flex flex-col items-center justify-center p-6 space-y-3.5 select-none bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xs max-w-xs w-full mx-4">
    <div class="relative flex items-center justify-center">
        <svg class="w-8 h-8 text-zinc-900 dark:text-zinc-100 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-20" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </div>

    <div class="text-center min-h-[36px] flex flex-col items-center justify-center">
        <p class="loading-text-target text-xs font-semibold text-zinc-900 dark:text-zinc-100 tracking-tight transition-all duration-200">
            {{ $texts[0] ?? 'Memuat...' }}
        </p>
        <p class="text-[10px] text-zinc-500 dark:text-zinc-400 font-mono tabular-nums mt-0.5">
            SMKN 1 BERINGIN
        </p>
    </div>
</div>

<script>
    (function() {
        const container = document.getElementById('{{ $id }}');
        if (!container) return;
        const textEl = container.querySelector('.loading-text-target');
        if (!textEl) return;

        const texts = @json($texts);
        if (!texts || texts.length <= 1) return;

        let currentIndex = 0;
        setInterval(() => {
            textEl.style.opacity = '0';
            setTimeout(() => {
                currentIndex = (currentIndex + 1) % texts.length;
                textEl.textContent = texts[currentIndex];
                textEl.style.opacity = '1';
            }, 150);
        }, {{ $interval }});
    })();
</script>
