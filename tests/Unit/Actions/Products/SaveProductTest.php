<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Products;

use App\Actions\Products\SaveProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveProductTest extends TestCase
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

    public function test_it_creates_a_product_with_ordered_links_and_translations(): void
    {
        $product = app(SaveProduct::class)->execute(null, 'Epclen', 'epclen', $this->data());

        $this->assertTrue($product->exists);
        $this->assertSame(['A', 'B'], $product->links()->pluck('label')->all());
        $this->assertSame(['en'], $product->translations()->pluck('locale')->all());
    }

    public function test_it_updates_in_place_and_replaces_links_and_languages(): void
    {
        $action = app(SaveProduct::class);
        $product = $action->execute(null, 'Epclen', 'epclen', $this->data());

        $data = $this->data();
        $data['links'] = [['label' => 'C', 'url' => 'https://c.test']];
        $data['descriptions'] = ['id' => 'Indonesia'];
        $again = $action->execute($product, 'Epclen 2', 'epclen', $data);

        $this->assertTrue($again->is($product));
        $this->assertSame(1, Product::query()->count());
        $this->assertSame(['C'], $product->links()->pluck('label')->all());
        $this->assertSame(['id'], $product->translations()->pluck('locale')->all());
    }
}
