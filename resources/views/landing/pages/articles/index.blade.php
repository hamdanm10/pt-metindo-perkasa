@extends('landing.layouts.app')

@section('title', __('landing.nav.articles'))

@section('content')
    @php
        $filters = [['value' => null, 'key' => 'all'], ['value' => 'article', 'key' => 'article'], ['value' => 'news', 'key' => 'news']];
        $featured = $articles[0] ?? null;
        $rest = array_slice($articles, 1);
    @endphp

    {{-- Opening: the headline, the intro, and the filter. --}}
    <section class="bg-ink text-white" style="--focus-ring: #ffffff">
        <div class="container-site pb-[clamp(3rem,6.67vw,8rem)] pt-[clamp(2.5rem,6vw,7.5rem)]">
            <h1 data-anim="hero-title" class="max-w-[12em] text-balance text-[clamp(2.75rem,7.2vw,9rem)] font-black uppercase leading-[0.92] tracking-tight">{{ __('pages.articles.title') }}</h1>
            <div class="mt-[3vw] grid items-end gap-8 max-lg:mt-8 lg:grid-cols-[1fr_auto]">
                <p data-anim="hero-item" class="max-w-[34em] text-[clamp(1.05rem,1.5vw,1.9rem)] leading-[1.4] text-white/85">{{ __('pages.articles.intro') }}</p>
                <nav data-anim="hero-item" aria-label="{{ __('pages.articles.title') }}">
                    <ul class="flex flex-wrap gap-3">
                        @foreach ($filters as $filter)
                            @php($active = $type === $filter['value'])
                            <li>
                                <a href="{{ route('landing.articles.index', $filter['value'] ? ['type' => $filter['value']] : []) }}" @if ($active) aria-current="true" @endif
                                   class="inline-flex min-h-11 items-center gap-2 rounded-full border px-5 text-[clamp(0.85rem,0.95vw,1.2rem)] font-bold transition-colors duration-300 {{ $active ? 'border-white bg-white text-ink' : 'border-white/30 hover:border-white hover:bg-white hover:text-ink' }}">
                                    {{ __("pages.articles.filters.{$filter['key']}") }}
                                    <span class="text-[0.75em] {{ $active ? 'text-brand-600' : 'text-brand-300' }}">{{ $counts[$filter['key']] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
        </div>
    </section>

    <section class="bg-lavender text-ink">
        <div class="container-site section-y">
            @if ($featured)
                @if ($featured['sample'])
                    <p class="mb-8 inline-block rounded-lg border border-ink/30 px-4 py-2 text-sm font-semibold">{{ __('landing.sample_note') }}</p>
                @endif

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
                            <span class="rounded-full border border-ink/40 px-3 py-1 text-[clamp(0.7rem,0.78vw,1rem)] font-bold uppercase">{{ __("articles.tags.{$featured['type']}") }}</span>
                            <time datetime="{{ $featured['date'] }}">{{ \Illuminate\Support\Carbon::parse($featured['date'])->isoFormat('LL') }}</time>
                            <span aria-hidden="true">·</span>
                            <span>{{ __('articles.minutes', ['count' => $featured['minutes']]) }}</span>
                            @if ($viewsLabel = \App\Services\Articles\ArticleViews::label($featured['views']))
                                <span aria-hidden="true">·</span>
                                <span>{{ $viewsLabel }}</span>
                            @endif
                        </span>
                        <span class="mt-[1.4vw] block text-balance text-[clamp(1.75rem,3.3vw,4.25rem)] font-black leading-[1.02] tracking-tight max-lg:mt-4" @if ($featured['sample']) lang="en" dir="ltr" @endif><span class="title-hover">{{ $featured['title'] }}</span></span>
                        @if ($featured['excerpt'] !== '')
                            <span class="mt-[1.2vw] block max-w-[32em] text-[clamp(1rem,1.25vw,1.55rem)] leading-snug text-ink/75 max-lg:mt-4" @if ($featured['sample']) lang="en" dir="ltr" @endif>{{ $featured['excerpt'] }}</span>
                        @endif
                        <span class="mt-[2vw] inline-flex items-center gap-2 text-[clamp(0.9rem,1vw,1.25rem)] font-bold max-lg:mt-6"><span class="uline">{{ __('landing.read') }}</span><span aria-hidden="true" class="transition-transform duration-500 group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1">→</span></span>
                    </span>
                </a>

                @if ($rest)
                    <ul data-art-grid class="mt-[5vw] grid gap-x-[2.5vw] gap-y-[4vw] max-lg:mt-14 max-lg:gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($rest as $article)
                            <li><x-landing.article-card :article="$article" /></li>
                        @endforeach
                    </ul>
                @endif
            @else
                <p class="text-xl text-ink/70">{{ __('pages.articles.empty') }}</p>
            @endif
        </div>
    </section>

    <x-landing.cta />
@endsection
