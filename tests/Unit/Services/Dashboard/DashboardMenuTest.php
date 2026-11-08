<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Dashboard;

use App\Models\User;
use App\Services\Dashboard\DashboardMenu;
use Tests\TestCase;

class DashboardMenuTest extends TestCase
{
    public function test_admin_gets_the_home_and_the_accounts_page(): void
    {
        $menu = (new DashboardMenu)->forUser(User::factory()->admin()->make());

        $this->assertSame(['app.dashboard', 'app.users.index', 'app.articles.index', 'app.products.index'], array_column($menu, 'route'));
    }

    public function test_staff_gets_only_the_home(): void
    {
        $menu = (new DashboardMenu)->forUser(User::factory()->staff()->make());

        $this->assertSame(['app.dashboard'], array_column($menu, 'route'));
    }

    public function test_customers_get_the_customer_home(): void
    {
        $menu = (new DashboardMenu)->forUser(User::factory()->customer()->make());

        $this->assertSame(['customer.dashboard'], array_column($menu, 'route'));
    }
}
