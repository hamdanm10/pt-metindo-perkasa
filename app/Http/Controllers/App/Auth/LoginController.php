<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Auth;

use App\Enums\Role;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

class LoginController extends AuthenticatedSessionController
{
    protected function roles(): array
    {
        return Role::staffArea();
    }

    protected function loginRoute(): string
    {
        return 'app.login';
    }

    protected function titleKey(): string
    {
        return 'auth.staff_title';
    }
}
