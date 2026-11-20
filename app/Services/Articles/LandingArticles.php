<?php

declare(strict_types=1);

namespace App\Services\Articles;

use App\Models\Article;
use App\Models\ArticleTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Published articles for the landing, one language at a time. There is no fallback to
 * another language: a language without a published translation has no articles.
 *
 * Nothing here loads the whole list. The list page reads one page at a time from the database (LIMIT and
 * OFFSET on the index of language, status and date), the home page and the menu read a few cached entries,
 * so the cost stays the same however many articles there are. Only two small things are cached per language,
 * the counts and the newest few entries, and both are cleared whenever an article changes.
 *
 * @phpstan-type Summary array{slug: string, type: string, title: string, excerpt: string, date: string, image: string|null, alt: string, minutes: int, views: int, new: bool}
 * @phpstan-type Counts array{all: int, article: int, news: int}
 */
class LandingArticles
{
    /** An article or news item counts as new for this many days after the day it was published. */
    public const NEW_DAYS = 14;

    /** Entries in the grid of one page of the list. The newest entry is shown large above the grid of page 1. */
    public const PER_PAGE = 12;

    /** Entries kept in the cache for the home page. */
    public const LATEST = 4;

    public function __construct(private readonly ThumbnailStore $thumbnails) {}

    /**
     * Whether a publication date (Y-m-d) is within the last two weeks. Worked out when the page is built, never stored
     * with the cached entries, so the badge disappears by itself.
     */
    public static function isNew(string $date): bool
    {
        if ($date === '') {
            return false;
        }

        $published = Carbon::parse($date)->startOfDay();

        return $published->lessThanOrEqualTo(Carbon::today()) && $published->greaterThanOrEqualTo(Carbon::today()->subDays(self::NEW_DAYS));
    }

    /**
     * Number of published items of a language, in total and per type. Without a search the numbers are cached until an
     * article changes; a search is different every time, so its numbers are read fresh with one grouped query.
     *
     * @return Counts
     */
    public function counts(string $locale, ?string $search = null): array
    {
        if ($search !== null) {
            return $this->countByType($locale, $search);
        }

        /** @var Counts $counts */
        $counts = Cache::rememberForever($this->key($locale, 'counts'), fn (): array => $this->countByType($locale, null));

        return $counts;
    }

    public function hasAny(string $locale): bool
    {
        return $this->counts($locale)['all'] > 0;
    }

    /**
     * One page of the list, newest first, optionally only the items whose title or summary contain the search words.
     * Page 1 reads one entry more than the others, because its newest entry is shown large above the grid; every later
     * page holds exactly PER_PAGE entries.
     *
     * @return array{items: list<Summary>, last: int, total: int, counts: Counts}
     */
    public function page(string $locale, ?string $type, int $page, ?string $search = null): array
    {
        $page = max(1, $page);
        $counts = $this->counts($locale, $search);
        $total = $type === null ? $counts['all'] : $counts[$type];
        $last = max(1, (int) ceil(max($total - 1, 0) / self::PER_PAGE));
        $offset = $page === 1 ? 0 : 1 + ($page - 1) * self::PER_PAGE;
        $limit = $page === 1 ? self::PER_PAGE + 1 : self::PER_PAGE;

        $items = $this->published($locale, $type, $search)->offset($offset)->limit($limit)->get()
            ->map(fn (ArticleTranslation $translation): array => $this->summaryOf($translation))
            ->all();

        return ['items' => $this->decorate(array_values($items)), 'last' => $last, 'total' => $total, 'counts' => $counts];
    }

    /**
     * The newest items for the home page. The entries are cached; the view counts are read fresh with one query on
     * the few slugs shown.
     *
     * @return list<Summary>
     */
    public function latest(string $locale, int $limit = self::LATEST): array
    {
        $items = array_slice($this->cachedLatest($locale), 0, $limit);
        $slugs = array_column($items, 'slug');
        $views = $slugs === []
            ? []
            : ArticleTranslation::query()->published()->where('locale', $locale)->whereIn('slug', $slugs)->pluck('views_count', 'slug')->all();

        return $this->decorate(array_map(fn (array $item): array => [...$item, 'views' => (int) ($views[$item['slug']] ?? $item['views'])], $items));
    }

    /**
     * True when the newest published item of the language is new, for the badge on the menu. Reads only the cached
     * newest entries, so it costs no query on a page.
     */
    public function hasNew(string $locale): bool
    {
        return self::isNew($this->cachedLatest($locale)[0]['date'] ?? '');
    }

