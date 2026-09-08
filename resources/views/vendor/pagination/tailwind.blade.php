@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between py-3">
        <!-- Mobile View (Previous & Next Buttons with Counter) -->
        <div class="flex justify-between flex-1 sm:hidden items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-500 bg-slate-800/50 border border-slate-700/50 cursor-not-allowed rounded-xl select-none">
                    <i class="fa-solid fa-chevron-left mr-1.5 text-[10px]"></i> Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-200 bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-indigo-500 rounded-xl transition shadow-sm">
                    <i class="fa-solid fa-chevron-left mr-1.5 text-[10px]"></i> Sebelumnya
                </a>
            @endif

            <span class="text-xs font-medium text-slate-400 font-mono">
                Hal {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-200 bg-slate-800 hover:bg-slate-700 border border-slate-700 hover:border-indigo-500 rounded-xl transition shadow-sm">
                    Berikutnya <i class="fa-solid fa-chevron-right ml-1.5 text-[10px]"></i>
                </a>
            @else
                <span class="relative inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-500 bg-slate-800/50 border border-slate-700/50 cursor-not-allowed rounded-xl select-none">
                    Berikutnya <i class="fa-solid fa-chevron-right ml-1.5 text-[10px]"></i>
                </span>
            @endif
        </div>

        <!-- Desktop View (Showing x to y of z + Numbered Pagination) -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs text-slate-400">
                    Menampilkan
                    <span class="font-bold text-white font-mono">{{ $paginator->firstItem() ?? 0 }}</span>
                    sampai
                    <span class="font-bold text-white font-mono">{{ $paginator->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-bold text-indigo-400 font-mono">{{ $paginator->total() }}</span>
                    data
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex rounded-xl shadow-sm space-x-1">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="@lang('pagination.previous')" class="relative inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-slate-600 bg-slate-900/60 border border-slate-800 cursor-not-allowed rounded-lg">
                            <i class="fa-solid fa-chevron-left text-[11px]"></i>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" class="relative inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-slate-300 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 hover:text-white transition">
                            <i class="fa-solid fa-chevron-left text-[11px]"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true" class="relative inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-500 bg-slate-900/60 border border-slate-800 rounded-lg">
                                {{ $element }}
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="relative inline-flex items-center px-3 py-1.5 text-xs font-bold text-white bg-indigo-600 border border-indigo-500 rounded-lg shadow-md shadow-indigo-600/30">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-3 py-1.5 text-xs font-medium text-slate-300 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 hover:text-white hover:border-slate-600 transition">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" class="relative inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-slate-300 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 hover:text-white transition">
                            <i class="fa-solid fa-chevron-right text-[11px]"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="@lang('pagination.next')" class="relative inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-slate-600 bg-slate-900/60 border border-slate-800 cursor-not-allowed rounded-lg">
                            <i class="fa-solid fa-chevron-right text-[11px]"></i>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
