<?php

declare(strict_types=1);

namespace App\Livewire\Materials;

use App\Actions\Materials\ListMaterials;
use App\Models\Material;
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
        $this->authorize('viewAny', Material::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render(ListMaterials $listMaterials): View
    {
        $this->authorize('viewAny', Material::class);

        return view('livewire.materials.index', ['materials' => $listMaterials->execute($this->search)])
            ->layout('components.dashboard.layout', ['title' => __('materials.title')]);
    }
}
