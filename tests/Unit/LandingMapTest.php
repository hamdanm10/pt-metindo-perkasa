<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Landing\LandingMap;
use PHPUnit\Framework\TestCase;

class LandingMapTest extends TestCase
{
    public function test_it_places_cities_where_the_drawn_map_has_them(): void
    {
        $map = new LandingMap;

        // Values taken from the prototype map of the same drawing.
        $this->assertEqualsWithDelta(8.88, $map->locate(3.5952, 98.6722)['left'], 0.05);
        $this->assertEqualsWithDelta(16.14, $map->locate(3.5952, 98.6722)['top'], 0.05);
        $this->assertEqualsWithDelta(26.27, $map->locate(-6.2088, 106.8456)['left'], 0.05);
        $this->assertEqualsWithDelta(70.6, $map->locate(-6.2088, 106.8456)['top'], 0.05);
        $this->assertEqualsWithDelta(47.56, $map->locate(-1.2379, 116.8529)['left'], 0.05);
        $this->assertEqualsWithDelta(42.99, $map->locate(-1.2379, 116.8529)['top'], 0.05);
    }

    public function test_pins_keep_the_subsidiary_data_and_add_the_position(): void
    {
        $pins = (new LandingMap)->pins([
            ['key' => 'medan', 'name' => 'PT Metindo Surface Medan', 'lat' => 3.5952, 'lng' => 98.6722, 'timezone' => 'Asia/Jakarta'],
        ]);

        $this->assertCount(1, $pins);
        $this->assertSame('medan', $pins[0]['key']);
        $this->assertArrayHasKey('left', $pins[0]);
        $this->assertArrayHasKey('top', $pins[0]);
    }

    public function test_no_subsidiaries_gives_no_pins(): void
    {
        $this->assertSame([], (new LandingMap)->pins([]));
    }
}
