<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListServices
{
    /**
     * @return LengthAwarePaginator<int, Service>
     */
    public function execute(string $search): LengthAwarePaginator
    {
        $term = '%'.addcslashes(trim($search), '%_\\').'%';

        return Service::query()
            ->when(trim($search) !== '', fn ($query) => $query->where('name', 'ilike', $term))
            ->orderBy('name')
            ->paginate(15);
    }
}
