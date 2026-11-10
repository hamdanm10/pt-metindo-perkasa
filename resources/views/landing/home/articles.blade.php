@if ($articles)
    <section class="bg-white">
        <div class="container-site section-y">
            <div class="mx-auto flex max-w-[130rem] flex-col items-center text-center">
                <h2 data-anim="grow" class="text-balance text-[clamp(3rem,9vw,11rem)] font-black uppercase leading-[0.9] tracking-tight text-ink">{{ __('home.articles.title') }}</h2>
                <p data-anim="heading" class="mt-5 max-w-[54rem] text-[clamp(1rem,1.22vw,1.5rem)] text-muted">{{ __('home.articles.intro') }}</p>
            </div>

            <div class="mt-[2.8vw] grid grid-cols-1 gap-x-[3.6vw] gap-y-10 md:grid-cols-3">
                @foreach ($articles as $article)
                    <article>
                        <a href="{{ route('landing.articles.show', ['article' => $article['slug']]) }}" class="group flex h-full flex-col focus-visible:outline-offset-4">
                            <div class="relative aspect-[1/0.575] w-full overflow-hidden rounded-lg bg-lavender">
                                @if ($article['image'])
                                    <img src="{{ $article['image'] }}" alt="{{ $article['alt'] }}" loading="lazy" decoding="async" class="absolute inset-0 size-full object-cover transition-transform duration-700 [transition-timing-function:var(--header-ease)] group-hover:scale-105">
                                @endif
                            </div>
                            <div class="mt-5 flex items-center gap-x-4">
                                <span class="inline-flex h-[clamp(1.7rem,1.875vw,2.4rem)] items-center rounded-full border border-ink px-[clamp(0.6rem,0.67vw,0.9rem)] text-[clamp(0.7rem,0.78vw,1rem)]">{{ __("articles.tags.{$article['type']}") }}</span>
                                <time datetime="{{ $article['date'] }}" class="text-[clamp(0.75rem,0.83vw,1.05rem)] text-ink" dir="ltr">{{ \Illuminate\Support\Carbon::parse($article['date'])->format('d.m.y') }}</time>
                            </div>
                            <h3 class="mt-4 text-[clamp(1rem,1.22vw,1.5rem)] leading-snug text-ink sm:pe-12" @if ($article['sample']) lang="en" dir="ltr" @endif><span class="title-hover">{{ $article['title'] }}</span></h3>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="mt-16 flex justify-center">
                <x-landing.button :href="route('landing.articles.index')">{{ __('home.articles.all') }}</x-landing.button>
            </div>
        </div>
    </section>
@endif
