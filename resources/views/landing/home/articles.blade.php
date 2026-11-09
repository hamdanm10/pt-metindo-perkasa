@if ($articles)
    <section class="bg-white">
        <div class="container-site section-y">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div class="max-w-2xl">
                    <h2 data-anim="heading" class="text-balance text-[clamp(2.25rem,5vw,6rem)] font-black uppercase leading-[0.98] tracking-tight text-ink">{{ __('home.articles.title') }}</h2>
                    <p class="mt-5 text-lg text-muted sm:text-xl">{{ __('home.articles.intro') }}</p>
                </div>
                <a href="{{ route('landing.articles.index') }}" class="inline-flex min-h-12 items-center gap-2 rounded-lg border-2 border-ink px-6 font-bold text-ink hover:bg-ink hover:text-white">
                    {{ __('home.services.all') }}<x-landing.arrow />
                </a>
            </div>

            <div data-anim="rows" class="mt-14 grid gap-8 md:grid-cols-3">
                @foreach ($articles as $article)
                    <article>
                        <a href="{{ route('landing.articles.show', ['article' => $article['slug']]) }}" class="group block">
                            @if ($article['image'])
                                <img src="{{ $article['image'] }}" alt="{{ $article['alt'] }}" loading="lazy" decoding="async" class="aspect-video w-full rounded-lg object-cover">
                            @endif
                            <p class="mt-5 text-sm font-semibold text-muted">
                                {{ __("articles.tags.{$article['type']}") }}
                                <span class="mx-1" aria-hidden="true">/</span>
                                <time datetime="{{ $article['date'] }}">{{ $article['date'] }}</time>
                            </p>
                            <h3 class="mt-2 text-balance text-2xl font-bold leading-tight text-ink group-hover:text-brand-700">{{ $article['title'] }}</h3>
                            <p class="mt-3 text-muted">{{ $article['excerpt'] }}</p>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
