<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Str;

class UpdateUser
{
    /**
     * Changes the name and email only. The role of an account never changes after it is created.
     */
    public function execute(User $target, string $name, string $email): User
    {
        $target->forceFill([
            'name' => $name,
            'email' => Str::lower($email),
        ])->save();

        return $target;
    }
}
