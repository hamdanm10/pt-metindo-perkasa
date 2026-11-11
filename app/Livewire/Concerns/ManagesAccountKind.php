<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Enums\Role;
use Livewire\Attributes\Locked;

/**
 * The account pages serve one role at a time (staff or customer), taken from the route. The admin is not managed here.
 */
trait ManagesAccountKind
{
    #[Locked]
    public string $kind = '';

    protected function setKind(string $kind): void
    {
        $role = Role::tryFrom($kind);

        abort_if($role === null || $role->accountRoutePrefix() === null, 404);

        $this->kind = $kind;
    }

    protected function role(): Role
    {
        return Role::from($this->kind);
    }

    protected function routePrefix(): string
    {
        return $this->role()->accountRoutePrefix() ?? abort(404);
    }
}
