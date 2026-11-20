<?php

declare(strict_types=1);

namespace App\Actions\ProcessCategories;

use App\Models\ProcessCategory;

class SaveProcessCategory
{
    public function execute(?ProcessCategory $processCategory, string $name, ?string $description): ProcessCategory
    {
        $processCategory ??= new ProcessCategory;

        $processCategory->fill([
            'name' => $name,
            'description' => $description === null || $description === '' ? null : $description,
        ])->save();

        return $processCategory;
    }
}
