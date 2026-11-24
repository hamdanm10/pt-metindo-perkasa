<?php

declare(strict_types=1);

namespace App\Actions\Dashboard;

use App\Enums\Role;
use App\Models\Article;
use App\Models\Inquiry;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The numbers and latest records shown on the home page of each role. Admin and staff see the whole system
 * (account counts are admin only); a customer or distributor only sees their own questions and purchases.
 */
class BuildHomeSummary
{
    private const RECENT = 5;

    /**
     * @return array{stats: array<int, array{label: string, value: int, route: string, icon: string}>, recent: list<array{title: string, route: string, rows: array<int, array{title: string, meta: string, url: string}>}>}
     */
    public function execute(User $user): array
    {
        return $user->role->usesStaffArea() ? $this->forStaffArea($user) : $this->forPortal($user);
    }

    /**
     * @return array{stats: array<int, array{label: string, value: int, route: string, icon: string}>, recent: list<array{title: string, route: string, rows: array<int, array{title: string, meta: string, url: string}>}>}
     */
    private function forStaffArea(User $user): array
    {
        $accounts = User::query()->select('role', DB::raw('count(*) as total'))->groupBy('role')->pluck('total', 'role');
        $inquiries = $this->countByOwnerRole('inquiries', 'customer_id');
        $purchases = $this->countByOwnerRole('purchases', 'buyer_id');

        $accountStats = $user->role === Role::Admin ? [
            ['label' => 'dashboard.nav.customers', 'value' => (int) ($accounts[Role::Customer->value] ?? 0), 'route' => 'app.customers.index', 'icon' => 'users'],
            ['label' => 'dashboard.nav.distributors', 'value' => (int) ($accounts[Role::Distributor->value] ?? 0), 'route' => 'app.distributors.index', 'icon' => 'truck'],
        ] : [];

        $stats = [
            ...$accountStats,
            ['label' => 'dashboard.nav.customer_inquiries', 'value' => $inquiries[Role::Customer->value] ?? 0, 'route' => 'app.customer-inquiries.index', 'icon' => 'chat'],
            ['label' => 'dashboard.nav.distributor_inquiries', 'value' => $inquiries[Role::Distributor->value] ?? 0, 'route' => 'app.distributor-inquiries.index', 'icon' => 'chat'],
            ['label' => 'dashboard.nav.customer_purchases', 'value' => $purchases[Role::Customer->value] ?? 0, 'route' => 'app.customer-purchases.index', 'icon' => 'document'],
            ['label' => 'dashboard.nav.distributor_purchases', 'value' => $purchases[Role::Distributor->value] ?? 0, 'route' => 'app.distributor-purchases.index', 'icon' => 'document'],
            ['label' => 'dashboard.nav.articles', 'value' => Article::query()->count(), 'route' => 'app.articles.index', 'icon' => 'document'],
        ];

        $latestInquiries = Inquiry::query()->with('customer:id,name,role')->latest('id')->limit(self::RECENT)->get();
        $latestPurchases = Purchase::query()->with('buyer:id,name,role')->latest('id')->limit(self::RECENT)->get();

        return [
            'stats' => $stats,
            'recent' => [
                [
                    'title' => 'dashboard.home.recent_inquiries',
                    'route' => 'app.customer-inquiries.index',
                    'rows' => $latestInquiries->flatMap(function (Inquiry $inquiry): array {
                        $owner = $inquiry->customer;

                        return $owner === null ? [] : [[
                            'title' => Str::limit(strip_tags($inquiry->question), 80),
                            'meta' => $owner->name.' · '.$inquiry->created_at?->format('Y-m-d'),
                            'url' => route("app.{$owner->role->value}-inquiries.show", $inquiry),
                        ]];
                    })->values()->all(),
                ],
                [
                    'title' => 'dashboard.home.recent_purchases',
                    'route' => 'app.customer-purchases.index',
                    'rows' => $latestPurchases->flatMap(function (Purchase $purchase): array {
                        $owner = $purchase->buyer;

                        return $owner === null ? [] : [[
                            'title' => $this->purchaseTitle($purchase),
                            'meta' => $owner->name.' · '.$purchase->purchased_at->format('Y-m-d'),
                            'url' => route("app.{$owner->role->value}-purchases.show", $purchase),
                        ]];
                    })->values()->all(),
                ],
            ],
        ];
    }

    /**
     * @return array{stats: array<int, array{label: string, value: int, route: string, icon: string}>, recent: list<array{title: string, route: string, rows: array<int, array{title: string, meta: string, url: string}>}>}
     */
    private function forPortal(User $user): array
    {
        $area = $user->role->area();

        $inquiries = Inquiry::query()->where('customer_id', $user->id);
        $purchases = Purchase::query()->where('buyer_id', $user->id);

        return [
            'stats' => [
                ['label' => 'dashboard.nav.inquiries', 'value' => (clone $inquiries)->count(), 'route' => "{$area}.inquiries.index", 'icon' => 'chat'],
                ['label' => 'dashboard.nav.purchases', 'value' => (clone $purchases)->count(), 'route' => "{$area}.purchases.index", 'icon' => 'document'],
            ],
            'recent' => [
                [
                    'title' => 'dashboard.home.recent_inquiries',
                    'route' => "{$area}.inquiries.index",
                    'rows' => $inquiries->latest('id')->limit(self::RECENT)->get()->map(fn (Inquiry $inquiry): array => [
                        'title' => Str::limit(strip_tags($inquiry->question), 80),
                        'meta' => (string) $inquiry->created_at?->format('Y-m-d'),
                        'url' => route("{$area}.inquiries.show", $inquiry),
                    ])->values()->all(),
                ],
                [
                    'title' => 'dashboard.home.recent_purchases',
                    'route' => "{$area}.purchases.index",
                    'rows' => $purchases->latest('id')->limit(self::RECENT)->get()->map(fn (Purchase $purchase): array => [
                        'title' => $this->purchaseTitle($purchase),
                        'meta' => $purchase->purchased_at->format('Y-m-d'),
                        'url' => route("{$area}.purchases.show", $purchase),
                    ])->values()->all(),
                ],
            ],
        ];
    }

    private function purchaseTitle(Purchase $purchase): string
    {
        return $purchase->invoice_number ?? '#'.$purchase->id;
    }

    /**
     * Rows of a table grouped by the role of the account that owns them.
     *
     * @return array<string, int>
     */
    private function countByOwnerRole(string $table, string $ownerColumn): array
    {
        return DB::table($table)
            ->join('users', 'users.id', '=', "{$table}.{$ownerColumn}")
            ->select('users.role', DB::raw('count(*) as total'))
            ->groupBy('users.role')
            ->pluck('total', 'role')
            ->map(fn (mixed $total): int => (int) $total)
            ->all();
    }
}
