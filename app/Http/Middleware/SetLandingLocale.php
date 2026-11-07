<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLandingLocale
{
    /**
     * Apply the locale from the URL. The route constraint only lets supported
     * landing locales through, so an unknown locale never reaches this point.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) $request->route('locale');

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        return $next($request);
    }
}
