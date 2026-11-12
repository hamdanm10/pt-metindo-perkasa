<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal\Auth;

use App\Enums\Role;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

class LoginController extends AuthenticatedSessionController
{
    protected function roles(): array
    {
        return Role::portalArea();
    }

    protected function loginRoute(): string
    {
        return 'portal.login';
    }

    protected function homeRoute(): string
    {
        return 'portal.dashboard';
    }

    protected function titleKey(): string
    {
        return 'auth.portal_title';
    }
}
