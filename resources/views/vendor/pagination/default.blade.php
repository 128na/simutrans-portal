@if ($paginator->hasPages())
    <ul class="pagination justify-content-center my-3">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled">
                <a class="page-link" href="#" rel="prev" aria-disabled="true"  aria-label="@lang('pagination.previous')">&lsaquo;</a>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="page-item disabled">
                    <a class="page-link" href="#" aria-disabled="true" >{{ $element }}</a>
                </li>
            @endif
            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item disabled">
                            <a class="page-link" href="#" aria-disabled="true"  aria-current="page">{{ $page }}</a>
                        </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endif
                @endforeach
            @endif
        @endforeach
        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&rsaquo;</a>
            </li>
            @else
            <li class="page-item disabled">
                <a class="page-link" href="#" rel="next" aria-disabled="true"  aria-label="@lang('pagination.next')">&rsaquo;</a>
            </li>
        @endif
    </ul>
@endif
