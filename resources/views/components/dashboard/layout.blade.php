@props(['title'])

@php
    $user = auth()->user();
    $groups = app(\App\Services\Dashboard\DashboardMenu::class)->groupsForUser($user);
    $area = $user->role->area();
    $sidebarLink = 'flex min-h-12 items-center gap-3 rounded-lg px-4 text-base font-bold text-white/70 transition-colors hover:bg-white/10 hover:text-white '
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

    <div class="lg:grid lg:min-h-screen lg:grid-cols-[18rem_minmax(0,1fr)]">
        <div id="sidebar-backdrop" class="fixed inset-0 z-40 bg-ink/50 lg:hidden" data-sidebar-close aria-hidden="true"></div>

        <aside id="sidebar" class="fixed inset-y-0 start-0 z-50 flex w-72 flex-col bg-ink text-white lg:sticky lg:top-0 lg:z-auto lg:h-screen lg:w-auto" style="--focus-ring: #ffffff">
            <div class="flex justify-end px-3 pt-3 lg:hidden">
                <button type="button" data-sidebar-close class="inline-flex size-11 items-center justify-center rounded-lg hover:bg-white/10" aria-label="{{ __('dashboard.close_menu') }}">
                    <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <div class="px-6 pb-6 pt-2 lg:pt-6">
                <a href="{{ route($user->role->homeRoute()) }}" class="brand-lockup inline-flex items-center" aria-label="PT Metindo Perkasa">
                    <img src="{{ asset('images/brand/logo-white.svg') }}" alt="" width="612" height="256" class="h-9 w-auto">
                    <span class="brand-lockup__name"><span class="font-extrabold">Metindo</span> <span>Perkasa</span></span>
                </a>
            </div>
            <nav aria-label="{{ __('dashboard.menu') }}" class="flex-1 space-y-6 overflow-y-auto px-4 pb-6 pt-2">
                @foreach ($groups as $group)
                    <div>
                        <p class="mb-2 px-4 text-xs font-extrabold uppercase tracking-[0.2em] text-white" id="nav-group-{{ $loop->index }}">{{ __($group['label']) }}</p>
                        <ul class="space-y-0.5" aria-labelledby="nav-group-{{ $loop->index }}">
                            @foreach ($group['items'] as $item)
                                <li>
                                    <a href="{{ route($item['route']) }}" @if (request()->routeIs($item['active'])) aria-current="page" @endif class="{{ $sidebarLink }}">
                                        <x-dashboard.icon :name="$item['icon']" />
                                        {{ __($item['label']) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>
            <span class="h-1.5 shrink-0 bg-brand-600" aria-hidden="true"></span>
        </aside>

        <div class="flex min-w-0 flex-col">
            <header class="sticky top-0 z-30 h-16 border-b border-ink/10 bg-white">
                <div class="flex h-full items-center justify-between gap-3 px-4 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" data-sidebar-toggle aria-controls="sidebar" aria-expanded="false" aria-label="{{ __('dashboard.open_menu') }}"
                                class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg bg-brand-600 text-white transition-colors hover:bg-brand-700 lg:hidden">
                            <svg viewBox="0 0 24 24" class="size-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                        </button>
                        <a href="{{ route($user->role->homeRoute()) }}" class="inline-flex lg:hidden" aria-label="PT Metindo Perkasa">
                            <img src="{{ asset('images/brand/logo.svg') }}" alt="" width="612" height="256" class="h-8 w-auto">
                        </a>
                    </div>

                    <div class="ms-auto flex items-center gap-1 sm:gap-2">
                        <x-dashboard.language-dropdown :action="route($area.'.locale')" />
                        <x-dashboard.user-menu :user="$user" :area="$area" />
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
    <dialog id="confirm-dialog" class="confirm" aria-labelledby="confirm-message">
        <form method="dialog" class="confirm__panel">
            <p id="confirm-message" class="confirm__message" data-confirm-message></p>
            <div class="confirm__actions">
                <button type="submit" value="cancel" class="row-action" autofocus>{{ __('dashboard.cancel') }}</button>
                <button type="submit" value="ok" class="row-action row-action--danger" data-confirm-ok></button>
            </div>
        </form>
    </dialog>
</body>
</html>
