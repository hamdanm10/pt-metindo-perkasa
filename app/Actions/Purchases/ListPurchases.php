<?php

declare(strict_types=1);

namespace App\Actions\Purchases;

use App\Enums\Role;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListPurchases
{
    /**
     * @param  User  $viewer  A customer or distributor only gets their own purchases; admin and staff get the purchases of one kind of buyer ($buyerRole), optionally for one account.
     * @return LengthAwarePaginator<int, Purchase>
     */
    public function execute(User $viewer, ?Role $buyerRole, string $search, ?int $buyerId): LengthAwarePaginator
    {
        $term = '%'.addcslashes(trim($search), '%_\\').'%';

        return Purchase::query()
            ->with(['buyer:id,name,role', 'creator:id,name'])
            ->withCount(['items', 'files'])
            ->when(trim($search) !== '', fn ($query) => $query->where(
                fn ($match) => $match->where('invoice_number', 'ilike', $term)
                    ->orWhereHas('buyer', fn ($buyer) => $buyer->where('name', 'ilike', $term)),
            ))
            ->when(! $viewer->role->usesStaffArea(), fn ($query) => $query->where('buyer_id', $viewer->id))
            ->when($viewer->role->usesStaffArea() ? $buyerRole : null, fn ($query, Role $role) => $query->whereHas('buyer', fn ($buyer) => $buyer->where('role', $role->value)))
            ->when($viewer->role->usesStaffArea() && $buyerId !== null, fn ($query) => $query->where('buyer_id', $buyerId))
            ->orderByDesc('purchased_at')
            ->orderByDesc('id')
            ->paginate(15);
    }
}
