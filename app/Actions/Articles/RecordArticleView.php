<?php

declare(strict_types=1);

namespace App\Actions\Articles;

use App\Models\ArticleTranslation;
use App\Services\Articles\ArticleViews;

class RecordArticleView
{
    public function __construct(private readonly ArticleViews $views) {}

    /**
     * Count one view of a published article, unless it comes from a bot, from the staff, or from a visitor
     * who already counted within the window. Returns the visitor's updated memory when it counted, null otherwise.
     *
     * @param  array<array-key, mixed>  $remembered
     * @return array<int, int>|null
     */
    public function execute(string $locale, string $slug, ?string $userAgent, bool $isStaff, array $remembered): ?array
    {
        if ($isStaff || $this->views->isBot($userAgent)) {
            return null;
        }

        $translation = ArticleTranslation::query()->published()->where('locale', $locale)->where('slug', $slug)->first();

        if ($translation === null || $this->views->hasCounted($remembered, $translation->id)) {
            return null;
        }

        $this->views->record($translation);

        return $this->views->remember($remembered, $translation->id);
    }
}
