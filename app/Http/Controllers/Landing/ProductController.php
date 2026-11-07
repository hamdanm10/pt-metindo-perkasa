<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\Landing\LandingContent;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function index(LandingContent $content): View
    {
        return view('landing.pages.products.index', ['categories' => $content->categories()]);
    }

    public function show(LandingContent $content, string $category): View
    {
        $current = $content->category($category);

        abort_if($current === null, 404);

        $others = array_values(array_filter(
            $content->categories(),
            fn (array $item): bool => $item['slug'] !== $current['slug'],
        ));

        return view('landing.pages.products.show', ['category' => $current, 'others' => $others]);
    }
}
