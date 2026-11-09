@php
    $slides = app(\App\Services\Landing\LandingClients::class)->slides();
    $testimonial = config('landing.sample_content');
@endphp

@if ($slides)
    <section data-clients data-clients-previous="{{ __('home.clients.previous') }}" class="bg-lavender text-ink">
        <div class="container-site section-y">
            <h2 data-anim="heading" class="text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase">{{ __('home.clients.eyebrow') }}</h2>

            <div class="mt-[2.8vw] text-[clamp(3rem,9vw,11rem)] font-black uppercase leading-[0.9] tracking-tight">
                {{-- "Our" and a chip beside it. The chip changes with the slide: its text slides and its width follows. --}}
                <div class="flex flex-wrap items-center gap-x-[2.1vw]">
                    @if (__('home.clients.lead') !== '')
                        <span data-anim="grow">{{ __('home.clients.lead') }}</span>
                    @endif
                    <span data-chip class="inline-flex h-[clamp(3rem,6.4vw,8rem)] items-center overflow-hidden rounded-lg bg-brand-600 px-[clamp(1.25rem,3.5vw,4rem)] align-middle text-[clamp(1.25rem,2.11vw,2.75rem)] font-bold normal-case leading-none tracking-normal text-white">
                        <span data-chip-text class="inline-block whitespace-nowrap">{{ $slides[0]['label'] }}</span>
                    </span>
                    @if (__('home.clients.trail') !== '')
                        <span data-anim="grow">{{ __('home.clients.trail') }}</span>
                    @endif
                </div>
                <div data-anim="grow" class="mt-[0.4vw]">{{ __('home.clients.title') }}</div>
            </div>

            <div data-clients-stage class="relative mt-[3vw] lg:px-[3.9vw]">
                <div class="grid gap-5 lg:grid-cols-5 lg:gap-[2.5vw]">
                    <div class="{{ $testimonial ? 'lg:col-span-3' : 'lg:col-span-5' }}">
                        @foreach ($slides as $slide)
                            <ul data-clients-slide data-label="{{ $slide['label'] }}" @if (! $loop->first) hidden @endif
                                class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:gap-[2.5vw] {{ $testimonial ? '' : 'lg:grid-cols-5' }}">
                                @foreach ($slide['items'] as $item)
                                    <li data-client-cell class="relative flex aspect-[218/124] items-center justify-center rounded-lg border border-ink">
                                        @if ($item['src'])
                                            <span class="flex aspect-[3/2] w-1/2 items-center justify-center"><img src="{{ $item['src'] }}" alt="{{ $item['name'] }}" loading="lazy" decoding="async" class="size-full object-contain"></span>
                                        @elseif ($slide['key'] === 'partners')
                                            <span class="text-center text-xl font-bold" lang="en" dir="ltr">{{ $item['name'] }}</span>
                                        @else
                                            <span class="text-xs font-semibold text-muted" lang="en" dir="ltr">[LOGO] client</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endforeach
                    </div>

                    @if ($testimonial)
                        {{-- Placeholder text only: there is no real testimonial yet, and none is made up. --}}
                        <figure class="flex flex-col justify-between rounded-lg bg-accent-blue/10 px-[clamp(1.25rem,2.22vw,2.75rem)] py-[clamp(1.5rem,2.78vw,3.5rem)] lg:col-span-2" lang="en" dir="ltr">
                            <div>
                                <svg class="h-[clamp(0.7rem,0.83vw,1.05rem)] w-auto" viewBox="0 0 30 17" fill="none" aria-hidden="true"><path fill="currentColor" d="M15.0742 0.109896L10.8054 16.6515H0.2668L7.4037 0.109896H15.0742ZM29.5481 0.109896L25.346 16.6515H14.7407L21.8776 0.109896H29.5481Z"/></svg>
                                <blockquote class="my-[1.39vw] text-[clamp(0.95rem,1.11vw,1.4rem)] leading-[1.42]">[SAMPLE] A short quote from a real client appears here, with the name and position of the person and the logo of the company, once there is one to show.</blockquote>
                                <svg class="ms-auto h-[clamp(0.7rem,0.83vw,1.05rem)] w-auto" viewBox="0 0 31 18" fill="none" aria-hidden="true"><path fill="currentColor" d="M15.2143 17.312L19.4831 0.770378H30.0217L22.8848 17.312H15.2143ZM0.740402 17.312L4.9425 0.770378H15.5478L8.4109 17.312H0.740402Z"/></svg>
                            </div>
                            <figcaption class="mt-[2.8vw] text-[clamp(0.75rem,0.83vw,1.05rem)] leading-[1.35]">
                                <div class="font-bold">[SAMPLE] Name</div>
                                <div>Position, Company</div>
                            </figcaption>
                        </figure>
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
