<?php

declare(strict_types=1);

namespace App\Actions\ProcessCategories;

use App\Models\ProcessCategory;
use App\Services\Landing\LandingProcesses;
use App\Services\ProcessCategories\ProcessCategoryImageStore;

/**
 * Deletes a process category with its translations and image file. A category that still has processes is
 * kept and false is returned: the processes must be moved or deleted first.
 */
class DeleteProcessCategory
{
    public function __construct(
        private readonly ProcessCategoryImageStore $images,
        private readonly LandingProcesses $landing,
    ) {}

    public function execute(ProcessCategory $processCategory): bool
    {
        if ($processCategory->processes()->exists()) {
            return false;
        }

        $path = $processCategory->image_path;

        $processCategory->delete();

        $this->images->delete($path);
        $this->landing->flush();

        return true;
    }
}
