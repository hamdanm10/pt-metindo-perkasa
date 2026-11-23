<?php

declare(strict_types=1);

namespace App\Livewire\Services;

use App\Actions\Services\DeleteService;
use App\Actions\Services\ListServices;
use App\Actions\Services\MoveService;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Service::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function move(MoveService $moveService, int $id, string $direction): void
    {
        $service = Service::query()->findOrFail($id);

        $this->authorize('update', $service);

        $moveService->execute($service, $direction === 'up');
    }

    public function delete(DeleteService $deleteAction, int $id): void
    {
        $service = Service::query()->findOrFail($id);

        $this->authorize('delete', $service);

        $deleteAction->execute($service);

        session()->now('status', __('services.messages.deleted'));
    }

    public function render(ListServices $listServices): View
    {
        $this->authorize('viewAny', Service::class);

        return view('livewire.services.index', [
            'services' => $listServices->execute($this->search),
            'ends' => $listServices->ends(),
        ])
            ->layout('components.dashboard.layout', ['title' => __('services.title')]);
    }
}
