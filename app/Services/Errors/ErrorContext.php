<?php

declare(strict_types=1);

namespace App\Services\Errors;

use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

/**
 * Decides which look and language an error page uses. A 404 never reaches the locale
 * middleware, so the area and language are read from the URL. For server errors the
 * session and database are not touched, because they may be the cause of the error.
 */
class ErrorContext
{
    /**
     * @return array{area: 'landing'|'dashboard', locale: string, dashboardArea: 'app'|'customer'|null}
     */
    public function resolve(Request $request, int $status): array
    {
        $first = $request->segment(1);
        $default = config()->string('locales.default');

        if (is_string($first) && in_array($first, config()->array('locales.landing'), true)) {
            return ['area' => 'landing', 'locale' => $first, 'dashboardArea' => null];
        }

        if ($first === 'app' || $first === 'customer') {
            return [
                'area' => 'dashboard',
                'locale' => $status >= 500 ? $default : $this->dashboardLocale($request, $default),
                'dashboardArea' => $first,
            ];
        }

        return ['area' => 'landing', 'locale' => $default, 'dashboardArea' => null];
    }

    private function dashboardLocale(Request $request, string $default): string
    {
        $allowed = config()->array('locales.dashboard');

        try {
            $user = $request->user();
            $candidates = [
                $user instanceof User ? $user->locale : null,
                $request->hasSession() ? $request->session()->get('locale') : null,
            ];
        } catch (Throwable) {
            return $default;
        }

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && in_array($candidate, $allowed, true)) {
                return $candidate;
            }
        }

        return $default;
    }
}
