@php
    $navLink = 'inline-flex min-h-11 items-center rounded px-3 text-base';
    $menu = app(\App\Services\Landing\LandingContent::class)->menu();
@endphp

<header class="border-b border-ink/10 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-2 sm:px-6 lg:px-8">
        <a href="{{ route('landing.home') }}" class="inline-flex shrink-0 items-center rounded">
            <img src="{{ asset('images/brand/logo.svg') }}" alt="PT Metindo Perkasa" width="555" height="100" class="h-8 w-auto sm:h-9">
        </a>

        <div class="hidden items-center gap-4 lg:flex">
            <nav aria-label="{{ __('landing.menu') }}">
                <ul class="flex items-center">
                    @foreach ($menu as $item)
                        @php($active = request()->routeIs($item['active']))
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @if ($active) aria-current="page" @endif
                               class="{{ $navLink }} {{ $active ? 'font-bold text-brand-700' : 'text-ink hover:text-brand-700' }}">
                                {{ __($item['label']) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
            <details class="relative" data-dismissible>
                <summary class="inline-flex min-h-11 cursor-pointer list-none items-center gap-2 rounded border border-ink/60 px-4 text-sm font-semibold text-ink [&::-webkit-details-marker]:hidden"
                         aria-label="{{ __('landing.language') }}">
                    <span lang="{{ config('locales.locales.'.app()->getLocale().'.html_lang') }}">{{ config('locales.locales.'.app()->getLocale().'.name') }}</span>
                    <svg viewBox="0 0 20 20" class="size-4" fill="currentColor" aria-hidden="true"><path d="M5.25 7.5 10 12.25 14.75 7.5l1.06 1.06L10 14.37 4.19 8.56 5.25 7.5Z"/></svg>
                </summary>
                <div class="absolute end-0 top-full z-20 mt-2 w-56 rounded border border-ink/15 bg-white p-2 shadow-lg">
                    <x-landing.language-links stacked />
                </div>
            </details>
        </div>

        <details class="relative lg:hidden" data-dismissible>
            <summary class="inline-flex min-h-11 min-w-11 cursor-pointer list-none items-center rounded border border-ink/60 px-4 text-base font-semibold text-ink [&::-webkit-details-marker]:hidden">
                {{ __('landing.menu') }}
            </summary>
            <div class="absolute end-0 top-full z-20 mt-2 w-72 max-w-[calc(100vw-2rem)] rounded border border-ink/15 bg-white p-4 shadow-lg">
                <nav aria-label="{{ __('landing.menu') }}">
                    <ul>
                        @foreach ($menu as $item)
                            @php($active = request()->routeIs($item['active']))
                            <li>
                                <a href="{{ route($item['route']) }}"
                                   @if ($active) aria-current="page" @endif
                                   class="{{ $navLink }} w-full {{ $active ? 'font-bold text-brand-700' : 'text-ink' }}">
                                    {{ __($item['label']) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
                <div class="mt-3 border-t border-ink/10 pt-3">
                    <x-landing.language-links />
                </div>
            </div>
        </details>
    </div>
</header>
