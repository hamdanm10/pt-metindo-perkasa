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
@endpush

@section('content')
    <x-landing.page-header :title="$article['title']">
        <a href="{{ route('landing.articles.index') }}" class="mb-6 inline-flex min-h-11 items-center font-semibold text-white underline underline-offset-4">
            {{ __('landing.back') }}
        </a>
        <p class="mb-4 text-sm font-semibold text-white/85">
            {{ __("articles.tags.{$article['type']}") }}
            <span class="mx-1" aria-hidden="true">/</span>
            <time datetime="{{ $article['date'] }}">{{ $article['date'] }}</time>
        </p>
    </x-landing.page-header>

    <article class="bg-white">
        <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
            @if ($article['image'])
                <img src="{{ $article['image'] }}" alt="{{ $article['alt'] }}" decoding="async" class="mb-8 aspect-video w-full rounded-2xl object-cover">
            @endif
            @if ($article['excerpt'] !== '')
                <p class="text-2xl font-semibold leading-snug text-ink">{{ $article['excerpt'] }}</p>
            @endif
            <div class="article-body mt-8 text-lg text-ink">{!! $bodyHtml !!}</div>
        </div>
    </article>

    @if ($related)
        <section class="bg-lavender">
            <div class="container-site lg: section-y">
                <h2 data-anim="heading" class="text-2xl font-black tracking-tight text-ink">{{ __('pages.articles.related') }}</h2>
                <ul data-anim="rows" class="mt-8 grid gap-8 md:grid-cols-3">
                    @foreach ($related as $item)
                        <li class="border-t-2 border-ink pt-5">
                            <p class="text-sm font-semibold text-muted">
                                {{ __("articles.tags.{$item['type']}") }}
                                <span class="mx-1" aria-hidden="true">/</span>
                                <time datetime="{{ $item['date'] }}">{{ $item['date'] }}</time>
                            </p>
                            <h3 class="mt-2 text-xl font-bold text-ink">
                                <a href="{{ route('landing.articles.show', ['article' => $item['slug']]) }}" class="hover:text-brand-700">{{ $item['title'] }}</a>
                            </h3>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <x-landing.cta />
@endsection
