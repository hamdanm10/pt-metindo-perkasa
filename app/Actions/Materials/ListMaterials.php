<?php

declare(strict_types=1);

namespace App\Actions\Materials;

use App\Models\Material;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListMaterials
{
    /**
     * @return LengthAwarePaginator<int, Material>
     */
    public function execute(string $search): LengthAwarePaginator
    {
        $term = '%'.addcslashes(trim($search), '%_\\').'%';

        return Material::query()
            ->with('translations:id,material_id,locale')
            ->when(trim($search) !== '', fn ($query) => $query->where('name', 'ilike', $term))
            ->orderBy('name')
            ->paginate(15);
    }
}
