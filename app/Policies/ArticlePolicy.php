<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\Role;
use App\Models\Article;
use App\Models\User;

/**
 * Admin and staff see every article and may write new ones. The admin changes or deletes any article,
 * a staff member only the ones they created.
 */
class ArticlePolicy
{
    public function viewAny(User $actor): bool
    {
        return $actor->role->usesStaffArea();
    }

    public function create(User $actor): bool
    {
        return $actor->role->usesStaffArea();
    }

    public function update(User $actor, Article $article): bool
    {
        return $this->owns($actor, $article);
    }

    public function delete(User $actor, Article $article): bool
    {
        return $this->owns($actor, $article);
    }

    private function owns(User $actor, Article $article): bool
    {
        return $actor->role === Role::Admin
            || ($actor->role === Role::Staff && $article->created_by === $actor->id);
    }
}
