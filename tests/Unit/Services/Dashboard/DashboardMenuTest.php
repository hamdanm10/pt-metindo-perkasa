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

        $this->assertSame(['app.dashboard', 'app.articles.index', 'app.products.index', 'app.services.index', 'app.staff.index', 'app.customers.index'], array_column($menu, 'route'));
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

    public function test_admin_items_are_grouped_under_menu_content_and_user_management_with_icons(): void
    {
        $groups = (new DashboardMenu)->groupsForUser(User::factory()->admin()->make());

        $this->assertSame(['dashboard.groups.menu', 'dashboard.groups.content', 'dashboard.groups.users'], array_column($groups, 'label'));
        $this->assertSame(['app.dashboard'], array_column($groups[0]['items'], 'route'));
        $this->assertSame(['app.articles.index', 'app.products.index', 'app.services.index'], array_column($groups[1]['items'], 'route'));
        $this->assertSame(['app.staff.index', 'app.customers.index'], array_column($groups[2]['items'], 'route'));
        $this->assertSame(['home', 'document', 'cube', 'wrench', 'briefcase', 'users'], array_column(array_merge(...array_column($groups, 'items')), 'icon'));
    }

    public function test_a_group_without_items_is_left_out(): void
    {
        $groups = (new DashboardMenu)->groupsForUser(User::factory()->staff()->make());

        $this->assertSame(['dashboard.groups.menu'], array_column($groups, 'label'));
    }
}
