<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait ResolvesActor
{
    protected function actor(): User
    {
        $user = Auth::user();

        abort_unless($user instanceof User, 403);

        return $user;
    }
}
