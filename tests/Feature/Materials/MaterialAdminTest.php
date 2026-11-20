<?php

declare(strict_types=1);

namespace Tests\Feature\Materials;

use App\Livewire\Materials\Editor;
use App\Livewire\Materials\Index;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MaterialAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_open_the_pages(): void
    {
        $material = Material::factory()->create();

        foreach (['/app/materials', '/app/materials/create', "/app/materials/{$material->id}/edit"] as $url) {
            $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk();
            $this->actingAs(User::factory()->staff()->create())->get($url)->assertForbidden();
            $this->actingAs(User::factory()->customer()->create())->get($url)->assertForbidden();
        }

        auth()->logout();
        $this->get('/app/materials')->assertRedirect(route('app.login'));
    }

    public function test_staff_cannot_use_the_livewire_components(): void
    {
        $staff = User::factory()->staff()->create();

        Livewire::actingAs($staff)->test(Index::class)->assertForbidden();
        Livewire::actingAs($staff)->test(Editor::class)->assertForbidden();
    }

    public function test_the_menu_item_is_for_admins_only(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/app/dashboard')->assertSee(route('app.materials.index'), false);
        $this->actingAs(User::factory()->staff()->create())->get('/app/dashboard')->assertDontSee(route('app.materials.index'), false);
    }

    public function test_admin_adds_a_material_with_links_languages_and_a_tds_file(): void
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
            ->assertRedirect(route('app.materials.index'));

        $material = Material::query()->with(['translations', 'links'])->firstOrFail();
        $this->assertSame('Epclen 62', $material->name);
        $this->assertSame('epclen-62', $material->slug);
        $this->assertSame(125000, $material->price);
        $this->assertTrue($material->is_active);
        $this->assertSame('https://example.com/brochure', $material->brochure_url);
        $this->assertNotNull($material->tds_path);
        Storage::disk('public')->assertExists((string) $material->tds_path);
        $this->assertSame(['en', 'id'], $material->translations->pluck('locale')->sort()->values()->all());
        $this->assertSame(['Tokopedia', 'Shopee'], $material->links->pluck('label')->all());
        $this->assertSame([0, 1], $material->links->pluck('position')->all());
    }

    public function test_optional_fields_are_saved_as_null_and_empty_link_rows_are_ignored(): void
    {
        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'Plain')
            ->set('form.descriptions.en', 'Text')
            ->call('addLink')
            ->call('save')
            ->assertHasNoErrors();

        $material = Material::query()->with('links')->firstOrFail();
        $this->assertNull($material->brochure_url);
        $this->assertNull($material->video_url);
        $this->assertNull($material->price);
        $this->assertNull($material->tds_path);
        $this->assertCount(0, $material->links);
    }

    public function test_english_is_required_and_empty_languages_are_not_saved(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'No English')
            ->set('form.descriptions.id', 'Hanya Indonesia')
            ->call('save');

        $component->assertHasErrors(['form.descriptions']);
        $this->assertSame(0, Material::query()->count());
    }

    public function test_the_name_is_validated_and_must_be_unique(): void
    {
        Material::factory()->create(['name' => 'Taken', 'slug' => 'taken']);
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.descriptions.en', 'x');

        $component->set('form.name', '')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', str_repeat('a', 256))->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', 'Taken')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', 'TAKEN!')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', '!!!')->call('save')->assertHasErrors(['form.name']);
        $this->assertSame(1, Material::query()->count());
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
        $this->assertSame(0, Material::query()->count());
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

    public function test_admin_edits_a_material_replaces_links_and_removes_an_emptied_language(): void
    {
        $material = Material::factory()->create(['name' => 'Old', 'slug' => 'old', 'price' => 5]);
        $material->translations()->create(['locale' => 'en', 'description' => 'English']);
        $material->translations()->create(['locale' => 'id', 'description' => 'Indonesia']);
        $material->links()->create(['label' => 'Old shop', 'url' => 'https://old.test', 'position' => 0]);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['material' => $material])
            ->assertSet('form.name', 'Old')
            ->assertSet('form.price', '5')
            ->assertSet('form.links.0.label', 'Old shop')
            ->set('form.descriptions.id', '')
            ->set('form.price', '')
            ->set('form.links.0.label', 'New shop')
            ->set('form.isActive', false)
            ->call('save')
            ->assertHasNoErrors();

        $material->refresh()->load(['translations', 'links']);
        $this->assertSame(['en'], $material->translations->pluck('locale')->all());
        $this->assertSame(['New shop'], $material->links->pluck('label')->all());
        $this->assertNull($material->price);
        $this->assertFalse($material->is_active);
    }

    public function test_editing_keeps_the_slug_and_the_own_name_is_not_a_duplicate(): void
    {
        $material = Material::factory()->create(['name' => 'Alpha', 'slug' => 'custom-slug']);
        $material->translations()->create(['locale' => 'en', 'description' => 'x']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['material' => $material])
            ->set('form.name', 'Beta')
            ->call('save')->assertHasNoErrors();

        $this->assertSame('custom-slug', $material->refresh()->slug);
        $this->assertSame('Beta', $material->name);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['material' => $material])
            ->call('save')->assertHasNoErrors();
    }

    public function test_a_new_tds_replaces_the_old_file_and_it_can_be_removed(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        Storage::disk('public')->put('materials/tds/old.pdf', 'old');
        $material = Material::factory()->create(['tds_path' => 'materials/tds/old.pdf']);
        $material->translations()->create(['locale' => 'en', 'description' => 'x']);

        Livewire::actingAs($admin)->test(Editor::class, ['material' => $material])
            ->set('form.tds', UploadedFile::fake()->create('new.pdf', 50, 'application/pdf'))
            ->call('save')->assertHasNoErrors();

        $material->refresh();
        Storage::disk('public')->assertMissing('materials/tds/old.pdf');
        Storage::disk('public')->assertExists((string) $material->tds_path);
        $newPath = (string) $material->tds_path;

        Livewire::actingAs($admin)->test(Editor::class, ['material' => $material])
            ->set('form.removeTds', true)
            ->call('save')->assertHasNoErrors();

        $this->assertNull($material->refresh()->tds_path);
        Storage::disk('public')->assertMissing($newPath);
    }

    public function test_the_list_searches_by_name_without_regard_to_case(): void
    {
        Material::factory()->create(['name' => 'Epclen 62', 'slug' => 'epclen-62']);
        Material::factory()->create(['name' => 'Jasco CA', 'slug' => 'jasco-ca']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Index::class)
            ->set('search', 'EPCLEN')->assertSee('Epclen 62')->assertDontSee('Jasco CA');
    }

    public function test_the_list_is_paginated_and_the_query_count_stays_flat(): void
    {
        $admin = User::factory()->admin()->create();

        $count = function (int $rows) use ($admin): int {
            Material::query()->delete();
            Material::factory()->count($rows)->create()->each(
                fn (Material $material) => $material->translations()->create(['locale' => 'en', 'description' => 'x'])
            );

            DB::enableQueryLog();
            DB::flushQueryLog();
            $component = Livewire::actingAs($admin)->test(Index::class);
            $queries = count(DB::getQueryLog());
            DB::disableQueryLog();

            $this->assertCount(min($rows, 15), $component->viewData('materials')->items());

            return $queries;
        };

        $this->assertSame($count(2), $count(20));
    }

    public function test_texts_exist_in_both_dashboard_languages(): void
    {
        $en = require lang_path('en/materials.php');
        $id = require lang_path('id/materials.php');

        $this->assertSame(array_keys($en), array_keys($id));
        $this->assertSame(array_keys($en['fields']), array_keys($id['fields']));
        $this->assertSame(array_keys($en['hints']), array_keys($id['hints']));
        $this->assertSame(array_keys($en['messages']), array_keys($id['messages']));
    }
}
