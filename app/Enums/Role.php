<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Staff = 'staff';
    case Customer = 'customer';

    /**
     * Roles that sign in at /app.
     *
     * @return list<self>
     */
    public static function staffArea(): array
    {
        return [self::Admin, self::Staff];
    }

    /**
     * Roles that sign in at /customer.
     *
     * @return list<self>
     */
    public static function customerArea(): array
    {
        return [self::Customer];
    }

    /**
     * Route name prefix of the account pages for this role, or null when its accounts are not managed in the dashboard (admin).
     */
    public function accountRoutePrefix(): ?string
    {
        return match ($this) {
            self::Staff => 'app.staff',
            self::Customer => 'app.customers',
            self::Admin => null,
        };
    }

    public function usesStaffArea(): bool
    {
        return in_array($this, self::staffArea(), true);
    }

    /**
     * Name of the route that shows the home page of this role's area.
     */
    public function homeRoute(): string
    {
        return $this->usesStaffArea() ? 'app.dashboard' : 'customer.dashboard';
    }
}
