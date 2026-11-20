<?php

declare(strict_types=1);

namespace App\Actions\Processes;

use App\Models\Process;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProcesses
{
    /**
     * Processes grouped by category order, then their own order.
     *
     * @return LengthAwarePaginator<int, Process>
     */
    public function execute(string $search, ?int $categoryId): LengthAwarePaginator
    {
        $term = '%'.addcslashes(trim($search), '%_\\').'%';

        return Process::query()
            ->select('processes.*')
            ->join('process_categories', 'process_categories.id', '=', 'processes.process_category_id')
            ->with(['processCategory:id,name', 'translations:id,process_id,locale'])
            ->withCount('steps')
            ->when(trim($search) !== '', fn ($query) => $query->where('processes.name', 'ilike', $term))
            ->when($categoryId !== null, fn ($query) => $query->where('processes.process_category_id', $categoryId))
            ->orderBy('process_categories.position')
            ->orderBy('process_categories.name')
            ->orderBy('processes.position')
            ->orderBy('processes.name')
            ->paginate(15);
    }
}
