<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Enums\Role;
use Livewire\Attributes\Locked;

/**
 * The staff area has one set of purchase pages per kind of buyer (customers, distributors), taken from the route.
 * In the customer and distributor areas there is no kind: a buyer only ever sees their own purchases.
 */
trait ScopesPurchaseKind
{
    use ResolvesActor;

    #[Locked]
    public string $kind = '';

    protected function setKind(string $kind): void
    {
        if (! $this->actor()->role->usesStaffArea()) {
            $this->kind = '';

            return;
        }

        $role = Role::tryFrom($kind);

        abort_unless($role !== null && in_array($role, Role::portalArea(), true), 404);

        $this->kind = $kind;
    }

    protected function buyerRole(): ?Role
    {
        return $this->kind === '' ? null : Role::from($this->kind);
    }

    /**
     * Route name prefix of the purchase pages this component belongs to.
     */
    protected function routePrefix(): string
    {
        return $this->kind === '' ? $this->actor()->role->area().'.purchases' : "app.{$this->kind}-purchases";
    }
}
