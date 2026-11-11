<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateUser
{
    /**
     * @throws ValidationException when an admin is asked for and the maximum number of admins already exists
     */
    public function execute(string $name, string $email, string $password, Role $role): User
    {
        if ($role === Role::Admin && User::query()->where('role', Role::Admin->value)->count() >= config()->integer('auth.max_admins')) {
            throw ValidationException::withMessages(['role' => __('users.errors.admin_limit', ['max' => config()->integer('auth.max_admins')])]);
        }

        $user = new User;
        $user->forceFill([
            'name' => $name,
            'email' => Str::lower($email),
            'password' => $password,
            'role' => $role,
            'locale' => config()->string('locales.default'),
            'is_active' => true,
        ])->save();

        return $user;
    }
}
