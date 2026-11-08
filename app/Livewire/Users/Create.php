<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\Actions\Users\CreateUser;
use App\Enums\Role;
use App\Livewire\Forms\UserForm;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    public UserForm $form;

    public function mount(): void
    {
        $this->authorize('create', User::class);
    }

    public function save(CreateUser $createUser): void
    {
        $this->authorize('create', User::class);

        $this->form->normalize();
        $this->form->validate();

        $createUser->execute(
            $this->form->name,
            $this->form->email,
            $this->form->password,
            Role::from($this->form->role),
            $this->form->locale,
        );

        $this->form->password = '';

        session()->flash('status', __('users.messages.created'));

        $this->redirectRoute('app.users.index');
    }

    public function render(): View
    {
        return view('livewire.users.create')
            ->layout('components.dashboard.layout', ['title' => __('users.create_title')]);
    }
}
