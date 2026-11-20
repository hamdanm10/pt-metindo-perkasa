<?php

declare(strict_types=1);

namespace App\Actions\ProcessCategories;

use App\Models\ProcessCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProcessCategories
{
    /**
     * @return LengthAwarePaginator<int, ProcessCategory>
     */
    public function execute(string $search): LengthAwarePaginator
    {
        $term = '%'.addcslashes(trim($search), '%_\\').'%';

        return ProcessCategory::query()
            ->when(trim($search) !== '', fn ($query) => $query->where('name', 'ilike', $term))
            ->orderBy('name')
            ->paginate(15);
    }
}
