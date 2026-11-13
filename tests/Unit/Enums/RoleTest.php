<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function test_admin_and_staff_use_the_staff_area_and_customer_does_not(): void
    {
        $this->assertSame([Role::Admin, Role::Staff], Role::staffArea());
        $this->assertSame([Role::Customer, Role::Distributor], Role::portalArea());
        $this->assertTrue(Role::Admin->usesStaffArea());
        $this->assertTrue(Role::Staff->usesStaffArea());
        $this->assertFalse(Role::Customer->usesStaffArea());
        $this->assertFalse(Role::Distributor->usesStaffArea());
    }

    public function test_each_role_has_a_home_route_in_its_area(): void
    {
        $this->assertSame('app.dashboard', Role::Admin->homeRoute());
        $this->assertSame('app.dashboard', Role::Staff->homeRoute());
        $this->assertSame('customer.dashboard', Role::Customer->homeRoute());
        $this->assertSame('distributor.dashboard', Role::Distributor->homeRoute());
        $this->assertSame('app', Role::Staff->area());
        $this->assertSame('customer', Role::Customer->area());
        $this->assertSame('distributor', Role::Distributor->area());
        $this->assertSame('app.login', Role::Admin->loginRoute());
        $this->assertSame('portal.login', Role::Customer->loginRoute());
        $this->assertSame('portal.login', Role::Distributor->loginRoute());
    }

    public function test_values_match_the_database_strings(): void
    {
        $this->assertSame(['admin', 'staff', 'customer', 'distributor'], array_column(Role::cases(), 'value'));
    }
}
