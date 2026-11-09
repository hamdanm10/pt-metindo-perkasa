@php
    $photos = app(\App\Services\Landing\LandingGallery::class)->slides();
    $count = count($photos);
    $cardWidth = 100 - 13.7 * max($count - 1, 0);
    $tones = ['bg-ink', 'bg-accent-indigo', 'bg-accent-blue', 'bg-brand-600'];
    $lines = __('home.about.lines');
    $line = 'flex items-center md:justify-between';
@endphp

{{-- About, as on the reference: a label, a three-line headline with a graphic in the gap of each line, then the company text and button beside a deck of photos that changes its front card by itself. The three graphics are still pictures; the reference animates them. --}}
<section class="bg-lavender text-ink">
    <div class="container-site section-y">
        <h2 data-anim="heading" class="text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase">{{ __('home.about.label') }}</h2>

        <p class="mt-[3.4vw] text-[clamp(2.25rem,9.06vw,11rem)] font-black uppercase leading-[0.9] tracking-tight">
            <span class="{{ $line }}">
                <span data-anim="grow" class="inline-block">{{ $lines[0] }}</span>
                <x-landing.about-art kind="bar" class="ms-auto md:ms-0" />
            </span>
            <span class="{{ $line }} flex-row-reverse md:flex-row">
                <x-landing.about-art kind="chart" />
                <span data-anim="grow" class="inline-block">{{ $lines[1] }}</span>
            </span>
            <span class="{{ $line }}">
                <span data-anim="grow" class="inline-block">{{ $lines[2] }}</span>
                <x-landing.about-art kind="hexagons" />
            </span>
        </p>

        <div class="mt-8 grid lg:mt-[3.3vw] items-start gap-10 lg:grid-cols-[1fr_46.1%] lg:gap-[2.5vw]">
            <div>
                <p data-anim="heading" class="max-w-[38.9vw] text-[clamp(1rem,1.11vw,1.4rem)] leading-[1.35] max-lg:max-w-xl">{{ __('home.about.text') }}</p>
                <x-landing.button :href="route('landing.about')" data-anim="heading" class="mt-[1.4vw] max-lg:mt-6">{{ __('landing.nav.about') }}</x-landing.button>
            </div>

            @if ($count > 0)
                {{-- Deck: the cards have the same size, each shifted by 13.7% of the panel and stacked by z-index, like the deck at the top of the page. --}}
                <div data-about-deck-panel class="relative aspect-square w-full" aria-hidden="true">
                    <div data-deck class="absolute inset-0 origin-bottom">
                        @foreach ($photos as $photo)
                            <article data-deck-card class="absolute top-0 h-full" style="width: {{ $cardWidth }}%; inset-inline-start: {{ $loop->index * 13.7 }}%; z-index: {{ $count - $loop->index }}">
                                <div data-deck-media class="{{ $tones[$loop->index % count($tones)] }} relative size-full overflow-hidden rounded-lg">
                                    <x-landing.photo :name="'gallery-'.$photo['key']" ratio="aspect-[365/620]" bare class="absolute inset-0 size-full bg-transparent! text-white!" />
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
