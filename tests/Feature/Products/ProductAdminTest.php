<?php

declare(strict_types=1);

namespace Tests\Feature\Products;

use App\Livewire\Products\Editor;
use App\Livewire\Products\Index;
use App\Models\Process;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_adds_a_product_with_links_languages_and_a_tds_file(): void
    {
        Storage::fake('public');

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', '  Epclen 62  ')
            ->set('form.brochureUrl', 'https://example.com/brochure')
            ->set('form.videoUrl', 'https://example.com/video')
            ->set('form.price', '125000')
            ->set('form.tds', UploadedFile::fake()->create('sheet.pdf', 200, 'application/pdf'))
            ->call('addLink')
            ->set('form.links.0.label', ' Tokopedia ')
            ->set('form.links.0.url', 'https://tokopedia.com/epclen')
            ->call('addLink')
            ->set('form.links.1.label', 'Shopee')
            ->set('form.links.1.url', 'https://shopee.co.id/epclen')
            ->set('form.descriptions.en', 'A soak cleaner.')
            ->set('form.descriptions.id', 'Pembersih rendam.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.products.index'));

        $product = Product::query()->with(['translations', 'links'])->firstOrFail();
        $this->assertSame('Epclen 62', $product->name);
        $this->assertSame('epclen-62', $product->slug);
        $this->assertSame(125000, $product->price);
        $this->assertTrue($product->is_active);
        $this->assertSame('https://example.com/brochure', $product->brochure_url);
        $this->assertNotNull($product->tds_path);
        Storage::disk('public')->assertExists((string) $product->tds_path);
        $this->assertSame(['en', 'id'], $product->translations->pluck('locale')->sort()->values()->all());
        $this->assertSame(['Tokopedia', 'Shopee'], $product->links->pluck('label')->all());
        $this->assertSame([0, 1], $product->links->pluck('position')->all());
    }

    public function test_optional_fields_are_saved_as_null_and_empty_link_rows_are_ignored(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'Plain')
            ->set('form.descriptions.en', 'Text')
            ->call('addLink')
            ->call('save')
            ->assertHasNoErrors();

        $product = Product::query()->with('links')->firstOrFail();
        $this->assertNull($product->brochure_url);
        $this->assertNull($product->video_url);
        $this->assertNull($product->price);
        $this->assertNull($product->tds_path);
        $this->assertCount(0, $product->links);
    }

    public function test_english_is_required_and_empty_languages_are_not_saved(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'No English')
            ->set('form.descriptions.id', 'Hanya Indonesia')
            ->call('save');

        $component->assertHasErrors(['form.descriptions']);
        $this->assertSame(0, Product::query()->count());
    }

    public function test_the_name_is_validated_and_must_be_unique(): void
    {
        Product::factory()->create(['name' => 'Taken', 'slug' => 'taken']);
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.descriptions.en', 'x');

        $component->set('form.name', '')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', str_repeat('a', 256))->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', 'Taken')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', 'TAKEN!')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', '!!!')->call('save')->assertHasErrors(['form.name']);
        $this->assertSame(1, Product::query()->count());
    }

    public function test_urls_must_be_https_and_links_are_validated(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'Linked')
            ->set('form.descriptions.en', 'x');

        $component->set('form.brochureUrl', 'http://example.com')->call('save')->assertHasErrors(['form.brochureUrl']);
        $component->set('form.brochureUrl', 'javascript:alert(1)')->call('save')->assertHasErrors(['form.brochureUrl']);
        $component->set('form.brochureUrl', '')->set('form.videoUrl', 'not a url')->call('save')->assertHasErrors(['form.videoUrl']);
        $component->set('form.videoUrl', '')->call('addLink')->set('form.links.0.label', 'Shop')->call('save')->assertHasErrors(['form.links.0.url']);
        $component->set('form.links.0.label', '')->set('form.links.0.url', 'https://x.test')->call('save')->assertHasErrors(['form.links.0.label']);
        $component->set('form.links.0.label', 'Shop')->set('form.links.0.url', 'http://x.test')->call('save')->assertHasErrors(['form.links.0.url']);
        $this->assertSame(0, Product::query()->count());
    }

    public function test_the_number_of_links_and_the_text_lengths_are_limited(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'Limits')
            ->set('form.descriptions.en', str_repeat('a', 2001));

        $component->call('save')->assertHasErrors(['form.descriptions.en']);

        $component->set('form.descriptions.en', 'ok');
        for ($i = 0; $i < 12; $i++) {
            $component->call('addLink');
        }
        $this->assertCount(10, $component->get('form.links'));

        $component->set('form.price', '-1')->call('save')->assertHasErrors(['form.price']);
        $component->set('form.price', '12.5')->call('save')->assertHasErrors(['form.price']);
    }

    public function test_the_tds_must_be_a_pdf_within_the_size_limit(): void
    {
        Storage::fake('public');
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $component->set('form.tds', UploadedFile::fake()->create('big.pdf', 6000, 'application/pdf'))->assertHasErrors(['form.tds']);
        $component->set('form.tds', UploadedFile::fake()->create('sheet.txt', 10, 'text/plain'))->assertHasErrors(['form.tds']);
        $component->set('form.tds', UploadedFile::fake()->create('ok.pdf', 100, 'application/pdf'))->assertHasNoErrors(['form.tds']);
    }

    public function test_admin_edits_a_product_replaces_links_and_removes_an_emptied_language(): void
    {
        $product = Product::factory()->create(['name' => 'Old', 'slug' => 'old', 'price' => 5]);
        $product->translations()->create(['locale' => 'en', 'description' => 'English']);
        $product->translations()->create(['locale' => 'id', 'description' => 'Indonesia']);
        $product->links()->create(['label' => 'Old shop', 'url' => 'https://old.test', 'position' => 0]);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['product' => $product])
            ->assertSet('form.name', 'Old')
            ->assertSet('form.price', '5')
            ->assertSet('form.links.0.label', 'Old shop')
            ->set('form.descriptions.id', '')
            ->set('form.price', '')
            ->set('form.links.0.label', 'New shop')
            ->set('form.isActive', false)
            ->call('save')
            ->assertHasNoErrors();

        $product->refresh()->load(['translations', 'links']);
        $this->assertSame(['en'], $product->translations->pluck('locale')->all());
        $this->assertSame(['New shop'], $product->links->pluck('label')->all());
        $this->assertNull($product->price);
        $this->assertFalse($product->is_active);
    }

    public function test_editing_keeps_the_slug_and_the_own_name_is_not_a_duplicate(): void
    {
        $product = Product::factory()->create(['name' => 'Alpha', 'slug' => 'custom-slug']);
        $product->translations()->create(['locale' => 'en', 'description' => 'x']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['product' => $product])
            ->set('form.name', 'Beta')
            ->call('save')->assertHasNoErrors();

        $this->assertSame('custom-slug', $product->refresh()->slug);
        $this->assertSame('Beta', $product->name);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['product' => $product])
            ->call('save')->assertHasNoErrors();
    }

    public function test_a_new_tds_replaces_the_old_file_and_it_can_be_removed(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        Storage::disk('public')->put('products/tds/old.pdf', 'old');
        $product = Product::factory()->create(['tds_path' => 'products/tds/old.pdf']);
        $product->translations()->create(['locale' => 'en', 'description' => 'x']);

        Livewire::actingAs($admin)->test(Editor::class, ['product' => $product])
            ->set('form.tds', UploadedFile::fake()->create('new.pdf', 50, 'application/pdf'))
            ->call('save')->assertHasNoErrors();

        $product->refresh();
        Storage::disk('public')->assertMissing('products/tds/old.pdf');
        Storage::disk('public')->assertExists((string) $product->tds_path);
        $newPath = (string) $product->tds_path;

        Livewire::actingAs($admin)->test(Editor::class, ['product' => $product])
            ->set('form.removeTds', true)
            ->call('save')->assertHasNoErrors();

        $this->assertNull($product->refresh()->tds_path);
        Storage::disk('public')->assertMissing($newPath);
    }

    public function test_the_list_searches_by_name_without_regard_to_case(): void
    {
        Product::factory()->create(['name' => 'Epclen 62', 'slug' => 'epclen-62']);
        Product::factory()->create(['name' => 'Jasco CA', 'slug' => 'jasco-ca']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Index::class)
            ->set('search', 'EPCLEN')->assertSee('Epclen 62')->assertDontSee('Jasco CA');
    }

    public function test_the_list_is_paginated_and_the_query_count_stays_flat(): void
    {
        $admin = User::factory()->admin()->create();

        $count = function (int $rows) use ($admin): int {
            Product::query()->delete();
            Product::factory()->count($rows)->create()->each(
                fn (Product $product) => $product->translations()->create(['locale' => 'en', 'description' => 'x'])
            );

            DB::enableQueryLog();
            DB::flushQueryLog();
            $component = Livewire::actingAs($admin)->test(Index::class);
            $queries = count(DB::getQueryLog());
            DB::disableQueryLog();

            $this->assertCount(min($rows, 15), $component->viewData('products')->items());

            return $queries;
        };

        $this->assertSame($count(2), $count(20));
    }

    public function test_the_list_shows_how_many_steps_use_each_product(): void
    {
        $product = Product::factory()->create(['name' => 'Used Product']);
        $process = Process::factory()->create();

        foreach ([0, 1] as $position) {
            $process->steps()->create(['name' => 'Step '.$position, 'position' => $position])->products()->attach($product->id, ['position' => 0]);
        }

        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Index::class);

        $this->assertSame(2, (int) $component->viewData('products')->items()[0]->steps_count);
    }

    public function test_texts_exist_in_both_dashboard_languages(): void
    {
        $en = require lang_path('en/products.php');
        $id = require lang_path('id/products.php');

        $this->assertSame(array_keys($en), array_keys($id));
        $this->assertSame(array_keys($en['fields']), array_keys($id['fields']));
        $this->assertSame(array_keys($en['hints']), array_keys($id['hints']));
        $this->assertSame(array_keys($en['messages']), array_keys($id['messages']));
    }
}
