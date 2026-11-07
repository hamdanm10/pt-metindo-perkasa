<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;

class UpdateLocale
{
    /**
     * Remember the locale on the account. A guest has no account, and the controller
     * keeps their choice in the session.
     */
    public function execute(?User $user, string $locale): void
    {
        $user?->forceFill(['locale' => $locale])->save();
    }
}
