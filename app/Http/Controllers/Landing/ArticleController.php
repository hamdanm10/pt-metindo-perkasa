<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\Landing\LandingContent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request, LandingContent $content): View
    {
        abort_unless($content->sampleContentEnabled(), 404);

        $type = in_array($request->query('type'), ['article', 'news'], true) ? $request->query('type') : null;

        return view('landing.pages.articles.index', [
            'articles' => $content->articles($type),
            'type' => $type,
        ]);
    }

    public function show(LandingContent $content, string $article): View
    {
        $current = $content->article($article);

        abort_if($current === null, 404);

        $related = array_values(array_filter(
            $content->articles(),
            fn (array $item): bool => $item['slug'] !== $current['slug'],
        ));

        return view('landing.pages.articles.show', ['article' => $current, 'related' => array_slice($related, 0, 3)]);
    }
}
