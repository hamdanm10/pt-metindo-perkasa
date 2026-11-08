<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\Actions\Users\ListUsers;
use App\Enums\Role;
use App\Models\User;
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
    public string $role = '';

    #[Url(except: '')]
    public string $status = '';

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRole(): void
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
            $this->search,
            Role::tryFrom($this->role),
            in_array($this->status, ['active', 'inactive'], true) ? $this->status : null,
        );

        return view('livewire.users.index', ['users' => $users])
            ->layout('components.dashboard.layout', ['title' => __('users.title')]);
    }
}
