<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\Actions\Users\CreateUser;
use App\Livewire\Concerns\ManagesAccountKind;
use App\Livewire\Forms\UserForm;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    use ManagesAccountKind;

    public UserForm $form;

    public function mount(string $kind): void
    {
        $this->setKind($kind);
        $this->authorize('create', User::class);

        $this->form->password = config()->string('auth.default_account_password');
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
            $this->role(),
        );

        $this->form->password = '';

        session()->flash('status', __('users.messages.created'));

        $this->redirectRoute($this->routePrefix().'.index');
    }

    public function render(): View
    {
        return view('livewire.users.create', ['kind' => $this->kind, 'prefix' => $this->routePrefix()])
            ->layout('components.dashboard.layout', ['title' => __("users.kinds.{$this->kind}.create_title")]);
    }
}
