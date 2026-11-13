<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class WhatsappAgentPolicy
{
    public function viewAny(User $actor): bool
    {
        return $this->isAdmin($actor);
    }

    public function create(User $actor): bool
    {
        return $this->isAdmin($actor);
    }

    public function update(User $actor): bool
    {
        return $this->isAdmin($actor);
    }

    public function delete(User $actor): bool
    {
        return $this->isAdmin($actor);
    }

    private function isAdmin(User $actor): bool
    {
        return $actor->role === Role::Admin;
    }
}
