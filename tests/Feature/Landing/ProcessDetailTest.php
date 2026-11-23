<?php

declare(strict_types=1);

namespace Tests\Feature\Landing;

use App\Models\Process;
use App\Models\Product;
use App\Services\Landing\LandingContent;
use App\Services\Landing\LandingProcesses;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Concerns\CreatesCatalog;
use Tests\TestCase;

class ProcessDetailTest extends TestCase
{
    use CreatesCatalog;
    use RefreshDatabase;

    /** @var list<array{slug: string, name: string, processes: list<string>}> */
    private array $categories = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->categories = $this->createCatalog();
    }

    public function test_process_slugs_are_unique_across_categories(): void
    {
        $slugs = [];

        foreach ($this->categories as $category) {
            foreach ($category['processes'] as $name) {
                $slugs[] = Str::slug($name);
            }
        }

        $this->assertSame(count($slugs), count(array_unique($slugs)));
    }

    public function test_every_process_has_a_page(): void
    {
        foreach ($this->categories as $category) {
            foreach ($category['processes'] as $name) {
                $this->get('/en/process/'.Str::slug($name))->assertOk()->assertSee($name);
            }
        }
    }

    public function test_a_process_with_steps_shows_its_flow_materials_and_diagram_button(): void
    {
        $response = $this->get('/en/process/rack-zinc-plating')->assertOk();

        $response->assertSee('Rack Zinc Plating process flow.')
            ->assertSee('Zinc Plating Chemicals')
            ->assertSee('Degreasing')->assertSee('Rinsing')->assertSee('Activation')->assertSee('Plating')
            ->assertSee('Epclen 1')->assertSee('Epclen 8')
            ->assertSee('data-diagram-open', false)
            ->assertSee('data-diagram-dialog', false)
            ->assertSee('data-material-open', false)
            ->assertDontSee(__('pages.process.empty'));

        $this->assertSame(4, substr_count((string) $response->getContent(), 'data-diagram-node'));
        $this->assertSame(8, substr_count((string) $response->getContent(), 'data-material-open'));
        $response->assertSeeInOrder([__('pages.process.steps'), '04', __('pages.process.materials'), '08']);
    }

    public function test_a_process_without_steps_says_so_and_has_no_diagram(): void
    {
        $this->get('/en/process/zinc-nickel-alloy')->assertOk()
            ->assertSee(__('pages.process.empty'))
            ->assertDontSee('data-diagram-open', false)
            ->assertDontSee('data-diagram-dialog', false);

        $this->get('/en/process/barrel-zinc-plating')->assertOk()
            ->assertSee('Barrel Zinc Plating process flow.');
    }

    public function test_the_page_links_back_and_lists_the_other_processes_of_its_category(): void
    {
        $response = $this->get('/en/process/rack-zinc-plating')->assertOk();

        $response->assertSee(route('landing.processes.show', ['locale' => 'en', 'category' => 'zinc-plating']), false)
            ->assertSee(route('landing.process.show', ['locale' => 'en', 'process' => 'barrel-zinc-plating']), false)
            ->assertSee(route('landing.process.show', ['locale' => 'en', 'process' => 'clear-passivation']), false);

        $this->assertStringNotContainsString('process/rack-zinc-plating"', $this->othersSection((string) $response->getContent()));
    }

    public function test_unknown_process_is_not_found(): void
    {
        $this->get('/en/process/does-not-exist')->assertNotFound();
        $this->get('/xx/process/rack-zinc-plating')->assertNotFound();
    }

    public function test_category_page_and_index_link_to_each_process(): void
    {
        $category = $this->categories[0];
        $page = $this->get('/en/processes/'.$category['slug'])->assertOk();
        $index = $this->get('/en/processes')->assertOk();

        foreach ($category['processes'] as $name) {
            $url = route('landing.process.show', ['locale' => 'en', 'process' => Str::slug($name)]);

            $page->assertSee($url, false);
            $index->assertSee($url, false);
        }

        $this->get('/en/processes/zinc-plating')->assertOk()
            ->assertSee('Clear Passivation process flow.');
    }

    public function test_labels_exist_in_every_language_and_direction_follows_the_locale(): void
    {
        foreach (config('locales.landing') as $code) {
            $response = $this->get("/{$code}/process/rack-zinc-plating")->assertOk();

            foreach (['view', 'category', 'steps', 'materials', 'flow_label', 'flow_title', 'flow_intro', 'view_diagram', 'replay', 'close', 'others', 'material_detail', 'usage', 'brochure', 'video', 'ecommerce', 'tds', 'price', 'request'] as $key) {
                $text = __("pages.process.{$key}", [], $code);

                $this->assertNotSame("pages.process.{$key}", $text, "{$code} {$key}");

                if ($key !== 'view') {
                    $response->assertSee($text, false);
                }
            }
        }

        $this->get('/ar/process/rack-zinc-plating')->assertSee('dir="rtl"', false);
    }

    public function test_the_page_does_not_touch_the_database_or_grow_with_more_steps(): void
    {
        $queries = 0;
        DB::listen(function () use (&$queries): void {
            $queries++;
        });

        // The first request warms the layout caches, so measure the second one.
        $this->get('/en/process/rack-zinc-plating')->assertOk();
        $queries = 0;
        $this->get('/en/process/rack-zinc-plating')->assertOk();
        $first = $queries;

        $process = Process::query()->where('slug', 'rack-zinc-plating')->firstOrFail();
        $step = $process->steps()->create(['name' => 'Drying', 'position' => 9]);
        $step->products()->attach([Product::factory()->create()->id, Product::factory()->create()->id], ['position' => 0]);
        app(LandingProcesses::class)->flush();

        // Warm the new cache entry, then measure again.
        $this->get('/en/process/rack-zinc-plating')->assertOk();

        $queries = 0;
        $this->get('/en/process/rack-zinc-plating')->assertOk()->assertSee('Drying');

        $this->assertSame($first, $queries);
    }

    public function test_content_service_returns_counts_and_other_processes(): void
    {
        $process = app(LandingContent::class)->process('rack-zinc-plating');

        $this->assertNotNull($process);
        $this->assertSame(4, count($process['steps']));
        $this->assertSame(8, $process['products']);
        $this->assertSame('zinc-plating', $process['category']['slug']);
        $this->assertSame(3, count($process['others']));
        $this->assertNull(app(LandingContent::class)->process('nope'));
    }

    private function othersSection(string $html): string
    {
        return substr($html, (int) strpos($html, __('pages.process.others')));
    }
}
