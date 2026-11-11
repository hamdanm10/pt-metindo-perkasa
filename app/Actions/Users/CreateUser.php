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
     * @throws ValidationException when an admin is asked for and one already exists (the app has a single admin)
     */
    public function execute(string $name, string $email, string $password, Role $role): User
    {
        if ($role === Role::Admin && User::query()->where('role', Role::Admin->value)->exists()) {
            throw ValidationException::withMessages(['role' => __('users.errors.admin_exists')]);
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
