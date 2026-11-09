@php
    $featured = array_slice(config('landing.categories'), 0, 4);
    $tones = ['bg-ink', 'bg-brand-600', 'bg-accent-indigo', 'bg-accent-blue'];
@endphp

{{-- One section. On wide screens the four cards start as a stacked deck beside the heading, change the front card by themselves, and travel down to the row when the page scrolls on. --}}
<section id="catalog" class="scroll-mt-4 bg-lavender">
    <div data-catalog-stage class="container-site relative py-16 lg:py-[clamp(4rem,7vw,7rem)]">
        <div class="grid items-center gap-10 lg:grid-cols-2 lg:gap-[4vw]">
            <div>
                <h2 data-anim="heading" class="text-balance text-[clamp(2.25rem,5vw,6rem)] font-black leading-[0.98] tracking-tight text-ink">
                    {{ __('home.catalog.title') }}
                </h2>
                <p class="mt-6 max-w-xl text-lg text-muted sm:text-xl">{{ __('home.catalog.text') }}</p>
            </div>

            {{-- Deck: four cards of the same size, each shifted by 13.7% of the panel, stacked by z-index. Decorative, the row below holds the real links. --}}
            <div data-catalog-panel class="relative hidden aspect-square w-full justify-self-end lg:block lg:max-w-[min(100%,68svh)]" aria-hidden="true">
                <div data-deck class="absolute inset-0 origin-bottom">
                    @foreach ($featured as $category)
                        <article data-deck-card class="absolute top-0 h-full w-[58.9%]" style="inset-inline-start: {{ $loop->index * 13.7 }}%; z-index: {{ 4 - $loop->index }}">
                            <div data-deck-media class="{{ $tones[$loop->index] }} relative size-full overflow-hidden rounded-lg">
                                <x-landing.photo :name="'product-'.$category['slug']" ratio="aspect-[365/620]" bare class="absolute inset-0 size-full bg-transparent! text-white!" />
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>

        <ul class="mt-10 grid grid-cols-2 gap-4 lg:mt-8 lg:grid-cols-4">
            @foreach ($featured as $category)
                <li>
                    <a href="{{ route('landing.processes.show', ['category' => $category['slug']]) }}" class="group block focus-visible:outline-offset-4">
                        <div data-catalog-tile class="{{ $tones[$loop->index] }} relative aspect-[365/620] overflow-hidden rounded-lg">
                            <x-landing.photo :name="'product-'.$category['slug']" ratio="aspect-[365/620]" bare class="absolute inset-0 size-full bg-transparent! text-white!" />
                        </div>
                        <div data-catalog-caption class="mt-4">
                            <h3 class="text-xl font-bold leading-snug text-ink group-hover:text-brand-700" lang="en" dir="ltr">{{ $category['name'] }}</h3>
                            <ul class="mt-3 flex flex-wrap gap-2" lang="en" dir="ltr">
                                @foreach (array_slice($category['processes'], 0, 2) as $process)
                                    <li class="rounded-full border border-ink/40 px-3 py-1 text-xs font-semibold text-ink">{{ $process }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>

        <x-landing.button :href="route('landing.processes.index')" data-catalog-action class="mt-10">{{ __('home.catalog.all') }}</x-landing.button>
    </div>
</section>
