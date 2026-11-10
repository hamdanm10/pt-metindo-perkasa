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
 * @phpstan-type Summary array{slug: string, type: string, title: string, excerpt: string, date: string, image: string|null, alt: string, minutes: int, sample: bool}
 */
class LandingArticles
{
    public function __construct(
        private readonly ThumbnailStore $thumbnails,
        private readonly SampleArticles $samples = new SampleArticles,
    ) {}

    /**
     * Newest first. Cached per language and cleared whenever an article changes.
     *
     * @return list<Summary>
     */
    public function list(string $locale, ?string $type = null): array
    {
        /** @var list<Summary> $all */
        $all = Cache::rememberForever($this->key($locale), fn (): array => $this->query($locale));

        if ($all === [] && $this->sampling()) {
            $all = $this->samples->summaries();
        }

        return $type === null
            ? $all
            : array_values(array_filter($all, fn (array $item): bool => $item['type'] === $type));
    }

    /** True while the sample articles stand in for real ones: sample mode, and nothing published in the language. */
    private function sampling(): bool
    {
        return config()->boolean('landing.sample_content');
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
            return $this->sample($locale, $slug);
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
     * A sample article, but only while the language has no published article of its own.
     *
     * @return array{summary: Summary, body_html: string, meta_title: string, meta_description: string, alternates: array<string, string>}|null
     */
    private function sample(string $locale, string $slug): ?array
    {
        if (! $this->sampling() || $this->hasPublished($locale)) {
            return null;
        }

        $sample = $this->samples->find($slug);

        if ($sample === null) {
            return null;
        }

        return [
            'summary' => $sample['summary'],
            'body_html' => $sample['body_html'],
            'meta_title' => $sample['summary']['title'],
            'meta_description' => $sample['summary']['excerpt'],
            'alternates' => array_fill_keys(config()->array('locales.landing'), $slug),
        ];
    }

    private function hasPublished(string $locale): bool
    {
        /** @var list<Summary> $all */
        $all = Cache::rememberForever($this->key($locale), fn (): array => $this->query($locale));

        return $all !== [];
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
            'minutes' => ArticleBody::readingMinutes($translation->body_html ?? ''),
            'sample' => false,
        ];
    }
}
