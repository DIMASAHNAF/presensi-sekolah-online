@if ($paginator->hasPages())
<nav class="flex items-center justify-center gap-1 flex-wrap" role="navigation" aria-label="Pagination">

    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-300 cursor-not-allowed select-none">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span>Sebelumnya</span>
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            <span>Sebelumnya</span>
        </a>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-2 py-1.5 text-xs font-bold text-slate-400 select-none">···</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold text-white bg-blue-700 rounded-lg shadow-sm select-none">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $url }}"
                       class="inline-flex items-center justify-center w-8 h-8 text-xs font-semibold text-slate-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors border border-transparent hover:border-blue-100">
                        {{ $page }}
                    </a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
            <span>Berikutnya</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </a>
    @else
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-300 cursor-not-allowed select-none">
            <span>Berikutnya</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </span>
    @endif
</nav>
@endif
