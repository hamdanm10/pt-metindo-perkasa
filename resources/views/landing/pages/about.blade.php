@extends('landing.layouts.app')

@section('title', __('landing.nav.about'))

@section('content')
    {{-- Opening: a large headline, the intro, and two ways on. --}}
    <section class="bg-ink text-white" style="--focus-ring: #ffffff">
        <div class="container-site pb-[clamp(3rem,6.67vw,8rem)] pt-[clamp(2.5rem,6vw,7.5rem)]">
            <h1 data-anim="hero-title" class="max-w-[14em] text-balance text-[clamp(2.75rem,7.2vw,9rem)] font-black uppercase leading-[0.92] tracking-tight">{{ __('pages.about.title') }}</h1>
            <div class="mt-[3vw] grid items-end gap-8 max-lg:mt-8 lg:grid-cols-[1fr_auto]">
                <p data-anim="hero-item" class="max-w-[38.9vw] text-[clamp(1.05rem,1.5vw,1.9rem)] leading-[1.4] text-white/85 max-lg:max-w-2xl">{{ __('pages.about.intro') }}</p>
                <div data-anim="hero-item" class="flex flex-wrap gap-4">
                    <x-landing.button :href="route('landing.processes.index')">{{ __('landing.nav.processes') }}</x-landing.button>
                    <x-landing.button :href="route('landing.services')" variant="outline">{{ __('landing.nav.services') }}</x-landing.button>
                </div>
            </div>
        </div>
    </section>

    {{-- Approach: the heading stays beside the text while it is read. --}}
    <section class="bg-lavender text-ink">
        <div class="container-site section-y grid gap-10 lg:grid-cols-[5fr_7fr] lg:gap-[5vw]">
            <div class="lg:sticky lg:top-[calc(var(--header-h)+2rem)] lg:self-start">
                <h2 data-anim="grow" class="text-balance text-[clamp(2rem,4.6vw,5.75rem)] font-black uppercase leading-[0.95] tracking-tight">{{ __('pages.about.approach.title') }}</h2>
            </div>
            <div>
                <div class="space-y-[1.6vw] text-[clamp(1.05rem,1.5vw,1.9rem)] leading-[1.45] max-lg:space-y-5">
                    <p data-anim="heading">{{ __('pages.about.approach.first') }}</p>
                    <p data-anim="heading" class="text-ink/75">{{ __('pages.about.approach.second') }}</p>
                </div>
                <x-landing.photo name="about-company" ratio="aspect-[16/10]" class="mt-[3vw] rounded-lg max-lg:mt-8" />
            </div>
        </div>
    </section>

    {{-- Vision and mission: two large numbered cards. --}}
    <section class="bg-lavender pb-[clamp(3rem,6.67vw,8rem)] text-white">
        <div class="container-site grid gap-[1.5vw] max-lg:gap-5 lg:grid-cols-[3fr_2fr]">
            <article data-anim="card" class="group flex min-h-[clamp(18rem,26vw,34rem)] flex-col justify-between rounded-lg bg-brand-600 p-[clamp(1.5rem,3.33vw,4rem)] transition-transform duration-700 [transition-timing-function:var(--header-ease)] hover:-translate-y-1" style="--focus-ring: #ffffff">
                <span class="text-[clamp(3rem,7vw,9rem)] font-black leading-none text-white/25 transition-colors duration-500 group-hover:text-white/45" aria-hidden="true">01</span>
                <div>
                    <h2 class="text-[clamp(1.5rem,2.6vw,3.25rem)] font-black uppercase tracking-tight">{{ __('pages.about.vision.title') }}</h2>
                    <p class="mt-[1vw] max-w-[28em] text-[clamp(1.05rem,1.4vw,1.75rem)] leading-[1.4] max-lg:mt-4">{{ __('pages.about.vision.text') }}</p>
                </div>
            </article>
            <article data-anim="card" class="group flex min-h-[clamp(18rem,26vw,34rem)] flex-col justify-between rounded-lg bg-ink p-[clamp(1.5rem,3.33vw,4rem)] transition-transform duration-700 [transition-timing-function:var(--header-ease)] hover:-translate-y-1" style="--focus-ring: #ffffff">
                <span class="text-[clamp(3rem,7vw,9rem)] font-black leading-none text-white/20 transition-colors duration-500 group-hover:text-white/40" aria-hidden="true">02</span>
                <div>
                    <h2 class="text-[clamp(1.5rem,2.6vw,3.25rem)] font-black uppercase tracking-tight">{{ __('pages.about.mission.title') }}</h2>
                    <p class="mt-[1vw] text-[clamp(1.05rem,1.4vw,1.75rem)] leading-[1.4] max-lg:mt-4">{{ __('pages.about.mission.text') }}</p>
                </div>
            </article>
        </div>
    </section>

    {{-- Facilities --}}
    <section class="bg-white text-ink">
        <div class="container-site section-y grid items-center gap-10 lg:grid-cols-[7fr_5fr] lg:gap-[5vw]">
            <x-landing.photo name="about-facilities" ratio="aspect-video" class="rounded-lg max-lg:order-2" />
            <div>
                <h2 data-anim="grow" class="text-balance text-[clamp(2rem,4.6vw,5.75rem)] font-black uppercase leading-[0.95] tracking-tight">{{ __('pages.about.facilities.title') }}</h2>
                <p data-anim="heading" class="mt-[2vw] text-[clamp(1.05rem,1.5vw,1.9rem)] leading-[1.45] text-ink/75 max-lg:mt-6">{{ __('pages.about.facilities.text') }}</p>
            </div>
        </div>
    </section>

    {{-- Subsidiaries: the pins on the map and the cards below answer each other when one is hovered or focused. --}}
    @if ($subsidiaries)
        <section class="bg-ink text-white" style="--focus-ring: #ffffff">
            <div class="container-site section-y">
                <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-5">
                    <h2 data-anim="grow" class="text-[clamp(2rem,4.6vw,5.75rem)] font-black uppercase leading-[0.95] tracking-tight">{{ __('pages.about.subsidiaries.title') }}</h2>
                    <p data-anim="heading" class="max-w-[30em] text-[clamp(1rem,1.22vw,1.5rem)] leading-[1.45] text-white/80">{{ __('pages.about.subsidiaries.intro') }}</p>
                </div>
                <p class="mt-5 inline-block rounded-lg border border-white/30 px-4 py-2 text-sm font-semibold">{{ __('landing.sample_note') }}</p>

                <div data-map-stage class="mt-[3vw] max-lg:mt-8">
                    <x-landing.indonesia-map :label="__('pages.about.subsidiaries.map')">
                        @foreach ($subsidiaries as $subsidiary)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $subsidiary['lat'] }},{{ $subsidiary['lng'] }}" target="_blank" rel="noopener noreferrer"
                               data-pin="{{ $subsidiary['key'] }}" aria-label="{{ $subsidiary['name'] }}, {{ ucfirst($subsidiary['key']) }} — {{ __('pages.about.subsidiaries.open_map') }}"
                               style="left: {{ $subsidiary['left'] }}%; top: {{ $subsidiary['top'] }}%"
                               class="map-pin group absolute z-10 -translate-x-1/2 -translate-y-full rounded-full p-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                                <span class="map-pin__ring" aria-hidden="true"></span>
                                <svg viewBox="0 0 24 24" fill="currentColor" class="map-pin__icon relative size-6 text-brand-500 drop-shadow sm:size-8 lg:size-10" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z"/>
                                </svg>
                                <span class="map-pin__label" lang="en" dir="ltr" aria-hidden="true">{{ ucfirst($subsidiary['key']) }}</span>
                            </a>
                        @endforeach
                    </x-landing.indonesia-map>

                    <ul class="mt-[3vw] grid gap-[1.5vw] max-lg:mt-8 max-lg:gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($subsidiaries as $subsidiary)
                            <li data-sub="{{ $subsidiary['key'] }}" class="map-card rounded-lg border border-white/20 p-[clamp(1.25rem,1.8vw,2.25rem)]">
                                <span class="text-[clamp(0.75rem,0.833vw,1.05rem)] font-extrabold text-brand-300" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="mt-[0.8vw] text-[clamp(1.1rem,1.5vw,1.9rem)] font-bold leading-snug max-lg:mt-3" lang="en" dir="ltr">{{ $subsidiary['name'] }}</h3>
                                <p class="mt-[0.4vw] text-[clamp(0.875rem,1vw,1.25rem)] text-white/70 max-lg:mt-2">{{ __("pages.about.subsidiaries.items.{$subsidiary['key']}") }}</p>
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $subsidiary['lat'] }},{{ $subsidiary['lng'] }}" target="_blank" rel="noopener noreferrer"
                                   class="mt-[1.2vw] inline-flex min-h-11 items-center text-[clamp(0.875rem,1vw,1.25rem)] font-bold max-lg:mt-4"><span class="uline">{{ __('pages.about.subsidiaries.open_map') }}</span><span aria-hidden="true" class="ms-2 rtl:-scale-x-100">↗</span></a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    @endif

    <x-landing.cta />
@endsection
