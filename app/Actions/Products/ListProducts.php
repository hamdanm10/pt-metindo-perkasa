<?php

declare(strict_types=1);

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProducts
{
    /**
     * @return LengthAwarePaginator<int, Product>
     */
    public function execute(string $search): LengthAwarePaginator
    {
        $term = '%'.addcslashes(trim($search), '%_\\').'%';

        return Product::query()
            ->when(trim($search) !== '', fn ($query) => $query->where('name', 'ilike', $term))
            ->orderBy('name')
            ->paginate(15);
    }
}
