<?php

declare(strict_types=1);

namespace App\Actions\Whatsapp;

use App\Models\WhatsappAgent;
use Illuminate\Support\Facades\DB;

/**
 * Moves a number one place up or down. The positions are renumbered first, so gaps and duplicates
 * never get in the way.
 */
class MoveWhatsappAgent
{
    public function execute(WhatsappAgent $agent, bool $up): void
    {
        DB::transaction(function () use ($agent, $up): void {
            $ids = array_map('intval', WhatsappAgent::query()
                ->orderBy('position')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id')
                ->all());

            $index = array_search($agent->id, $ids, true);

            if (is_int($index)) {
                $target = $index + ($up ? -1 : 1);

                if (isset($ids[$target])) {
                    [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];
                }
            }

            foreach ($ids as $position => $id) {
                WhatsappAgent::query()->whereKey($id)->update(['position' => $position]);
            }
        });
    }
}
