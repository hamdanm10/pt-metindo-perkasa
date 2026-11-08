<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Models\Service;

class SaveService
{
    public function execute(?Service $service, string $name, ?string $description): Service
    {
        $service ??= new Service;

        $service->fill([
            'name' => $name,
            'description' => $description === null || $description === '' ? null : $description,
        ])->save();

        return $service;
    }
}
