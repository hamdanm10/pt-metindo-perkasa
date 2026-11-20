@extends('landing.layouts.app')

@section('title', $service['title'])

@php
    $tones = ['bg-brand-600', 'bg-accent-indigo', 'bg-accent-blue'];
    $tint = $tones[$tone % count($tones)];
    $position = str_pad((string) ($tone + 1), 2, '0', STR_PAD_LEFT);
    $total = str_pad((string) (count($others) + 1), 2, '0', STR_PAD_LEFT);
@endphp

@section('content')
    {{-- Opening: the name and summary on the left, the service picture on the right in a tilted frame. --}}
    <section class="relative overflow-hidden bg-ink text-white" style="--focus-ring: #ffffff">
        <div class="container-site relative grid items-center gap-10 pb-14 pt-10 sm:pb-20 sm:pt-14 lg:grid-cols-12 lg:gap-[4vw] lg:pb-24 lg:pt-20">
            <div class="{{ $service['image'] ? 'lg:col-span-7' : 'lg:col-span-12' }}">
                <nav aria-label="{{ __('landing.nav.services') }}" class="mb-6 flex flex-wrap items-center gap-x-2 text-white/85">
                    <a href="{{ route('landing.services.index') }}" class="inline-flex min-h-11 items-center font-semibold underline underline-offset-4 hover:text-white">{{ __('landing.nav.services') }}</a>
                    <span aria-hidden="true" class="rtl:-scale-x-100">/</span>
                    <span aria-current="page">{{ __('pages.services.detail.label') }}</span>
                </nav>
                <p data-anim="hero-item" class="inline-flex items-center gap-3 text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase tracking-wider text-brand-300">
                    <span class="h-px w-10 bg-brand-300" aria-hidden="true"></span>
                    {{ $position }} / {{ $total }}
                </p>
                <h1 data-anim="hero-title" class="mt-4 max-w-4xl text-balance text-[clamp(2.5rem,6vw,7.5rem)] font-black uppercase leading-[0.95] tracking-tight">{{ $service['title'] }}</h1>
                @if ($service['summary'])
                    <p data-anim="hero-item" class="mt-6 max-w-2xl text-lg text-white/85 sm:text-xl">{{ $service['summary'] }}</p>
                @endif
                <div data-anim="hero-item" class="mt-8 flex flex-wrap gap-4">
                    <x-landing.number-picker :agents="$agents">{{ __('pages.services.detail.whatsapp') }}</x-landing.number-picker>
                    <x-landing.button :href="route('landing.contact')" variant="outline">{{ __('pages.services.detail.contact') }}</x-landing.button>
                </div>
            </div>

            @if ($service['image'])
                <div class="relative lg:col-span-5" aria-hidden="true">
                    <div class="absolute -inset-3 rotate-2 rounded-xl ring-1 ring-white/25 max-sm:hidden"></div>
                    <div class="{{ $tint }} relative aspect-[4/3] overflow-hidden rounded-xl shadow-2xl shadow-black/40">
                        <x-landing.service-image :service="$service" class="absolute inset-0 size-full" />
                    </div>
                </div>
            @endif
        </div>
    </section>

    <section class="bg-lavender text-ink">
        <div class="container-site section-y grid items-start gap-12 lg:grid-cols-12 lg:gap-[5vw]">
            <div class="space-y-[clamp(2.5rem,4vw,5rem)] lg:col-span-8">
                @if ($service['description'])
                    <p data-anim="heading" class="max-w-[34em] border-s-4 border-brand-600 ps-6 text-[clamp(1.15rem,1.6vw,2rem)] font-semibold leading-[1.45] text-ink">{{ $service['description'] }}</p>
                @endif

                @if ($service['included'] !== [])
                    <div>
                        <h2 data-anim="heading" class="text-[clamp(1.5rem,2.2vw,2.75rem)] font-black uppercase tracking-tight">{{ __('pages.services.detail.included') }}</h2>
                        <ul data-anim="rows" class="mt-6 grid gap-4 sm:grid-cols-2">
                            @foreach ($service['included'] as $item)
                                <li class="flex items-start gap-4 rounded-lg bg-white p-5 shadow-sm ring-1 ring-ink/10">
                                    <span class="grid size-10 shrink-0 place-items-center rounded-full bg-brand-600 text-sm font-black tabular-nums text-white" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="pt-1.5 text-lg font-bold leading-snug">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if ($service['fit'])
                    <div data-anim="card" class="rounded-lg bg-ink p-[clamp(1.5rem,2.6vw,3.25rem)] text-white">
                        <h2 class="text-sm font-extrabold uppercase tracking-wider text-brand-300">{{ __('pages.services.detail.fit') }}</h2>
                        <p class="mt-4 max-w-[34em] text-[clamp(1.15rem,1.5vw,1.9rem)] font-semibold leading-snug">{{ $service['fit'] }}</p>
                    </div>
                @endif
            </div>

            <aside class="lg:sticky lg:top-[calc(var(--header-h)+1.5rem)] lg:col-span-4">
                <div class="rounded-lg bg-white p-[clamp(1.5rem,2vw,2.5rem)] shadow-sm ring-1 ring-ink/10">
                    <span class="{{ $tint }} block h-1.5 w-16 rounded-full" aria-hidden="true"></span>
                    <h2 class="mt-5 text-2xl font-black tracking-tight">{{ __('pages.services.detail.talk') }}</h2>
                    <p class="mt-3 text-ink/80">{{ __('pages.services.detail.talk_text') }}</p>
                    <div class="mt-6 flex flex-wrap gap-4">
                        <x-landing.number-picker :agents="$agents">{{ __('pages.services.detail.whatsapp') }}</x-landing.number-picker>
                        <x-landing.button :href="route('landing.contact')" variant="dark">{{ __('pages.services.detail.contact') }}</x-landing.button>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    @include('landing.pages.services.steps')

    @if ($others !== [])
    <section class="bg-white text-ink">
        <div class="container-site section-y">
            <h2 data-anim="heading" class="text-[clamp(1.5rem,2.2vw,2.75rem)] font-black uppercase tracking-tight">{{ __('pages.services.others') }}</h2>
            <ul data-anim="rows" class="mt-8 grid gap-6 md:grid-cols-2">
                @foreach ($others as $other)
                    <li>
                        <a href="{{ route('landing.services.show', ['service' => $other['slug']]) }}" class="group flex h-full flex-col overflow-hidden rounded-lg bg-lavender ring-1 ring-ink/10 transition-shadow duration-300 hover:shadow-lg hover:ring-brand-700">
                            @if ($other['image'])
                                <span class="{{ $tones[($tone + $loop->iteration) % count($tones)] }} relative block aspect-[16/9] overflow-hidden" aria-hidden="true">
                                    <x-landing.service-image :service="$other" ratio="aspect-[16/9]" class="size-full transition-transform duration-500 group-hover:scale-105" />
                                </span>
                            @endif
                            <span class="flex flex-1 flex-col p-6">
                                <span class="block text-xl font-black group-hover:text-brand-700">{{ $other['title'] }}</span>
                                <span class="mt-2 block text-ink/75">{{ $other['summary'] ?? $other['description'] }}</span>
                                <span class="mt-auto inline-flex items-center gap-2 pt-5 font-bold">{{ __('home.services.more') }} <span aria-hidden="true" class="transition-transform duration-300 group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1">→</span></span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
    @endif

    <x-landing.cta />
@endsection
