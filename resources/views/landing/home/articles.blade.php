@if ($articles)
    <section class="cut-top bg-lavender">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
            <div class="max-w-2xl">
                <h2 data-anim="heading" class="text-3xl font-bold text-ink sm:text-5xl lg:text-6xl">{{ __('home.articles.title') }}</h2>
                <p class="mt-4 text-lg text-muted">{{ __('home.articles.intro') }}</p>
                <a href="{{ route('landing.articles.index') }}" class="mt-3 inline-flex min-h-11 items-center font-semibold text-brand-700 underline underline-offset-4">{{ __('home.services.all') }}</a>
            </div>

            <div data-anim="rows" class="mt-12 grid gap-x-12 gap-y-10 lg:grid-cols-12">
                @foreach ($articles as $article)
                    <article class="{{ $loop->first ? 'lg:col-span-7 lg:row-span-2' : 'lg:col-span-5' }} border-t-2 border-ink pt-6">
                        @if ($loop->first)
                            @if ($article['image'])
                                <img src="{{ $article['image'] }}" alt="{{ $article['alt'] }}" loading="lazy" decoding="async" class="mb-5 aspect-video w-full rounded-2xl object-cover">
                            @endif
                        @endif
                        <p class="text-sm font-semibold text-muted">
                            {{ __("articles.tags.{$article['type']}") }}
                            <span class="mx-1" aria-hidden="true">/</span>
                            <time datetime="{{ $article['date'] }}">{{ $article['date'] }}</time>
                        </p>
                        <h3 class="{{ $loop->first ? 'text-3xl sm:text-4xl' : 'text-2xl' }} mt-3 text-balance font-bold leading-tight text-ink">
                            <a href="{{ route('landing.articles.show', ['article' => $article['slug']]) }}" class="hover:text-brand-700">{{ $article['title'] }}</a>
                        </h3>
                        <p class="mt-4 max-w-xl text-lg text-muted">{{ $article['excerpt'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
