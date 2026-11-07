<section id="catalog" class="cut-top relative scroll-mt-4 overflow-hidden bg-ink text-white" style="--focus-ring: #ffffff">
    <x-landing.art name="slash-pattern" class="pointer-events-none absolute -top-6 end-0 hidden h-80 w-auto max-w-none lg:block rtl:-scale-x-100" />
    <div class="relative mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
        <h2 data-anim="heading" class="max-w-3xl text-balance text-3xl font-bold leading-tight sm:text-5xl">
            {{ __('home.catalog.title') }}
        </h2>

        <ul data-anim="rows" class="mt-14 grid gap-x-16 sm:grid-cols-2">
            @foreach (config('landing.categories') as $category)
                <li class="border-t border-white/20">
                    <a href="{{ route('landing.products.show', ['category' => $category['slug']]) }}" class="flex items-center gap-4 py-5 hover:underline hover:underline-offset-8">
                        <x-landing.category-icon :slug="$category['slug']" />
                        <span class="text-xl font-semibold sm:text-2xl" lang="en" dir="ltr">{{ $category['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>
