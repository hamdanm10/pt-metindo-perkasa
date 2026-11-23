<?php

declare(strict_types=1);

namespace Tests\Feature\ProcessCategories;

use App\Livewire\ProcessCategories\Index;
use App\Models\Process;
use App\Models\ProcessCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProcessCategoryDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_deletes_a_process_category_and_the_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('process-categories/a.webp', 'x');
        $processCategory = ProcessCategory::factory()->create(['image_path' => 'process-categories/a.webp']);

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(Index::class)
            ->call('delete', $processCategory->id)
            ->assertSee(__('process_categories.messages.deleted'));

        $this->assertDatabaseMissing('process_categories', ['id' => $processCategory->id]);
        Storage::disk('public')->assertMissing('process-categories/a.webp');
    }

    public function test_a_category_with_processes_is_kept_with_a_message(): void
    {
        $category = ProcessCategory::factory()->create();
        Process::factory()->create(['process_category_id' => $category->id]);

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(Index::class)
            ->call('delete', $category->id)
            ->assertSee(__('process_categories.messages.has_processes'));

        $this->assertDatabaseHas('process_categories', ['id' => $category->id]);
    }

    public function test_staff_cannot_delete_a_process_category(): void
    {
        $processCategory = ProcessCategory::factory()->create();

        Livewire::actingAs(User::factory()->staff()->create())->test(Index::class)->assertForbidden();
        $this->assertTrue(Gate::forUser(User::factory()->staff()->create())->denies('delete', $processCategory));

        $this->assertDatabaseHas('process_categories', ['id' => $processCategory->id]);
    }

    public function test_the_list_shows_a_delete_button_per_row_without_extra_queries(): void
    {
        $admin = User::factory()->admin()->create();
        ProcessCategory::factory()->count(2)->create();

        $count = function () use ($admin): int {
            DB::flushQueryLog();
            DB::enableQueryLog();
            $html = (string) $this->actingAs($admin)->get('/app/process-categories')->getContent();
            $this->assertSame(substr_count($html, 'wire:key="process-category-'), substr_count($html, 'wire:click="delete('));

            return count(DB::getQueryLog());
        };

        $before = $count();
        ProcessCategory::factory()->count(3)->create();

        $this->assertSame($before, $count());
    }
}
