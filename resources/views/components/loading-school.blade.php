{{--
    Component: loading-school.blade.php
    Deskripsi: Kolaborasi LoadingSchoolIcon + Kokonut AI Text Loading untuk Laravel Blade
    Mendukung transisi teks halus dengan glowing pinging logo sekolah (icon.png)
--}}

@props([
    'iconSrc' => asset('icon.png'),
    'texts' => [
        'Memverifikasi Wajah...',
        'Menganalisis Biometrik AI...',
        'Mengecek Koordinat Presensi...',
        'Menghubungkan ke Server...',
        'Hampir Selesai...',
    ],
    'interval' => 1600,
    'id' => 'loading-school-' . uniqid(),
])

<div id="{{ $id }}" class="loading-school-box flex flex-col items-center justify-center p-6 space-y-4 select-none">
    {{-- 1. Glowing Pinging School Logo --}}
    <div class="relative flex items-center justify-center">
        {{-- Ping Animation --}}
        <div class="absolute w-[68px] h-[68px] bg-teal-500/25 rounded-full animate-ping pointer-events-none"></div>
        {{-- Soft Ambient Glow --}}
        <div class="absolute w-[84px] h-[84px] bg-emerald-400/20 rounded-full blur-md animate-pulse pointer-events-none"></div>
        {{-- School Icon --}}
        <img src="{{ $iconSrc }}"
             alt="School Logo Loading"
             class="relative z-10 w-[60px] h-[60px] object-contain animate-pulse filter drop-shadow-md">
    </div>

    {{-- 2. Shimmering Kokonut AI Text Loading --}}
    <div class="relative w-full max-w-xs text-center px-4 py-2 overflow-hidden min-h-[52px] flex flex-col items-center justify-center">
        <div class="loading-text-target font-bold text-base sm:text-lg tracking-wide bg-[length:200%_100%] bg-gradient-to-r from-teal-400 via-emerald-200 to-teal-400 bg-clip-text text-transparent animate-[shimmer_2.5s_linear_infinite] drop-shadow-sm transition-all duration-300 transform">
            {{ $texts[0] ?? 'Memuat...' }}
        </div>

        <p class="text-[11px] text-slate-400/80 mt-1 font-medium tracking-wider uppercase">
            Sistem Presensi AI SMKN 1 Beringin
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
            textEl.style.transform = 'translateY(-5px)';
            
            setTimeout(() => {
                currentIndex = (currentIndex + 1) % texts.length;
                textEl.textContent = texts[currentIndex];
                textEl.style.opacity = '1';
                textEl.style.transform = 'translateY(0)';
            }, 200);
        }, {{ $interval }});
    })();
</script>

<style>
    @keyframes shimmer {
        0%   { background-position: 200% center; }
        100% { background-position: -200% center; }
    }
</style>
