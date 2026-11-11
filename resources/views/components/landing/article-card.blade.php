@props(['article'])

{{-- A card for the article lists: the picture zooms and the title is highlighted on hover. --}}
<a href="{{ route('landing.articles.show', ['article' => $article['slug']]) }}" {{ $attributes->class(['group flex h-full flex-col focus-visible:outline-offset-4']) }}>
    <span class="relative block aspect-[16/10] w-full overflow-hidden rounded-lg bg-lavender">
        @if ($article['image'])
            <img src="{{ $article['image'] }}" alt="{{ $article['alt'] }}" loading="lazy" decoding="async" class="absolute inset-0 size-full object-cover transition-transform duration-700 [transition-timing-function:var(--header-ease)] group-hover:scale-105">
        @endif
        <span class="absolute start-3 top-3 rounded-full bg-white px-3 py-1 text-[clamp(0.7rem,0.78vw,1rem)] font-extrabold uppercase text-ink">{{ __("articles.tags.{$article['type']}") }}</span>
        @if ($article['sample'])
            <span class="absolute end-3 top-3 rounded-full bg-ink/80 px-3 py-1 text-[clamp(0.7rem,0.78vw,1rem)] font-bold text-white">{{ __('articles.sample') }}</span>
        @endif
    </span>
    <span class="mt-5 flex items-center gap-x-3 text-[clamp(0.75rem,0.83vw,1.05rem)] text-ink/70">
        <time datetime="{{ $article['date'] }}">{{ \Illuminate\Support\Carbon::parse($article['date'])->isoFormat('LL') }}</time>
        <span aria-hidden="true">·</span>
        <span>{{ __('articles.minutes', ['count' => $article['minutes']]) }}</span>
        @if ($viewsLabel = \App\Services\Articles\ArticleViews::label($article['views']))
            <span aria-hidden="true">·</span>
            <span>{{ $viewsLabel }}</span>
        @endif
    </span>
    <span class="mt-3 block text-[clamp(1.15rem,1.5vw,1.9rem)] font-black leading-[1.15] tracking-tight text-ink" @if ($article['sample']) lang="en" dir="ltr" @endif><span class="title-hover">{{ $article['title'] }}</span></span>
    @if ($article['excerpt'] !== '')
        <span class="mt-3 line-clamp-3 block text-[clamp(0.9rem,1.05vw,1.3rem)] leading-snug text-ink/70" @if ($article['sample']) lang="en" dir="ltr" @endif>{{ $article['excerpt'] }}</span>
    @endif
    <span class="mt-auto inline-flex items-center gap-2 pt-4 text-[clamp(0.85rem,0.95vw,1.2rem)] font-bold text-ink"><span class="uline">{{ __('landing.read') }}</span><span aria-hidden="true" class="transition-transform duration-500 group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1">→</span></span>
</a>
