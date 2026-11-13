<?php

declare(strict_types=1);

namespace App\Livewire\Chat;

use App\Actions\Chat\SaveChatEntry;
use App\Livewire\Forms\ChatEntryForm;
use App\Models\ChatEntry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Editor extends Component
{
    #[Locked]
    public ?ChatEntry $entry = null;

    /** The question this one follows up on, taken from `?parent=` when adding. Fixed once the question exists. */
    #[Locked]
    public ?int $parentId = null;

    public ChatEntryForm $form;

    /** The language tab that is open. All tabs stay in the page, so what was typed in a hidden one is kept. */
    public string $activeLocale = 'en';

    public function mount(?ChatEntry $entry = null): void
    {
        if ($entry?->exists) {
            $this->authorize('update', $entry);
            $this->entry = $entry->load('translations');
            $this->parentId = $entry->parent_id;
        } else {
            $this->authorize('create', ChatEntry::class);

            $parent = request()->integer('parent');

            if ($parent > 0) {
                abort_unless(ChatEntry::query()->whereKey($parent)->exists(), 404);
                $this->parentId = $parent;
            }
        }

        $this->form->initialize($this->entry);
        $this->activeLocale = config()->string('locales.default');
    }

    public function selectLocale(string $locale): void
    {
        if (in_array($locale, config()->array('locales.landing'), true)) {
            $this->activeLocale = $locale;
        }
    }

    public function save(SaveChatEntry $saveChatEntry): void
    {
        $this->entry === null
            ? $this->authorize('create', ChatEntry::class)
            : $this->authorize('update', $this->entry);

        $this->form->normalize();
        $this->form->validate();

        $saveChatEntry->execute($this->entry, $this->parentId, $this->form->payload());

        session()->flash('status', __($this->entry === null ? 'chat_manager.messages.created' : 'chat_manager.messages.updated'));

        $this->redirectRoute('app.chat.index');
    }

    public function render(): View
    {
        $parent = $this->parentId === null
            ? null
            : ChatEntry::query()->with('translations:id,chat_entry_id,locale,question')->find($this->parentId);

        return view('livewire.chat.editor', [
            'languages' => config()->array('locales.landing'),
            'parentLabel' => $parent === null ? null : $this->labelOf($parent),
        ])->layout('components.dashboard.layout', [
            'title' => __($this->entry === null ? 'chat_manager.create_title' : 'chat_manager.edit_title'),
        ]);
    }

    private function labelOf(ChatEntry $entry): string
    {
        foreach (config()->array('locales.landing') as $locale) {
            $translation = $entry->translations->firstWhere('locale', $locale);

            if ($translation !== null) {
                return $translation->question;
            }
        }

        return '—';
    }
}
