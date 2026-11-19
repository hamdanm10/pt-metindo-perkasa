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
        return view('landing.home', ['articles' => $articles->latest(app()->getLocale(), LandingArticles::LATEST)]);
    }
}
