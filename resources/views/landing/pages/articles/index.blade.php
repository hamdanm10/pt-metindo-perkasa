@extends('landing.layouts.app')

@section('title', __('landing.nav.articles'))

@section('content')
    <x-landing.page-header :title="__('pages.articles.title')" :intro="__('pages.articles.intro')" />

    <section class="bg-white">
        <div class="container-site section-y">
            <nav aria-label="{{ __('pages.articles.title') }}">
                <ul class="flex flex-wrap gap-2">
                    @foreach ([['value' => null, 'key' => 'all'], ['value' => 'article', 'key' => 'article'], ['value' => 'news', 'key' => 'news']] as $filter)
                        @php($active = $type === $filter['value'])
                        <li>
                            <a href="{{ route('landing.articles.index', $filter['value'] ? ['type' => $filter['value']] : []) }}"
                               @if ($active) aria-current="true" @endif
                               class="inline-flex min-h-11 items-center rounded-lg border px-5 font-semibold {{ $active ? 'border-brand-700 bg-brand-700 text-white' : 'border-ink/40 text-ink hover:border-brand-700 hover:text-brand-700' }}">
                                {{ __("pages.articles.filters.{$filter['key']}") }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            @forelse ($articles as $article)
                @if ($loop->first)
                    <div data-anim="rows" class="mt-12 grid gap-x-12 gap-y-10 lg:grid-cols-12">
                @endif
                <article class="{{ $loop->first ? 'lg:col-span-7 lg:row-span-3' : 'lg:col-span-5' }} border-t-2 border-ink pt-6">
                    @if ($article['image'])
                        <img src="{{ $article['image'] }}" alt="{{ $article['alt'] }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async" class="mb-5 aspect-video w-full rounded-2xl object-cover">
                    @endif
                    <p class="text-sm font-semibold text-muted">
                        {{ __("articles.tags.{$article['type']}") }}
                        <span class="mx-1" aria-hidden="true">/</span>
                        <time datetime="{{ $article['date'] }}">{{ $article['date'] }}</time>
                    </p>
                    <h2 data-anim="heading" class="{{ $loop->first ? 'text-3xl sm:text-5xl lg:text-6xl' : 'text-2xl' }} mt-3 text-balance font-black tracking-tight leading-tight text-ink">
                        <a href="{{ route('landing.articles.show', ['article' => $article['slug']]) }}" class="hover:text-brand-700">{{ $article['title'] }}</a>
                    </h2>
                    <p class="mt-4 max-w-xl text-lg text-muted">{{ $article['excerpt'] }}</p>
                    <a href="{{ route('landing.articles.show', ['article' => $article['slug']]) }}" class="mt-4 inline-flex min-h-11 items-center font-semibold text-brand-700 underline underline-offset-4">{{ __('landing.read') }}</a>
                </article>
                @if ($loop->last)
                    </div>
                @endif
            @empty
                <p class="mt-12 text-xl text-muted">{{ __('pages.articles.empty') }}</p>
            @endforelse
        </div>
    </section>

    <x-landing.cta />
@endsection
