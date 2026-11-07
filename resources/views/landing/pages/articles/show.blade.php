@extends('landing.layouts.app')

@section('title', __("articles.items.{$article['slug']}.title"))

@section('content')
    <x-landing.page-header :title="__('articles.items.'.$article['slug'].'.title')">
        <a href="{{ route('landing.articles.index') }}" class="mb-6 inline-flex min-h-11 items-center font-semibold text-brand-700 underline underline-offset-4">
            {{ __('landing.back') }}
        </a>
        <p class="mb-4 text-sm font-semibold text-muted">
            {{ __("articles.tags.{$article['type']}") }}
            <span class="mx-1" aria-hidden="true">/</span>
            <time datetime="{{ $article['date'] }}">{{ $article['date'] }}</time>
        </p>
    </x-landing.page-header>

    <article class="bg-white">
        <div class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:py-24">
            <p class="mb-4 inline-block rounded-lg border border-ink/30 px-4 py-2 text-sm font-semibold text-ink">{{ __('landing.sample_note') }}</p>
            <p class="text-2xl font-semibold leading-snug text-ink">{{ __("articles.items.{$article['slug']}.text") }}</p>
            <p class="mt-6 text-lg text-muted">{{ __("articles.items.{$article['slug']}.body") }}</p>
        </div>
    </article>

    @if ($related)
        <section class="bg-lavender">
            <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                <h2 class="text-2xl font-bold text-ink">{{ __('pages.articles.related') }}</h2>
                <ul class="mt-8 grid gap-8 md:grid-cols-3">
                    @foreach ($related as $item)
                        <li class="border-t-2 border-ink pt-5">
                            <p class="text-sm font-semibold text-muted">
                                {{ __("articles.tags.{$item['type']}") }}
                                <span class="mx-1" aria-hidden="true">/</span>
                                <time datetime="{{ $item['date'] }}">{{ $item['date'] }}</time>
                            </p>
                            <h3 class="mt-2 text-xl font-bold text-ink">
                                <a href="{{ route('landing.articles.show', ['article' => $item['slug']]) }}" class="hover:text-brand-700">{{ __("articles.items.{$item['slug']}.title") }}</a>
                            </h3>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <x-landing.cta />
@endsection
