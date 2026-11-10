@extends('landing.layouts.app')

@section('title', __('landing.nav.services'))

@php
    $services = ['electroplating', 'chemical_supply', 'consultation'];
    $steps = ['enquiry', 'spec_review', 'process_supply', 'delivery'];
    $tones = ['bg-brand-600', 'bg-accent-indigo', 'bg-accent-blue'];
    $total = str_pad((string) count($services), 2, '0', STR_PAD_LEFT);
@endphp

@section('content')
    {{-- Opening: the headline, the intro, and a jump link to each service. --}}
    <section class="bg-ink text-white" style="--focus-ring: #ffffff">
        <div class="container-site pb-[clamp(3rem,6.67vw,8rem)] pt-[clamp(2.5rem,6vw,7.5rem)]">
            <h1 data-anim="hero-title" class="max-w-[12em] text-balance text-[clamp(2.75rem,7.2vw,9rem)] font-black uppercase leading-[0.92] tracking-tight">{{ __('pages.services.title') }}</h1>
            <div class="mt-[3vw] grid items-end gap-8 max-lg:mt-8 lg:grid-cols-[1fr_auto]">
                <p data-anim="hero-item" class="max-w-[34em] text-[clamp(1.05rem,1.5vw,1.9rem)] leading-[1.4] text-white/85">{{ __('pages.services.intro') }}</p>
                <nav data-anim="hero-item" aria-label="{{ __('pages.services.jump') }}">
                    <ul class="flex flex-wrap gap-3">
                        @foreach ($services as $service)
                            <li>
                                <a href="#service-{{ str_replace('_', '-', $service) }}" class="group inline-flex min-h-11 items-center gap-3 rounded-full border border-white/30 px-4 text-[clamp(0.85rem,0.95vw,1.2rem)] font-bold transition-colors duration-300 hover:border-white hover:bg-white hover:text-ink">
                                    <span class="text-[0.75em] text-brand-300 group-hover:text-brand-600" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    {{ __("home.services.items.{$service}.title") }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </div>
    </section>

    {{-- One block per service: the text on one side and its illustration on the other, switching sides from block to block. The illustrations are the same pictures as in the home page list. --}}
    @foreach ($services as $service)
        @php($slug = str_replace('_', '-', $service))
        <section id="service-{{ $slug }}" class="scroll-mt-[calc(var(--header-h)+1rem)] {{ $loop->odd ? 'bg-lavender' : 'bg-white' }} text-ink">
            <div class="container-site section-y grid items-center gap-10 lg:grid-cols-2 lg:gap-[5vw]">
                <div class="{{ $loop->even ? 'lg:order-2' : '' }}">
                    <p data-anim="heading" class="text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase text-brand-600">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ $total }}</p>
                    <h2 data-anim="grow" class="mt-[1.2vw] text-balance text-[clamp(2rem,4.6vw,5.75rem)] font-black uppercase leading-[0.95] tracking-tight max-lg:mt-3">{{ __("home.services.items.{$service}.title") }}</h2>
                    <p data-anim="heading" class="mt-[2vw] max-w-[30em] text-[clamp(1.05rem,1.4vw,1.75rem)] leading-[1.45] text-ink/75 max-lg:mt-5">{{ __("home.services.items.{$service}.text") }}</p>
                    <div data-anim="heading" class="mt-[2.4vw] flex flex-wrap items-center gap-x-6 gap-y-4 max-lg:mt-7">
                        <x-landing.button :href="route('landing.contact')">{{ __('landing.cta.button') }}</x-landing.button>
                        @unless ($service === 'consultation')
                            <a href="{{ route('landing.processes.index') }}" class="inline-flex min-h-11 items-center text-[clamp(0.875rem,1vw,1.25rem)] font-bold"><span class="uline">{{ __('pages.services.see_processes') }}</span></a>
                        @endunless
                    </div>
                </div>

                <div data-svc-art class="{{ $tones[$loop->index % count($tones)] }} {{ $loop->even ? 'lg:order-1' : '' }} relative aspect-[4/3] overflow-hidden rounded-lg" aria-hidden="true">
                    <div data-svc-art-inner class="absolute -inset-y-[9%] inset-x-0">
                        <x-landing.photo :name="'service-'.$slug" ratio="aspect-[4/3]" bare class="absolute inset-0 size-full items-center! justify-center! bg-transparent! text-center! text-white/80!" />
                    </div>
                    <span class="absolute bottom-0 start-[clamp(1rem,2vw,2.5rem)] block overflow-hidden text-[clamp(4rem,9vw,11rem)] font-black leading-[0.8] text-white/30"><span data-svc-number class="block">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></span>
                </div>
            </div>
        </section>
    @endforeach

    {{-- How we work: four steps on a line that is drawn as the page scrolls. --}}
    <section class="bg-ink text-white" style="--focus-ring: #ffffff">
        <div class="container-site section-y">
            <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
                <h2 data-anim="grow" class="max-w-[10em] text-balance text-[clamp(2rem,4.6vw,5.75rem)] font-black uppercase leading-[0.95] tracking-tight">{{ __('pages.services.how.title') }}</h2>
                <p data-anim="heading" class="max-w-[26em] text-[clamp(1rem,1.22vw,1.5rem)] leading-[1.45] text-white/80">{{ __('pages.services.how.intro') }}</p>
            </div>

            <ol data-svc-steps class="relative mt-[4vw] grid gap-10 max-lg:mt-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-[2vw]">
                <span data-svc-line class="pointer-events-none absolute start-0 top-[clamp(1.25rem,1.6vw,2rem)] hidden h-px w-full origin-left bg-white/40 lg:block" aria-hidden="true"></span>
                @foreach ($steps as $step)
                    <li data-svc-step class="relative">
                        <span class="relative grid size-[clamp(2.5rem,3.2vw,4rem)] place-items-center rounded-full bg-brand-600 text-[clamp(0.85rem,1vw,1.25rem)] font-black text-white">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="mt-[1.6vw] text-[clamp(1.25rem,1.7vw,2.1rem)] font-black uppercase tracking-tight max-lg:mt-5">{{ __("pages.services.how.steps.{$step}.title") }}</h3>
                        <p class="mt-[0.6vw] text-[clamp(0.95rem,1.05vw,1.35rem)] leading-snug text-white/75 max-lg:mt-2">{{ __("pages.services.how.steps.{$step}.text") }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <x-landing.cta />
@endsection
