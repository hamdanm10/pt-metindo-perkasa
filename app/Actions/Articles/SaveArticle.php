<?php

declare(strict_types=1);

namespace App\Actions\Articles;

use App\Enums\ArticleType;
use App\Enums\PublicationStatus;
use App\Models\Article;
use App\Models\User;
use App\Services\Articles\ArticleBody;
use App\Services\Articles\LandingArticles;
use App\Services\Articles\ThumbnailStore;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Saves an article with all its language tabs. A language missing from `$translations`
 * is removed, and a language can only be published once the article has a thumbnail.
 *
 * @phpstan-type TranslationInput array{title: string, slug: string, excerpt: string, body: string, meta_title: string, meta_description: string, image_alt: string, published: bool}
 */
class SaveArticle
{
    public function __construct(
        private readonly ArticleBody $body,
        private readonly ThumbnailStore $thumbnails,
        private readonly LandingArticles $landing,
    ) {}

    /**
     * @param  array<string, TranslationInput>  $translations  keyed by locale
     */
    public function execute(?Article $article, User $actor, ArticleType $type, array $translations, ?UploadedFile $thumbnail): Article
    {
        $newPath = $thumbnail === null ? null : $this->thumbnails->store($thumbnail);
        $oldPath = $article?->thumbnail_path;
        $hasThumbnail = ($newPath ?? $oldPath) !== null;

        foreach ($translations as $input) {
            if ($input['published'] && ! $hasThumbnail) {
                $this->thumbnails->delete($newPath);

                throw ValidationException::withMessages(['thumbnail' => __('article_manager.messages.thumbnail_required')]);
            }
        }

        $saved = DB::transaction(function () use ($article, $actor, $type, $translations, $newPath): Article {
            $article ??= new Article(['created_by' => $actor->id]);
            $article->type = $type;

            if ($newPath !== null) {
                $article->thumbnail_path = $newPath;
            }

            $article->save();

            $article->translations()->whereNotIn('locale', array_keys($translations))->delete();

            foreach ($translations as $locale => $input) {
                $this->saveTranslation($article, $locale, $input);
            }

            return $article;
        });

        if ($newPath !== null) {
            $this->thumbnails->delete($oldPath);
        }

        $this->landing->flush();

        return $saved;
    }

    /**
     * @param  TranslationInput  $input
     */
    private function saveTranslation(Article $article, string $locale, array $input): void
    {
        $existing = $article->translations()->where('locale', $locale)->first();
        $document = $this->body->parse($input['body']);
        $publishedAt = $existing !== null && $existing->status === PublicationStatus::Published ? $existing->published_at : null;

        $article->translations()->updateOrCreate(['locale' => $locale], [
            'slug' => $input['slug'],
            'title' => $input['title'],
            'excerpt' => $input['excerpt'] !== '' ? $input['excerpt'] : null,
            'body' => $document,
            'body_html' => $this->body->render($document),
            'meta_title' => $input['meta_title'] !== '' ? $input['meta_title'] : null,
            'meta_description' => $input['meta_description'] !== '' ? $input['meta_description'] : null,
            'image_alt' => $input['image_alt'] !== '' ? $input['image_alt'] : null,
            'status' => $input['published'] ? PublicationStatus::Published : PublicationStatus::Draft,
            'published_at' => $input['published'] ? ($publishedAt ?? now()) : null,
        ]);
    }
}
