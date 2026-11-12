<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role;
use App\Models\Inquiry;
use App\Models\User;

/**
 * Admin and staff read every record. A customer reads only the records of their own account.
 * Admin and staff write; the admin changes any record, a staff member only the ones they created.
 */
class InquiryPolicy
{
    public function viewAny(User $actor): bool
    {
        return true;
    }

    public function view(User $actor, Inquiry $inquiry): bool
    {
        return $actor->role->usesStaffArea() || $inquiry->customer_id === $actor->id;
    }

    public function create(User $actor): bool
    {
        return $actor->role->usesStaffArea();
    }

    public function update(User $actor, Inquiry $inquiry): bool
    {
        return $this->owns($actor, $inquiry);
    }

    public function delete(User $actor, Inquiry $inquiry): bool
    {
        return $this->owns($actor, $inquiry);
    }

    private function owns(User $actor, Inquiry $inquiry): bool
    {
        return $actor->role === Role::Admin
            || ($actor->role === Role::Staff && $inquiry->created_by === $actor->id);
    }
}
