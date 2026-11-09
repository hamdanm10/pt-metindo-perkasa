@php
    $featured = array_slice(config('landing.categories'), 0, 4);
    $tones = ['bg-ink text-white', 'bg-brand-600 text-white', 'bg-accent-indigo text-white', 'bg-accent-blue text-white'];
@endphp

<section id="catalog" class="scroll-mt-4 bg-lavender">
    <div class="container-site section-y">
        <div data-catalog-stage class="relative">
            <div class="max-w-xl">
                <h2 data-anim="heading" class="text-balance text-[clamp(2.25rem,5vw,6rem)] font-black leading-[0.98] tracking-tight text-ink">
                    {{ __('home.catalog.title') }}
                </h2>
                <p class="mt-6 text-lg text-muted sm:text-xl">{{ __('home.catalog.text') }}</p>
            </div>

            <ul class="mt-12 grid grid-cols-2 gap-4 lg:mt-16 lg:grid-cols-4">
                @foreach ($featured as $category)
                    <li>
                        <a href="{{ route('landing.products.show', ['category' => $category['slug']]) }}" class="group block focus-visible:outline-offset-4">
                            <div data-catalog-tile class="{{ $tones[$loop->index % count($tones)] }} relative aspect-[3/4] overflow-hidden rounded-lg">
                                <div data-catalog-tile-content class="absolute inset-0 flex flex-col justify-between p-5 sm:p-6">
                                    <span class="text-4xl font-black sm:text-6xl" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <x-landing.arrow class="size-8 self-end" />
                                </div>
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
        </div>

        <a href="{{ route('landing.products.index') }}" class="mt-10 inline-flex min-h-12 items-center gap-2 rounded-lg bg-brand-600 px-6 font-bold text-white hover:bg-brand-700">
            {{ __('home.catalog.all') }}<x-landing.arrow />
        </a>
    </div>
</section>
