<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use App\Services\Auth\SessionRevoker;

class ResetUserPassword
{
    public function __construct(private readonly SessionRevoker $sessions) {}

    /**
     * Set a new password and end the account's sessions, keeping the given one so an
     * admin resetting their own password is not signed out of the page they are on.
     */
    public function execute(User $target, string $password, ?string $keepSessionId = null): void
    {
        $target->forceFill(['password' => $password])->save();

        $this->sessions->forUser($target, $keepSessionId);
    }
}
