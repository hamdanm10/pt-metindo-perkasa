<?php

declare(strict_types=1);

namespace App\Services\Lookup;

use App\Enums\Role;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Finds records for the searchable dropdown: a few at a time, never the whole table. The dropdown
 * asks for the first page of matches while the user types, so a long list does not have to load.
 *
 * @phpstan-type Item array{id: int, label: string, hint: string|null}
 */
class LookupSearch
{
    public const LIMIT = 20;

    public const SOURCES = ['customer', 'service', 'product'];

    /**
     * @return array{items: list<Item>, more: bool}
     */
    public function search(string $source, string $term): array
    {
        $term = trim($term);
        $query = $this->query($source);

        if ($term !== '') {
            $like = '%'.addcslashes($term, '%_\\').'%';

            $source === 'customer'
                ? $query->where(fn (Builder $match) => $match->where('name', 'ilike', $like)->orWhere('email', 'ilike', $like))
                : $query->where('name', 'ilike', $like);
        }

        $rows = $query->orderBy('name')->orderBy('id')->limit(self::LIMIT + 1)->get();

        return [
            'items' => array_values($rows->take(self::LIMIT)->map(fn ($row): array => $this->item($source, $row))->all()),
            'more' => $rows->count() > self::LIMIT,
        ];
    }

    /**
     * The text to show for one chosen id, or null when it does not exist (or is not allowed here).
     */
    public function label(string $source, int $id): ?string
    {
        $row = $this->query($source)->whereKey($id)->first();

        return $row === null ? null : $this->item($source, $row)['label'];
    }

    /**
     * @return Builder<User>|Builder<Service>|Builder<Product>
     */
    private function query(string $source): Builder
    {
        return match ($source) {
            'customer' => User::query()->select(['id', 'name', 'email'])->whereIn('role', Role::portalArea()),
            'service' => Service::query()->select(['id', 'name']),
            'product' => Product::query()->select(['id', 'name']),
            default => throw new \InvalidArgumentException("Unknown lookup source [{$source}]."),
        };
    }

    /**
     * @return Item
     */
    private function item(string $source, User|Service|Product $row): array
    {
        return [
            'id' => $row->id,
            'label' => $row->name,
            'hint' => $row instanceof User ? $row->email : null,
        ];
    }
}
