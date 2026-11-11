<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Str;

class CreateUser
{
    public function execute(string $name, string $email, string $password, Role $role): User
    {
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
