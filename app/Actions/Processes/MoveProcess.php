<?php

declare(strict_types=1);

namespace App\Actions\Processes;

use App\Models\Process;
use App\Services\Landing\LandingProcesses;
use Illuminate\Support\Facades\DB;

/**
 * Moves a process one place up or down inside its category. The positions of the category are renumbered
 * first, in the order they are shown, so gaps and duplicates never get in the way.
 */
class MoveProcess
{
    public function __construct(private readonly LandingProcesses $landing) {}

    public function execute(Process $process, bool $up): void
    {
        DB::transaction(function () use ($process, $up): void {
            $ids = array_map('intval', Process::query()
                ->where('process_category_id', $process->process_category_id)
                ->orderBy('position')
                ->orderBy('name')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id')
                ->all());

            $index = array_search($process->id, $ids, true);

            if (is_int($index)) {
                $target = $index + ($up ? -1 : 1);

                if (isset($ids[$target])) {
                    [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];
                }
            }

            foreach ($ids as $position => $id) {
                Process::query()->whereKey($id)->update(['position' => $position]);
            }
        });

        $this->landing->flush();
    }
}
