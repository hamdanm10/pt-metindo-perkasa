@extends('landing.layouts.app')

@section('title', $search !== null ? __('pages.articles.search.title', ['query' => $search]) : __('landing.nav.articles'))

@section('content')
    @php
        $filters = [['value' => null, 'key' => 'all'], ['value' => 'article', 'key' => 'article'], ['value' => 'news', 'key' => 'news']];
        // Only page 1 has the large newest entry above the grid; later pages are grids of their own.
        $featured = $page === 1 ? ($articles[0] ?? null) : null;
        $rest = $page === 1 ? array_slice($articles, 1) : $articles;
    @endphp

    @push('head')
        {{-- A search result list is different for every word typed, so it stays out of the search engines. --}}
        @if ($search !== null)
            <meta name="robots" content="noindex, follow">
        @endif
        @if ($page > 1)
            <link rel="prev" href="{{ route('landing.articles.index', array_filter(['q' => $search, 'type' => $type, 'page' => $page > 2 ? $page - 1 : null])) }}">
        @endif
        @if ($page < $lastPage)
            <link rel="next" href="{{ route('landing.articles.index', array_filter(['q' => $search, 'type' => $type, 'page' => $page + 1])) }}">
        @endif
    @endpush

    {{-- Opening: the headline, the intro, and the filter. --}}
    <section class="bg-ink text-white" style="--focus-ring: #ffffff">
        <div class="container-site pb-[clamp(3rem,6.67vw,8rem)] pt-[clamp(2.5rem,6vw,7.5rem)]">
            <h1 data-anim="hero-title" class="max-w-[12em] text-balance text-[clamp(2.75rem,7.2vw,9rem)] font-black uppercase leading-[0.92] tracking-tight">{{ __('pages.articles.title') }}</h1>
            <p data-anim="hero-item" class="mt-[3vw] max-w-[34em] text-[clamp(1.05rem,1.5vw,1.9rem)] leading-[1.4] text-white/85 max-lg:mt-8">{{ __('pages.articles.intro') }}</p>

            {{-- Toolbar: the type filter on one side, the search on the other (the search comes first on a phone). --}}
            <div data-anim="hero-item" class="mt-[3.5vw] flex flex-col gap-6 border-t border-white/20 pt-[clamp(1.25rem,2vw,2.5rem)] max-lg:mt-10 lg:flex-row lg:items-center lg:justify-between">
                {{-- A plain GET form, so it works without JavaScript and the result address can be shared. --}}
                <form role="search" method="get" action="{{ route('landing.articles.index') }}" class="relative w-full lg:order-2 lg:max-w-[min(30rem,40%)] lg:flex-1">
                    @if ($type !== null)
                        <input type="hidden" name="type" value="{{ $type }}">
                    @endif
                    <label for="article-search" class="sr-only">{{ __('pages.articles.search.label') }}</label>
                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="pointer-events-none absolute start-5 top-1/2 size-5 -translate-y-1/2 text-white/60"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
                    <input id="article-search" type="search" name="q" value="{{ $search }}" maxlength="100" autocomplete="off" placeholder="{{ __('pages.articles.search.placeholder') }}"
                           class="h-12 w-full rounded-full border border-white/30 bg-white/5 ps-12 pe-28 text-[clamp(1rem,1.1vw,1.35rem)] text-white transition-colors duration-300 placeholder:text-white/60 hover:border-white/60 focus:border-white [&::-webkit-search-cancel-button]:hidden">
                    <button type="submit" class="absolute end-1.5 top-1/2 inline-flex h-9 -translate-y-1/2 items-center rounded-full bg-white px-5 text-[clamp(0.8rem,0.9vw,1.1rem)] font-bold text-ink transition-colors duration-300 hover:bg-brand-600 hover:text-white">{{ __('pages.articles.search.submit') }}</button>
                </form>

                <nav aria-label="{{ __('pages.articles.title') }}" class="lg:order-1">
                    <ul class="flex flex-wrap gap-3">
                        @foreach ($filters as $filter)
                            @php($active = $type === $filter['value'])
                            <li>
                                <a href="{{ route('landing.articles.index', array_filter(['q' => $search, 'type' => $filter['value']])) }}" @if ($active) aria-current="true" @endif
                                   class="inline-flex min-h-11 items-center gap-2 rounded-full border px-5 text-[clamp(0.85rem,0.95vw,1.2rem)] font-bold transition-colors duration-300 {{ $active ? 'border-white bg-white text-ink' : 'border-white/30 hover:border-white hover:bg-white hover:text-ink' }}">
                                    {{ __("pages.articles.filters.{$filter['key']}") }}
                                    <span class="text-[0.75em] {{ $active ? 'text-brand-600' : 'text-brand-300' }}">{{ $counts[$filter['key']] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>

            @if ($search !== null)
                <p data-search-summary role="status" class="mt-6 text-[clamp(0.95rem,1.05vw,1.3rem)] text-white/85">
                    {{ trans_choice('pages.articles.search.results', $counts[$type ?? 'all'], ['count' => $counts[$type ?? 'all'], 'query' => $search]) }}
                    <a href="{{ route('landing.articles.index', array_filter(['type' => $type])) }}" class="ms-2 inline-flex min-h-11 items-center font-bold underline underline-offset-4">{{ __('pages.articles.search.clear') }}</a>
                </p>
            @endif
        </div>
    </section>

    <section class="bg-lavender text-ink">
        <div class="container-site section-y">
            @if ($articles !== [])
                @if ($featured)
                    {{-- The newest item, large. --}}
                    <a href="{{ route('landing.articles.show', ['article' => $featured['slug']]) }}" data-art-featured class="group grid items-center gap-[2.5vw] max-lg:gap-6 lg:grid-cols-[7fr_5fr] focus-visible:outline-offset-4">
                        <span data-art-featured-media class="relative block aspect-[16/10] overflow-hidden rounded-lg bg-white">
                            @if ($featured['image'])
                                <img src="{{ $featured['image'] }}" alt="{{ $featured['alt'] }}" loading="eager" decoding="async" class="absolute inset-0 size-full object-cover transition-transform duration-1000 [transition-timing-function:var(--header-ease)] group-hover:scale-105">
                            @endif
                        </span>
                        <span class="block">
                            <span class="flex flex-wrap items-center gap-x-3 gap-y-2 text-[clamp(0.75rem,0.83vw,1.05rem)] text-ink/70">
                                <span class="rounded-full bg-ink px-3 py-1 text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase text-white">{{ __('articles.latest') }}</span>
                                @if ($featured['new'])
                                    <span data-new-badge class="rounded-full bg-brand-600 px-3 py-1 text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase text-white">{{ __('articles.new') }}</span>
                                @endif
                                <span class="rounded-full border border-ink/40 px-3 py-1 text-[clamp(0.7rem,0.78vw,1rem)] font-bold uppercase">{{ __("articles.tags.{$featured['type']}") }}</span>
                                <time datetime="{{ $featured['date'] }}">{{ \Illuminate\Support\Carbon::parse($featured['date'])->isoFormat('LL') }}</time>
                                <span aria-hidden="true">·</span>
                                <span>{{ __('articles.minutes', ['count' => $featured['minutes']]) }}</span>
                                @if ($viewsLabel = \App\Services\Articles\ArticleViews::label($featured['views']))
                                    <span aria-hidden="true">·</span>
                                    <span>{{ $viewsLabel }}</span>
                                @endif
                            </span>
                            <span class="mt-[1.4vw] line-clamp-3 text-balance pb-[0.1em] [overflow-wrap:anywhere] text-[clamp(1.75rem,3.3vw,4.25rem)] font-black leading-[1.02] tracking-tight max-lg:mt-4"><span class="title-hover">{{ $featured['title'] }}</span></span>
                            @if ($featured['excerpt'] !== '')
                                <span class="mt-[1.2vw] block max-w-[32em] [overflow-wrap:anywhere] text-[clamp(1rem,1.25vw,1.55rem)] leading-snug text-ink/75 max-lg:mt-4">{{ $featured['excerpt'] }}</span>
                            @endif
                            <span class="mt-[2vw] inline-flex items-center gap-2 text-[clamp(0.9rem,1vw,1.25rem)] font-bold max-lg:mt-6"><span class="uline">{{ __('landing.read') }}</span><span aria-hidden="true" class="transition-transform duration-500 group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1">→</span></span>
                        </span>
                    </a>
                @endif

                @if ($rest)
                    <ul data-art-grid class="grid gap-x-[2.5vw] gap-y-[4vw] max-lg:gap-y-12 sm:grid-cols-2 lg:grid-cols-3 {{ $featured ? 'mt-[5vw] max-lg:mt-14' : '' }}">
                        @foreach ($rest as $article)
                            <li><x-landing.article-card :article="$article" /></li>
                        @endforeach
                    </ul>
                @endif

                <x-landing.pagination :page="$page" :last="$lastPage" :type="$type" :search="$search" />
            @else
                <p class="text-xl text-ink/70">{{ $search !== null ? __('pages.articles.search.empty') : __('pages.articles.empty') }}</p>
            @endif
        </div>
    </section>

    <x-landing.cta />
@endsection
