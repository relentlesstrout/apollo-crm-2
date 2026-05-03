@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination"
         class="flex items-center justify-between">

        {{-- Left: Results --}}
        <div class="hidden sm:block">
            <p class="text-sm text-slate-600">
                Showing
                <span class="font-medium text-slate-800">{{ $paginator->firstItem() }}</span>
                to
                <span class="font-medium text-slate-800">{{ $paginator->lastItem() }}</span>
                of
                <span class="font-medium text-slate-800">{{ $paginator->total() }}</span>
                results
            </p>
        </div>

        {{-- Right: Pagination --}}
        <div class="flex items-center gap-1">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-300 bg-white cursor-not-allowed">
                    ‹
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 hover:text-sky-600 transition">
                    ‹
                </a>
            @endif

            {{-- Page Numbers (pill style) --}}
            @foreach ($elements as $element)

                {{-- Ellipsis --}}
                @if (is_string($element))
                    <span class="px-3 py-1.5 text-slate-400">…</span>
                @endif

                {{-- Pages --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)

                        @if ($page == $paginator->currentPage())
                            <span
                                class="px-4 py-1.5 rounded-full bg-sky-500 text-white font-medium shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="px-4 py-1.5 rounded-full border border-slate-200 text-slate-700 bg-white hover:border-sky-200 hover:text-sky-600 hover:bg-sky-50 transition">
                                {{ $page }}
                            </a>
                        @endif

                    @endforeach
                @endif

            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-600 bg-white hover:bg-slate-50 hover:text-sky-600 transition">
                    ›
                </a>
            @else
                <span class="px-3 py-1.5 rounded-full border border-slate-200 text-slate-300 bg-white cursor-not-allowed">
                    ›
                </span>
            @endif

        </div>
    </nav>
@endif
