@php
    $featured = array_slice(app(\App\Services\Landing\LandingContent::class)->categories(), 0, 4);
    $tones = ['bg-ink', 'bg-brand-600', 'bg-accent-indigo', 'bg-accent-blue'];
    // Until a category has its own photo (product-{slug}), a card uses one of the two existing landscape photos, zoomed on a different part so the four do not look alike.
    $photos = [
        'zinc-plating' => ['service-electroplating', ''],
        'nickel-plating' => ['service-chemical-supply', ''],
        'copper-plating' => ['service-chemical-supply', 'origin-[30%_82%] scale-[1.9]'],
        'hard-chrome' => ['service-electroplating', 'origin-[88%_46%] scale-[1.9]'],
    ];
    $count = count($featured);
@endphp

{{-- One section. On wide screens the four cards start as a cascade of landscape cards beside the heading, change the front card by themselves, and travel down to the row when the page scrolls on. In the row each card is a photo with the category's data under it. --}}
<section id="catalog" class="scroll-mt-4 bg-lavender">
    <div data-catalog-stage class="container-site relative py-16 lg:py-[clamp(4rem,7vw,7rem)]">
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-[4vw]">
            <div>
                <h2 data-anim="heading" class="text-balance text-[clamp(2.25rem,5vw,6rem)] font-black leading-[0.98] tracking-tight text-ink">
                    {{ __('home.catalog.title') }}
                </h2>
                <p class="mt-6 max-w-xl text-lg text-muted sm:text-xl">{{ __('home.catalog.text') }}</p>
            </div>

            {{-- Cascade: four landscape (4:3) cards of the same size in a 4:3 panel, each shifted right and down by the same 8.67%, stacked by z-index. Decorative, the row below holds the real links. --}}
            <div data-catalog-panel class="relative hidden aspect-[4/3] w-full justify-self-end lg:block lg:max-w-[min(100%,90svh)]" aria-hidden="true">
                <div data-deck class="absolute inset-0 origin-bottom">
                    @foreach ($featured as $category)
                        @php
                            [$photo, $zoom] = $photos[$category['slug']] ?? ['service-electroplating', ''];
                        @endphp
                        <article data-deck-card class="absolute aspect-[4/3] w-[74%]" style="inset-inline-start: {{ $loop->index * 8.67 }}%; top: {{ $loop->index * 8.67 }}%; z-index: {{ $count - $loop->index }}">
                            <div data-deck-media class="{{ $tones[$loop->index] }} relative size-full overflow-hidden rounded-lg shadow-[0_18px_40px_-18px_rgba(27,31,59,0.55)]">
                                <div class="{{ $zoom }} absolute inset-0">
                                    <x-landing.photo :name="'product-'.$category['slug']" :fallback="$photo" ratio="aspect-[4/3]" bare class="size-full bg-transparent! text-white!" />
                                </div>
                                <span class="absolute start-3 top-3 grid h-8 min-w-8 place-items-center rounded-md bg-white px-2 text-xs font-extrabold tabular-nums text-ink">{{ sprintf('%02d', $loop->iteration) }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        <ul class="mt-10 grid gap-6 sm:grid-cols-2 lg:mt-8 lg:grid-cols-4 lg:gap-[1.4vw]">
            @foreach ($featured as $category)
                @php
                    [$photo, $zoom] = $photos[$category['slug']] ?? ['service-electroplating', ''];
                    $processes = $category['processes'];
                    $shown = array_slice($processes, 0, 3);
                    $more = count($processes) - count($shown);
                @endphp
                <li>
                    {{-- The link must not be positioned: catalog.js measures where each cascade card lands with offsetLeft/offsetTop, which count from the nearest positioned ancestor (the stage). --}}
                    <a href="{{ route('landing.processes.show', ['category' => $category['slug']]) }}" class="group block focus-visible:outline-offset-4">
                        <div data-catalog-tile class="{{ $tones[$loop->index] }} relative z-10 aspect-[4/3] overflow-hidden rounded-lg">
                            <div class="{{ $zoom }} absolute inset-0">
                                <x-landing.photo :name="'product-'.$category['slug']" :fallback="$photo" ratio="aspect-[4/3]" bare class="size-full bg-transparent! text-white! transition-transform duration-700 ease-out group-hover:scale-105 motion-reduce:transition-none" />
                            </div>
                            <span class="absolute start-3 top-3 grid h-8 min-w-8 place-items-center rounded-md bg-white px-2 text-xs font-extrabold tabular-nums text-ink">{{ sprintf('%02d', $loop->iteration) }}</span>
                        </div>

                        {{-- The data panel is tucked under the photo's rounded corners, so photo and panel read as one card. --}}
                        <div data-catalog-caption class="relative -mt-4 flex flex-col rounded-b-lg bg-white px-5 pb-5 pt-8 shadow-[0_14px_30px_-20px_rgba(27,31,59,0.45)] ring-1 ring-ink/[0.07] transition-shadow duration-500 group-hover:shadow-[0_22px_40px_-18px_rgba(27,31,59,0.5)]">
                            <h3 class="border-b border-ink/10 pb-3 text-[clamp(1.05rem,1.3vw,1.4rem)] font-bold leading-snug text-ink transition-colors group-hover:text-brand-700" lang="en" dir="ltr">{{ $category['name'] }}</h3>

                            <p class="mt-3 flex items-baseline gap-2 text-sm text-muted">
                                <span class="text-[clamp(1.6rem,2vw,2.4rem)] font-black leading-none tabular-nums text-ink" dir="ltr">{{ count($processes) }}</span>
                                <span class="font-semibold uppercase tracking-wide text-[0.7rem]">{{ __('landing.nav.processes') }}</span>
                            </p>

                            <ul class="mt-4 flex flex-wrap gap-1.5" lang="en" dir="ltr">
                                @foreach ($shown as $process)
                                    <li class="rounded-full border border-ink/25 px-2.5 py-1 text-xs font-semibold leading-none text-ink">{{ $process['name'] }}</li>
                                @endforeach
                                @if ($more > 0)
                                    <li class="rounded-full bg-lavender px-2.5 py-1 text-xs font-bold leading-none text-brand-700">+{{ $more }}</li>
                                @endif
                            </ul>

                            <span class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-ink">
                                <span class="uline">{{ __('home.catalog.view') }}</span>
                                <span aria-hidden="true" class="nudge inline-block transition-transform duration-300 group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1">→</span>
                            </span>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>

        <x-landing.button :href="route('landing.processes.index')" data-catalog-action class="mt-10">{{ __('home.catalog.all') }}</x-landing.button>
    </div>
</section>
