@props(['page', 'last', 'type' => null])

@php
    $url = fn (int $number): string => route('landing.articles.index', array_filter(['type' => $type, 'page' => $number > 1 ? $number : null]));
    $window = collect([1, $last, $page - 1, $page, $page + 1])
        ->filter(fn (int $number): bool => $number >= 1 && $number <= $last)
        ->unique()
        ->sort()
        ->values();
    $pill = 'inline-flex min-h-11 min-w-11 items-center justify-center gap-2 rounded-full border px-4 text-[clamp(0.85rem,0.95vw,1.2rem)] font-bold transition-colors duration-300';
    $idle = 'border-ink/30 text-ink hover:border-ink hover:bg-ink hover:text-white';
@endphp

{{-- The pager of the article list: plain links (so every page is reachable and readable without JavaScript), the first and last page, the current one with its neighbours, and "…" for the gaps. --}}
@if ($last > 1)
    <nav aria-label="{{ __('articles.pagination.label') }}" class="mt-[clamp(3rem,5vw,6rem)]" data-pagination>
        <ul class="flex flex-wrap items-center justify-center gap-2 sm:gap-3">
            <li>
                @if ($page > 1)
                    <a href="{{ $url($page - 1) }}" rel="prev" class="{{ $pill }} {{ $idle }}">
                        <span aria-hidden="true" class="rtl:-scale-x-100">←</span>
                        <span class="max-sm:sr-only">{{ __('articles.pagination.previous') }}</span>
                    </a>
                @else
                    <span class="{{ $pill }} cursor-not-allowed border-ink/10 text-ink/30" aria-disabled="true">
                        <span aria-hidden="true" class="rtl:-scale-x-100">←</span>
                        <span class="max-sm:sr-only">{{ __('articles.pagination.previous') }}</span>
                    </span>
                @endif
            </li>

            @foreach ($window as $number)
                @if (! $loop->first && $number - $window[$loop->index - 1] > 1)
                    <li aria-hidden="true" class="px-1 text-ink/50">…</li>
                @endif
                <li>
                    @if ($number === $page)
                        <span aria-current="page" aria-label="{{ __('articles.pagination.page', ['number' => $number]) }}" class="{{ $pill }} border-ink bg-ink text-white tabular-nums">{{ $number }}</span>
                    @else
                        <a href="{{ $url($number) }}" aria-label="{{ __('articles.pagination.page', ['number' => $number]) }}" class="{{ $pill }} {{ $idle }} tabular-nums">{{ $number }}</a>
                    @endif
                </li>
            @endforeach

            <li>
                @if ($page < $last)
                    <a href="{{ $url($page + 1) }}" rel="next" class="{{ $pill }} {{ $idle }}">
                        <span class="max-sm:sr-only">{{ __('articles.pagination.next') }}</span>
                        <span aria-hidden="true" class="rtl:-scale-x-100">→</span>
                    </a>
                @else
                    <span class="{{ $pill }} cursor-not-allowed border-ink/10 text-ink/30" aria-disabled="true">
                        <span class="max-sm:sr-only">{{ __('articles.pagination.next') }}</span>
                        <span aria-hidden="true" class="rtl:-scale-x-100">→</span>
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
