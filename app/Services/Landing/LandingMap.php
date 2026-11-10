<?php

declare(strict_types=1);

namespace App\Services\Landing;

/**
 * Places locations on the map of Indonesia drawn in `x-landing.indonesia-map`. The drawing is an
 * equirectangular map 1000 x 383 units, its top left corner at 94.5 degrees east and 6.5 degrees
 * north, 21.276 units to the degree in both directions.
 */
class LandingMap
{
    private const WIDTH = 1000.0;

    private const HEIGHT = 383.0;

    private const WEST = 94.5;

    private const NORTH = 6.5;

    private const UNITS_PER_DEGREE = 21.276;

    /**
     * Where a point sits on the map, as percentages of its width and height.
     *
     * @return array{left: float, top: float}
     */
    public function locate(float $latitude, float $longitude): array
    {
        return [
            'left' => round(($longitude - self::WEST) * self::UNITS_PER_DEGREE / self::WIDTH * 100, 2),
            'top' => round((self::NORTH - $latitude) * self::UNITS_PER_DEGREE / self::HEIGHT * 100, 2),
        ];
    }

    /**
     * The given subsidiaries with their position on the map.
     *
     * @param  list<array{key: string, name: string, lat: float, lng: float, timezone: string}>  $subsidiaries
     * @return list<array{key: string, name: string, lat: float, lng: float, timezone: string, left: float, top: float}>
     */
    public function pins(array $subsidiaries): array
    {
        return array_map(
            fn (array $subsidiary): array => [...$subsidiary, ...$this->locate($subsidiary['lat'], $subsidiary['lng'])],
            $subsidiaries,
        );
    }
}
