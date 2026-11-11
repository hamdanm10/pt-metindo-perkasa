<?php

declare(strict_types=1);

namespace App\Services\Articles;

use App\Models\ArticleTranslation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;

/**
 * View counts of articles, kept per language: a total on the translation (for display and sorting)
 * and a count per day (for later reports). The same visitor counts once per window.
 */
class ArticleViews
{
    /** Hours before the same visitor counts again on the same article. */
    public const WINDOW_HOURS = 24;

    /** The landing shows a count only from this many views, so a new article does not look empty. */
    public const MINIMUM_SHOWN = 10;

    private const MAX_REMEMBERED = 100;

    private const BOT_PATTERN = '/bot|crawl|spider|slurp|facebookexternalhit|preview|fetch|monitor|lighthouse|headless|curl|wget|python|httpclient|scrapy/i';

    public function isBot(?string $userAgent): bool
    {
        return $userAgent === null || trim($userAgent) === '' || preg_match(self::BOT_PATTERN, $userAgent) === 1;
    }

    public function record(ArticleTranslation $translation): void
    {
        DB::transaction(function () use ($translation): void {
            DB::table('article_view_days')->upsert(
                [['article_translation_id' => $translation->id, 'day' => Carbon::today()->toDateString(), 'views' => 1]],
                ['article_translation_id', 'day'],
                ['views' => DB::raw('article_view_days.views + 1')],
            );

            ArticleTranslation::query()->whereKey($translation->id)->increment('views_count');
        });
    }

    /**
     * The visitor's memory of what they counted: translation id => time it counts again.
     *
     * @param  array<array-key, mixed>  $remembered
     * @return array<int, int>
     */
    public function remember(array $remembered, int $translationId): array
    {
        $now = now()->getTimestamp();
        $kept = [];

        foreach ($remembered as $id => $until) {
            if (is_int($until) && $until > $now) {
                $kept[(int) $id] = $until;
            }
        }

        $kept[$translationId] = $now + self::WINDOW_HOURS * 3600;

        return array_slice($kept, -self::MAX_REMEMBERED, null, true);
    }

    /**
     * @param  array<array-key, mixed>  $remembered
     */
    public function hasCounted(array $remembered, int $translationId): bool
    {
        $until = $remembered[$translationId] ?? null;

        return is_int($until) && $until > now()->getTimestamp();
    }

    /**
     * Published translations of a language: slug => total views.
     *
     * @return array<string, int>
     */
    public function forLocale(string $locale): array
    {
        /** @var array<string, int> */
        return ArticleTranslation::query()->published()->where('locale', $locale)->pluck('views_count', 'slug')
            ->map(fn (mixed $count): int => (int) $count)->all();
    }

    /** The text the landing shows ("1.2K views"), or null while the count is too small to show. */
    public static function label(int $views): ?string
    {
        if ($views < self::MINIMUM_SHOWN) {
            return null;
        }

        $short = Number::withLocale(app()->getLocale(), fn (): string|false => Number::abbreviate($views, maxPrecision: 1));

        return trans_choice('articles.views', $views, ['count' => $short === false ? (string) $views : $short]);
    }
}
