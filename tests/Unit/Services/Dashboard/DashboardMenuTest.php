<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Dashboard;

use App\Models\User;
use App\Services\Dashboard\DashboardMenu;
use Tests\TestCase;

class DashboardMenuTest extends TestCase
{
    public function test_staff_area_roles_get_the_staff_home(): void
    {
        foreach (['admin', 'staff'] as $role) {
            $menu = (new DashboardMenu)->forUser(User::factory()->{$role}()->make());

            $this->assertSame(['app.dashboard'], array_column($menu, 'route'));
        }
    }

    public function test_customers_get_the_customer_home(): void
    {
        $menu = (new DashboardMenu)->forUser(User::factory()->customer()->make());

        $this->assertSame(['customer.dashboard'], array_column($menu, 'route'));
    }
}
