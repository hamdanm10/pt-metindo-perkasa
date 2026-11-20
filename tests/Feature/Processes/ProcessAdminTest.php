<?php

declare(strict_types=1);

namespace Tests\Feature\Processes;

use App\Livewire\Lookup;
use App\Livewire\Processes\Editor;
use App\Livewire\Processes\Index;
use App\Models\Process;
use App\Models\ProcessCategory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ProcessAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_only_admins_open_the_pages(): void
    {
        $process = Process::factory()->create();

        foreach (['/app/processes', '/app/processes/create', "/app/processes/{$process->id}/edit"] as $url) {
            $this->actingAs($this->admin())->get($url)->assertOk();
            $this->actingAs(User::factory()->staff()->create())->get($url)->assertForbidden();
            $this->actingAs(User::factory()->customer()->create())->get($url)->assertForbidden();
        }

        auth()->logout();
        $this->get('/app/processes')->assertRedirect(route('app.login'));
    }

    public function test_staff_cannot_use_the_livewire_components(): void
    {
        $staff = User::factory()->staff()->create();

        Livewire::actingAs($staff)->test(Index::class)->assertForbidden();
        Livewire::actingAs($staff)->test(Editor::class)->assertForbidden();
    }

    public function test_the_menu_item_is_for_admins_only(): void
    {
        $this->actingAs($this->admin())->get('/app/dashboard')->assertSee(route('app.processes.index'), false);
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')->assertDontSee(route('app.processes.index'), false);
    }

    public function test_admin_adds_a_process_with_steps_products_and_languages(): void
    {
        $category = ProcessCategory::factory()->create();
        $first = Product::factory()->create(['name' => 'Epclen 62']);
        $second = Product::factory()->create(['name' => 'Epclen 541']);

        $component = Livewire::actingAs($this->admin())->test(Editor::class)
            ->set('form.name', '  Alkaline Zinc  ')
            ->set('form.processCategoryId', (string) $category->id)
            ->set('form.descriptions.en', 'Zinc flow.')
            ->set('form.descriptions.id', 'Alur seng.')
            ->call('addStep')
            ->set('form.steps.0.name', ' Degreasing ')
            ->set('form.steps.0.descriptions.en', 'Remove oil.')
            ->set('form.steps.0.descriptions.id', 'Buang minyak.')
            ->set('picker.0', (string) $first->id)
            ->set('picker.0', (string) $second->id)
            ->call('addStep')
            ->set('form.steps.1.name', 'Rinsing');

        $component->assertSet('form.steps.0.products', [$first->id, $second->id])
            ->assertSet('picker.0', '')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.processes.index'));

        $process = Process::query()->with(['translations', 'steps.translations', 'steps.products'])->firstOrFail();
        $this->assertSame('Alkaline Zinc', $process->name);
        $this->assertSame('alkaline-zinc', $process->slug);
        $this->assertSame($category->id, $process->process_category_id);
        $this->assertSame(0, $process->position);
        $this->assertSame(['en', 'id'], $process->translations->pluck('locale')->sort()->values()->all());
        $this->assertSame(['Degreasing', 'Rinsing'], $process->steps->pluck('name')->all());
        $this->assertSame([0, 1], $process->steps->pluck('position')->all());
        $one = $process->steps->sole(fn ($step): bool => $step->name === 'Degreasing');
        $two = $process->steps->sole(fn ($step): bool => $step->name === 'Rinsing');
        $this->assertSame(['en', 'id'], $one->translations->pluck('locale')->sort()->values()->all());
        $this->assertCount(0, $two->translations);
        $this->assertSame([$first->id, $second->id], $one->products->pluck('id')->all());
        $this->assertCount(0, $two->products);
    }

    public function test_english_is_required_and_the_category_must_exist(): void
    {
        $category = ProcessCategory::factory()->create();
        $component = Livewire::actingAs($this->admin())->test(Editor::class)
            ->set('form.name', 'Flow')
            ->set('form.processCategoryId', (string) $category->id)
            ->set('form.descriptions.id', 'Hanya Indonesia');

        $component->call('save')->assertHasErrors(['form.descriptions']);

        $component->set('form.descriptions.en', 'x')->set('form.processCategoryId', '')->call('save')->assertHasErrors(['form.processCategoryId']);
        $component->set('form.processCategoryId', '999999')->call('save')->assertHasErrors(['form.processCategoryId']);
        $this->assertSame(0, Process::query()->count());
    }

    public function test_the_name_is_validated_and_must_be_unique(): void
    {
        $category = ProcessCategory::factory()->create();
        Process::factory()->create(['name' => 'Taken', 'slug' => 'taken']);
        $component = Livewire::actingAs($this->admin())->test(Editor::class)
            ->set('form.processCategoryId', (string) $category->id)
            ->set('form.descriptions.en', 'x');

        $component->set('form.name', '')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', str_repeat('a', 256))->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', 'Taken')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', 'TAKEN!')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', '!!!')->call('save')->assertHasErrors(['form.name']);
        $this->assertSame(1, Process::query()->count());
    }

    public function test_steps_are_validated(): void
    {
        $category = ProcessCategory::factory()->create();
        $product = Product::factory()->create();
        $component = Livewire::actingAs($this->admin())->test(Editor::class)
            ->set('form.name', 'Flow')
            ->set('form.processCategoryId', (string) $category->id)
            ->set('form.descriptions.en', 'x')
            ->call('addStep');

        $component->call('save')->assertHasErrors(['form.steps.0.name']);
        $component->set('form.steps.0.name', str_repeat('a', 256))->call('save')->assertHasErrors(['form.steps.0.name']);
        $component->set('form.steps.0.name', 'Ok')->set('form.steps.0.descriptions.en', str_repeat('a', 1001))->call('save')->assertHasErrors(['form.steps.0.descriptions.en']);
        $component->set('form.steps.0.descriptions.en', '')->set('form.steps.0.products', [999999])->call('save')->assertHasErrors(['form.steps.0.products.0']);
        $component->set('form.steps.0.products', [$product->id, $product->id])->call('save')->assertHasErrors();
        $this->assertSame(0, Process::query()->count());
    }

    public function test_the_number_of_steps_and_products_is_limited_and_unknown_products_are_ignored(): void
    {
        $component = Livewire::actingAs($this->admin())->test(Editor::class);

        for ($i = 0; $i < 35; $i++) {
            $component->call('addStep');
        }
        $this->assertCount(30, $component->get('form.steps'));

        $component->set('picker.0', '999999')->assertSet('form.steps.0.products', []);
        $component->set('picker.0', 'abc')->assertSet('form.steps.0.products', []);

        $product = Product::factory()->create();
        $component->set('picker.0', (string) $product->id)->set('picker.0', (string) $product->id)->assertSet('form.steps.0.products', [$product->id]);
    }

    public function test_steps_can_be_removed_and_moved_and_products_removed(): void
    {
        $product = Product::factory()->create();
        $component = Livewire::actingAs($this->admin())->test(Editor::class)
            ->call('addStep')->set('form.steps.0.name', 'One')
            ->call('addStep')->set('form.steps.1.name', 'Two')
            ->call('addStep')->set('form.steps.2.name', 'Three')
            ->set('picker.1', (string) $product->id);

        $component->call('moveStep', 1, true)->assertSet('form.steps.0.name', 'Two')->assertSet('form.steps.1.name', 'One');
        $component->call('moveStep', 0, true)->assertSet('form.steps.0.name', 'Two');
        $component->call('moveStep', 2, false)->assertSet('form.steps.2.name', 'Three');
        $component->call('removeStep', 1)->assertSet('form.steps.1.name', 'Three')->assertCount('form.steps', 2);
        $component->call('removeProduct', 0, $product->id)->assertSet('form.steps.0.products', []);
    }

    public function test_editing_replaces_the_steps_keeps_the_slug_and_removes_an_emptied_language(): void
    {
        $category = ProcessCategory::factory()->create();
        $product = Product::factory()->create();
        $process = Process::factory()->create(['process_category_id' => $category->id, 'name' => 'Old', 'slug' => 'custom']);
        $process->translations()->create(['locale' => 'en', 'description' => 'English']);
        $process->translations()->create(['locale' => 'id', 'description' => 'Indonesia']);
        $step = $process->steps()->create(['name' => 'Old step', 'position' => 0]);
        $step->products()->attach($product->id, ['position' => 0]);

        Livewire::actingAs($this->admin())->test(Editor::class, ['process' => $process])
            ->assertSet('form.name', 'Old')
            ->assertSet('form.steps.0.name', 'Old step')
            ->assertSet('form.steps.0.products', [$product->id])
            ->set('form.name', 'Renamed')
            ->set('form.descriptions.id', '')
            ->set('form.isActive', false)
            ->call('removeStep', 0)
            ->call('addStep')
            ->set('form.steps.0.name', 'New step')
            ->call('save')->assertHasNoErrors();

        $process->refresh()->load(['translations', 'steps']);
        $this->assertSame('custom', $process->slug);
        $this->assertSame('Renamed', $process->name);
        $this->assertFalse($process->is_active);
        $this->assertSame(['en'], $process->translations->pluck('locale')->all());
        $this->assertSame(['New step'], $process->steps->pluck('name')->all());
        $this->assertSame(0, DB::table('process_step_product')->count());
    }

    public function test_new_processes_go_last_in_their_category_and_moving_category_puts_it_last(): void
    {
        $a = ProcessCategory::factory()->create();
        $b = ProcessCategory::factory()->create();
        Process::factory()->create(['process_category_id' => $a->id, 'position' => 4]);
        $inB = Process::factory()->create(['process_category_id' => $b->id, 'position' => 9]);
        $admin = $this->admin();

        Livewire::actingAs($admin)->test(Editor::class)
            ->set('form.name', 'Fresh')->set('form.processCategoryId', (string) $a->id)->set('form.descriptions.en', 'x')->call('save')->assertHasNoErrors();
        $fresh = Process::query()->where('name', 'Fresh')->firstOrFail();
        $this->assertSame(5, $fresh->position);

        Livewire::actingAs($admin)->test(Editor::class, ['process' => $fresh])
            ->set('form.processCategoryId', (string) $b->id)->call('save')->assertHasNoErrors();
        $this->assertSame(10, $fresh->refresh()->position);
        $this->assertSame($b->id, $fresh->process_category_id);
        $this->assertSame(9, $inB->refresh()->position);
    }

    public function test_processes_move_up_and_down_inside_their_category_only(): void
    {
        $a = ProcessCategory::factory()->create();
        $b = ProcessCategory::factory()->create();
        $p1 = Process::factory()->create(['process_category_id' => $a->id, 'name' => 'A1', 'position' => 0]);
        $p2 = Process::factory()->create(['process_category_id' => $a->id, 'name' => 'A2', 'position' => 0]);
        $p3 = Process::factory()->create(['process_category_id' => $a->id, 'name' => 'A3', 'position' => 7]);
        $other = Process::factory()->create(['process_category_id' => $b->id, 'name' => 'B1', 'position' => 3]);
        $admin = $this->admin();

        Livewire::actingAs($admin)->test(Index::class)->call('move', $p3->id, 'up');
        $this->assertSame([$p1->id, $p3->id, $p2->id], Process::query()->where('process_category_id', $a->id)->orderBy('position')->pluck('id')->all());

        Livewire::actingAs($admin)->test(Index::class)->call('move', $p1->id, 'up');
        $this->assertSame(0, $p1->refresh()->position);

        Livewire::actingAs($admin)->test(Index::class)->call('move', $p1->id, 'down');
        $this->assertSame([$p3->id, $p1->id, $p2->id], Process::query()->where('process_category_id', $a->id)->orderBy('position')->pluck('id')->all());
        $this->assertSame(3, $other->refresh()->position);
    }

    public function test_only_admins_can_move_processes(): void
    {
        $process = Process::factory()->create(['position' => 0]);

        Livewire::actingAs(User::factory()->staff()->create())->test(Index::class)->assertForbidden();
        $this->assertSame(0, $process->refresh()->position);
    }

    public function test_the_list_searches_filters_by_category_and_is_ordered_by_category(): void
    {
        $first = ProcessCategory::factory()->create(['name' => 'Cat One', 'position' => 0]);
        $second = ProcessCategory::factory()->create(['name' => 'Cat Two', 'position' => 1]);
        Process::factory()->create(['process_category_id' => $second->id, 'name' => 'Beta Flow']);
        Process::factory()->create(['process_category_id' => $first->id, 'name' => 'Alpha Flow']);

        $component = Livewire::actingAs($this->admin())->test(Index::class);
        $component->assertSeeInOrder(['Alpha Flow', 'Beta Flow']);

        $component->set('search', 'BETA');
        $component->assertSee('Beta Flow');
        $component->assertDontSee('Alpha Flow');

        $component->set('search', '')->set('category', (string) $first->id);
        $component->assertSee('Alpha Flow');
        $component->assertDontSee('Beta Flow');
    }

    public function test_the_list_shows_languages_steps_and_status_with_a_flat_query_count(): void
    {
        $admin = $this->admin();
        $make = function (int $count): void {
            Process::factory()->count($count)->create()->each(function (Process $process): void {
                $process->translations()->create(['locale' => 'en', 'description' => 'x']);
                $process->steps()->create(['name' => 'Step', 'position' => 0]);
            });
        };

        $make(2);
        DB::enableQueryLog();
        Livewire::actingAs($admin)->test(Index::class);
        $few = count(DB::getQueryLog());

        $make(20);
        DB::flushQueryLog();
        $component = Livewire::actingAs($admin)->test(Index::class);

        $this->assertSame($few, count(DB::getQueryLog()));
        $this->assertCount(15, $component->viewData('processes')->items());
    }

    public function test_the_product_lookup_is_for_the_staff_area_only(): void
    {
        $product = Product::factory()->create(['name' => 'Zinc Pickle']);

        Livewire::actingAs(User::factory()->staff()->create())->test(Lookup::class, ['source' => 'product'])
            ->call('openPanel')->assertSee('Zinc Pickle');

        Livewire::actingAs(User::factory()->customer()->create())->test(Lookup::class, ['source' => 'product'])->assertForbidden();
        $this->assertSame('Zinc Pickle', $product->name);
    }

    public function test_texts_exist_in_both_dashboard_languages(): void
    {
        $en = require lang_path('en/processes.php');
        $id = require lang_path('id/processes.php');

        $this->assertSame(array_keys($en), array_keys($id));
        $this->assertSame(array_keys($en['fields']), array_keys($id['fields']));
        $this->assertSame(array_keys($en['hints']), array_keys($id['hints']));
        $this->assertSame(array_keys($en['messages']), array_keys($id['messages']));
    }
}
