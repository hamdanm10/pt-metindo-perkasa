<?php

declare(strict_types=1);

namespace App\Livewire\Services;

use App\Actions\Services\ListServices;
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

    public function render(ListServices $listServices): View
    {
        $this->authorize('viewAny', Service::class);

        return view('livewire.services.index', ['services' => $listServices->execute($this->search)])
            ->layout('components.dashboard.layout', ['title' => __('services.title')]);
    }
}
