<?php

declare(strict_types=1);

namespace Tests\Feature\ProcessCategories;

use App\Livewire\ProcessCategories\Editor;
use App\Livewire\ProcessCategories\Index;
use App\Models\ProcessCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ProcessCategoryAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_open_the_pages(): void
    {
        $processCategory = ProcessCategory::factory()->create();

        foreach (['/app/process-categories', '/app/process-categories/create', "/app/process-categories/{$processCategory->id}/edit"] as $url) {
            $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk();
            $this->actingAs(User::factory()->staff()->create())->get($url)->assertForbidden();
            $this->actingAs(User::factory()->customer()->create())->get($url)->assertForbidden();
        }

        auth()->logout();
        $this->get('/app/process-categories')->assertRedirect(route('app.login'));
    }

    public function test_staff_cannot_use_the_livewire_components(): void
    {
        $staff = User::factory()->staff()->create();

        Livewire::actingAs($staff)->test(Index::class)->assertForbidden();
        Livewire::actingAs($staff)->test(Editor::class)->assertForbidden();
    }

    public function test_the_menu_item_is_for_admins_only(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')->assertSee(route('app.process-categories.index'), false);
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')->assertDontSee(route('app.process-categories.index'), false);
    }

    public function test_admin_adds_a_process_category(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', '  Zinc Brightener  ')
            ->set('form.description', 'Bright finish additive.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.process-categories.index'));

        $processCategory = ProcessCategory::query()->firstOrFail();
        $this->assertSame('Zinc Brightener', $processCategory->name);
        $this->assertSame('Bright finish additive.', $processCategory->description);
    }

    public function test_the_description_is_optional(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'Only a name')->call('save')->assertHasNoErrors();

        $this->assertNull(ProcessCategory::query()->firstOrFail()->description);
    }

    public function test_name_is_required_and_lengths_are_limited(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $component->set('form.name', '   ')->call('save')->assertHasErrors(['form.name' => 'required']);
        $component->set('form.name', str_repeat('a', 256))->call('save')->assertHasErrors(['form.name' => 'max']);
        $component->set('form.name', 'Ok')->set('form.description', str_repeat('a', 5001))->call('save')->assertHasErrors(['form.description' => 'max']);

        $this->assertSame(0, ProcessCategory::query()->count());
    }

    public function test_admin_edits_a_process_category(): void
    {
        $processCategory = ProcessCategory::factory()->create(['name' => 'Old']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['processCategory' => $processCategory])
            ->assertSet('form.name', 'Old')
            ->set('form.name', 'New')->set('form.description', '')
            ->call('save')->assertHasNoErrors();

        $processCategory->refresh();
        $this->assertSame('New', $processCategory->name);
        $this->assertNull($processCategory->description);
        $this->assertSame(1, ProcessCategory::query()->count());
    }

    public function test_the_list_searches_by_name_without_regard_to_case(): void
    {
        ProcessCategory::factory()->create(['name' => 'Alpha Zinc']);
        ProcessCategory::factory()->create(['name' => 'Beta Nickel']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Index::class)
            ->assertSee('Alpha Zinc')->assertSee('Beta Nickel')
            ->set('search', 'ZINC')->assertSee('Alpha Zinc')->assertDontSee('Beta Nickel');
    }

    public function test_the_list_is_paginated_and_the_query_count_stays_flat(): void
    {
        $admin = User::factory()->admin()->create();
        ProcessCategory::factory()->count(2)->create();

        DB::enableQueryLog();
        Livewire::actingAs($admin)->test(Index::class);
        $few = count(DB::getQueryLog());

        ProcessCategory::factory()->count(20)->create();
        DB::flushQueryLog();
        $component = Livewire::actingAs($admin)->test(Index::class);

        $this->assertSame($few, count(DB::getQueryLog()));
        $this->assertCount(15, $component->viewData('processCategories')->items());
    }

    public function test_texts_exist_in_both_dashboard_languages(): void
    {
        $en = require lang_path('en/process_categories.php');
        $id = require lang_path('id/process_categories.php');

        $this->assertSame(array_keys($en), array_keys($id));
        $this->assertSame(array_keys($en['fields']), array_keys($id['fields']));
        $this->assertSame(array_keys($en['messages']), array_keys($id['messages']));
    }
}
