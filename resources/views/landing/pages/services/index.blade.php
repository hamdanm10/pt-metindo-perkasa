@extends('landing.layouts.app')

@section('title', __('landing.nav.services'))

@php
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
                                <a href="#service-{{ $service['slug'] }}" class="group inline-flex min-h-11 items-center gap-3 rounded-full border border-white/30 px-4 text-[clamp(0.85rem,0.95vw,1.2rem)] font-bold transition-colors duration-300 hover:border-white hover:bg-white hover:text-ink">
                                    <span class="text-[0.75em] text-brand-300 group-hover:text-brand-600" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    {{ $service['title'] }}
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
        @php($slug = $service['slug'])
        <section id="service-{{ $slug }}" class="scroll-mt-[calc(var(--header-h)+1rem)] overflow-x-clip {{ $loop->odd ? 'bg-lavender' : 'bg-white' }} text-ink">
            <div class="container-site section-y grid items-center gap-10 lg:grid-cols-2 lg:gap-[5vw]">
                <div class="relative {{ $loop->even ? 'lg:order-2' : '' }}">
                    <span class="pointer-events-none absolute -top-[3vw] end-0 select-none text-[clamp(7rem,17vw,21rem)] font-black leading-none text-ink/[0.05] max-lg:-top-6" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="relative">
                        <p data-anim="heading" class="text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase text-brand-600">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ $total }}</p>
                        <h2 data-anim="grow" class="mt-[1.2vw] text-balance text-[clamp(2rem,4.6vw,5.75rem)] font-black uppercase leading-[0.95] tracking-tight max-lg:mt-3">{{ $service['title'] }}</h2>
                        <p data-anim="heading" class="mt-[2vw] max-w-[30em] text-[clamp(1.05rem,1.4vw,1.75rem)] leading-[1.45] text-ink/75 max-lg:mt-5">{{ $service['description'] ?? $service['summary'] }}</p>
                        @if ($service['included'] !== [])
                            <ul data-anim="heading" class="mt-[1.8vw] flex flex-wrap gap-2 max-lg:mt-6" aria-label="{{ __('pages.services.detail.included') }}">
                                @foreach (array_slice($service['included'], 0, 4) as $item)
                                    <li class="inline-flex items-center gap-2 rounded-2xl border border-ink/15 {{ $loop->parent->odd ? 'bg-white' : 'bg-lavender' }} px-4 py-2 text-[clamp(0.85rem,0.95vw,1.2rem)] font-bold">
                                        <svg viewBox="0 0 20 20" fill="currentColor" class="size-4 shrink-0 text-brand-600" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 5.296a1 1 0 010 1.408l-7.5 7.5a1 1 0 01-1.408 0l-3.5-3.5a1 1 0 011.408-1.408L8.5 12.092l6.796-6.796a1 1 0 011.408 0z" clip-rule="evenodd"/></svg>
                                        {{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        <div data-anim="heading" class="mt-[2.4vw] flex flex-wrap items-center gap-x-6 gap-y-4 max-lg:mt-7">
                            <x-landing.button :href="route('landing.services.show', ['service' => $slug])">{{ __('home.services.more') }}</x-landing.button>
                        </div>
                    </div>
                </div>

                {{-- The picture sits on an offset plate, so the block has a front and a back instead of one flat card. --}}
                <div class="group/frame relative {{ $loop->even ? 'lg:order-1' : '' }}" aria-hidden="true">
                    <span data-svc-plate class="{{ $loop->odd ? 'bg-ink' : 'bg-lavender' }} absolute inset-0 rounded-lg ltr:translate-x-[4%] ltr:translate-y-[5%] rtl:-translate-x-[4%] rtl:translate-y-[5%]"></span>
                    <div data-svc-art class="{{ $tones[$loop->index % count($tones)] }} relative aspect-[4/3] overflow-hidden rounded-lg">
                        <div data-svc-art-inner class="absolute -inset-y-[9%] inset-x-0">
                            <x-landing.service-image :service="$service" class="absolute inset-0 size-full items-center! justify-center! bg-transparent! text-center! text-white/80! transition-transform duration-[1400ms] [transition-timing-function:var(--header-ease)] group-hover/frame:scale-105" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endforeach

    @include('landing.pages.services.steps')

    <x-landing.cta />
@endsection
