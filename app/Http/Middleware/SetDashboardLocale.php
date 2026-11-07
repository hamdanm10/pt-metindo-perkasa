<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetDashboardLocale
{
    /**
     * Pick the dashboard locale: the signed-in user's choice, then the session, then
     * the default. Only locales allowed for the dashboard are accepted.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowed = config()->array('locales.dashboard');
        $user = $request->user();

        $candidates = [
            $user instanceof User ? $user->locale : null,
            $request->hasSession() ? $request->session()->get('locale') : null,
        ];

        $locale = collect($candidates)->first(
            fn (mixed $candidate): bool => is_string($candidate) && in_array($candidate, $allowed, true),
            config()->string('locales.default'),
        );

        app()->setLocale($locale);

        return $next($request);
    }
}
