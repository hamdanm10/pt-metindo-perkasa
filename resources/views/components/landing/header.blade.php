@props(['alternates' => null])

@php
    $menu = app(\App\Services\Landing\LandingContent::class)->menu();
    // The logo already leads home, so the menu has no Home item.
    $menu = array_values(array_filter($menu, fn (array $item): bool => $item['route'] !== 'landing.home'));
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
                    @foreach ($menu as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @if (request()->routeIs($item['active'])) aria-current="page" @endif
                               class="{{ $link }}"><span class="uline">{{ __($item['label']) }}</span></a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <details class="relative" data-dismissible>
                <summary class="header-link lang-summary inline-flex cursor-pointer list-none items-center gap-2 font-bold text-white [&::-webkit-details-marker]:hidden"
                         aria-label="{{ __('landing.language') }}">
                    <span class="uline" lang="{{ $currentLocale['html_lang'] }}" title="{{ $currentLocale['name'] }}">{{ strtoupper(app()->getLocale()) }}</span>
                    <svg viewBox="0 0 20 20" class="size-4" fill="currentColor" aria-hidden="true"><path d="M5.25 7.5 10 12.25 14.75 7.5l1.06 1.06L10 14.37 4.19 8.56 5.25 7.5Z"/></svg>
                </summary>
                <div class="lang-panel absolute end-0 top-full z-20 mt-4 w-[clamp(13rem,15vw,17rem)] rounded-lg bg-ink p-2 text-white shadow-[0_24px_60px_-12px_rgba(0,0,0,0.55)] ring-1 ring-white/15" style="--focus-ring: #ffffff">
                    <x-landing.language-links dropdown :alternates="$alternates" />
                </div>
            </details>
            </div>

            <x-landing.button :href="route('landing.contact')">{{ __('landing.talk') }}</x-landing.button>
        </div>

        {{-- Hamburger, as on the reference: a small crimson square whose three bars turn into a cross. It opens the full-screen menu below. --}}
        <button type="button" data-menu-toggle aria-expanded="false" aria-controls="mobile-menu" aria-label="{{ __('landing.menu') }}" class="menu-toggle pointer-events-auto flex shrink-0 items-center justify-center bg-brand-600 text-white lg:hidden">
            <span class="menu-toggle__bars" aria-hidden="true"><span></span><span></span><span></span></span>
        </button>
    </div>
</header>

{{-- The full-screen menu of the reference: a dark sheet that swings down from the top right over a dimmed page. Opened and closed by mobile-menu.js. --}}
<aside id="mobile-menu" data-menu hidden class="fixed inset-0 z-[45] lg:hidden" style="--focus-ring: #ffffff">
    <div data-menu-backdrop class="absolute inset-0 bg-ink/60"></div>
    <div data-menu-mask class="absolute inset-0 origin-top-left overflow-hidden">
        <div data-menu-panel class="absolute inset-0 overflow-y-auto overflow-x-hidden bg-ink text-white">
            <nav aria-label="{{ __('landing.menu') }}" class="pt-[calc(var(--header-h)+1.5rem)]">
                <ul class="border-b border-white/20">
                    @foreach ($menu as $item)
                        <li data-menu-item class="border-t border-white/20">
                            <a href="{{ route($item['route']) }}" @if (request()->routeIs($item['active'])) aria-current="page" @endif class="menu-link block px-4 py-3 text-2xl font-bold aria-[current=page]:text-brand-300">{{ __($item['label']) }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
            <div data-menu-item class="px-4 py-6">
                <x-landing.language-links dark :alternates="$alternates" />
            </div>
        </div>
    </div>
</aside>