    /**
     * @return array{summary: Summary, body_html: string, meta_title: string, meta_description: string, alternates: array<string, string>}|null
     */
    public function find(string $locale, string $slug): ?array
    {
        $article = Article::query()
            ->whereHas('translations', fn ($query) => $query->published()->where('locale', $locale)->where('slug', $slug))
            ->with(['translations' => fn ($query) => $query->published()])
            ->first();

        if ($article === null) {
            return null;
        }

        $translation = $article->translations->where('locale', $locale)->firstOrFail();
        $alternates = [];

        foreach ($article->translations as $other) {
            $alternates[$other->locale] = $other->slug;
        }

        return [
            'summary' => $this->summary($article, $translation),
            'body_html' => $translation->body_html ?? '',
            'meta_title' => $translation->meta_title ?: $translation->title,
            'meta_description' => (string) ($translation->meta_description ?: $translation->excerpt),
            'alternates' => $alternates,
        ];
    }

    /**
     * Other published items of the same language, newest first: one query for a few rows.
     *
     * @return list<Summary>
     */
    public function related(string $locale, string $slug, int $limit = 3): array
    {
        $items = $this->published($locale, null)->where('slug', '!=', $slug)->limit($limit)->get()
            ->map(fn (ArticleTranslation $translation): array => $this->summaryOf($translation))
            ->all();

        return $this->decorate(array_values($items));
    }

    public function flush(): void
    {
        foreach (config()->array('locales.landing') as $locale) {
            Cache::forget($this->key($locale, 'counts'));
            Cache::forget($this->key($locale, 'latest'));
        }
    }

    private function key(string $locale, string $what): string
    {
        return "landing.articles.{$locale}.{$what}";
    }

    /**
     * The newest entries of a language, cached until an article changes.
     *
     * @return list<Summary>
     */
    private function cachedLatest(string $locale): array
    {
        /** @var list<Summary> $items */
        $items = Cache::rememberForever($this->key($locale, 'latest'), fn (): array => array_values($this->published($locale, null)->limit(self::LATEST)->get()
            ->map(fn (ArticleTranslation $translation): array => $this->summaryOf($translation))
            ->all()));

        return $items;
    }

    /**
     * Published translations of a language, newest first, with their article.
     *
     * @return Builder<ArticleTranslation>
     */
    private function published(string $locale, ?string $type, ?string $search = null): Builder
    {
        return ArticleTranslation::query()
            ->published()
            ->where('locale', $locale)
            ->when($type !== null, fn (Builder $query) => $query->whereHas('article', fn ($article) => $article->where('type', $type)))
            ->tap(fn (Builder $query) => $this->matching($query, $search))
            ->with('article')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    /**
     * @return Counts
     */
    private function countByType(string $locale, ?string $search): array
    {
        $byType = ArticleTranslation::query()
            ->published()
            ->where('locale', $locale)
            ->tap(fn (Builder $query) => $this->matching($query, $search))
            ->join('articles', 'articles.id', '=', 'article_translations.article_id')
            ->selectRaw('articles.type as type, count(*) as total')
            ->groupBy('articles.type')
            ->pluck('total', 'type');

        return ['all' => (int) $byType->sum(), 'article' => (int) ($byType['article'] ?? 0), 'news' => (int) ($byType['news'] ?? 0)];
    }

    /**
     * Keeps the translations whose title or summary contain every word of the search, in any case (no search keeps all). The characters that
     * mean something to LIKE are escaped, so "50%" or "a_b" is looked up as typed.
     *
     * @param  Builder<ArticleTranslation>  $query
     * @return Builder<ArticleTranslation>
     */
    private function matching(Builder $query, ?string $search): Builder
    {
        if ($search === null) {
            return $query;
        }

        foreach (preg_split('/\s+/u', $search, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $word) {
            $like = '%'.addcslashes($word, '\\%_').'%';

            $query->where(fn (Builder $inner) => $inner
                ->where('article_translations.title', 'ilike', $like)
                ->orWhere('article_translations.excerpt', 'ilike', $like));
        }

        return $query;
    }

    /**
     * The new badge changes with the day, so it is never part of the cached entries: it is worked out here.
     *
     * @param  list<Summary>  $items
     * @return list<Summary>
     */
    private function decorate(array $items): array
    {
        return array_map(fn (array $item): array => [...$item, 'new' => self::isNew($item['date'])], $items);
    }

    /**
     * The summary of a translation whose article was loaded with it (the fallback only guards a missing relation).
     *
     * @return Summary
     */
    private function summaryOf(ArticleTranslation $translation): array
    {
        return $this->summary($translation->article ?? $translation->article()->firstOrFail(), $translation);
    }

    /**
     * @return Summary
     */
    private function summary(Article $article, ArticleTranslation $translation): array
    {
        $path = $article->thumbnail_path;

        return [
            'slug' => $translation->slug,
            'type' => $article->type->value,
            'title' => $translation->title,
            'excerpt' => (string) ($translation->excerpt ?: $translation->meta_description),
            'date' => $translation->published_at?->toDateString() ?? '',
            'image' => $path === null ? null : $this->thumbnails->url($path),
            'alt' => (string) ($translation->image_alt ?: $translation->title),
            'minutes' => ArticleBody::readingMinutes($translation->body_html ?? ''),
            'views' => (int) $translation->views_count,
            'new' => false,
        ];
    }
}
