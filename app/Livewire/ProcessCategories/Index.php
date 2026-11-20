<?php

declare(strict_types=1);

namespace App\Livewire\ProcessCategories;

use App\Actions\ProcessCategories\ListProcessCategories;
use App\Actions\ProcessCategories\MoveProcessCategory;
use App\Models\ProcessCategory;
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
        $this->authorize('viewAny', ProcessCategory::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function move(MoveProcessCategory $moveProcessCategory, int $id, string $direction): void
    {
        $processCategory = ProcessCategory::query()->findOrFail($id);

        $this->authorize('update', $processCategory);

        $moveProcessCategory->execute($processCategory, $direction === 'up');
    }

    public function render(ListProcessCategories $listProcessCategories): View
    {
        $this->authorize('viewAny', ProcessCategory::class);

        return view('livewire.process-categories.index', [
            'processCategories' => $listProcessCategories->execute($this->search),
            'ends' => $listProcessCategories->ends(),
        ])
            ->layout('components.dashboard.layout', ['title' => __('process_categories.title')]);
    }
}
