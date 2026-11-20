<?php

declare(strict_types=1);

namespace App\Actions\ProcessCategories;

use App\Models\ProcessCategory;
use App\Services\Landing\LandingProcesses;
use Illuminate\Support\Facades\DB;

/**
 * Saves a process category with its language tabs. A new category goes to the end of the list. A language
 * missing from `$descriptions` is removed.
 */
class SaveProcessCategory
{
    public function __construct(private readonly LandingProcesses $landing) {}

    /**
     * @param  array<string, string>  $descriptions  keyed by locale
     */
    public function execute(?ProcessCategory $processCategory, string $name, string $slug, bool $active, array $descriptions): ProcessCategory
    {
        $saved = DB::transaction(function () use ($processCategory, $name, $slug, $active, $descriptions): ProcessCategory {
            $processCategory ??= new ProcessCategory;

            $processCategory->fill(['name' => $name, 'slug' => $slug, 'is_active' => $active]);

            if (! $processCategory->exists) {
                $processCategory->fill(['position' => ProcessCategory::query()->exists() ? (int) ProcessCategory::query()->max('position') + 1 : 0]);
            }

            $processCategory->save();

            $processCategory->translations()->whereNotIn('locale', array_keys($descriptions))->delete();

            foreach ($descriptions as $locale => $description) {
                $processCategory->translations()->updateOrCreate(['locale' => $locale], ['description' => $description]);
            }

            return $processCategory;
        });

        $this->landing->flush();

        return $saved;
    }
}
