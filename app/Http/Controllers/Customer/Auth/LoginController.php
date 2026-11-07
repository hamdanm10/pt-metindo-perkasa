<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer\Auth;

use App\Enums\Role;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

class LoginController extends AuthenticatedSessionController
{
    protected function roles(): array
    {
        return Role::customerArea();
    }

    protected function loginRoute(): string
    {
        return 'customer.login';
    }

    protected function homeRoute(): string
    {
        return 'customer.dashboard';
    }

    protected function titleKey(): string
    {
        return 'auth.customer_title';
    }
}
