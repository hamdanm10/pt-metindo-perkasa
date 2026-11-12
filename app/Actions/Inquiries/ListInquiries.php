<?php

declare(strict_types=1);

namespace App\Actions\Inquiries;

use App\Models\Inquiry;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListInquiries
{
    /**
     * @return LengthAwarePaginator<int, Inquiry>
     */
    public function execute(string $search, ?int $serviceId, ?int $productId): LengthAwarePaginator
    {
        $term = '%'.addcslashes(trim($search), '%_\\').'%';

        return Inquiry::query()
            ->with(['service:id,name', 'product:id,name', 'creator:id,name'])
            ->withCount('images')
            ->when(trim($search) !== '', fn ($query) => $query->where(
                fn ($match) => $match->where('question', 'ilike', $term)->orWhere('answer', 'ilike', $term),
            ))
            ->when($serviceId !== null, fn ($query) => $query->where('service_id', $serviceId))
            ->when($productId !== null, fn ($query) => $query->where('product_id', $productId))
            ->orderByDesc('asked_at')
            ->orderByDesc('id')
            ->paginate(15);
    }
}
