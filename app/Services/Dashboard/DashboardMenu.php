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
     * Dropdown buttons that gather related items inside a group, with their icon. Items point to one with `parent`.
     */
    private const PARENTS = [
        'catalog' => ['label' => 'dashboard.nav.parents.catalog', 'icon' => 'cube'],
        'contact' => ['label' => 'dashboard.nav.parents.contact', 'icon' => 'phone'],
        'clients' => ['label' => 'dashboard.nav.parents.clients', 'icon' => 'users'],
        'qa' => ['label' => 'dashboard.nav.parents.qa', 'icon' => 'chat'],
    ];

    /**
     * @return list<array{route: string, label: string, active: string, icon: string, group: string, parent: string|null, roles: list<Role>}>
     */
    private function items(): array
    {
        return [
            ['route' => 'app.dashboard', 'label' => 'dashboard.nav.home', 'active' => 'app.dashboard', 'icon' => 'home', 'group' => 'menu', 'parent' => null, 'roles' => Role::staffArea()],
            ['route' => 'app.articles.index', 'label' => 'dashboard.nav.articles', 'active' => 'app.articles.*', 'icon' => 'document', 'group' => 'content', 'parent' => null, 'roles' => Role::staffArea()],
            ['route' => 'app.products.index', 'label' => 'dashboard.nav.products', 'active' => 'app.products.*', 'icon' => 'cube', 'group' => 'content', 'parent' => 'catalog', 'roles' => [Role::Admin]],
            ['route' => 'app.services.index', 'label' => 'dashboard.nav.services', 'active' => 'app.services.*', 'icon' => 'wrench', 'group' => 'content', 'parent' => 'catalog', 'roles' => [Role::Admin]],
            ['route' => 'app.chat.index', 'label' => 'dashboard.nav.chat', 'active' => 'app.chat.*', 'icon' => 'question', 'group' => 'content', 'parent' => 'contact', 'roles' => [Role::Admin]],
            ['route' => 'app.whatsapp.index', 'label' => 'dashboard.nav.whatsapp', 'active' => 'app.whatsapp.*', 'icon' => 'phone', 'group' => 'content', 'parent' => 'contact', 'roles' => [Role::Admin]],
            ['route' => 'app.staff.index', 'label' => 'dashboard.nav.staff', 'active' => 'app.staff.*', 'icon' => 'briefcase', 'group' => 'users', 'parent' => null, 'roles' => [Role::Admin]],
            ['route' => 'app.customers.index', 'label' => 'dashboard.nav.customers', 'active' => 'app.customers.*', 'icon' => 'users', 'group' => 'users', 'parent' => 'clients', 'roles' => [Role::Admin]],
            ['route' => 'app.distributors.index', 'label' => 'dashboard.nav.distributors', 'active' => 'app.distributors.*', 'icon' => 'truck', 'group' => 'users', 'parent' => 'clients', 'roles' => [Role::Admin]],
            ['route' => 'app.customer-inquiries.index', 'label' => 'dashboard.nav.customer_inquiries', 'active' => 'app.customer-inquiries.*', 'icon' => 'chat', 'group' => 'support', 'parent' => 'qa', 'roles' => Role::staffArea()],
            ['route' => 'app.distributor-inquiries.index', 'label' => 'dashboard.nav.distributor_inquiries', 'active' => 'app.distributor-inquiries.*', 'icon' => 'chat', 'group' => 'support', 'parent' => 'qa', 'roles' => Role::staffArea()],
            ['route' => 'customer.dashboard', 'label' => 'dashboard.nav.home', 'active' => 'customer.dashboard', 'icon' => 'home', 'group' => 'menu', 'parent' => null, 'roles' => [Role::Customer]],
            ['route' => 'customer.inquiries.index', 'label' => 'dashboard.nav.inquiries', 'active' => 'customer.inquiries.*', 'icon' => 'chat', 'group' => 'support', 'parent' => null, 'roles' => [Role::Customer]],
            ['route' => 'distributor.dashboard', 'label' => 'dashboard.nav.home', 'active' => 'distributor.dashboard', 'icon' => 'home', 'group' => 'menu', 'parent' => null, 'roles' => [Role::Distributor]],
            ['route' => 'distributor.inquiries.index', 'label' => 'dashboard.nav.inquiries', 'active' => 'distributor.inquiries.*', 'icon' => 'chat', 'group' => 'support', 'parent' => null, 'roles' => [Role::Distributor]],
        ];
    }

    /**
     * The items this user may open, in order.
     *
     * @return list<array{route: string, label: string, active: string, icon: string, group: string, parent: string|null}>
     */
    public function forUser(User $user): array
    {
        $menu = [];

        foreach ($this->items() as $item) {
            if (in_array($user->role, $item['roles'], true) && Route::has($item['route'])) {
                $menu[] = ['route' => $item['route'], 'label' => $item['label'], 'active' => $item['active'], 'icon' => $item['icon'], 'group' => $item['group'], 'parent' => $item['parent']];
            }
        }

        return $menu;
    }

    /**
     * The same items under their group labels (Menu, Content, User Management). A group with no item is left out.
     * `items` is the flat list; `entries` is what the sidebar draws: single items and dropdowns that gather the items sharing a `parent`.
     *
     * @return list<array{label: string, items: list<array{route: string, label: string, active: string, icon: string, group: string, parent: string|null}>, entries: list<array{label: string, icon: string, items: list<array{route: string, label: string, active: string, icon: string, group: string, parent: string|null}>}|array{route: string, label: string, active: string, icon: string, group: string, parent: string|null}>}>
     */
    public function groupsForUser(User $user): array
    {
        $groups = [];

        foreach ($this->forUser($user) as $item) {
            $groups[$item['group']]['label'] = 'dashboard.groups.'.$item['group'];
            $groups[$item['group']]['items'][] = $item;
        }

        foreach ($groups as &$group) {
            $group['entries'] = $this->entries($group['items']);
        }
        unset($group);

        return array_values($groups);
    }

    /**
     * A parent with a single visible item is drawn as that item, since a dropdown of one is pointless.
     *
     * @param  list<array{route: string, label: string, active: string, icon: string, group: string, parent: string|null}>  $items
     * @return list<array{label: string, icon: string, items: list<array{route: string, label: string, active: string, icon: string, group: string, parent: string|null}>}|array{route: string, label: string, active: string, icon: string, group: string, parent: string|null}>
     */
    private function entries(array $items): array
    {
        $slots = [];
        $children = [];

        foreach ($items as $item) {
            $parent = $item['parent'];

            if ($parent === null) {
                $slots[] = $item;
            } else {
                if (! isset($children[$parent])) {
                    $slots[] = $parent;
                }
                $children[$parent][] = $item;
            }
        }

        $entries = [];

        foreach ($slots as $slot) {
            if (! is_string($slot)) {
                $entries[] = $slot;
            } elseif (count($children[$slot]) === 1) {
                $entries[] = $children[$slot][0];
            } else {
                $entries[] = ['label' => self::PARENTS[$slot]['label'], 'icon' => self::PARENTS[$slot]['icon'], 'items' => $children[$slot]];
            }
        }

        return $entries;
    }
}
