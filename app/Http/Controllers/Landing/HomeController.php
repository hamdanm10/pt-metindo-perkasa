<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\Articles\LandingArticles;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(LandingArticles $articles): View
    {
        $latest = array_slice($articles->list(app()->getLocale()), 0, 3);

        return view('landing.home', [
            'articles' => $latest ?: $articles->placeholders(),
            'articlesAreSamples' => $latest === [],
        ]);
    }
}
