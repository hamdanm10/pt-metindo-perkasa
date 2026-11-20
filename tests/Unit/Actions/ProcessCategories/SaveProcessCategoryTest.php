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

    public function test_it_creates_a_process_category(): void
    {
        $processCategory = (new SaveProcessCategory)->execute(null, 'Name', 'Text');

        $this->assertTrue($processCategory->exists);
        $this->assertSame('Text', $processCategory->description);
    }

    public function test_it_stores_an_empty_description_as_null(): void
    {
        $this->assertNull((new SaveProcessCategory)->execute(null, 'Name', '')->description);
        $this->assertNull((new SaveProcessCategory)->execute(null, 'Name', null)->description);
    }

    public function test_it_updates_the_given_process_category_in_place(): void
    {
        $processCategory = ProcessCategory::factory()->create();

        (new SaveProcessCategory)->execute($processCategory, 'Changed', 'New text');

        $this->assertSame(1, ProcessCategory::query()->count());
        $this->assertSame('Changed', $processCategory->fresh()?->name);
    }
}
