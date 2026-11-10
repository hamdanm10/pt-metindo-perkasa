@php
    $email = config('landing.contact.email');
    $phone = config('landing.contact.phone');
@endphp

@if ($email)
    {{-- A crimson card inside the page container: the call to action on the left, and on the right the three graphics of the About headline stacked, which move by themselves and follow the pointer. --}}
    <section class="bg-lavender text-ink">
        <div class="container-site pb-[clamp(3rem,6.67vw,8rem)] pt-[clamp(1rem,2vw,2.5rem)]">
            <div data-cta-card class="relative overflow-hidden rounded-lg bg-brand-600 px-[clamp(1.5rem,3.33vw,4rem)] py-[clamp(2.25rem,4.4vw,5.5rem)] text-white" style="--focus-ring: #ffffff">
                <div data-cta-glow class="pointer-events-none absolute -end-[12vw] -top-[18vw] size-[40vw] rounded-full bg-white/10 blur-3xl" aria-hidden="true"></div>

                <div class="relative grid items-center gap-[clamp(2rem,4vw,5rem)] lg:grid-cols-[1.25fr_1fr]">
                    <div>
                        <p data-anim="heading" class="text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase">{{ __('landing.nav.contact') }}</p>
                        <h2 data-anim="grow" class="mt-[1.4vw] max-w-[42rem] text-balance text-[clamp(2rem,5.2vw,6.5rem)] font-black uppercase leading-[0.95] tracking-tight max-lg:mt-4">{{ __('landing.cta.title') }}</h2>
                        <p data-anim="heading" class="mt-[1.6vw] max-w-[34rem] text-[clamp(1rem,1.22vw,1.5rem)] leading-[1.42] max-lg:mt-5">{{ __('landing.cta.text') }}</p>

                        <div data-anim="heading" class="mt-[2.2vw] flex flex-wrap items-center gap-x-[1.2vw] gap-y-4 max-lg:mt-8">
                            <x-landing.button :href="route('landing.contact')" variant="light">{{ __('landing.cta.button') }}</x-landing.button>
                            <x-landing.button :href="'mailto:'.$email" variant="outline" :arrow="false">{{ $email }}</x-landing.button>
                        </div>

                        @if ($phone)
                            <p data-anim="heading" class="mt-[1.6vw] text-[clamp(0.875rem,1vw,1.25rem)] font-bold max-lg:mt-5"><a href="tel:{{ preg_replace('/[^+\d]/', '', $phone) }}" dir="ltr" class="inline-flex"><span class="uline">{{ $phone }}</span></a></p>
                        @endif
                    </div>

                    {{-- The three graphics, stacked. Decorative. --}}
                    <div data-cta-art class="hidden space-y-[1.1vw] lg:block" aria-hidden="true">
                        <x-landing.about-art kind="bar" fluid on-brand />
                        <x-landing.about-art kind="chart" fluid on-brand />
                        <x-landing.about-art kind="hexagons" fluid on-brand />
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
