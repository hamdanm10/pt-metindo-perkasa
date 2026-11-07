<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticateUser
{
    private const int MAX_ATTEMPTS = 5;

    private const int DECAY_SECONDS = 60;

    /**
     * Sign in an active account whose role belongs to the given area.
     *
     * A wrong password, an unknown email, a role from another area and an inactive
     * account all fail with the same message, so nothing leaks about the account.
     *
     * @param  list<Role>  $roles
     *
     * @throws ValidationException
     */
    public function execute(string $email, string $password, bool $remember, array $roles, string $throttleKey): void
    {
        $key = Str::lower($email).'|'.$throttleKey;

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', ['seconds' => RateLimiter::availableIn($key)]),
            ]);
        }

        $signedIn = Auth::attempt([
            'email' => Str::lower($email),
            'password' => $password,
            'is_active' => true,
            fn (Builder $query) => $query->whereIn('role', array_map(fn (Role $role): string => $role->value, $roles)),
        ], $remember);

        if (! $signedIn) {
            RateLimiter::hit($key, self::DECAY_SECONDS);

            throw ValidationException::withMessages(['email' => trans('auth.failed')]);
        }

        RateLimiter::clear($key);
    }
}
