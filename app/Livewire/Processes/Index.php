<?php

declare(strict_types=1);

namespace App\Livewire\Processes;

use App\Actions\Processes\ListProcesses;
use App\Actions\Processes\MoveProcess;
use App\Models\Process;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $category = '';

    public function mount(): void
    {
        $this->authorize('viewAny', Process::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategory(): void
    {
        $this->resetPage();
    }

    public function move(MoveProcess $moveProcess, int $id, string $direction): void
    {
        $process = Process::query()->findOrFail($id);

        $this->authorize('update', $process);

        $moveProcess->execute($process, $direction === 'up');
    }

    public function render(ListProcesses $listProcesses): View
    {
        $this->authorize('viewAny', Process::class);

        return view('livewire.processes.index', [
            'processes' => $listProcesses->execute($this->search, ctype_digit($this->category) ? (int) $this->category : null),
        ])->layout('components.dashboard.layout', ['title' => __('processes.title')]);
    }
}
