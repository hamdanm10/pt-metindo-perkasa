<?php

declare(strict_types=1);

namespace Tests\Feature\ProcessCategories;

use App\Livewire\ProcessCategories\Editor;
use App\Livewire\ProcessCategories\Index;
use App\Models\Process;
use App\Models\ProcessCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_adds_a_process_category_with_languages(): void
    {
        Storage::fake('public');

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', '  Zinc Brightener  ')
            ->set('form.image', UploadedFile::fake()->image('cover.jpg', 1000, 800))
            ->set('form.descriptions.en', 'Bright finish additive.')
            ->set('form.descriptions.id', 'Aditif kilap.')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('app.process-categories.index'));

        $category = ProcessCategory::query()->with('translations')->firstOrFail();
        $this->assertSame('Zinc Brightener', $category->name);
        $this->assertSame('zinc-brightener', $category->slug);
        $this->assertSame(0, $category->position);
        $this->assertTrue($category->is_active);
        $this->assertSame(['en', 'id'], $category->translations->pluck('locale')->sort()->values()->all());
    }

    public function test_english_is_required(): void
    {
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'No English')
            ->set('form.descriptions.id', 'Hanya Indonesia')
            ->call('save');

        $component->assertHasErrors(['form.descriptions']);
        $this->assertSame(0, ProcessCategory::query()->count());
    }

    public function test_the_name_is_validated_and_must_be_unique(): void
    {
        ProcessCategory::factory()->create(['name' => 'Taken', 'slug' => 'taken']);
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.descriptions.en', 'x');

        $component->set('form.name', '   ')->call('save')->assertHasErrors(['form.name' => 'required']);
        $component->set('form.name', str_repeat('a', 256))->call('save')->assertHasErrors(['form.name' => 'max']);
        $component->set('form.name', 'Taken')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', 'TAKEN!')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', '!!!')->call('save')->assertHasErrors(['form.name']);
        $component->set('form.name', 'Ok')->set('form.descriptions.en', str_repeat('a', 5001))->call('save')->assertHasErrors(['form.descriptions.en']);

        $this->assertSame(1, ProcessCategory::query()->count());
    }

    public function test_new_categories_go_last_and_editing_keeps_the_slug(): void
    {
        Storage::fake('public');
        ProcessCategory::factory()->create(['position' => 4]);
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(Editor::class)
            ->set('form.image', UploadedFile::fake()->image('cover.jpg', 1000, 800))
            ->set('form.name', 'Second')->set('form.descriptions.en', 'x')->call('save')->assertHasNoErrors();

        $second = ProcessCategory::query()->where('name', 'Second')->firstOrFail();
        $this->assertSame(5, $second->position);

        Livewire::actingAs($admin)->test(Editor::class, ['processCategory' => $second])
            ->set('form.name', 'Renamed')->set('form.isActive', false)->call('save')->assertHasNoErrors();

        $second->refresh();
        $this->assertSame('second', $second->slug);
        $this->assertSame('Renamed', $second->name);
        $this->assertFalse($second->is_active);
        $this->assertSame(5, $second->position);
    }

    public function test_admin_edits_a_category_and_removes_an_emptied_language(): void
    {
        $category = ProcessCategory::factory()->create(['name' => 'Old', 'image_path' => 'process-categories/old.webp']);
        $category->translations()->create(['locale' => 'en', 'description' => 'English']);
        $category->translations()->create(['locale' => 'id', 'description' => 'Indonesia']);

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class, ['processCategory' => $category])
            ->assertSet('form.name', 'Old')
            ->assertSet('form.descriptions.id', 'Indonesia')
            ->set('form.descriptions.id', '')
            ->call('save')->assertHasNoErrors();

        $this->assertSame(['en'], $category->translations()->pluck('locale')->all());
        $this->assertSame(1, ProcessCategory::query()->count());
    }

    public function test_admin_adds_a_category_with_a_picture(): void
    {
        Storage::fake('public');

        Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class)
            ->set('form.name', 'With Picture')
            ->set('form.descriptions.en', 'x')
            ->set('form.image', UploadedFile::fake()->image('cover.jpg', 1000, 800))
            ->call('save')->assertHasNoErrors();

        $category = ProcessCategory::query()->firstOrFail();
        $this->assertNotNull($category->image_path);
        $this->assertStringStartsWith('process-categories/', (string) $category->image_path);
        Storage::disk('public')->assertExists((string) $category->image_path);
    }

    public function test_the_picture_is_required_until_the_category_has_one(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(Editor::class)
            ->set('form.name', 'No Picture')->set('form.descriptions.en', 'x')
            ->call('save')->assertHasErrors(['form.image' => 'required']);
        $this->assertSame(0, ProcessCategory::query()->count());

        $without = ProcessCategory::factory()->create(['image_path' => null]);
        $without->translations()->create(['locale' => 'en', 'description' => 'x']);

        Livewire::actingAs($admin)->test(Editor::class, ['processCategory' => $without])
            ->call('save')->assertHasErrors(['form.image' => 'required']);

        $with = ProcessCategory::factory()->create(['image_path' => 'process-categories/kept.webp']);
        $with->translations()->create(['locale' => 'en', 'description' => 'x']);

        Livewire::actingAs($admin)->test(Editor::class, ['processCategory' => $with])
            ->call('save')->assertHasNoErrors();
        $this->assertSame('process-categories/kept.webp', $with->refresh()->image_path);
    }

    public function test_the_picture_label_is_marked_as_required_only_while_there_is_no_picture(): void
    {
        $admin = User::factory()->admin()->create();
        $with = ProcessCategory::factory()->create(['image_path' => 'process-categories/kept.webp']);

        $marked = function (string $url) use ($admin): bool {
            preg_match('/<label for="form-image".*?<\/label>/s', (string) $this->actingAs($admin)->get($url)->assertOk()->getContent(), $label);

            return str_contains($label[0] ?? '', ' *</span>');
        };

        $this->assertTrue($marked('/app/process-categories/create'));
        $this->assertFalse($marked("/app/process-categories/{$with->id}/edit"));
    }

    public function test_the_picture_must_be_a_large_enough_picture(): void
    {
        Storage::fake('public');
        $component = Livewire::actingAs(User::factory()->admin()->create())->test(Editor::class);

        $component->set('form.image', UploadedFile::fake()->image('small.jpg', 200, 200))->assertHasErrors(['form.image']);
        $component->set('form.image', UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'))->assertHasErrors(['form.image']);
        $component->set('form.image', UploadedFile::fake()->image('big.jpg', 1000, 800)->size(3000))->assertHasErrors(['form.image']);
        $component->set('form.image', UploadedFile::fake()->image('ok.png', 1000, 800))->assertHasNoErrors(['form.image']);
    }

    public function test_a_new_picture_replaces_the_old_file_and_saving_without_one_keeps_it(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        Storage::disk('public')->put('process-categories/old.webp', 'old');
        $category = ProcessCategory::factory()->create(['image_path' => 'process-categories/old.webp']);
        $category->translations()->create(['locale' => 'en', 'description' => 'x']);

        Livewire::actingAs($admin)->test(Editor::class, ['processCategory' => $category])
            ->set('form.name', 'Renamed')->call('save')->assertHasNoErrors();
        $this->assertSame('process-categories/old.webp', $category->refresh()->image_path);
        Storage::disk('public')->assertExists('process-categories/old.webp');

        Livewire::actingAs($admin)->test(Editor::class, ['processCategory' => $category])
            ->set('form.image', UploadedFile::fake()->image('new.jpg', 1000, 800))
            ->call('save')->assertHasNoErrors();

        $category->refresh();
        Storage::disk('public')->assertMissing('process-categories/old.webp');
        Storage::disk('public')->assertExists((string) $category->image_path);
        $this->assertNotSame('process-categories/old.webp', $category->image_path);
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
        $make = function (int $count): void {
            ProcessCategory::factory()->count($count)->create()->each(function (ProcessCategory $category): void {
                $category->translations()->create(['locale' => 'en', 'description' => 'x']);
                Process::factory()->create(['process_category_id' => $category->id]);
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
        $this->assertCount(15, $component->viewData('processCategories')->items());
    }

    public function test_categories_move_up_and_down_in_the_whole_list(): void
    {
        $a = ProcessCategory::factory()->create(['name' => 'A', 'position' => 0]);
        $b = ProcessCategory::factory()->create(['name' => 'B', 'position' => 0]);
        $c = ProcessCategory::factory()->create(['name' => 'C', 'position' => 7]);
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)->test(Index::class)->call('move', $c->id, 'up');
        $this->assertSame([$a->id, $c->id, $b->id], ProcessCategory::query()->orderBy('position')->pluck('id')->all());

        Livewire::actingAs($admin)->test(Index::class)->call('move', $a->id, 'up');
        $this->assertSame(0, $a->refresh()->position);

        Livewire::actingAs($admin)->test(Index::class)->call('move', $a->id, 'down');
        $this->assertSame([$c->id, $a->id, $b->id], ProcessCategory::query()->orderBy('position')->pluck('id')->all());
    }

    public function test_only_admins_can_move_categories(): void
    {
        $category = ProcessCategory::factory()->create(['position' => 0]);

        Livewire::actingAs(User::factory()->staff()->create())->test(Index::class)->assertForbidden();

        $this->assertSame(0, $category->refresh()->position);
    }

    public function test_texts_exist_in_both_dashboard_languages(): void
    {
        $en = require lang_path('en/process_categories.php');
        $id = require lang_path('id/process_categories.php');

        $this->assertSame(array_keys($en), array_keys($id));
        $this->assertSame(array_keys($en['fields']), array_keys($id['fields']));
        $this->assertSame(array_keys($en['hints']), array_keys($id['hints']));
        $this->assertSame(array_keys($en['messages']), array_keys($id['messages']));
    }
}
