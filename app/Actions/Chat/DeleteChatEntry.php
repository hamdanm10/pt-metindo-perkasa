<?php

declare(strict_types=1);

namespace App\Actions\Chat;

use App\Models\ChatEntry;
use App\Services\Landing\LandingChat;

/**
 * Deletes a question together with its translations and every follow-up question below it
 * (the database cascades).
 */
class DeleteChatEntry
{
    public function __construct(private readonly LandingChat $chat) {}

    public function execute(ChatEntry $entry): void
    {
        $entry->delete();

        $this->chat->flush();
    }
}
