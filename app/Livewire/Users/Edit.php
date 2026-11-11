<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\Actions\Users\ResetUserPassword;
use App\Actions\Users\SetUserActive;
use App\Actions\Users\UpdateUser;
use App\Livewire\Concerns\ManagesAccountKind;
use App\Livewire\Concerns\ResolvesActor;
use App\Livewire\Forms\UserForm;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Edit extends Component
{
    use ManagesAccountKind;
    use ResolvesActor;

    #[Locked]
    public User $user;

    public UserForm $form;

    public string $newPassword = '';

    public function mount(User $user, string $kind): void
    {
        $this->setKind($kind);
        abort_unless($user->role === $this->role(), 404);
        $this->authorize('update', $user);

        $this->user = $user;
        $this->form->setUser($user);
    }

    public function save(UpdateUser $updateUser): void
    {
        $this->authorize('update', $this->user);

        $this->form->normalize();
        $this->form->validate();

        $updateUser->execute($this->user, $this->form->name, $this->form->email);

        $this->user->refresh();

        session()->now('status', __('users.messages.updated'));
    }

    public function resetPassword(ResetUserPassword $resetPassword): void
    {
        $this->authorize('resetPassword', $this->user);

        $this->validate(
            ['newPassword' => ['required', 'string', 'max:72', Password::min(8)->mixedCase()->numbers()]],
            attributes: ['newPassword' => __('users.fields.new_password')],
        );

        $keep = $this->actor()->is($this->user) ? session()->getId() : null;

        $resetPassword->execute($this->user, $this->newPassword, $keep);

        $this->newPassword = '';

        session()->now('status', __('users.messages.password_reset'));
    }

    public function toggleActive(SetUserActive $setActive): void
    {
        $this->authorize('setActive', $this->user);

        $activate = ! $this->user->is_active;

        $setActive->execute($this->actor(), $this->user, $activate);

        $this->user->refresh();

        session()->now('status', $activate ? __('users.messages.activated') : __('users.messages.deactivated'));
    }

    public function render(): View
    {
        return view('livewire.users.edit', ['kind' => $this->kind, 'prefix' => $this->routePrefix()])
            ->layout('components.dashboard.layout', ['title' => __("users.kinds.{$this->kind}.edit_title")]);
    }
}
