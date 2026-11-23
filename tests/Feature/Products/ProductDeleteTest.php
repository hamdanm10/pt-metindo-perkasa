<?php

declare(strict_types=1);

namespace Tests\Feature\Products;

use App\Livewire\Products\Index;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProductDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_deletes_a_product_and_the_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('tds/a.pdf', 'x');
        $product = Product::factory()->create(['tds_path' => 'tds/a.pdf']);

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(Index::class)
            ->call('delete', $product->id)
            ->assertSee(__('products.messages.deleted'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('tds/a.pdf');
    }

    public function test_staff_cannot_delete_a_product(): void
    {
        $product = Product::factory()->create();

        Livewire::actingAs(User::factory()->staff()->create())->test(Index::class)->assertForbidden();
        $this->assertTrue(Gate::forUser(User::factory()->staff()->create())->denies('delete', $product));

        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    public function test_the_list_shows_a_delete_button_per_row_without_extra_queries(): void
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->count(2)->create();

        $count = function () use ($admin): int {
            DB::flushQueryLog();
            DB::enableQueryLog();
            $html = (string) $this->actingAs($admin)->get('/app/products')->getContent();
            $this->assertSame(substr_count($html, 'wire:key="product-'), substr_count($html, 'wire:click="delete('));

            return count(DB::getQueryLog());
        };

        $before = $count();
        Product::factory()->count(3)->create();

        $this->assertSame($before, $count());
    }
}
