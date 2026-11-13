<?php

declare(strict_types=1);

namespace App\Services\Dashboard;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/**
 * The dashboard menu. Add an item here when a feature gets its page. Hiding an item is
 * not access control: the route stays protected by middleware and policies.
 */
class DashboardMenu
{
    /**
     * @return list<array{route: string, label: string, active: string, icon: string, group: string, roles: list<Role>}>
     */
    private function items(): array
    {
        return [
            ['route' => 'app.dashboard', 'label' => 'dashboard.nav.home', 'active' => 'app.dashboard', 'icon' => 'home', 'group' => 'menu', 'roles' => Role::staffArea()],
            ['route' => 'app.articles.index', 'label' => 'dashboard.nav.articles', 'active' => 'app.articles.*', 'icon' => 'document', 'group' => 'content', 'roles' => [Role::Admin]],
            ['route' => 'app.products.index', 'label' => 'dashboard.nav.products', 'active' => 'app.products.*', 'icon' => 'cube', 'group' => 'content', 'roles' => [Role::Admin]],
            ['route' => 'app.services.index', 'label' => 'dashboard.nav.services', 'active' => 'app.services.*', 'icon' => 'wrench', 'group' => 'content', 'roles' => [Role::Admin]],
            ['route' => 'app.chat.index', 'label' => 'dashboard.nav.chat', 'active' => 'app.chat.*', 'icon' => 'question', 'group' => 'content', 'roles' => [Role::Admin]],
            ['route' => 'app.whatsapp.index', 'label' => 'dashboard.nav.whatsapp', 'active' => 'app.whatsapp.*', 'icon' => 'phone', 'group' => 'content', 'roles' => [Role::Admin]],
            ['route' => 'app.staff.index', 'label' => 'dashboard.nav.staff', 'active' => 'app.staff.*', 'icon' => 'briefcase', 'group' => 'users', 'roles' => [Role::Admin]],
            ['route' => 'app.customers.index', 'label' => 'dashboard.nav.customers', 'active' => 'app.customers.*', 'icon' => 'users', 'group' => 'users', 'roles' => [Role::Admin]],
            ['route' => 'app.distributors.index', 'label' => 'dashboard.nav.distributors', 'active' => 'app.distributors.*', 'icon' => 'truck', 'group' => 'users', 'roles' => [Role::Admin]],
            ['route' => 'app.customer-inquiries.index', 'label' => 'dashboard.nav.customer_inquiries', 'active' => 'app.customer-inquiries.*', 'icon' => 'chat', 'group' => 'support', 'roles' => Role::staffArea()],
            ['route' => 'app.distributor-inquiries.index', 'label' => 'dashboard.nav.distributor_inquiries', 'active' => 'app.distributor-inquiries.*', 'icon' => 'chat', 'group' => 'support', 'roles' => Role::staffArea()],
            ['route' => 'portal.dashboard', 'label' => 'dashboard.nav.home', 'active' => 'portal.dashboard', 'icon' => 'home', 'group' => 'menu', 'roles' => Role::portalArea()],
            ['route' => 'portal.inquiries.index', 'label' => 'dashboard.nav.inquiries', 'active' => 'portal.inquiries.*', 'icon' => 'chat', 'group' => 'support', 'roles' => Role::portalArea()],
        ];
    }

    /**
     * The items this user may open, in order.
     *
     * @return list<array{route: string, label: string, active: string, icon: string, group: string}>
     */
    public function forUser(User $user): array
    {
        $menu = [];

        foreach ($this->items() as $item) {
            if (in_array($user->role, $item['roles'], true) && Route::has($item['route'])) {
                $menu[] = ['route' => $item['route'], 'label' => $item['label'], 'active' => $item['active'], 'icon' => $item['icon'], 'group' => $item['group']];
            }
        }

        return $menu;
    }

    /**
     * The same items under their group labels (Menu, Content, User Management). A group with no item is left out.
     *
     * @return list<array{label: string, items: list<array{route: string, label: string, active: string, icon: string, group: string}>}>
     */
    public function groupsForUser(User $user): array
    {
        $groups = [];

        foreach ($this->forUser($user) as $item) {
            $groups[$item['group']]['label'] = 'dashboard.groups.'.$item['group'];
            $groups[$item['group']]['items'][] = $item;
        }

        return array_values($groups);
    }
}
