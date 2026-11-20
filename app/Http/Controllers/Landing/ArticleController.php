<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\Articles\LandingArticles;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request, LandingArticles $articles): View
    {
        $locale = app()->getLocale();

        abort_unless($articles->hasAny($locale), 404);

        $type = in_array($request->query('type'), ['article', 'news'], true) ? $request->query('type') : null;
        $page = max(1, $request->integer('page', 1));
        $search = $this->search($request);
        $paged = $articles->page($locale, $type, $page, $search);

        // A page past the last one does not exist (page 1 always does, even when a filter matches nothing).
        abort_if($page > $paged['last'], 404);

        return view('landing.pages.articles.index', [
            'articles' => $paged['items'],
            'page' => $page,
            'lastPage' => $paged['last'],
            'type' => $type,
            'search' => $search,
            'counts' => $paged['counts'],
            // The canonical address of page 2 and later keeps its page; a filtered or searched list points at the plain list.
            'pageQuery' => $type === null && $search === null && $page > 1 ? ['page' => $page] : [],
        ]);
    }

    /**
     * The search words from ?q=, trimmed and cut to 100 characters; null when empty or not plain text.
     */
    private function search(Request $request): ?string
    {
        $query = $request->query('q');

        if (! is_string($query)) {
            return null;
        }

        $query = mb_substr(trim((string) preg_replace('/\s+/u', ' ', $query)), 0, 100);

        return $query === '' ? null : $query;
    }

    public function show(LandingArticles $articles, string $article): View
    {
        $locale = app()->getLocale();
        $current = $articles->find($locale, $article);

        abort_if($current === null, 404);

        return view('landing.pages.articles.show', [
            'article' => $current['summary'],
            'bodyHtml' => $current['body_html'],
            'metaTitle' => $current['meta_title'],
            'metaDescription' => $current['meta_description'],
            'related' => $articles->related($locale, $article),
            'alternateUrls' => collect($current['alternates'])
                ->map(fn (string $slug, string $code): string => route('landing.articles.show', ['locale' => $code, 'article' => $slug]))
                ->all(),
        ]);
    }
}
