<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListUsers
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function execute(string $search = '', ?Role $role = null, ?string $status = null, int $perPage = 15): LengthAwarePaginator
    {
        $term = '%'.addcslashes(trim($search), '%_\\').'%';

        return User::query()
            ->when(trim($search) !== '', fn ($query) => $query->where(
                fn ($query) => $query->where('name', 'ilike', $term)->orWhere('email', 'ilike', $term),
            ))
            ->when($role !== null, fn ($query) => $query->where('role', $role?->value))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderByDesc('created_at')
            ->orderBy('id')
            ->paginate($perPage);
    }
}
