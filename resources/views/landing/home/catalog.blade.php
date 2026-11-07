<section id="catalog" class="scroll-mt-4 bg-ink text-white" style="--focus-ring: #ffffff">
    <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
        <h2 class="max-w-3xl text-balance text-3xl font-bold leading-tight sm:text-5xl">
            {{ __('home.catalog.title') }}
        </h2>

        <ul class="mt-14 grid gap-x-16 sm:grid-cols-2">
            @foreach (config('landing.categories') as $category)
                <li class="flex items-center gap-4 border-t border-white/20 py-5">
                    <x-landing.art :name="'icon-'.$category['slug']" class="size-10 shrink-0" />
                    <span class="text-xl font-semibold sm:text-2xl" lang="en" dir="ltr">{{ $category['name'] }}</span>
                </li>
            @endforeach
        </ul>
    </div>
</section>
