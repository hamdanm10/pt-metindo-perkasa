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
