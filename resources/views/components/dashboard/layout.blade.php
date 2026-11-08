@props(['title'])

@php
    $user = auth()->user();
    $menu = app(\App\Services\Dashboard\DashboardMenu::class)->forUser($user);
    $area = $user->role->usesStaffArea() ? 'app' : 'customer';
    $sidebarLink = 'relative flex min-h-11 items-center rounded-lg px-4 text-base font-semibold text-white/85 hover:bg-white/10 hover:text-white '
        .'aria-[current=page]:bg-white/10 aria-[current=page]:font-bold aria-[current=page]:text-white '
        .'aria-[current=page]:before:absolute aria-[current=page]:before:inset-y-2 aria-[current=page]:before:start-0 aria-[current=page]:before:w-1.5 '
        .'aria-[current=page]:before:-skew-x-[20deg] aria-[current=page]:before:rounded-sm aria-[current=page]:before:bg-brand-600';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} | PT Metindo Perkasa</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    @vite(['resources/css/dashboard.css', 'resources/css/fonts/latin.css', 'resources/js/dashboard.js'])
</head>
<body class="min-h-screen bg-lavender font-sans text-ink antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-ink focus:ring-2 focus:ring-accent-blue">
        {{ __('dashboard.skip_to_content') }}
    </a>

    <div class="min-h-screen lg:grid lg:grid-cols-[16rem_minmax(0,1fr)]">
        <aside class="hidden bg-ink text-white lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col" style="--focus-ring: #ffffff">
            <div class="px-6 py-6">
                <a href="{{ route($user->role->homeRoute()) }}" class="inline-flex rounded">
                    <img src="{{ asset('images/brand/logo-white.svg') }}" alt="PT Metindo Perkasa" width="555" height="100" class="h-8 w-auto">
                </a>
            </div>
            <nav aria-label="{{ __('dashboard.menu') }}" class="flex-1 px-3">
                <ul class="space-y-1">
                    @foreach ($menu as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" @if (request()->routeIs($item['active'])) aria-current="page" @endif class="{{ $sidebarLink }}">{{ __($item['label']) }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>

        <div class="flex min-w-0 flex-col">
            <header class="sticky top-0 z-30 border-b border-ink/10 bg-white">
                <div class="px-4 py-2 lg:px-8">
                <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-2">
                    <a href="{{ route($user->role->homeRoute()) }}" class="inline-flex rounded lg:hidden">
                        <img src="{{ asset('images/brand/logo.svg') }}" alt="PT Metindo Perkasa" width="555" height="100" class="h-6 w-auto sm:h-8">
                    </a>

                    <div class="ms-auto flex items-center gap-1 sm:gap-2">
                        <x-dashboard.language-dropdown :action="route($area.'.locale')" />
                        <x-dashboard.user-menu :user="$user" :area="$area" />

                        <details class="relative lg:hidden" data-dismissible>
                            <summary class="inline-flex min-h-11 min-w-11 cursor-pointer list-none items-center justify-center rounded-lg bg-brand-600 text-white hover:bg-brand-700 [&::-webkit-details-marker]:hidden"
                                     aria-label="{{ __('dashboard.menu') }}">
                                <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                            </summary>
                            <div class="absolute end-0 top-full z-40 mt-2 w-60 max-w-[calc(100vw-2rem)] rounded-2xl bg-white p-2 shadow-xl ring-1 ring-ink/10">
                                <nav aria-label="{{ __('dashboard.menu') }}">
                                    <ul>
                                        @foreach ($menu as $item)
                                            <li>
                                                <a href="{{ route($item['route']) }}" @if (request()->routeIs($item['active'])) aria-current="page" @endif
                                                   class="flex min-h-11 items-center rounded-lg px-3 text-base font-semibold hover:bg-lavender aria-[current=page]:bg-lavender aria-[current=page]:font-bold aria-[current=page]:text-brand-700">{{ __($item['label']) }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </nav>
                            </div>
                        </details>
                    </div>
                </div>
                </div>
            </header>

            <main id="main" class="flex-1 overflow-x-clip px-4 py-8 lg:px-8 lg:py-10">
                <div class="mx-auto w-full max-w-6xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>
