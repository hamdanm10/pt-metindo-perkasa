<?php

declare(strict_types=1);

namespace Tests\Feature\Products;

use App\Livewire\Products\Editor;
use App\Livewire\Products\Index;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ProductAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_open_the_pages(): void
    {
        $product = Product::factory()->create();

        foreach (['/app/products', '/app/products/create', "/app/products/{$product->id}/edit"] as $url) {
            $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk();
            $this->actingAs(User::factory()->staff()->create())->get($url)->assertForbidden();
            $this->actingAs(User::factory()->customer()->create())->get($url)->assertForbidden();
        }

        auth()->logout();
        $this->get('/app/products')->assertRedirect(route('app.login'));
    }

    public function test_staff_cannot_use_the_livewire_components(): void
    {
        $staff = User::factory()->staff()->create();

        Livewire::actingAs($staff)->test(Index::class)->assertForbidden();
        Livewire::actingAs($staff)->test(Editor::class)->assertForbidden();
    }

    public function test_the_menu_item_is_for_admins_only(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')->assertSee(route('app.products.index'), false);
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')->assertDontSee(route('app.products.index'), false);
    }

    public function test_admin_adds_a_product(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', '  Zinc Brightener  ')
            ->set('form.description', 'Bright finish additive.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.products.index'));

        $product = Product::query()->firstOrFail();
        $this->assertSame('Zinc Brightener', $product->name);
        $this->assertSame('Bright finish additive.', $product->description);
    }

    public function test_the_description_is_optional(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'Only a name')->call('save')->assertHasNoErrors();

        $this->assertNull(Product::query()->firstOrFail()->description);
    }

    public function test_name_is_required_and_lengths_are_limited(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $component->set('form.name', '   ')->call('save')->assertHasErrors(['form.name' => 'required']);
        $component->set('form.name', str_repeat('a', 256))->call('save')->assertHasErrors(['form.name' => 'max']);
        $component->set('form.name', 'Ok')->set('form.description', str_repeat('a', 5001))->call('save')->assertHasErrors(['form.description' => 'max']);

        $this->assertSame(0, Product::query()->count());
    }

    public function test_admin_edits_a_product(): void
    {
        $product = Product::factory()->create(['name' => 'Old']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['product' => $product])
            ->assertSet('form.name', 'Old')
            ->set('form.name', 'New')->set('form.description', '')
            ->call('save')->assertHasNoErrors();

        $product->refresh();
        $this->assertSame('New', $product->name);
        $this->assertNull($product->description);
        $this->assertSame(1, Product::query()->count());
    }

    public function test_the_list_searches_by_name_without_regard_to_case(): void
    {
        Product::factory()->create(['name' => 'Alpha Zinc']);
        Product::factory()->create(['name' => 'Beta Nickel']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Index::class)
            ->assertSee('Alpha Zinc')->assertSee('Beta Nickel')
            ->set('search', 'ZINC')->assertSee('Alpha Zinc')->assertDontSee('Beta Nickel');
    }

    public function test_the_list_is_paginated_and_the_query_count_stays_flat(): void
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->count(2)->create();

        DB::enableQueryLog();
        Livewire::actingAs($admin)->test(Index::class);
        $few = count(DB::getQueryLog());

        Product::factory()->count(20)->create();
        DB::flushQueryLog();
        $component = Livewire::actingAs($admin)->test(Index::class);

        $this->assertSame($few, count(DB::getQueryLog()));
        $this->assertCount(15, $component->viewData('products')->items());
    }

    public function test_texts_exist_in_both_dashboard_languages(): void
    {
        $en = require lang_path('en/products.php');
        $id = require lang_path('id/products.php');

        $this->assertSame(array_keys($en), array_keys($id));
        $this->assertSame(array_keys($en['fields']), array_keys($id['fields']));
        $this->assertSame(array_keys($en['messages']), array_keys($id['messages']));
    }
}
