<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use App\Actions\ProcessCategories\MoveProcessCategory;
use App\Actions\Processes\SaveProcess;
use App\Actions\Products\SaveProduct;
use App\Models\Process;
use App\Models\ProcessCategory;
use App\Models\Product;
use App\Services\Landing\LandingProcesses;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProcessCatalogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * An active category with one active process, one step, and one product that has links.
     *
     * @return array{category: ProcessCategory, process: Process, product: Product}
     */
    private function catalog(): array
    {
        $category = ProcessCategory::factory()->create(['name' => 'Custom Category', 'slug' => 'custom-category', 'position' => 0]);
        $category->translations()->create(['locale' => 'en', 'description' => 'English category text.']);
        $category->translations()->create(['locale' => 'id', 'description' => 'Teks kategori.']);

        $process = Process::factory()->create(['process_category_id' => $category->id, 'name' => 'Custom Flow', 'slug' => 'custom-flow', 'position' => 0]);
        $process->translations()->create(['locale' => 'en', 'description' => 'English flow text.']);

        $product = Product::factory()->create(['name' => 'Epclen 62', 'slug' => 'epclen-62', 'brochure_url' => 'https://example.com/b.pdf', 'price' => 125000, 'tds_path' => 'products/tds/a.pdf']);
        $product->translations()->create(['locale' => 'en', 'description' => 'A soak cleaner.']);
        $product->links()->create(['label' => 'Tokopedia', 'url' => 'https://tokopedia.com/epclen', 'position' => 0]);

        $step = $process->steps()->create(['name' => 'Degreasing', 'position' => 0]);
        $step->translations()->create(['locale' => 'en', 'description' => 'Remove oil.']);
        $step->products()->attach($product->id, ['position' => 0]);

        return ['category' => $category, 'process' => $process, 'product' => $product];
    }

    public function test_without_an_active_category_the_static_catalog_is_shown(): void
    {
        $this->get('/en/processes')->assertOk()->assertSee('Zinc Plating Chemicals');

        $category = ProcessCategory::factory()->create(['is_active' => false, 'name' => 'Hidden One', 'slug' => 'hidden-one']);
        $category->translations()->create(['locale' => 'en', 'description' => 'x']);

        $this->get('/en/processes')->assertOk()->assertSee('Zinc Plating Chemicals')->assertDontSee('Hidden One');
    }

    public function test_the_database_catalog_replaces_the_static_one(): void
    {
        $this->catalog();

        $this->get('/en/processes')->assertOk()
            ->assertSee('Custom Category')->assertSee('English category text.')->assertSee('Custom Flow')
            ->assertSee(route('landing.process.show', ['locale' => 'en', 'process' => 'custom-flow']), false)
            ->assertDontSee('Zinc Plating Chemicals');

        $this->get('/en/processes/custom-category')->assertOk()->assertSee('Custom Flow')->assertSee('English flow text.');
        $this->get('/en/processes/zinc-plating')->assertNotFound();
        $this->get('/en/process/alkaline-zinc')->assertNotFound();
        $this->get('/en/processes/not-a-category')->assertNotFound();
    }

    public function test_the_home_page_shows_the_first_four_database_categories(): void
    {
        $this->catalog();

        $this->get('/en')->assertOk()->assertSee('Custom Category')->assertDontSee('Zinc Plating Chemicals');
    }

    public function test_a_language_without_text_shows_the_english_text(): void
    {
        $this->catalog();

        $this->get('/id/processes/custom-category')->assertOk()->assertSee('Teks kategori.')->assertSee('English flow text.');
        $this->get('/ja/processes/custom-category')->assertOk()->assertSee('English category text.');
    }

    public function test_the_process_page_shows_steps_and_products_with_their_data(): void
    {
        $this->catalog();

        $html = (string) $this->get('/en/process/custom-flow')->assertOk()
            ->assertSee('Degreasing')->assertSee('Remove oil.')->assertSee('Epclen 62')
            ->assertSee(__('pages.process.view_diagram'))->getContent();

        $this->assertStringContainsString('data-product=', $html);
        $this->assertStringContainsString('A soak cleaner.', $html);
        $this->assertStringContainsString('Rp 125.000', $html);
        $this->assertStringContainsString('https://tokopedia.com/epclen', $html);
        $this->assertStringContainsString('https://example.com/b.pdf', $html);
        $this->assertStringContainsString('/storage/products/tds/a.pdf', $html);
    }

    public function test_a_product_without_a_description_gets_the_generic_usage_text(): void
    {
        $data = $this->catalog();
        $data['product']->translations()->delete();
        app(SaveProduct::class)->execute($data['product'], 'Epclen 62', 'epclen-62', [
            'brochure_url' => null, 'video_url' => null, 'price' => null, 'is_active' => true, 'links' => [], 'descriptions' => [],
        ]);

        $this->get('/en/process/custom-flow')->assertOk()
            ->assertSee(__('pages.process.usage_text', ['name' => 'Epclen 62', 'step' => 'degreasing']), false);
    }

    public function test_product_text_is_escaped(): void
    {
        $data = $this->catalog();
        $data['product']->translations()->where('locale', 'en')->update(['description' => '<script>alert(1)</script>']);
        app(SaveProduct::class)->execute($data['product']->refresh(), 'Epclen 62', 'epclen-62', [
            'brochure_url' => null, 'video_url' => null, 'price' => null, 'is_active' => true, 'links' => [], 'descriptions' => ['en' => '<script>alert(1)</script>'],
        ]);

        $this->get('/en/process/custom-flow')->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_inactive_items_are_hidden(): void
    {
        $data = $this->catalog();
        $other = Process::factory()->create(['process_category_id' => $data['category']->id, 'name' => 'Second Flow', 'slug' => 'second-flow', 'position' => 1, 'is_active' => false]);
        app(SaveProcess::class)->execute($data['process'], 'Custom Flow', 'custom-flow', $data['category']->id, true, ['en' => 'English flow text.'], [
            ['name' => 'Degreasing', 'descriptions' => [], 'products' => [$data['product']->id]],
        ]);

        $this->get('/en/process/second-flow')->assertNotFound();
        $this->get('/en/processes/custom-category')->assertDontSee('Second Flow');

        app(SaveProduct::class)->execute($data['product'], 'Epclen 62', 'epclen-62', [
            'brochure_url' => null, 'video_url' => null, 'price' => null, 'is_active' => false, 'links' => [], 'descriptions' => ['en' => 'x'],
        ]);
        $this->get('/en/process/custom-flow')->assertOk()->assertDontSee('Epclen 62');

        $data['category']->update(['is_active' => false]);
        app(MoveProcessCategory::class)->execute($data['category'], true);
        $this->get('/en/process/custom-flow')->assertNotFound();
        $this->assertSame('Second Flow', $other->name);
    }

    public function test_saving_in_the_dashboard_clears_the_cache(): void
    {
        $data = $this->catalog();
        $this->get('/en/processes/custom-category')->assertSee('Custom Flow');

        app(SaveProcess::class)->execute($data['process'], 'Renamed Flow', 'custom-flow', $data['category']->id, true, ['en' => 'English flow text.'], []);

        $this->get('/en/processes/custom-category')->assertSee('Renamed Flow')->assertDontSee('Custom Flow');
    }

    public function test_moving_a_category_changes_the_order_on_the_page(): void
    {
        $data = $this->catalog();
        $second = ProcessCategory::factory()->create(['name' => 'Second Category', 'slug' => 'second-category', 'position' => 1]);
        $second->translations()->create(['locale' => 'en', 'description' => 'x']);

        $this->get('/en/processes')->assertSeeInOrder(['Custom Category', 'Second Category']);

        app(MoveProcessCategory::class)->execute($second, true);

        $this->get('/en/processes')->assertSeeInOrder(['Second Category', 'Custom Category']);
        $this->assertSame(1, $data['category']->refresh()->position);
    }

    public function test_the_catalog_is_cached_and_the_query_count_does_not_grow_with_steps_and_products(): void
    {
        $data = $this->catalog();

        $queries = 0;
        DB::listen(function () use (&$queries): void {
            $queries++;
        });

        $this->get('/en/process/custom-flow')->assertOk();
        $queries = 0;
        $this->get('/en/process/custom-flow')->assertOk();
        $warm = $queries;

        $steps = [];
        for ($i = 0; $i < 6; $i++) {
            $steps[] = ['name' => "Step {$i}", 'descriptions' => ['en' => 'text'], 'products' => [Product::factory()->create()->id, $data['product']->id]];
        }
        app(SaveProcess::class)->execute($data['process'], 'Custom Flow', 'custom-flow', $data['category']->id, true, ['en' => 'English flow text.'], $steps);

        $this->get('/en/process/custom-flow')->assertOk();
        $queries = 0;
        $this->get('/en/process/custom-flow')->assertOk()->assertSee('Step 5');

        $this->assertSame($warm, $queries);
    }

    public function test_a_cold_load_uses_a_fixed_number_of_queries(): void
    {
        $data = $this->catalog();
        $count = function () use ($data): int {
            app(SaveProduct::class)->execute($data['product'], 'Epclen 62', 'epclen-62', [
                'brochure_url' => null, 'video_url' => null, 'price' => null, 'is_active' => true, 'links' => [], 'descriptions' => [],
            ]);

            DB::enableQueryLog();
            DB::flushQueryLog();
            app(LandingProcesses::class)->all('en');
            $total = count(DB::getQueryLog());
            DB::disableQueryLog();

            return $total;
        };

        $few = $count();

        for ($i = 0; $i < 5; $i++) {
            $category = ProcessCategory::factory()->create();
            $process = Process::factory()->create(['process_category_id' => $category->id]);
            $step = $process->steps()->create(['name' => 'S', 'position' => 0]);
            $step->products()->attach(Product::factory()->create()->id, ['position' => 0]);
        }

        $this->assertSame($few, $count());
    }
}
