@props(['title'])

@php
    $user = auth()->user();
    $menu = app(\App\Services\Dashboard\DashboardMenu::class)->forUser($user);
    $area = $user->role->usesStaffArea() ? 'app' : 'customer';
    $sidebarLink = 'flex min-h-12 items-center rounded-lg px-5 text-base font-bold text-ink/70 transition-colors hover:bg-lavender hover:text-ink '
        .'aria-[current=page]:bg-brand-600 aria-[current=page]:text-white';
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

    <header class="sticky top-0 z-50 h-16 border-b-4 border-brand-600 bg-ink text-white" style="--focus-ring: #ffffff">
        <div class="flex h-full items-center justify-between gap-3 px-4 lg:px-6">
            <div class="flex items-center gap-3">
                <button type="button" data-sidebar-toggle aria-controls="sidebar" aria-expanded="false" aria-label="{{ __('dashboard.open_menu') }}"
                        class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg bg-brand-600 text-white transition-colors hover:bg-brand-700 lg:hidden">
                    <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
                <a href="{{ route($user->role->homeRoute()) }}" class="brand-lockup inline-flex items-center" aria-label="PT Metindo Perkasa">
                    <img src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="h-8 w-auto">
                    <span class="hidden sm:block"><span class="brand-lockup__name"><span class="font-extrabold">Metindo</span> <span>Perkasa</span></span></span>
                </a>
            </div>

            <div class="flex items-center gap-1 sm:gap-2">
                <x-dashboard.language-dropdown :action="route($area.'.locale')" />
                <x-dashboard.user-menu :user="$user" :area="$area" />
            </div>
        </div>
    </header>

    <div class="lg:grid lg:min-h-[calc(100vh-4rem)] lg:grid-cols-[15rem_minmax(0,1fr)]">
        <div id="sidebar-backdrop" class="fixed inset-x-0 bottom-0 top-16 z-30 bg-ink/50 lg:hidden" data-sidebar-close aria-hidden="true"></div>

        <aside id="sidebar" class="fixed bottom-0 start-0 top-16 z-40 flex w-64 flex-col border-e border-ink/10 bg-white lg:sticky lg:top-16 lg:z-auto lg:h-[calc(100vh-4rem)] lg:w-auto">
            <div class="flex items-center justify-between px-3 pt-4 lg:hidden">
                <span class="px-5 text-xs font-extrabold uppercase tracking-[0.2em] text-brand-700" aria-hidden="true">{{ __('dashboard.menu') }}</span>
                <button type="button" data-sidebar-close class="inline-flex size-11 items-center justify-center rounded-lg hover:bg-lavender" aria-label="{{ __('dashboard.close_menu') }}">
                    <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <nav aria-label="{{ __('dashboard.menu') }}" class="flex-1 overflow-y-auto px-3 pb-6 pt-4">
                <p class="mb-3 hidden px-5 text-xs font-extrabold uppercase tracking-[0.2em] text-brand-700 lg:block" aria-hidden="true">{{ __('dashboard.menu') }}</p>
                <ul class="space-y-0.5">
                    @foreach ($menu as $item)
                        <li>
                            <a href="{{ route($item['route']) }}" @if (request()->routeIs($item['active'])) aria-current="page" @endif class="{{ $sidebarLink }}">{{ __($item['label']) }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </aside>

        <div class="flex min-w-0 flex-col">
            <main id="main" class="flex-1 overflow-x-clip px-4 pb-12 pt-6 lg:px-8 lg:pt-8">
                <div class="mx-auto w-full max-w-6xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>
