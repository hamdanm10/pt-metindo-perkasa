<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Models\ChatEntry;
use App\Services\Landing\LandingChat;
use Illuminate\Support\Facades\DB;

/**
 * Saves one chat question with its language tabs. A language missing from `$translations` is removed.
 * A new question goes last among the questions of its parent.
 *
 * @phpstan-type TranslationInput array{question: string, answer: string, link_label: string|null, link_url: string|null}
 */
class SaveChatEntry
{
    public function __construct(private readonly LandingChat $chat) {}

    /**
     * @param  array<string, TranslationInput>  $translations  keyed by locale
     */
    public function execute(?ChatEntry $entry, ?int $parentId, array $translations): ChatEntry
    {
        $saved = DB::transaction(function () use ($entry, $parentId, $translations): ChatEntry {
            if ($entry === null) {
                $last = ChatEntry::query()->where('parent_id', $parentId)->max('position');

                $entry = ChatEntry::query()->create([
                    'parent_id' => $parentId,
                    'position' => is_numeric($last) ? (int) $last + 1 : 0,
                ]);
            }

            $entry->translations()->whereNotIn('locale', array_keys($translations))->delete();

            foreach ($translations as $locale => $input) {
                $entry->translations()->updateOrCreate(['locale' => $locale], $input);
            }

            return $entry;
        });

        $this->chat->flush();

        return $saved;
    }
}
