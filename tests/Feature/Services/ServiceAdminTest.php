<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Livewire\Services\Editor;
use App\Livewire\Services\Index;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_adds_a_service_with_languages_and_an_image(): void
    {
        Storage::fake('public');

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', '  Zinc Plating Line  ')
            ->set('form.image', UploadedFile::fake()->image('cover.jpg', 1000, 800))
            ->set('form.translations.en.title', 'Zinc plating')
            ->set('form.translations.en.summary', 'Short.')
            ->set('form.translations.en.included', "Cleaning\n\n  Plating  \r\nInspection")
            ->set('form.translations.id.title', 'Pelapisan seng')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.services.index'));

        $service = Service::query()->with('translations')->firstOrFail();
        $this->assertSame('Zinc Plating Line', $service->name);
        $this->assertSame('zinc-plating-line', $service->slug);
        $this->assertSame(0, $service->position);
        $this->assertTrue($service->is_active);
        $this->assertNotNull($service->image_path);
        Storage::disk('public')->assertExists((string) $service->image_path);

        $this->assertSame(['en', 'id'], $service->translations->pluck('locale')->sort()->values()->all());
        $en = $service->translations->firstWhere('locale', 'en');
        $this->assertSame(['Cleaning', 'Plating', 'Inspection'], $en?->included);
        $this->assertNull($en->description);
        $this->assertNull($en->fit);
    }

    public function test_english_is_required_and_empty_languages_are_not_saved(): void
    {
        Storage::fake('public');
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.image', UploadedFile::fake()->image('cover.jpg', 1000, 800))
            ->set('form.name', 'X');

        $component->call('save')->assertHasErrors(['form.translations']);

        $component->set('form.translations.id.title', 'Hanya Indonesia')->call('save')->assertHasErrors(['form.translations']);

        $component->set('form.translations.en.title', 'English')->set('form.translations.id.title', '')->call('save')->assertHasNoErrors();

        $this->assertSame(['en'], Service::query()->firstOrFail()->translations->pluck('locale')->all());
    }

    public function test_a_filled_language_needs_a_title(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'X')
            ->set('form.translations.en.title', 'English')
            ->set('form.translations.ja.summary', 'Only a summary')
            ->call('save')
            ->assertHasErrors(['form.translations.ja.title' => 'required']);
    }

    public function test_the_name_is_validated_and_must_be_unique(): void
    {
        Service::factory()->create(['name' => 'Taken', 'slug' => 'taken']);
        Service::factory()->create(['name' => 'Legacy', 'slug' => 'a-b']);
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.translations.en.title', 'English');

        $component->set('form.name', '   ')->call('save')->assertHasErrors(['form.name' => 'required']);
        $component->set('form.name', str_repeat('a', 256))->call('save')->assertHasErrors(['form.name' => 'max']);
        $component->set('form.name', 'Taken')->call('save')->assertHasErrors(['form.name' => 'unique']);
        $component->set('form.name', 'A B')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', '!!!')->call('save')->assertHasErrors(['form.name']);

        $this->assertSame(2, Service::query()->count());
    }

    public function test_the_slug_is_made_from_the_name_and_new_services_go_last(): void
    {
        Storage::fake('public');
        Service::factory()->create(['position' => 4]);
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.image', UploadedFile::fake()->image('cover.jpg', 1000, 800))
            ->set('form.translations.en.title', 'English')
            ->set('form.name', 'Zinc Plating & Co')->call('save')->assertHasNoErrors();

        $service = Service::query()->where('name', 'Zinc Plating & Co')->firstOrFail();
        $this->assertSame('zinc-plating-co', $service->slug);
        $this->assertSame(5, $service->position);
    }

    public function test_text_lengths_are_limited(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'Ok')
            ->set('form.translations.en.title', 'English');

        $component->set('form.translations.en.summary', str_repeat('a', 501))->call('save')
            ->assertHasErrors(['form.translations.en.summary' => 'max']);
        $component->set('form.translations.en.summary', '')->set('form.translations.en.description', str_repeat('a', 5001))->call('save')
            ->assertHasErrors(['form.translations.en.description' => 'max']);
        $component->set('form.translations.en.description', '')->set('form.translations.en.included', str_repeat('a', 256))->call('save')
            ->assertHasErrors(['form.translations.en.included']);
    }

    public function test_the_image_must_be_a_large_enough_picture(): void
    {
        Storage::fake('public');
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $component->set('form.image', UploadedFile::fake()->image('small.jpg', 400, 300))->assertHasErrors(['form.image' => 'dimensions']);
        $component->set('form.image', UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'))->assertHasErrors(['form.image']);
        $component->set('form.image', UploadedFile::fake()->image('big.jpg', 800, 600))->assertHasNoErrors();
    }

    public function test_admin_edits_a_service_and_removes_an_emptied_language(): void
    {
        $service = Service::factory()->create(['name' => 'Old', 'slug' => 'old', 'image_path' => 'services/old.webp']);
        $service->translations()->create(['locale' => 'en', 'title' => 'Old title', 'included' => ['A', 'B']]);
        $service->translations()->create(['locale' => 'id', 'title' => 'Judul lama']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['service' => $service])
            ->assertSet('form.name', 'Old')
            ->assertSet('form.translations.en.included', "A\nB")
            ->set('form.name', 'New')
            ->set('form.isActive', false)
            ->set('form.translations.en.title', 'New title')
            ->set('form.translations.id.title', '')
            ->call('save')->assertHasNoErrors();

        $service->refresh()->load('translations');
        $this->assertSame('New', $service->name);
        $this->assertFalse($service->is_active);
        $this->assertSame(['en'], $service->translations->pluck('locale')->all());
        $this->assertSame('New title', $service->translations->first()?->title);
        $this->assertSame(1, Service::query()->count());
    }

    public function test_editing_keeps_the_slug_and_the_own_name_is_not_a_duplicate(): void
    {
        $service = Service::factory()->create(['name' => 'Same', 'slug' => 'custom-slug', 'position' => 3, 'image_path' => 'services/same.webp']);
        $service->translations()->create(['locale' => 'en', 'title' => 'T']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['service' => $service])
            ->call('save')->assertHasNoErrors()
            ->set('form.name', 'Renamed')->call('save')->assertHasNoErrors();

        $service->refresh();
        $this->assertSame('custom-slug', $service->slug);
        $this->assertSame(3, $service->position);
        $this->assertSame('Renamed', $service->name);
    }

    public function test_a_service_without_a_slug_gets_one_from_its_name_on_edit(): void
    {
        $service = Service::factory()->create(['name' => 'Old Service', 'slug' => null, 'image_path' => 'services/old.webp']);
        $service->translations()->create(['locale' => 'en', 'title' => 'T']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['service' => $service])
            ->call('save')->assertHasNoErrors();

        $this->assertSame('old-service', $service->refresh()->slug);
    }

    public function test_the_picture_is_required_until_the_service_has_one(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(Editor::class)
            ->set('form.name', 'No Picture')->set('form.translations.en.title', 'T')
            ->call('save')->assertHasErrors(['form.image' => 'required']);
        $this->assertSame(0, Service::query()->count());

        $without = Service::factory()->create(['image_path' => null]);
        $without->translations()->create(['locale' => 'en', 'title' => 'T']);

        Livewire::actingAs($admin)->test(Editor::class, ['service' => $without])
            ->call('save')->assertHasErrors(['form.image' => 'required']);

        $with = Service::factory()->create(['image_path' => 'services/kept.webp']);
        $with->translations()->create(['locale' => 'en', 'title' => 'T']);

        Livewire::actingAs($admin)->test(Editor::class, ['service' => $with])
            ->call('save')->assertHasNoErrors();
        $this->assertSame('services/kept.webp', $with->refresh()->image_path);
    }

    public function test_the_picture_label_is_marked_as_required_only_while_there_is_no_picture(): void
    {
        $admin = User::factory()->admin()->create();
        $with = Service::factory()->create(['image_path' => 'services/kept.webp']);

        $marked = function (string $url) use ($admin): bool {
            preg_match('/<label for="form-image".*?<\/label>/s', (string) $this->actingAs($admin)->get($url)->assertOk()->getContent(), $label);

            return str_contains($label[0] ?? '', ' *</span>');
        };

        $this->assertTrue($marked('/app/services/create'));
        $this->assertFalse($marked("/app/services/{$with->id}/edit"));
    }

    public function test_a_new_image_replaces_the_old_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('services/old.webp', 'x');
        $service = Service::factory()->create(['image_path' => 'services/old.webp']);
        $service->translations()->create(['locale' => 'en', 'title' => 'T']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['service' => $service])
            ->set('form.image', UploadedFile::fake()->image('new.png', 900, 700))
            ->call('save')->assertHasNoErrors();

        $path = (string) $service->refresh()->image_path;
        $this->assertNotSame('services/old.webp', $path);
        Storage::disk('public')->assertExists($path);
        Storage::disk('public')->assertMissing('services/old.webp');
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
        Service::factory()->count(2)->create()->each(fn (Service $service) => $service->translations()->create(['locale' => 'en', 'title' => 'T']));

        DB::enableQueryLog();
        Livewire::actingAs($admin)->test(Index::class);
        $few = count(DB::getQueryLog());

        Service::factory()->count(20)->create()->each(fn (Service $service) => $service->translations()->create(['locale' => 'en', 'title' => 'T']));
        DB::flushQueryLog();
        $component = Livewire::actingAs($admin)->test(Index::class);

        $this->assertSame($few, count(DB::getQueryLog()));
        $this->assertCount(15, $component->viewData('services')->items());
    }

    public function test_the_list_shows_languages_in_order_and_status(): void
    {
        $service = Service::factory()->create(['name' => 'Listed', 'slug' => 'listed', 'is_active' => false]);
        $service->translations()->create(['locale' => 'ja', 'title' => 'J']);
        $service->translations()->create(['locale' => 'en', 'title' => 'E']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Index::class)
            ->assertSeeInOrder(['Listed', 'listed', 'English:', __('services.language_filled'), 'Bahasa Indonesia:', __('services.language_empty'), '日本語:', __('services.language_filled'), __('services.inactive')]);
    }

    public function test_services_move_up_and_down_in_the_whole_list(): void
    {
        $a = Service::factory()->create(['name' => 'A', 'position' => 0]);
        $b = Service::factory()->create(['name' => 'B', 'position' => 0]);
        $c = Service::factory()->create(['name' => 'C', 'position' => 7]);
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(Index::class)->call('move', $c->id, 'up');
        $this->assertSame([$a->id, $c->id, $b->id], Service::query()->orderBy('position')->pluck('id')->all());

        Livewire::actingAs($admin)->test(Index::class)->call('move', $a->id, 'up');
        $this->assertSame(0, $a->refresh()->position);

        Livewire::actingAs($admin)->test(Index::class)->call('move', $a->id, 'down');
        $this->assertSame([$c->id, $a->id, $b->id], Service::query()->orderBy('position')->pluck('id')->all());
        $this->assertSame(2, $b->refresh()->position);
    }

    public function test_only_admins_can_move_services(): void
    {
        $service = Service::factory()->create(['position' => 0]);
        Service::factory()->create(['position' => 1]);

        Livewire::actingAs(User::factory()->staff()->create())->test(Index::class)->assertForbidden();
        $this->actingAs(User::factory()->staff()->create())->get('/app/services')->assertForbidden();

        $this->assertSame(0, $service->refresh()->position);
    }

    public function test_the_list_is_ordered_by_position_then_name(): void
    {
        Service::factory()->create(['name' => 'B first', 'position' => 0]);
        Service::factory()->create(['name' => 'A last', 'position' => 5]);

        Livewire::actingAs(User::factory()->admin()->create())->test(Index::class)
            ->assertSeeInOrder(['B first', 'A last']);
    }

    public function test_texts_exist_in_both_dashboard_languages(): void
    {
        $en = require lang_path('en/services.php');
        $id = require lang_path('id/services.php');

        $this->assertSame(array_keys($en), array_keys($id));
        $this->assertSame(array_keys($en['fields']), array_keys($id['fields']));
        $this->assertSame(array_keys($en['hints']), array_keys($id['hints']));
        $this->assertSame(array_keys($en['messages']), array_keys($id['messages']));
    }
}
