<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Allow only active accounts whose role is listed, for example `role:admin,staff`.
     * A deactivated account is signed out on its next request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return redirect()->guest(route('app.login'));
        }

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->guest($user->role->usesStaffArea() ? route('app.login') : route('portal.login'));
        }

        abort_unless(in_array($user->role, array_map(fn (string $role): Role => Role::from($role), $roles), true), 403);

        return $next($request);
    }
}
