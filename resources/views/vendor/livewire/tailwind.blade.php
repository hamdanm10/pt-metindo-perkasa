@php
$scrollTo ??= 'body';

$scrollIntoView = $scrollTo !== false
    ? "(\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()"
    : '';

$name = $paginator->getPageName();
$box = 'inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg border px-3 text-sm font-bold tabular-nums transition-colors';
$idle = 'border-ink/30 bg-white text-ink hover:border-brand-700 hover:text-brand-700';
$off = 'cursor-not-allowed border-ink/10 bg-white text-ink/40';
@endphp

{{-- The dashboard pager, in the dashboard colours. Livewire's own view is overridden here: its grey, blue and dark-mode classes do not belong to this theme. --}}
<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="{{ __('dashboard.pagination.label') }}" class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-muted">{{ __('dashboard.pagination.summary', ['from' => $paginator->firstItem(), 'to' => $paginator->lastItem(), 'total' => $paginator->total()]) }}</p>

            <ul class="flex flex-wrap items-center gap-2">
                <li>
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" class="{{ $box }} {{ $off }}">← <span class="sr-only sm:not-sr-only sm:ms-2">{{ __('dashboard.pagination.previous') }}</span></span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $name }}')" x-on:click="{{ $scrollIntoView }}" wire:loading.attr="disabled" class="{{ $box }} {{ $idle }}">← <span class="sr-only sm:not-sr-only sm:ms-2">{{ __('dashboard.pagination.previous') }}</span></button>
                    @endif
                </li>

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li aria-hidden="true" class="hidden px-1 text-ink/50 sm:block">{{ $element }}</li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li wire:key="paginator-{{ $name }}-page{{ $page }}" @class(['hidden sm:block' => $page != $paginator->currentPage()])>
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page" class="{{ $box }} border-brand-700 bg-brand-700 text-white">{{ $page }}</span>
                                @else
                                    <button type="button" wire:click="gotoPage({{ $page }}, '{{ $name }}')" x-on:click="{{ $scrollIntoView }}" aria-label="{{ __('dashboard.pagination.page', ['page' => $page]) }}" class="{{ $box }} {{ $idle }}">{{ $page }}</button>
                                @endif
                            </li>
                        @endforeach
                    @endif
                @endforeach

                <li>
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $name }}')" x-on:click="{{ $scrollIntoView }}" wire:loading.attr="disabled" class="{{ $box }} {{ $idle }}"><span class="sr-only sm:not-sr-only sm:me-2">{{ __('dashboard.pagination.next') }}</span> →</button>
                    @else
                        <span aria-disabled="true" class="{{ $box }} {{ $off }}"><span class="sr-only sm:not-sr-only sm:me-2">{{ __('dashboard.pagination.next') }}</span> →</span>
                    @endif
                </li>
            </ul>
        </nav>
    @endif
</div>
