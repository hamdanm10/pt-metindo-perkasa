@php
    $menu = app(\App\Services\Landing\LandingContent::class)->menu();
    $currentLocale = config('locales.locales.'.app()->getLocale());
    $link = 'relative inline-flex min-h-11 items-center px-3 text-base font-semibold text-ink hover:text-brand-700 aria-[current=page]:text-brand-700 '
        .'after:absolute after:inset-x-3 after:bottom-1 after:h-1 after:origin-left after:-skew-x-[20deg] after:scale-x-0 after:rounded-sm after:bg-brand-600 '
        .'after:transition-transform hover:after:scale-x-100 aria-[current=page]:after:scale-x-100 rtl:after:origin-right motion-reduce:after:transition-none';
@endphp

<header data-header class="sticky top-0 z-50 border-b border-ink/10 bg-white">
    <div data-header-inner class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('landing.home') }}" class="inline-flex shrink-0 items-center rounded">
            <img src="{{ asset('images/brand/logo.svg') }}" alt="PT Metindo Perkasa" width="555" height="100" class="h-8 w-auto sm:h-9">
        </a>

        <div class="hidden items-center gap-3 lg:flex">
            <nav aria-label="{{ __('landing.menu') }}">
                <ul class="flex items-center">
                    @foreach ($menu as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @if (request()->routeIs($item['active'])) aria-current="page" @endif
                               class="{{ $link }}">{{ __($item['label']) }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <details class="relative" data-dismissible>
                <summary class="inline-flex min-h-11 cursor-pointer list-none items-center gap-2 rounded-lg bg-lavender px-4 text-sm font-bold text-ink hover:bg-brand-50 [&::-webkit-details-marker]:hidden"
                         aria-label="{{ __('landing.language') }}">
                    <span lang="{{ $currentLocale['html_lang'] }}">{{ $currentLocale['name'] }}</span>
                    <svg viewBox="0 0 20 20" class="size-4" fill="currentColor" aria-hidden="true"><path d="M5.25 7.5 10 12.25 14.75 7.5l1.06 1.06L10 14.37 4.19 8.56 5.25 7.5Z"/></svg>
                </summary>
                <div class="absolute end-0 top-full z-20 mt-3 w-56 rounded-2xl bg-white p-2 shadow-xl ring-1 ring-ink/10">
                    <x-landing.language-links stacked />
                </div>
            </details>
        </div>

        <details class="relative lg:hidden" data-dismissible>
            <summary class="inline-flex min-h-11 min-w-11 cursor-pointer list-none items-center rounded-lg bg-brand-600 px-5 text-base font-bold text-white hover:bg-brand-700 [&::-webkit-details-marker]:hidden">
                {{ __('landing.menu') }}
            </summary>
            <div class="absolute end-0 top-full z-20 mt-3 w-72 max-w-[calc(100vw-2rem)] rounded-2xl bg-white p-3 shadow-xl ring-1 ring-ink/10">
                <nav aria-label="{{ __('landing.menu') }}">
                    <ul>
                        @foreach ($menu as $item)
                            <li>
                                <a href="{{ route($item['route']) }}"
                                   @if (request()->routeIs($item['active'])) aria-current="page" @endif
                                   class="flex min-h-11 items-center rounded-lg px-3 text-base font-semibold text-ink hover:bg-lavender aria-[current=page]:bg-lavender aria-[current=page]:font-bold aria-[current=page]:text-brand-700">
                                    {{ __($item['label']) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
                <div class="mt-2 border-t border-ink/10 pt-2">
                    <x-landing.language-links />
                </div>
            </div>
        </details>
    </div>
</header>
