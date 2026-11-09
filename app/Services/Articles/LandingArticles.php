<?php

declare(strict_types=1);

namespace App\Services\Articles;

use App\Models\Article;
use App\Models\ArticleTranslation;
use Illuminate\Support\Facades\Cache;

/**
 * Published articles for the landing, one language at a time. There is no fallback to
 * another language: a language without a published translation has no articles.
 *
 * @phpstan-type Summary array{slug: string, type: string, title: string, excerpt: string, date: string, image: string|null, alt: string}
 */
class LandingArticles
{
    public function __construct(private readonly ThumbnailStore $thumbnails) {}

    /**
     * Newest first. Cached per language and cleared whenever an article changes.
     *
     * @return list<Summary>
     */
    public function list(string $locale, ?string $type = null): array
    {
        /** @var list<Summary> $all */
        $all = Cache::rememberForever($this->key($locale), fn (): array => $this->query($locale));

        return $type === null
            ? $all
            : array_values(array_filter($all, fn (array $item): bool => $item['type'] === $type));
    }

    /**
     * Three static cards for the home page while a language has no published article, so the
     * layout can be reviewed. Only in sample mode. They link nowhere, because there is no page
     * behind them.
     *
     * @return list<Summary>
     */
    public function placeholders(): array
    {
        if (! config()->boolean('landing.sample_content')) {
            return [];
        }

        $today = now()->toDateString();

        return array_map(fn (string $type): array => [
            'slug' => '',
            'type' => $type,
            'title' => '[SAMPLE] Article title',
            'excerpt' => '',
            'date' => $today,
            'image' => null,
            'alt' => '',
        ], ['article', 'news', 'article']);
    }

    public function hasAny(string $locale): bool
    {
        return $this->list($locale) !== [];
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
     * Other published articles of the same language, newest first.
     *
     * @return list<Summary>
     */
    public function related(string $locale, string $slug, int $limit = 3): array
    {
        return array_slice(
            array_values(array_filter($this->list($locale), fn (array $item): bool => $item['slug'] !== $slug)),
            0,
            $limit,
        );
    }

    public function flush(): void
    {
        foreach (config()->array('locales.landing') as $locale) {
            Cache::forget($this->key($locale));
        }
    }

    private function key(string $locale): string
    {
        return "landing.articles.{$locale}";
    }

    /**
     * @return list<Summary>
     */
    private function query(string $locale): array
    {
        $articles = Article::query()
            ->whereHas('translations', fn ($query) => $query->published()->where('locale', $locale))
            ->with(['translations' => fn ($query) => $query->published()->where('locale', $locale)])
            ->get();

        return array_values($articles
            ->map(fn (Article $article): array => [$article, $article->translations->firstOrFail()])
            ->sortByDesc(fn (array $pair): string => $pair[1]->published_at?->format('Y-m-d H:i:s').sprintf('%010d', $pair[1]->id))
            ->map(fn (array $pair): array => $this->summary($pair[0], $pair[1]))
            ->all());
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
        ];
    }
}
