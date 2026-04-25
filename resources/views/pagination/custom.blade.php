@if ($paginator->hasPages())
    <div class="pagination" role="navigation" aria-label="Pagination Navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="page-item disabled" aria-disabled="true">
                <span class="page-link">« Previous</span>
            </span>
        @else
            <a class="page-item" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                <span class="page-link">« Previous</span>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="page-item disabled" aria-disabled="true">
                    <span class="page-link">{{ $element }}</span>
                </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    {{-- "Three Dots" Separator --}}
                    @if ($page > 1 && $page !== 2)
                        <span class="page-item disabled" aria-disabled="true">
                            <span class="page-link">...</span>
                        </span>
                    @endif

                    @if ($page == $paginator->currentPage())
                        <span class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </span>
                    @else
                        <a class="page-item" href="{{ $url }}">
                            <span class="page-link">{{ $page }}</span>
                        </a>
                    @endif

                    @if ($page < count($element) && $page !== count($element) - 1)
                        {{-- Skip the last iteration check --}}
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="page-item" href="{{ $paginator->nextPageUrl() }}" rel="next">
                <span class="page-link">Next »</span>
            </a>
        @else
            <span class="page-item disabled" aria-disabled="true">
                <span class="page-link">Next »</span>
            </span>
        @endif
    </div>
@endif
