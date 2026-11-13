<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Actions\Chat\DeleteChatEntry;
use App\Actions\Chat\ListChatEntries;
use App\Actions\Chat\MoveChatEntry;
use App\Models\ChatEntry;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Index extends Component
{
    public function mount(): void
    {
        $this->authorize('viewAny', ChatEntry::class);
    }

    public function move(MoveChatEntry $moveChatEntry, int $id, string $direction): void
    {
        $entry = ChatEntry::query()->findOrFail($id);

        $this->authorize('update', $entry);

        $moveChatEntry->execute($entry, $direction === 'up');
    }

    public function delete(DeleteChatEntry $deleteChatEntry, int $id): void
    {
        $entry = ChatEntry::query()->findOrFail($id);

        $this->authorize('delete', $entry);

        $deleteChatEntry->execute($entry);

        session()->now('status', __('chat_manager.messages.deleted'));
    }

    public function render(ListChatEntries $listChatEntries): View
    {
        $this->authorize('viewAny', ChatEntry::class);

        return view('livewire.chat.index', ['rows' => $listChatEntries->execute()])
            ->layout('components.dashboard.layout', ['title' => __('chat_manager.title')]);
    }
}
