<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user = null;

    public string $name = '';

    public string $email = '';

    public string $role = 'customer';

    public string $password = '';

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user?->id)],
            'role' => ['required', Rule::enum(Role::class)],
        ];

        if ($this->user === null) {
            $rules['password'] = ['required', 'string', 'max:72', Password::min(8)->mixedCase()->numbers()];
        }

        return $rules;
    }

    /**
     * Emails are stored in lower case, so they are compared in lower case too.
     */
    public function normalize(): void
    {
        $this->email = Str::lower(trim($this->email));
        $this->name = trim($this->name);
    }
}
