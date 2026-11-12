<?php

declare(strict_types=1);

namespace App\Actions\Inquiries;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListInquiries
{
    /**
     * @param  User  $viewer  A customer only gets their own records; admin and staff get all, optionally for one customer.
     * @return LengthAwarePaginator<int, Inquiry>
     */
    public function execute(User $viewer, string $search, ?int $customerId, ?int $serviceId, ?int $productId): LengthAwarePaginator
    {
        $term = '%'.addcslashes(trim($search), '%_\\').'%';

        return Inquiry::query()
            ->with(['customer:id,name', 'service:id,name', 'product:id,name', 'creator:id,name'])
            ->withCount('images')
            ->when(trim($search) !== '', fn ($query) => $query->where(
                fn ($match) => $match->where('question', 'ilike', $term)->orWhere('answer', 'ilike', $term),
            ))
            ->when(! $viewer->role->usesStaffArea(), fn ($query) => $query->where('customer_id', $viewer->id))
            ->when($viewer->role->usesStaffArea() && $customerId !== null, fn ($query) => $query->where('customer_id', $customerId))
            ->when($serviceId !== null, fn ($query) => $query->where('service_id', $serviceId))
            ->when($productId !== null, fn ($query) => $query->where('product_id', $productId))
            ->orderByRaw('asked_at desc nulls last')
            ->orderByDesc('id')
            ->paginate(15);
    }
}
