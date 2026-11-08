<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Services;

use App\Actions\Services\SaveService;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_service(): void
    {
        $service = (new SaveService)->execute(null, 'Name', 'Text');

        $this->assertTrue($service->exists);
        $this->assertSame('Text', $service->description);
    }

    public function test_it_stores_an_empty_description_as_null(): void
    {
        $this->assertNull((new SaveService)->execute(null, 'Name', '')->description);
        $this->assertNull((new SaveService)->execute(null, 'Name', null)->description);
    }

    public function test_it_updates_the_given_service_in_place(): void
    {
        $service = Service::factory()->create();

        (new SaveService)->execute($service, 'Changed', 'New text');

        $this->assertSame(1, Service::query()->count());
        $this->assertSame('Changed', $service->fresh()?->name);
    }
}
