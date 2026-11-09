<section id="catalog" class="scroll-mt-4 bg-lavender">
    <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
        <h2 data-anim="heading" class="max-w-4xl text-balance text-[clamp(2.25rem,6vw,5rem)] font-black leading-[0.98] tracking-tight text-ink">
            {{ __('home.catalog.title') }}
        </h2>

        <ul data-anim="rows" class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach (config('landing.categories') as $category)
                <li>
                    <a href="{{ route('landing.products.show', ['category' => $category['slug']]) }}"
                       class="group flex h-full min-h-44 flex-col justify-between rounded-lg border border-ink/20 bg-white p-6 text-ink hover:border-ink hover:bg-ink hover:text-white focus-visible:outline-offset-4">
                        <span class="text-sm font-bold text-brand-700 group-hover:text-brand-300" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="mt-8 flex items-end justify-between gap-3">
                            <span class="text-xl font-bold leading-snug" lang="en" dir="ltr">{{ $category['name'] }}</span>
                            <x-landing.arrow />
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>
