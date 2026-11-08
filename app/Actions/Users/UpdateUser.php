<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateUser
{
    /**
     * @throws ValidationException
     */
    public function execute(User $actor, User $target, string $name, string $email, Role $role): User
    {
        $losesAdminRole = $target->role === Role::Admin && $role !== Role::Admin;

        if ($losesAdminRole && $actor->is($target)) {
            throw ValidationException::withMessages(['form.role' => __('users.errors.self_role')]);
        }

        if ($losesAdminRole && $target->is_active && User::query()->activeAdmins()->count() <= 1) {
            throw ValidationException::withMessages(['form.role' => __('users.errors.last_admin')]);
        }

        $target->forceFill([
            'name' => $name,
            'email' => Str::lower($email),
            'role' => $role,
        ])->save();

        return $target;
    }
}
