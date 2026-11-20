<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Models\Service;
use App\Services\Landing\LandingServices;
use Illuminate\Support\Facades\DB;

/**
 * Moves a service one place up or down in the whole list. The positions are renumbered first, in
 * the order the list shows them, so gaps and duplicates never get in the way.
 */
class MoveService
{
    public function __construct(private readonly LandingServices $landing) {}

    public function execute(Service $service, bool $up): void
    {
        DB::transaction(function () use ($service, $up): void {
            $ids = array_map('intval', Service::query()
                ->orderBy('position')
                ->orderBy('name')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id')
                ->all());

            $index = array_search($service->id, $ids, true);

            if (is_int($index)) {
                $target = $index + ($up ? -1 : 1);

                if (isset($ids[$target])) {
                    [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];
                }
            }

            foreach ($ids as $position => $id) {
                Service::query()->whereKey($id)->update(['position' => $position]);
            }
        });

        $this->landing->flush();
    }
}
