<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Staff = 'staff';
    case Customer = 'customer';
    case Distributor = 'distributor';

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
     * Roles that sign in at /portal (customers and distributors).
     *
     * @return list<self>
     */
    public static function portalArea(): array
    {
        return [self::Customer, self::Distributor];
    }

    /**
     * Route name prefix of the account pages for this role, or null when its accounts are not managed in the dashboard (admin).
     */
    public function accountRoutePrefix(): ?string
    {
        return match ($this) {
            self::Staff => 'app.staff',
            self::Customer => 'app.customers',
            self::Distributor => 'app.distributors',
            self::Admin => null,
        };
    }

    public function usesStaffArea(): bool
    {
        return in_array($this, self::staffArea(), true);
    }

    /**
     * Route name prefix of this role's area: `app` or `portal`.
     */
    public function area(): string
    {
        return $this->usesStaffArea() ? 'app' : 'portal';
    }

    /**
     * Name of the route that shows the home page of this role's area.
     */
    public function homeRoute(): string
    {
        return $this->usesStaffArea() ? 'app.dashboard' : 'portal.dashboard';
    }
}
