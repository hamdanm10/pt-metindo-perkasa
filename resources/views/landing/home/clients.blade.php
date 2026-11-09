@php
    $slides = app(\App\Services\Landing\LandingClients::class)->slides();
    $testimonial = config('landing.sample_content');
@endphp

@if ($slides)
    <section data-clients data-clients-previous="{{ __('home.clients.previous') }}" class="bg-lavender text-ink">
        <div class="container-site section-y">
            <h2 data-anim="heading" class="text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase">{{ __('home.clients.eyebrow') }}</h2>

            <div class="mt-[2.8vw] text-[clamp(3rem,9vw,11rem)] font-black uppercase leading-[0.9] tracking-tight">
                {{-- The chip changes with the slide: its text slides and its width follows. --}}
                <div>
                    <span data-chip class="inline-flex h-[clamp(3rem,6.4vw,8rem)] items-center overflow-hidden rounded-lg bg-brand-600 px-[clamp(1.25rem,3.5vw,4rem)] align-middle text-[clamp(1.25rem,2.11vw,2.75rem)] font-bold normal-case leading-none tracking-normal text-white">
                        <span data-chip-text class="inline-block whitespace-nowrap">{{ $slides[0]['label'] }}</span>
                    </span>
                </div>
                <div data-anim="grow" class="mt-[0.4vw]">{{ __('home.clients.title') }}</div>
            </div>

            <div data-clients-stage class="relative mt-[3vw] lg:px-[3.9vw]">
                <div class="grid gap-5 lg:grid-cols-5 lg:gap-[3.1vw]">
                    <div class="{{ $testimonial ? 'lg:col-span-3' : 'lg:col-span-5' }}">
                        @foreach ($slides as $slide)
                            <ul data-clients-slide data-label="{{ $slide['label'] }}" @if (! $loop->first) hidden @endif
                                class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:gap-[2.5vw] {{ $testimonial ? '' : 'lg:grid-cols-5' }}">
                                @foreach ($slide['items'] as $item)
                                    <li data-client-cell class="relative flex aspect-[218/124] items-center justify-center rounded-lg border border-ink/60 p-5">
                                        @if ($item['src'])
                                            <img src="{{ $item['src'] }}" alt="{{ $item['name'] }}" loading="lazy" decoding="async" class="max-h-full max-w-full object-contain">
                                        @elseif ($slide['key'] === 'partners')
                                            <span class="text-center text-xl font-bold" lang="en" dir="ltr">{{ $item['name'] }}</span>
                                        @else
                                            <span class="text-xs font-semibold text-muted" lang="en" dir="ltr">[LOGO] {{ $slide['key'] }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>

                    @if ($testimonial)
                        {{-- Placeholder only: there is no real testimonial yet, and none is made up. --}}
                        <div class="flex min-h-64 items-start rounded-lg bg-accent-blue/10 p-8 text-sm font-semibold text-muted lg:col-span-2" lang="en" dir="ltr">[TESTIMONIAL] shown here when there is a real quote and name</div>
                    @endif
                </div>

                @if (count($slides) > 1)
                    <button type="button" data-clients-prev aria-label="{{ __('home.clients.previous') }}" class="clients-arrow absolute start-0 top-1/2 hidden size-12 -translate-y-1/2 items-center justify-center rounded-lg bg-brand-600 text-white hover:bg-brand-700 lg:flex">
                        <x-landing.arrow class="rotate-180" />
                    </button>
                    <button type="button" data-clients-next aria-label="{{ __('home.clients.next') }}" class="clients-arrow absolute end-0 top-1/2 hidden size-12 -translate-y-1/2 items-center justify-center rounded-lg bg-brand-600 text-white hover:bg-brand-700 lg:flex">
                        <x-landing.arrow />
                    </button>
                @endif

                @if (count($slides) > 1)
                    <div class="mt-6 flex gap-3 lg:hidden">
                        <button type="button" data-clients-prev aria-label="{{ __('home.clients.previous') }}" class="flex size-12 items-center justify-center rounded-lg bg-brand-600 text-white"><x-landing.arrow class="rotate-180" /></button>
                        <button type="button" data-clients-next aria-label="{{ __('home.clients.next') }}" class="flex size-12 items-center justify-center rounded-lg bg-brand-600 text-white"><x-landing.arrow /></button>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
