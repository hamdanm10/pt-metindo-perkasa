<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Livewire\Services\Editor;
use App\Livewire\Services\Index;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_open_the_pages(): void
    {
        $service = Service::factory()->create();

        foreach (['/app/services', '/app/services/create', "/app/services/{$service->id}/edit"] as $url) {
            $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk();
            $this->actingAs(User::factory()->staff()->create())->get($url)->assertForbidden();
            $this->actingAs(User::factory()->customer()->create())->get($url)->assertForbidden();
        }

        auth()->logout();
        $this->get('/app/services')->assertRedirect(route('app.login'));
    }

    public function test_staff_cannot_use_the_livewire_components(): void
    {
        $staff = User::factory()->staff()->create();

        Livewire::actingAs($staff)->test(Index::class)->assertForbidden();
        Livewire::actingAs($staff)->test(Editor::class)->assertForbidden();
    }

    public function test_the_menu_item_is_for_admins_only(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')->assertSee(route('app.services.index'), false);
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')->assertDontSee(route('app.services.index'), false);
    }

    public function test_admin_adds_a_service(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', '  Zinc Plating Line  ')
            ->set('form.description', 'Plating line service.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.services.index'));

        $service = Service::query()->firstOrFail();
        $this->assertSame('Zinc Plating Line', $service->name);
        $this->assertSame('Plating line service.', $service->description);
    }

    public function test_the_description_is_optional(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'Only a name')->call('save')->assertHasNoErrors();

        $this->assertNull(Service::query()->firstOrFail()->description);
    }

    public function test_name_is_required_and_lengths_are_limited(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $component->set('form.name', '   ')->call('save')->assertHasErrors(['form.name' => 'required']);
        $component->set('form.name', str_repeat('a', 256))->call('save')->assertHasErrors(['form.name' => 'max']);
        $component->set('form.name', 'Ok')->set('form.description', str_repeat('a', 5001))->call('save')->assertHasErrors(['form.description' => 'max']);

        $this->assertSame(0, Service::query()->count());
    }

    public function test_admin_edits_a_service(): void
    {
        $service = Service::factory()->create(['name' => 'Old']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['service' => $service])
            ->assertSet('form.name', 'Old')
            ->set('form.name', 'New')->set('form.description', '')
            ->call('save')->assertHasNoErrors();

        $service->refresh();
        $this->assertSame('New', $service->name);
        $this->assertNull($service->description);
        $this->assertSame(1, Service::query()->count());
    }

    public function test_the_list_searches_by_name_without_regard_to_case(): void
    {
        Service::factory()->create(['name' => 'Alpha Zinc']);
        Service::factory()->create(['name' => 'Beta Nickel']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Index::class)
            ->assertSee('Alpha Zinc')->assertSee('Beta Nickel')
            ->set('search', 'ZINC')->assertSee('Alpha Zinc')->assertDontSee('Beta Nickel');
    }

    public function test_the_list_is_paginated_and_the_query_count_stays_flat(): void
    {
        $admin = User::factory()->admin()->create();
        Service::factory()->count(2)->create();

        DB::enableQueryLog();
        Livewire::actingAs($admin)->test(Index::class);
        $few = count(DB::getQueryLog());

        Service::factory()->count(20)->create();
        DB::flushQueryLog();
        $component = Livewire::actingAs($admin)->test(Index::class);

        $this->assertSame($few, count(DB::getQueryLog()));
        $this->assertCount(15, $component->viewData('services')->items());
    }

    public function test_texts_exist_in_both_dashboard_languages(): void
    {
        $en = require lang_path('en/services.php');
        $id = require lang_path('id/services.php');

        $this->assertSame(array_keys($en), array_keys($id));
        $this->assertSame(array_keys($en['fields']), array_keys($id['fields']));
        $this->assertSame(array_keys($en['messages']), array_keys($id['messages']));
    }
}
