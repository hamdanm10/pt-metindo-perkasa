<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Actions\Services\SaveService;
use App\Models\Service;
use App\Services\Landing\LandingServices;
use Illuminate\Support\Facades\Storage;

trait CreatesServices
{
    /**
     * Three active services with English text and a picture each, as the admin would have set them.
     *
     * @return list<Service>
     */
    protected function createServices(): array
    {
        Storage::fake('public');

        $services = [];

        foreach ([
            ['electroplating-service', 'Electroplating Service'],
            ['chemical-supply', 'Chemical Supply'],
            ['technical-consultation', 'Technical Consultation'],
        ] as $position => [$slug, $title]) {
            $service = app(SaveService::class)->execute(null, $title, $slug, $position, true, [
                'en' => [
                    'title' => $title,
                    'summary' => "{$title} summary.",
                    'description' => "{$title} description.",
                    'included' => ["{$title} first item", "{$title} second item"],
                    'fit' => "{$title} fit.",
                ],
            ]);
            $path = "services/{$slug}.webp";
            Storage::disk('public')->put($path, 'image');
            $service->update(['image_path' => $path]);
            $services[] = $service;
        }

        app(LandingServices::class)->flush();

        return $services;
    }
}
