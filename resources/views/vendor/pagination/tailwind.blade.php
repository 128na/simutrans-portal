@if ($paginator->hasPages())
<nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
    <ul class="inline-flex -space-x-px text-base h-10">
        {{-- Previous Page Link --}}
        <li>
            @if ($paginator->onFirstPage())
            <span class="flex items-center justify-center px-4 h-10 text-c-sub bg-white border border-c-sub/10 ms-0 border-e-0 rounded-s-lg cursor-default">
                前
            </span>
            @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex items-center justify-center px-4 h-10 text-c-sub bg-white border border-c-sub/10 ms-0 border-e-0 rounded-s-lg hover:bg-c-sub/10 cursor-pointer" aria-label="{{ __('pagination.previous') }}">
                前
            </a>
            @endif
        </li>

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
        <li>
            <span class="flex items-center justify-center px-4 h-10 text-c-sub bg-white border border-c-sub/10">{{ $element }}</span>
        </li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
        @foreach ($element as $page => $url)
        <li>
            @if ($page == $paginator->currentPage())
            <span aria-current="page" class="flex items-center justify-center px-4 h-10 text-c-sub bg-blue-50 text-blue-600 border border-c-sub/10 cursor-pointer">
                {{ $page }}
            </span>
            @else
            <a href="{{ $url }}" class="flex items-center justify-center px-4 h-10 text-c-sub bg-white border border-c-sub/10 cursor-pointer hover:bg-c-sub/10" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                {{ $page }}
            </a>
            @endif
        </li>
        @endforeach
        @endif
        @endforeach

        {{-- Next Page Link --}}
        <li>
            @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex items-center justify-center px-4 h-10 text-c-sub bg-white border border-c-sub/10 rounded-e-lg hover:bg-c-sub/10 cursor-pointer" aria-label="{{ __('pagination.next') }}">
                次
            </a>
            @else
            <span class="flex items-center justify-center px-4 h-10 text-c-sub bg-white border border-c-sub/10 rounded-e-lg cursor-default">
                次
            </span>
            @endif
        </li>
    </ul>
</nav>
@endif
