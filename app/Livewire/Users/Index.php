<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\Actions\Users\ListUsers;
use App\Livewire\Concerns\ManagesAccountKind;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use ManagesAccountKind;
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    public function mount(string $kind): void
    {
        $this->setKind($kind);
        $this->authorize('viewAny', User::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render(ListUsers $listUsers): View
    {
        $this->authorize('viewAny', User::class);

        $users = $listUsers->execute(
            $this->role(),
            $this->search,
            in_array($this->status, ['active', 'inactive'], true) ? $this->status : null,
        );

        return view('livewire.users.index', ['users' => $users, 'kind' => $this->kind, 'prefix' => $this->routePrefix()])
            ->layout('components.dashboard.layout', ['title' => __("users.kinds.{$this->kind}.title")]);
    }
}
