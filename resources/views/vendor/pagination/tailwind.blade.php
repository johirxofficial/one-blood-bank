@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('pagination.navigation') }}"
         class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs text-ink-muted">
            @if ($paginator->firstItem())
                {{ __('pagination.showing', [
                    'first' => $paginator->firstItem(),
                    'last' => $paginator->lastItem(),
                    'total' => $paginator->total(),
                ]) }}
            @else
                {{ __('pagination.showing_empty', ['total' => $paginator->total()]) }}
            @endif
        </p>

        <div class="flex flex-wrap items-center gap-1">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                      class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-line text-ink-muted/40">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <path d="m12 5-5 5 5 5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                   class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-line bg-surface text-ink-soft transition-colors hover:bg-raised hover:text-ink">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <path d="m12 5-5 5 5 5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-8 min-w-8 items-center justify-center px-1.5 text-xs text-ink-muted">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex h-8 min-w-8 items-center justify-center rounded-md bg-ink px-2 text-xs font-medium text-paper">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" aria-label="{{ __('pagination.go_to_page', ['page' => $page]) }}"
                               class="inline-flex h-8 min-w-8 items-center justify-center rounded-md border border-line bg-surface px-2 text-xs text-ink-soft transition-colors hover:bg-raised hover:text-ink">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                   class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-line bg-surface text-ink-soft transition-colors hover:bg-raised hover:text-ink">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <path d="m8 5 5 5-5 5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                      class="inline-flex h-8 w-8 items-center justify-center rounded-md border border-line text-ink-muted/40">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                        <path d="m8 5 5 5-5 5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
