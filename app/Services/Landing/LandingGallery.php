<?php

declare(strict_types=1);

namespace App\Services\Landing;

/**
 * The slides of the full-screen gallery: one per company location. A location shows its photo
 * from public/images/landing/gallery/<key>.{webp,jpg,jpeg,png,svg}, or a labelled placeholder
 * in sample mode. The locations are the subsidiaries, so there is no gallery while those are
 * not shown.
 *
 * @phpstan-type Slide array{key: string, city: string, name: string, timezone: string, time: string}
 */
class LandingGallery
{
    public function __construct(private readonly LandingContent $content) {}

    /**
     * @return list<Slide>
     */
    public function slides(): array
    {
        $slides = [];

        foreach ($this->content->subsidiaries() as $subsidiary) {
            $slides[] = [
                'key' => $subsidiary['key'],
                'city' => ucfirst($subsidiary['key']),
                'name' => $subsidiary['name'],
                'timezone' => $subsidiary['timezone'],
                'time' => now($subsidiary['timezone'])->format('H:i'),
            ];
        }

        return $slides;
    }
}
