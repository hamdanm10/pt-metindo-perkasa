<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Models\Service;
use App\Services\Landing\LandingServices;
use App\Services\Services\ServiceImageStore;

/**
 * Deletes a service with its translations (the database cascades) and its image file.
 */
class DeleteService
{
    public function __construct(
        private readonly ServiceImageStore $images,
        private readonly LandingServices $landing,
    ) {}

    public function execute(Service $service): void
    {
        $path = $service->image_path;

        $service->delete();

        $this->images->delete($path);
        $this->landing->flush();
    }
}
