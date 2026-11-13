<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Dashboard;

use App\Models\User;
use App\Services\Dashboard\DashboardMenu;
use Tests\TestCase;

class DashboardMenuTest extends TestCase
{
    public function test_admin_gets_the_home_the_content_the_accounts_and_the_questions(): void
    {
        $menu = (new DashboardMenu)->forUser(User::factory()->admin()->make());

        $this->assertSame(['app.dashboard', 'app.articles.index', 'app.products.index', 'app.services.index', 'app.chat.index', 'app.whatsapp.index', 'app.staff.index', 'app.customers.index', 'app.distributors.index', 'app.customer-inquiries.index', 'app.distributor-inquiries.index'], array_column($menu, 'route'));
    }

    public function test_staff_gets_the_home_and_the_questions(): void
    {
        $menu = (new DashboardMenu)->forUser(User::factory()->staff()->make());

        $this->assertSame(['app.dashboard', 'app.customer-inquiries.index', 'app.distributor-inquiries.index'], array_column($menu, 'route'));
    }

    public function test_customers_get_the_customer_home_and_the_questions(): void
    {
        $menu = (new DashboardMenu)->forUser(User::factory()->customer()->make());

        $this->assertSame(['portal.dashboard', 'portal.inquiries.index'], array_column($menu, 'route'));
    }

    public function test_distributors_get_the_same_portal_menu_as_customers(): void
    {
        $menu = (new DashboardMenu)->forUser(User::factory()->distributor()->make());

        $this->assertSame(['portal.dashboard', 'portal.inquiries.index'], array_column($menu, 'route'));
    }

    public function test_admin_items_are_grouped_under_menu_content_and_user_management_with_icons(): void
    {
        $groups = (new DashboardMenu)->groupsForUser(User::factory()->admin()->make());

        $this->assertSame(['dashboard.groups.menu', 'dashboard.groups.content', 'dashboard.groups.users', 'dashboard.groups.support'], array_column($groups, 'label'));
        $this->assertSame(['app.dashboard'], array_column($groups[0]['items'], 'route'));
        $this->assertSame(['app.articles.index', 'app.products.index', 'app.services.index', 'app.chat.index', 'app.whatsapp.index'], array_column($groups[1]['items'], 'route'));
        $this->assertSame(['app.staff.index', 'app.customers.index', 'app.distributors.index'], array_column($groups[2]['items'], 'route'));
        $this->assertSame(['app.customer-inquiries.index', 'app.distributor-inquiries.index'], array_column($groups[3]['items'], 'route'));
        $this->assertSame(['home', 'document', 'cube', 'wrench', 'question', 'phone', 'briefcase', 'users', 'truck', 'chat', 'chat'], array_column(array_merge(...array_column($groups, 'items')), 'icon'));
    }

    public function test_related_items_are_gathered_into_dropdown_entries_and_a_lone_item_stays_a_plain_item(): void
    {
        $groups = (new DashboardMenu)->groupsForUser(User::factory()->admin()->make());

        $this->assertSame(['app.articles.index', 'dashboard.nav.parents.catalog', 'dashboard.nav.parents.contact'], array_map(fn (array $entry): string => $entry['route'] ?? $entry['label'], $groups[1]['entries']));
        $this->assertSame(['app.staff.index', 'dashboard.nav.parents.clients'], array_map(fn (array $entry): string => $entry['route'] ?? $entry['label'], $groups[2]['entries']));
        $this->assertSame(['dashboard.nav.parents.qa'], array_column($groups[3]['entries'], 'label'));

        $staff = (new DashboardMenu)->groupsForUser(User::factory()->staff()->make());
        $this->assertSame('dashboard.nav.parents.qa', $staff[1]['entries'][0]['label']);

        $portal = (new DashboardMenu)->groupsForUser(User::factory()->customer()->make());
        $this->assertSame(['portal.inquiries.index'], array_column($portal[1]['entries'], 'route'));
    }

    public function test_a_group_without_items_is_left_out(): void
    {
        $groups = (new DashboardMenu)->groupsForUser(User::factory()->staff()->make());

        $this->assertSame(['dashboard.groups.menu', 'dashboard.groups.support'], array_column($groups, 'label'));
    }
}
