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

        return view('landing.pages.articles.index', [
            'articles' => $articles->list($locale, $type),
            'type' => $type,
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
