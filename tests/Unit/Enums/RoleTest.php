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
        $this->assertSame([Role::Customer], Role::customerArea());
        $this->assertTrue(Role::Admin->usesStaffArea());
        $this->assertTrue(Role::Staff->usesStaffArea());
        $this->assertFalse(Role::Customer->usesStaffArea());
    }

    public function test_each_role_has_a_home_route_in_its_area(): void
    {
        $this->assertSame('app.dashboard', Role::Admin->homeRoute());
        $this->assertSame('app.dashboard', Role::Staff->homeRoute());
        $this->assertSame('customer.dashboard', Role::Customer->homeRoute());
    }

    public function test_values_match_the_database_strings(): void
    {
        $this->assertSame(['admin', 'staff', 'customer'], array_column(Role::cases(), 'value'));
    }
}
