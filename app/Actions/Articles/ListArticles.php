<?php

declare(strict_types=1);

namespace App\Actions\Articles;

use App\Enums\ArticleType;
use App\Enums\PublicationStatus;
use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListArticles
{
    /**
     * @return LengthAwarePaginator<int, Article>
     */
    public function execute(string $search, ?ArticleType $type, ?PublicationStatus $status): LengthAwarePaginator
    {
        $search = trim($search);

        return Article::query()
            ->with('translations')
            ->when($type, fn ($query) => $query->where('type', $type))
            ->when($status, fn ($query) => $query->whereHas(
                'translations',
                fn ($translations) => $translations->where('status', $status),
            ))
            ->when($search !== '', fn ($query) => $query->whereHas(
                'translations',
                fn ($translations) => $translations->where('title', 'ilike', '%'.addcslashes($search, '%_\\').'%'),
            ))
            ->latest('id')
            ->paginate(15);
    }
}
