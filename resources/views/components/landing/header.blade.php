@php
    $navLink = 'inline-flex min-h-11 items-center rounded px-3 text-base';
    $homeActive = request()->routeIs('landing.home');
@endphp

<header class="border-b border-ink/10 bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-2 sm:px-6 lg:px-8">
        <a href="{{ route('landing.home') }}" class="inline-flex shrink-0 items-center rounded">
            <img src="{{ asset('images/brand/logo.svg') }}" alt="PT Metindo Perkasa" width="555" height="100" class="h-8 w-auto sm:h-9">
        </a>

        <div class="hidden items-center gap-6 md:flex">
            <nav aria-label="{{ __('landing.menu') }}">
                <ul>
                    <li>
                        <a href="{{ route('landing.home') }}"
                           @if ($homeActive) aria-current="page" @endif
                           class="{{ $navLink }} {{ $homeActive ? 'font-bold text-brand-700' : 'text-ink hover:text-brand-700' }}">
                            {{ __('landing.nav.home') }}
                        </a>
                    </li>
                </ul>
            </nav>
            <x-landing.language-links />
        </div>

        <details class="relative md:hidden" data-dismissible>
            <summary class="inline-flex min-h-11 min-w-11 cursor-pointer list-none items-center rounded border border-ink/60 px-4 text-base font-semibold text-ink [&::-webkit-details-marker]:hidden">
                {{ __('landing.menu') }}
            </summary>
            <div class="absolute end-0 top-full z-20 mt-2 w-72 max-w-[calc(100vw-2rem)] rounded border border-ink/15 bg-white p-4 shadow-lg">
                <nav aria-label="{{ __('landing.menu') }}">
                    <ul>
                        <li>
                            <a href="{{ route('landing.home') }}"
                               @if ($homeActive) aria-current="page" @endif
                               class="{{ $navLink }} w-full {{ $homeActive ? 'font-bold text-brand-700' : 'text-ink' }}">
                                {{ __('landing.nav.home') }}
                            </a>
                        </li>
                    </ul>
                </nav>
                <div class="mt-3 border-t border-ink/10 pt-3">
                    <x-landing.language-links />
                </div>
            </div>
        </details>
    </div>
</header>
