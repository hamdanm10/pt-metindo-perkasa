<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\Landing\LandingContent;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(LandingContent $content): View
    {
        return view('landing.home', ['articles' => array_slice($content->articles(), 0, 3)]);
    }
}
