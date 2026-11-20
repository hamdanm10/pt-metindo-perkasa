<?php

declare(strict_types=1);

namespace App\Livewire\Materials;

use App\Actions\Materials\SaveMaterial;
use App\Livewire\Forms\MaterialForm;
use App\Models\Material;
use App\Services\Materials\MaterialTdsStore;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class Editor extends Component
{
    use WithFileUploads;

    private const MAX_LINKS = 10;

    #[Locked]
    public ?Material $material = null;

    public MaterialForm $form;

    public string $activeLocale = 'en';

    public function mount(?Material $material = null): void
    {
        if ($material?->exists) {
            $this->authorize('update', $material);
            $material->load(['translations', 'links']);
            $this->material = $material;
        } else {
            $this->authorize('create', Material::class);
        }

        $this->form->initialize($this->material);
        $this->activeLocale = config()->string('locales.default');
    }

    public function updatedFormTds(): void
    {
        $this->form->validateOnly('tds');
    }

    public function selectLocale(string $locale): void
    {
        if (in_array($locale, config()->array('locales.landing'), true)) {
            $this->activeLocale = $locale;
        }
    }

    public function addLink(): void
    {
        if (count($this->form->links) < self::MAX_LINKS) {
            $this->form->links[] = ['label' => '', 'url' => ''];
        }
    }

    public function removeLink(int $index): void
    {
        unset($this->form->links[$index]);
        $this->form->links = array_values($this->form->links);
    }

    public function save(SaveMaterial $saveMaterial): void
    {
        $this->material === null
            ? $this->authorize('create', Material::class)
            : $this->authorize('update', $this->material);

        $this->form->normalize();
        $this->form->validate();

        $saveMaterial->execute(
            $this->material,
            $this->form->name,
            $this->form->slug(),
            $this->form->payload(),
            $this->form->tds,
            $this->form->removeTds,
        );

        session()->flash('status', __($this->material === null ? 'materials.messages.created' : 'materials.messages.updated'));

        $this->redirectRoute('app.materials.index');
    }

    public function render(MaterialTdsStore $tds): View
    {
        $path = $this->material?->tds_path;

        return view('livewire.materials.editor', [
            'currentTds' => $path === null ? null : $tds->url($path),
        ])->layout('components.dashboard.layout', [
            'title' => __($this->material === null ? 'materials.create_title' : 'materials.edit_title'),
        ]);
    }
}
