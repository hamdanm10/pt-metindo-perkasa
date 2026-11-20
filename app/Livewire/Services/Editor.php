<?php

declare(strict_types=1);

namespace App\Livewire\Services;

use App\Actions\Services\SaveService;
use App\Livewire\Forms\ServiceForm;
use App\Models\Service;
use App\Services\Services\ServiceImageStore;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class Editor extends Component
{
    use WithFileUploads;

    #[Locked]
    public ?Service $service = null;

    public ServiceForm $form;

    public string $activeLocale = 'en';

    public function mount(?Service $service = null): void
    {
        if ($service?->exists) {
            $this->authorize('update', $service);
            $service->load('translations');
            $this->service = $service;
        } else {
            $this->authorize('create', Service::class);
        }

        $this->form->initialize($this->service);
        $this->activeLocale = config()->string('locales.default');
    }

    public function updatedFormImage(): void
    {
        $this->form->validateOnly('image');
    }

    public function selectLocale(string $locale): void
    {
        if (in_array($locale, config()->array('locales.landing'), true)) {
            $this->activeLocale = $locale;
        }
    }

    public function save(SaveService $saveService): void
    {
        $this->service === null
            ? $this->authorize('create', Service::class)
            : $this->authorize('update', $this->service);

        $this->form->normalize();
        $this->form->validate();

        $saveService->execute(
            $this->service,
            $this->form->name,
            $this->form->slug(),
            null,
            $this->form->isActive,
            $this->form->payload(),
            $this->form->image,
        );

        session()->flash('status', __($this->service === null ? 'services.messages.created' : 'services.messages.updated'));

        $this->redirectRoute('app.services.index');
    }

    public function render(ServiceImageStore $images): View
    {
        $path = $this->service?->image_path;

        return view('livewire.services.editor', [
            'currentImage' => $path === null ? null : $images->url($path),
        ])->layout('components.dashboard.layout', [
            'title' => __($this->service === null ? 'services.create_title' : 'services.edit_title'),
        ]);
    }
}
