<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role;
use App\Models\Purchase;
use App\Models\User;

/**
 * Admin and staff read every record, and a buyer reads their own. Admin and staff write; the admin
 * changes any record, a staff member only the ones they created.
 */
class PurchasePolicy
{
    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, Purchase $purchase): bool
    {
        return $actor->role->usesStaffArea() || $purchase->buyer_id === $actor->id;
    }

    public function create(User $actor): bool
    {
        return $actor->role->usesStaffArea();
    }

    public function update(User $actor, Purchase $purchase): bool
    {
        return $this->owns($actor, $purchase);
    }

    public function delete(User $actor, Purchase $purchase): bool
    {
        return $this->owns($actor, $purchase);
    }

    private function owns(User $actor, Purchase $purchase): bool
    {
        return $actor->role === Role::Admin
            || ($actor->role === Role::Staff && $purchase->created_by === $actor->id);
    }
}
