<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\User;
use App\Services\Auth\SessionRevoker;
use Illuminate\Validation\ValidationException;

class SetUserActive
{
    public function __construct(private readonly SessionRevoker $sessions) {}

    /**
     * @throws ValidationException
     */
    public function execute(User $actor, User $target, bool $active): void
    {
        if (! $active && $target->is_active) {
            if ($actor->is($target)) {
                throw ValidationException::withMessages(['active' => __('users.errors.self_active')]);
            }

            if ($target->role === Role::Admin && User::query()->activeAdmins()->count() <= 1) {
                throw ValidationException::withMessages(['active' => __('users.errors.last_admin_active')]);
            }
        }

        $target->forceFill(['is_active' => $active])->save();

        if (! $active) {
            $this->sessions->forUser($target);
        }
    }
}
