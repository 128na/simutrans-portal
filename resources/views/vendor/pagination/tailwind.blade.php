@if ($paginator->hasPages())
  <nav
    role="navigation"
    aria-label="{{ __('Pagination Navigation') }}"
    class="flex items-center justify-between"
  >
    <div class="flex justify-between flex-1 sm:hidden">
      @if ($paginator->onFirstPage())
        <span
          class="v2-pagination-item v2-pagination-item-single v2-pagination-item-disabled"
        >
          {!! __('pagination.previous') !!}
        </span>
      @else
        <a
          href="{{ $paginator->previousPageUrl() }}"
          class="v2-pagination-item v2-pagination-item-single v2-pagination-item-hover"
        >
          {!! __('pagination.previous') !!}
        </a>
      @endif

      @if ($paginator->hasMorePages())
        <a
          href="{{ $paginator->nextPageUrl() }}"
          class="v2-pagination-item v2-pagination-item-single v2-pagination-item-hover ml-3"
        >
          {!! __('pagination.next') !!}
        </a>
      @else
        <span
          class="v2-pagination-item v2-pagination-item-single v2-pagination-item-disabled ml-3"
        >
          {!! __('pagination.next') !!}
        </span>
      @endif
    </div>

    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-c-sub leading-5">
          <span class="font-medium">{{ $paginator->total() }}</span>
          {!! __('件中') !!}
          @if ($paginator->firstItem())
            <span class="font-medium">{{ $paginator->firstItem() }}</span>
            {!! __('～') !!}
            <span class="font-medium">{{ $paginator->lastItem() }}</span>
          @else
            {{ $paginator->count() }}
          @endif
          {!! __('件目表示') !!}
        </p>
      </div>

      <div>
        <span class="v2-pagination">
          {{-- Previous Page Link --}}

          @if ($paginator->onFirstPage())
            <span
              aria-disabled="true"
              aria-label="{{ __('pagination.previous') }}"
            >
              <span
                class="v2-pagination-item v2-pagination-item-start v2-pagination-item-disabled"
                aria-hidden="true"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    fill-rule="evenodd"
                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </span>
            </span>
          @else
            <a
              href="{{ $paginator->previousPageUrl() }}"
              rel="prev"
              class="v2-pagination-item v2-pagination-item-start v2-pagination-item-hover"
              aria-label="{{ __('pagination.previous') }}"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </a>
          @endif

          {{-- Pagination Elements --}}
          @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
              <span aria-disabled="true">
                <span class="v2-pagination-item">{{ $element }}</span>
              </span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
              @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                  <span aria-current="page">
                    <span class="v2-pagination-item v2-pagination-item-active">
                      {{ $page }}
                    </span>
                  </span>
                @else
                  <a
                    href="{{ $url }}"
                    class="v2-pagination-item v2-pagination-item-hover"
                    aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                  >
                    {{ $page }}
                  </a>
                @endif
              @endforeach
            @endif
          @endforeach

          {{-- Next Page Link --}}

          @if ($paginator->hasMorePages())
            <a
              href="{{ $paginator->nextPageUrl() }}"
              rel="next"
              class="v2-pagination-item v2-pagination-item-end v2-pagination-item-hover"
              aria-label="{{ __('pagination.next') }}"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path
                  fill-rule="evenodd"
                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </a>
          @else
            <span
              aria-disabled="true"
              aria-label="{{ __('pagination.next') }}"
            >
              <span
                class="v2-pagination-item v2-pagination-item-end v2-pagination-item-disabled"
                aria-hidden="true"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    fill-rule="evenodd"
                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </span>
            </span>
          @endif
        </span>
      </div>
    </div>
  </nav>
@endif
