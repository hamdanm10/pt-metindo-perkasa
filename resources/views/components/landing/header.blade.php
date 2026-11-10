@props(['alternates' => null])

@php
    $menu = app(\App\Services\Landing\LandingContent::class)->menu();
    // The logo already leads home, so the menu has no Home item.
    $menu = array_values(array_filter($menu, fn (array $item): bool => $item['route'] !== 'landing.home'));
    $mainMenu = array_values(array_filter($menu, fn (array $item): bool => $item['route'] !== 'landing.contact'));
    $currentLocale = config('locales.locales.'.app()->getLocale());
    $link = 'header-link relative inline-flex items-center text-white';
@endphp

<header data-header class="sticky top-0 z-50 pointer-events-none text-white" style="--focus-ring: #ffffff">
    <div data-header-inner class="header-bar container-site flex items-center justify-between gap-4">
        <a href="{{ route('landing.home') }}" class="header-chip header-chip--logo pointer-events-auto inline-flex shrink-0 items-center">
            <img src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="header-mark w-auto">
            <span class="header-brand"><span class="header-brand__first">Metindo</span> <span class="header-brand__second">Perkasa</span></span>
        </a>

        <div class="pointer-events-auto hidden items-center gap-3 lg:flex">
            <div class="header-chip header-chip--nav flex items-center">
            <nav aria-label="{{ __('landing.menu') }}">
                <ul class="header-links flex items-center">
                    @foreach ($mainMenu as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @if (request()->routeIs($item['active'])) aria-current="page" @endif
                               class="{{ $link }}"><span class="uline">{{ __($item['label']) }}</span></a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <details class="relative" data-dismissible>
                <summary class="header-link inline-flex cursor-pointer list-none items-center gap-2 font-bold text-white [&::-webkit-details-marker]:hidden"
                         aria-label="{{ __('landing.language') }}">
                    <span class="uline" lang="{{ $currentLocale['html_lang'] }}" title="{{ $currentLocale['name'] }}">{{ strtoupper(app()->getLocale()) }}</span>
                    <svg viewBox="0 0 20 20" class="size-4" fill="currentColor" aria-hidden="true"><path d="M5.25 7.5 10 12.25 14.75 7.5l1.06 1.06L10 14.37 4.19 8.56 5.25 7.5Z"/></svg>
                </summary>
                <div class="absolute end-0 top-full z-20 mt-3 w-56 rounded-lg bg-white p-2 text-ink shadow-xl ring-1 ring-ink/10" style="--focus-ring: #1f4fb5">
                    <x-landing.language-links stacked :alternates="$alternates" />
                </div>
            </details>
            </div>

            <x-landing.button :href="route('landing.contact')">{{ __('landing.talk') }}</x-landing.button>
        </div>

        <details class="pointer-events-auto relative lg:hidden" data-dismissible>
            <x-landing.button as="summary" :arrow="false" class="list-none [&::-webkit-details-marker]:hidden">{{ __('landing.menu') }}</x-landing.button>
            <div class="absolute end-0 top-full z-20 mt-3 w-72 max-w-[calc(100vw-2rem)] rounded-lg bg-white p-3 text-ink shadow-xl ring-1 ring-ink/10" style="--focus-ring: #1f4fb5">
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
                    <x-landing.language-links :alternates="$alternates" />
                </div>
            </div>
        </details>
    </div>
</header>
