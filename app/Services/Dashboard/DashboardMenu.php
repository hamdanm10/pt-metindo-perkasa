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
     * @return list<array{route: string, label: string, active: string, roles: list<Role>}>
     */
    private function items(): array
    {
        return [
            ['route' => 'app.dashboard', 'label' => 'dashboard.nav.home', 'active' => 'app.dashboard', 'roles' => Role::staffArea()],
            ['route' => 'app.users.index', 'label' => 'dashboard.nav.users', 'active' => 'app.users.*', 'roles' => [Role::Admin]],
            ['route' => 'app.articles.index', 'label' => 'dashboard.nav.articles', 'active' => 'app.articles.*', 'roles' => [Role::Admin]],
            ['route' => 'app.products.index', 'label' => 'dashboard.nav.products', 'active' => 'app.products.*', 'roles' => [Role::Admin]],
            ['route' => 'customer.dashboard', 'label' => 'dashboard.nav.home', 'active' => 'customer.dashboard', 'roles' => Role::customerArea()],
        ];
    }

    /**
     * @return list<array{route: string, label: string, active: string}>
     */
    public function forUser(User $user): array
    {
        $menu = [];

        foreach ($this->items() as $item) {
            if (in_array($user->role, $item['roles'], true) && Route::has($item['route'])) {
                $menu[] = ['route' => $item['route'], 'label' => $item['label'], 'active' => $item['active']];
            }
        }

        return $menu;
    }
}
