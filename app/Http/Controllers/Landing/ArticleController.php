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
        $paged = $articles->page($locale, $type, $page);

        // A page past the last one does not exist (page 1 always does, even when a filter matches nothing).
        abort_if($page > $paged['last'], 404);

        return view('landing.pages.articles.index', [
            'articles' => $paged['items'],
            'page' => $page,
            'lastPage' => $paged['last'],
            'type' => $type,
            'counts' => $articles->counts($locale),
            // The canonical address of page 2 and later keeps its page; a filtered list points at the plain list.
            'pageQuery' => $type === null && $page > 1 ? ['page' => $page] : [],
        ]);
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
