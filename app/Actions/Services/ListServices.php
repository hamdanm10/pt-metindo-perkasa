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
            ->with('translations:id,service_id,locale')
            ->when(trim($search) !== '', fn ($query) => $query->where('name', 'ilike', $term))
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(15);
    }

    /**
     * The first and last service of the whole list, so the page can disable the move buttons that
     * would do nothing.
     *
     * @return array{first: int|null, last: int|null}
     */
    public function ends(): array
    {
        $first = Service::query()->orderBy('position')->orderBy('name')->orderBy('id')->value('id');
        $last = Service::query()->orderByDesc('position')->orderByDesc('name')->orderByDesc('id')->value('id');

        return ['first' => is_numeric($first) ? (int) $first : null, 'last' => is_numeric($last) ? (int) $last : null];
    }
}
