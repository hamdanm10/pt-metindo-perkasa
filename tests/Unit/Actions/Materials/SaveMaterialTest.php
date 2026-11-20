<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Materials;

use App\Actions\Materials\SaveMaterial;
use App\Models\Material;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveMaterialTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{brochure_url: string|null, video_url: string|null, price: int|null, is_active: bool, links: list<array{label: string, url: string}>, descriptions: array<string, string>}
     */
    private function data(): array
    {
        return [
            'brochure_url' => null,
            'video_url' => null,
            'price' => null,
            'is_active' => true,
            'links' => [['label' => 'A', 'url' => 'https://a.test'], ['label' => 'B', 'url' => 'https://b.test']],
            'descriptions' => ['en' => 'English'],
        ];
    }

    public function test_it_creates_a_material_with_ordered_links_and_translations(): void
    {
        $material = app(SaveMaterial::class)->execute(null, 'Epclen', 'epclen', $this->data());

        $this->assertTrue($material->exists);
        $this->assertSame(['A', 'B'], $material->links()->pluck('label')->all());
        $this->assertSame(['en'], $material->translations()->pluck('locale')->all());
    }

    public function test_it_updates_in_place_and_replaces_links_and_languages(): void
    {
        $action = app(SaveMaterial::class);
        $material = $action->execute(null, 'Epclen', 'epclen', $this->data());

        $data = $this->data();
        $data['links'] = [['label' => 'C', 'url' => 'https://c.test']];
        $data['descriptions'] = ['id' => 'Indonesia'];
        $again = $action->execute($material, 'Epclen 2', 'epclen', $data);

        $this->assertTrue($again->is($material));
        $this->assertSame(1, Material::query()->count());
        $this->assertSame(['C'], $material->links()->pluck('label')->all());
        $this->assertSame(['id'], $material->translations()->pluck('locale')->all());
    }
}
