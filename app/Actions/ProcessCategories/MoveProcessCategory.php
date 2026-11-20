<?php

declare(strict_types=1);

namespace App\Actions\ProcessCategories;

use App\Models\ProcessCategory;
use Illuminate\Support\Facades\DB;

/**
 * Moves a category one place up or down in the whole list. The positions are renumbered first, in the
 * order the list shows them, so gaps and duplicates never get in the way.
 */
class MoveProcessCategory
{
    public function execute(ProcessCategory $processCategory, bool $up): void
    {
        DB::transaction(function () use ($processCategory, $up): void {
            $ids = array_map('intval', ProcessCategory::query()
                ->orderBy('position')
                ->orderBy('name')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id')
                ->all());

            $index = array_search($processCategory->id, $ids, true);

            if (is_int($index)) {
                $target = $index + ($up ? -1 : 1);

                if (isset($ids[$target])) {
                    [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];
                }
            }

            foreach ($ids as $position => $id) {
                ProcessCategory::query()->whereKey($id)->update(['position' => $position]);
            }
        });
    }
}
