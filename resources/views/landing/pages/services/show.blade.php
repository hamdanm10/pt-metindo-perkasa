@extends('landing.layouts.app')

@section('title', $service['title'])

@php
    $tones = ['bg-brand-600', 'bg-accent-indigo', 'bg-accent-blue'];
@endphp

@section('content')
    <x-landing.page-header :title="$service['title']" :intro="$service['summary']">
        <nav aria-label="{{ __('landing.nav.services') }}" class="mb-6 flex flex-wrap items-center gap-x-2 text-white">
            <a href="{{ route('landing.services.index') }}" class="inline-flex min-h-11 items-center font-semibold underline underline-offset-4">{{ __('landing.nav.services') }}</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ __('pages.services.detail.label') }}</span>
        </nav>
    </x-landing.page-header>

    <section class="bg-lavender text-ink">
        <div class="container-site section-y grid items-start gap-10 lg:grid-cols-12 lg:gap-[5vw]">
            <div class="lg:col-span-7">
                @if ($service['description'])
                    <p data-anim="heading" class="max-w-[34em] text-[clamp(1.05rem,1.4vw,1.75rem)] leading-[1.45] text-ink/80">{{ $service['description'] }}</p>
                @endif

                @if ($service['included'] !== [])
                    <h2 data-anim="heading" class="mt-[3vw] text-2xl font-black tracking-tight max-lg:mt-10">{{ __('pages.services.detail.included') }}</h2>
                    <ul data-anim="rows" class="mt-5">
                        @foreach ($service['included'] as $item)
                            <li class="flex items-baseline gap-4 border-t border-ink/20 py-4 text-lg font-semibold last:border-b">
                                <span class="text-sm font-extrabold tabular-nums text-brand-600" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($service['fit'])
                    <h2 data-anim="heading" class="mt-[3vw] text-2xl font-black tracking-tight max-lg:mt-10">{{ __('pages.services.detail.fit') }}</h2>
                    <p data-anim="heading" class="mt-3 max-w-[34em] text-lg leading-snug text-ink/80">{{ $service['fit'] }}</p>
                @endif
            </div>

            <div class="space-y-6 lg:sticky lg:top-[calc(var(--header-h)+1.5rem)] lg:col-span-5">
                <div class="{{ $tones[$tone % count($tones)] }} relative aspect-[4/3] overflow-hidden rounded-lg" aria-hidden="true">
                    <x-landing.service-image :service="$service" class="absolute inset-0 size-full items-center! justify-center! bg-transparent! text-center! text-white/80!" />
                </div>

                <div class="rounded-lg bg-ink p-[clamp(1.5rem,2.4vw,3rem)] text-white" style="--focus-ring: #ffffff">
                    <h2 class="text-2xl font-black tracking-tight">{{ __('pages.services.detail.talk') }}</h2>
                    <p class="mt-3 text-white/80">{{ __('pages.services.detail.talk_text') }}</p>
                    <div class="mt-6 flex flex-wrap gap-4">
                        <x-landing.number-picker :agents="$agents" mode="whatsapp">{{ __('pages.services.detail.whatsapp') }}</x-landing.number-picker>
                        <x-landing.button :href="route('landing.contact')" variant="outline">{{ __('pages.services.detail.contact') }}</x-landing.button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('landing.pages.services.steps')

    @if ($others !== [])
    <section class="bg-white text-ink">
        <div class="container-site section-y">
            <h2 data-anim="heading" class="text-2xl font-black tracking-tight">{{ __('pages.services.others') }}</h2>
            <ul data-anim="rows" class="mt-6 grid gap-6 md:grid-cols-2">
                @foreach ($others as $other)
                    <li>
                        <a href="{{ route('landing.services.show', ['service' => $other['slug']]) }}" class="group block h-full rounded-lg border border-ink/20 p-6 hover:border-brand-700">
                            <span class="block text-xl font-black group-hover:text-brand-700">{{ $other['title'] }}</span>
                            <span class="mt-2 block text-ink/75">{{ $other['summary'] ?? $other['description'] }}</span>
                            <span class="mt-4 inline-block font-bold">{{ __('home.services.more') }} →</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
    @endif

    <x-landing.cta />
@endsection
