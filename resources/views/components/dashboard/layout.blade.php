@props(['title'])

@php
    $user = auth()->user();
    $menu = app(\App\Services\Dashboard\DashboardMenu::class)->forUser($user);
    $area = $user->role->usesStaffArea() ? 'app' : 'customer';
    $sidebarLink = 'relative flex min-h-12 items-center px-5 text-base font-bold text-white/70 transition-colors hover:text-white '
        .'aria-[current=page]:text-white '
        .'aria-[current=page]:before:absolute aria-[current=page]:before:inset-y-2.5 aria-[current=page]:before:start-0 aria-[current=page]:before:w-1.5 '
        .'aria-[current=page]:before:-skew-x-[20deg] aria-[current=page]:before:rounded-sm aria-[current=page]:before:bg-brand-600';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title }} | PT Metindo Perkasa</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="48x48">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    @vite(['resources/css/dashboard.css', 'resources/css/fonts/latin.css', 'resources/js/dashboard.js'])
</head>
<body class="min-h-screen bg-lavender font-sans text-ink antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:start-4 focus:top-4 focus:z-50 focus:rounded focus:bg-white focus:px-4 focus:py-2 focus:text-ink focus:ring-2 focus:ring-accent-blue">
        {{ __('dashboard.skip_to_content') }}
    </a>

    <div class="min-h-screen lg:grid lg:grid-cols-[16rem_minmax(0,1fr)]">
        <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-ink/50 lg:hidden" data-sidebar-close aria-hidden="true"></div>

        <aside id="sidebar" class="fixed inset-y-0 start-0 z-50 flex w-64 flex-col bg-ink text-white lg:sticky lg:top-0 lg:z-auto lg:h-screen lg:w-auto" style="--focus-ring: #ffffff">
            <div class="flex items-center justify-between px-6 py-7">
                <a href="{{ route($user->role->homeRoute()) }}" class="brand-lockup inline-flex items-center" aria-label="PT Metindo Perkasa">
                    <img src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="h-8 w-auto">
                    <span class="brand-lockup__name"><span class="font-extrabold">Metindo</span> <span>Perkasa</span></span>
                </a>
                <button type="button" data-sidebar-close class="-me-3 inline-flex size-11 items-center justify-center rounded-lg hover:bg-white/10 lg:hidden" aria-label="{{ __('dashboard.close_menu') }}">
                    <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <nav aria-label="{{ __('dashboard.menu') }}" class="flex-1 px-3 pt-4">
                <p class="mb-3 px-5 text-xs font-extrabold uppercase tracking-[0.2em] text-brand-300" aria-hidden="true">{{ __('dashboard.menu') }}</p>
                <ul class="space-y-0.5">
                    @foreach ($menu as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" @if (request()->routeIs($item['active'])) aria-current="page" @endif class="{{ $sidebarLink }}">{{ __($item['label']) }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
            <span class="h-1.5 shrink-0 bg-brand-600" aria-hidden="true"></span>
        </aside>

        <div class="flex min-w-0 flex-col">
            <header class="sticky top-0 z-30 bg-lavender/95 backdrop-blur">
                <div class="px-4 py-3 lg:px-8">
                <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <button type="button" data-sidebar-toggle aria-controls="sidebar" aria-expanded="false" aria-label="{{ __('dashboard.open_menu') }}"
                                class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg bg-brand-600 text-white transition-colors hover:bg-brand-700 lg:hidden">
                            <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                        </button>
                        <a href="{{ route($user->role->homeRoute()) }}" class="hidden rounded sm:inline-flex lg:hidden">
                            <img src="{{ asset('images/brand/logo.svg') }}" alt="PT Metindo Perkasa" width="612" height="256" class="h-8 w-auto">
                        </a>
                    </div>

                    <div class="ms-auto flex items-center gap-1 sm:gap-2">
                        <x-dashboard.language-dropdown :action="route($area.'.locale')" />
                        <x-dashboard.user-menu :user="$user" :area="$area" />
                    </div>
                </div>
                </div>
            </header>

            <main id="main" class="flex-1 overflow-x-clip px-4 pb-12 pt-6 lg:px-8 lg:pt-8">
                <div class="mx-auto w-full max-w-6xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>
