<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class SessionRevoker
{
    /**
     * End every session of the account, except the given one. Only the database session
     * driver can be searched by user. With another driver an inactive account is still
     * refused on its next request by the role middleware.
     */
    public function forUser(User $user, ?string $exceptSessionId = null): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::table(config()->string('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->when($exceptSessionId !== null, fn ($query) => $query->where('id', '!=', $exceptSessionId))
            ->delete();
    }
}
