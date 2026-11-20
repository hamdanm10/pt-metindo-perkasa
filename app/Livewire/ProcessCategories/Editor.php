<?php

declare(strict_types=1);

namespace App\Livewire\ProcessCategories;

use App\Actions\ProcessCategories\SaveProcessCategory;
use App\Livewire\Forms\ProcessCategoryForm;
use App\Models\ProcessCategory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Editor extends Component
{
    #[Locked]
    public ?ProcessCategory $processCategory = null;

    public ProcessCategoryForm $form;

    public string $activeLocale = 'en';

    public function mount(?ProcessCategory $processCategory = null): void
    {
        if ($processCategory?->exists) {
            $this->authorize('update', $processCategory);
            $processCategory->load('translations');
            $this->processCategory = $processCategory;
        } else {
            $this->authorize('create', ProcessCategory::class);
        }

        $this->form->initialize($this->processCategory);
        $this->activeLocale = config()->string('locales.default');
    }

    public function selectLocale(string $locale): void
    {
        if (in_array($locale, config()->array('locales.landing'), true)) {
            $this->activeLocale = $locale;
        }
    }

    public function save(SaveProcessCategory $saveProcessCategory): void
    {
        $this->processCategory === null
            ? $this->authorize('create', ProcessCategory::class)
            : $this->authorize('update', $this->processCategory);

        $this->form->normalize();
        $this->form->validate();

        $saveProcessCategory->execute($this->processCategory, $this->form->name, $this->form->slug(), $this->form->isActive, $this->form->payload());

        session()->flash('status', __($this->processCategory === null ? 'process_categories.messages.created' : 'process_categories.messages.updated'));

        $this->redirectRoute('app.process-categories.index');
    }

    public function render(): View
    {
        return view('livewire.process-categories.editor')
            ->layout('components.dashboard.layout', [
                'title' => __($this->processCategory === null ? 'process_categories.create_title' : 'process_categories.edit_title'),
            ]);
    }
}
