<?php

declare(strict_types=1);

namespace App\Actions\Articles;

use App\Models\Article;
use App\Services\Articles\LandingArticles;
use App\Services\Articles\ThumbnailStore;

class DeleteArticle
{
    public function __construct(
        private readonly ThumbnailStore $thumbnails,
        private readonly LandingArticles $landing,
    ) {}

    public function execute(Article $article): void
    {
        $path = $article->thumbnail_path;

        $article->delete();

        $this->thumbnails->delete($path);
        $this->landing->flush();
    }
}
