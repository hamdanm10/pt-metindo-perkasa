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
            ->with('translations:id,process_category_id,locale')
            ->withCount('processes')
            ->when(trim($search) !== '', fn ($query) => $query->where('name', 'ilike', $term))
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(15);
    }

    /**
     * The first and last category of the whole list, so the page can disable the move buttons that
     * would do nothing.
     *
     * @return array{first: int|null, last: int|null}
     */
    public function ends(): array
    {
        $first = ProcessCategory::query()->orderBy('position')->orderBy('name')->orderBy('id')->value('id');
        $last = ProcessCategory::query()->orderByDesc('position')->orderByDesc('name')->orderByDesc('id')->value('id');

        return ['first' => is_numeric($first) ? (int) $first : null, 'last' => is_numeric($last) ? (int) $last : null];
    }
}
