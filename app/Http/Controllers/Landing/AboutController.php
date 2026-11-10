<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\Landing\LandingContent;
use App\Services\Landing\LandingMap;
use Illuminate\Contracts\View\View;

class AboutController extends Controller
{
    public function __invoke(LandingContent $content, LandingMap $map): View
    {
        return view('landing.pages.about', ['subsidiaries' => $map->pins($content->subsidiaries())]);
    }
}
