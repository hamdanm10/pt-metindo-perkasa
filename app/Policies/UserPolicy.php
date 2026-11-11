<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $actor): bool
    {
        return $this->isAdmin($actor);
    }

    public function create(User $actor): bool
    {
        return $this->isAdmin($actor);
    }

    public function update(User $actor, User $target): bool
    {
        return $this->isAdmin($actor) && $this->isManaged($target);
    }

    public function resetPassword(User $actor, User $target): bool
    {
        return $this->isAdmin($actor) && $this->isManaged($target);
    }

    /**
     * An admin never switches off their own account. The action enforces this too.
     */
    public function setActive(User $actor, User $target): bool
    {
        return $this->isAdmin($actor) && $actor->isNot($target) && $this->isManaged($target);
    }

    /**
     * The only admin is not managed from the dashboard: only staff and customers are.
     */
    private function isManaged(User $target): bool
    {
        return $target->role !== Role::Admin;
    }

    private function isAdmin(User $actor): bool
    {
        return $actor->role === Role::Admin;
    }
}
