@unless (app()->isProduction())
    @php($articles = ['quality_control', 'pre_treatment', 'alkaline_zinc'])

    <section class="bg-lavender">
        <div class="mx-auto max-w-6xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
            <div class="max-w-2xl">
                <h2 class="text-3xl font-bold text-ink sm:text-4xl">{{ __('home.articles.title') }}</h2>
                <p class="mt-4 text-lg text-muted">{{ __('home.articles.intro') }}</p>
                <p class="mt-4 inline-block rounded-lg border border-ink/30 px-4 py-2 text-sm font-semibold text-ink">
                    {{ __('home.articles.sample') }}
                </p>
            </div>

            <div class="mt-12 grid gap-x-12 gap-y-10 lg:grid-cols-12">
                @foreach ($articles as $article)
                    @php($tag = __("home.articles.items.{$article}.tag"))
                    <article class="{{ $loop->first ? 'lg:col-span-7 lg:row-span-2' : 'lg:col-span-5' }} border-t-2 border-ink pt-6">
                        <p class="text-sm font-semibold text-muted">
                            {{ __("home.articles.tags.{$tag}") }}
                            <span class="mx-1" aria-hidden="true">/</span>
                            <time datetime="{{ __("home.articles.items.{$article}.date") }}">{{ __("home.articles.items.{$article}.date") }}</time>
                        </p>
                        <h3 class="{{ $loop->first ? 'text-3xl sm:text-4xl' : 'text-2xl' }} mt-3 text-balance font-bold leading-tight text-ink">
                            {{ __("home.articles.items.{$article}.title") }}
                        </h3>
                        <p class="mt-4 max-w-xl text-lg text-muted">{{ __("home.articles.items.{$article}.text") }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endunless
