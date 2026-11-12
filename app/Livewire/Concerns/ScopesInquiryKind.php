<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Enums\Role;
use Livewire\Attributes\Locked;

/**
 * The staff area has one set of question pages per kind of owner (customers, distributors), taken from the route.
 * In the portal there is no kind: a portal user only ever sees their own records.
 */
trait ScopesInquiryKind
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

    protected function ownerRole(): ?Role
    {
        return $this->kind === '' ? null : Role::from($this->kind);
    }

    /**
     * Route name prefix of the question pages this component belongs to.
     */
    protected function routePrefix(): string
    {
        return $this->kind === '' ? 'portal.inquiries' : "app.{$this->kind}-inquiries";
    }
}
