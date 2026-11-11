@extends('landing.layouts.app')

@section('title', $metaTitle)

@push('head')
    @if ($metaDescription !== '')
        <meta name="description" content="{{ $metaDescription }}">
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:url" content="{{ $alternateUrls[app()->getLocale()] }}">
    @if ($article['image'])
        <meta property="og:image" content="{{ $article['image'] }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    {{-- A thin bar under the top of the screen shows how far the article has been read (read-progress, set by articles.js). --}}
    <div data-read-progress class="pointer-events-none fixed inset-x-0 top-0 z-[55] h-1 origin-left scale-x-0 bg-brand-600" aria-hidden="true"></div>

    {{-- The reading page: one narrow column like Medium's (680 px) with generous spacing. The picture takes the width of the column, and nothing on the page moves except the reading bar. --}}
    <section class="bg-white text-ink">
        <div class="mx-auto max-w-[44.5rem] px-4 pb-8 pt-8 sm:px-6 lg:pb-10 lg:pt-14">
            <a href="{{ route('landing.articles.index') }}" class="group inline-flex min-h-11 items-center gap-2 text-sm font-bold text-ink/70 hover:text-ink">
                <span aria-hidden="true" class="transition-transform duration-500 group-hover:-translate-x-1 rtl:-scale-x-100 rtl:group-hover:translate-x-1">←</span>
                <span class="uline">{{ __('landing.back') }}</span>
            </a>

            <div class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-2 text-sm text-ink/70">
                <span class="rounded-full border border-ink/40 px-3 py-1 text-xs font-extrabold uppercase text-ink">{{ __("articles.tags.{$article['type']}") }}</span>
                <time datetime="{{ $article['date'] }}">{{ \Illuminate\Support\Carbon::parse($article['date'])->isoFormat('LL') }}</time>
                <span aria-hidden="true">·</span>
                <span>{{ __('articles.minutes', ['count' => $article['minutes']]) }}</span>
                @if ($viewsLabel = \App\Services\Articles\ArticleViews::label($article['views']))
                    <span aria-hidden="true">·</span>
                    <span>{{ $viewsLabel }}</span>
                @endif
                @if ($article['sample'])
                    <span class="rounded-full bg-ink px-3 py-1 text-xs font-bold text-white">{{ __('articles.sample') }}</span>
                @endif
            </div>

            <h1 class="mt-5 text-balance text-[clamp(2rem,4.4vw,3.5rem)] font-black leading-[1.08] tracking-tight" @if ($article['sample']) lang="en" dir="ltr" @endif>{{ $article['title'] }}</h1>

            @if ($article['excerpt'] !== '')
                <p class="mt-5 text-[clamp(1.15rem,1.6vw,1.5rem)] leading-snug text-ink/65" @if ($article['sample']) lang="en" dir="ltr" @endif>{{ $article['excerpt'] }}</p>
            @endif

            <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-y border-ink/10 py-3">
                <p class="text-sm font-bold text-ink/70">PT Metindo Perkasa</p>
                @include('landing.pages.articles.share', ['title' => $article['title']])
            </div>

            @if ($article['image'])
                <figure class="mt-8">
                    <div class="aspect-[16/9] overflow-hidden rounded-lg bg-lavender">
                        <img src="{{ $article['image'] }}" alt="{{ $article['alt'] }}" decoding="async" class="size-full object-cover">
                    </div>
                </figure>
            @endif
        </div>


        <div class="mx-auto max-w-[44.5rem] px-4 pb-16 pt-10 sm:px-6 lg:pb-24 lg:pt-14" @if ($article['sample']) lang="en" dir="ltr" @endif>
            @if ($article['sample'])
                <p class="mb-8 rounded-lg border border-ink/30 px-4 py-2 text-sm font-semibold" lang="{{ app()->getLocale() }}" dir="{{ config('locales.locales.'.app()->getLocale().'.dir') }}">{{ __('landing.sample_note') }}</p>
            @endif
            @unless ($article['sample'])
                <div data-article-view data-url="{{ route('landing.articles.views.store', ['article' => $article['slug']]) }}" hidden></div>
            @endunless
            <div data-read-body class="article-body reading">{!! $bodyHtml !!}</div>

            <div class="mt-14 flex flex-wrap items-center justify-between gap-4 border-t border-ink/10 pt-6" lang="{{ app()->getLocale() }}" dir="{{ config('locales.locales.'.app()->getLocale().'.dir') }}">
                <a href="{{ route('landing.articles.index') }}" class="group inline-flex min-h-11 items-center gap-2 text-sm font-bold">
                    <span aria-hidden="true" class="transition-transform duration-500 group-hover:-translate-x-1 rtl:-scale-x-100 rtl:group-hover:translate-x-1">←</span>
                    <span class="uline">{{ __('landing.back') }}</span>
                </a>
                @include('landing.pages.articles.share', ['title' => $article['title']])
            </div>
        </div>
    </section>

    @if ($related)
        <section class="bg-lavender text-ink">
            <div class="container-site section-y">
                <h2 data-anim="grow" class="text-[clamp(1.75rem,3.4vw,4.25rem)] font-black uppercase leading-[0.98] tracking-tight">{{ __('pages.articles.related') }}</h2>
                <ul class="mt-[3vw] grid gap-x-[2.5vw] gap-y-10 max-lg:mt-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <li><x-landing.article-card :article="$item" /></li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <x-landing.cta />
@endsection
