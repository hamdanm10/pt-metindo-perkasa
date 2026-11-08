<?php

declare(strict_types=1);

namespace App\Livewire\Services;

use App\Actions\Services\SaveService;
use App\Livewire\Forms\ServiceForm;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Editor extends Component
{
    #[Locked]
    public ?Service $service = null;

    public ServiceForm $form;

    public function mount(?Service $service = null): void
    {
        if ($service?->exists) {
            $this->authorize('update', $service);
            $this->service = $service;
            $this->form->setService($service);
        } else {
            $this->authorize('create', Service::class);
        }
    }

    public function save(SaveService $saveService): void
    {
        $this->service === null
            ? $this->authorize('create', Service::class)
            : $this->authorize('update', $this->service);

        $this->form->normalize();
        $this->form->validate();

        $saveService->execute($this->service, $this->form->name, $this->form->description);

        session()->flash('status', __($this->service === null ? 'services.messages.created' : 'services.messages.updated'));

        $this->redirectRoute('app.services.index');
    }

    public function render(): View
    {
        return view('livewire.services.editor')
            ->layout('components.dashboard.layout', [
                'title' => __($this->service === null ? 'services.create_title' : 'services.edit_title'),
            ]);
    }
}
