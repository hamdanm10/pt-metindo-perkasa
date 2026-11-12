<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Models\ChatEntry;
use App\Services\Landing\LandingChat;
use Illuminate\Support\Facades\DB;

/**
 * Moves a question one place up or down among the questions of the same parent. The positions of
 * the siblings are renumbered first, so gaps and duplicates never get in the way.
 */
class MoveChatEntry
{
    public function __construct(private readonly LandingChat $chat) {}

    public function execute(ChatEntry $entry, bool $up): void
    {
        DB::transaction(function () use ($entry, $up): void {
            $ids = array_map('intval', ChatEntry::query()
                ->where('parent_id', $entry->parent_id)
                ->orderBy('position')
                ->orderBy('id')
                ->lockForUpdate()
                ->pluck('id')
                ->all());

            $index = array_search($entry->id, $ids, true);

            if (is_int($index)) {
                $target = $index + ($up ? -1 : 1);

                if (isset($ids[$target])) {
                    [$ids[$index], $ids[$target]] = [$ids[$target], $ids[$index]];
                }
            }

            foreach ($ids as $position => $id) {
                ChatEntry::query()->whereKey($id)->update(['position' => $position]);
            }
        });

        $this->chat->flush();
    }
}
