<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\ProcessCategories;

use App\Actions\ProcessCategories\SaveProcessCategory;
use App\Models\ProcessCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveProcessCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_process_category_with_translations_at_the_end(): void
    {
        ProcessCategory::factory()->create(['position' => 3]);

        $category = (new SaveProcessCategory)->execute(null, 'Name', 'name', true, ['en' => 'Text']);

        $this->assertTrue($category->exists);
        $this->assertSame(4, $category->position);
        $this->assertSame(['en' => 'Text'], $category->translations()->pluck('description', 'locale')->all());
    }

    public function test_it_updates_in_place_and_replaces_the_languages(): void
    {
        $action = new SaveProcessCategory;
        $category = $action->execute(null, 'Name', 'name', true, ['en' => 'Text', 'id' => 'Teks']);

        $action->execute($category, 'Changed', 'name', false, ['en' => 'New text']);

        $this->assertSame(1, ProcessCategory::query()->count());
        $category->refresh();
        $this->assertSame('Changed', $category->name);
        $this->assertFalse($category->is_active);
        $this->assertSame(['en' => 'New text'], $category->translations()->pluck('description', 'locale')->all());
    }
}
