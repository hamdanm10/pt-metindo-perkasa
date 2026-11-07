<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLandingLocale
{
    /**
     * Apply the locale from the URL. The route constraint only lets supported
     * landing locales through, so an unknown locale never reaches this point.
     * The parameter is dropped afterwards so controllers only receive their own.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $locale = $route instanceof Route ? $route->parameter('locale') : null;

        if (! is_string($locale)) {
            return $next($request);
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);
        $route->forgetParameter('locale');

        return $next($request);
    }
}
