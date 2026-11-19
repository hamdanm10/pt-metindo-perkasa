<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Services;

use App\Actions\Services\SaveService;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SaveServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{title: string, summary: string, description: string, included: list<string>, fit: string}
     */
    private function text(string $title = 'Title'): array
    {
        return ['title' => $title, 'summary' => '', 'description' => '', 'included' => [], 'fit' => ''];
    }

    public function test_it_creates_a_service_with_its_translations(): void
    {
        $service = app(SaveService::class)->execute(null, 'Name', 'name', 3, true, [
            'en' => [...$this->text(), 'summary' => 'Sum', 'included' => ['A']],
            'id' => $this->text('Judul'),
        ]);

        $this->assertTrue($service->exists);
        $this->assertSame(3, $service->position);
        $this->assertCount(2, $service->translations);
        $this->assertSame('Sum', $service->translations->firstWhere('locale', 'en')?->summary);
    }

    public function test_it_stores_empty_texts_as_null(): void
    {
        $service = app(SaveService::class)->execute(null, 'Name', 'name', 0, true, ['en' => $this->text()]);

        $en = $service->translations->firstOrFail();
        $this->assertNull($en->summary);
        $this->assertNull($en->description);
        $this->assertNull($en->included);
        $this->assertNull($en->fit);
    }

    public function test_it_updates_in_place_and_drops_missing_languages(): void
    {
        $service = app(SaveService::class)->execute(null, 'Name', 'name', 0, true, ['en' => $this->text(), 'id' => $this->text()]);

        app(SaveService::class)->execute($service, 'Changed', 'changed', 1, false, ['en' => $this->text('New')]);

        $this->assertSame(1, Service::query()->count());
        $service = $service->fresh();
        $this->assertSame('Changed', $service?->name);
        $this->assertFalse($service->is_active);
        $this->assertSame(['en'], $service->translations->pluck('locale')->all());
        $this->assertSame('New', $service->translations->first()?->title);
    }

    public function test_it_stores_the_image_as_webp_on_the_public_disk(): void
    {
        Storage::fake('public');

        $service = app(SaveService::class)->execute(null, 'Name', 'name', 0, true, ['en' => $this->text()], UploadedFile::fake()->image('a.png', 1200, 1200));

        $this->assertStringStartsWith('services/', (string) $service->image_path);
        $this->assertStringEndsWith('.webp', (string) $service->image_path);
        Storage::disk('public')->assertExists((string) $service->image_path);
    }
}
