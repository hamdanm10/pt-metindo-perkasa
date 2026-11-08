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

    public function test_it_creates_a_product(): void
    {
        $product = (new SaveProduct)->execute(null, 'Name', 'Text');

        $this->assertTrue($product->exists);
        $this->assertSame('Text', $product->description);
    }

    public function test_it_stores_an_empty_description_as_null(): void
    {
        $this->assertNull((new SaveProduct)->execute(null, 'Name', '')->description);
        $this->assertNull((new SaveProduct)->execute(null, 'Name', null)->description);
    }

    public function test_it_updates_the_given_product_in_place(): void
    {
        $product = Product::factory()->create();

        (new SaveProduct)->execute($product, 'Changed', 'New text');

        $this->assertSame(1, Product::query()->count());
        $this->assertSame('Changed', $product->fresh()?->name);
    }
}
