<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Processes;

use App\Actions\Processes\SaveProcess;
use App\Models\Process;
use App\Models\ProcessCategory;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaveProcessTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_saves_steps_in_order_with_products_and_skips_empty_step_texts(): void
    {
        $category = ProcessCategory::factory()->create();
        $first = Product::factory()->create();
        $second = Product::factory()->create();

        $process = (new SaveProcess)->execute(null, 'Flow', 'flow', $category->id, true, ['en' => 'Text'], [
            ['name' => 'One', 'descriptions' => ['en' => 'First', 'id' => ''], 'products' => [$second->id, $first->id]],
            ['name' => 'Two', 'descriptions' => ['en' => ''], 'products' => []],
        ]);

        $process->load(['steps.translations', 'steps.products']);
        $this->assertSame(['One', 'Two'], $process->steps->pluck('name')->all());
        $one = $process->steps->sole(fn ($step): bool => $step->name === 'One');
        $two = $process->steps->sole(fn ($step): bool => $step->name === 'Two');
        $this->assertSame(['en'], $one->translations->pluck('locale')->all());
        $this->assertSame([$second->id, $first->id], $one->products->pluck('id')->all());
        $this->assertCount(0, $two->translations);
    }

    public function test_it_updates_in_place_and_replaces_steps_and_languages(): void
    {
        $category = ProcessCategory::factory()->create();
        $action = new SaveProcess;
        $process = $action->execute(null, 'Flow', 'flow', $category->id, true, ['en' => 'Text', 'id' => 'Teks'], [
            ['name' => 'One', 'descriptions' => [], 'products' => []],
        ]);

        $action->execute($process, 'Flow 2', 'flow', $category->id, true, ['en' => 'Text'], [
            ['name' => 'Two', 'descriptions' => [], 'products' => []],
            ['name' => 'Three', 'descriptions' => [], 'products' => []],
        ]);

        $this->assertSame(1, Process::query()->count());
        $this->assertSame(['Two', 'Three'], $process->steps()->pluck('name')->all());
        $this->assertSame(['en'], $process->translations()->pluck('locale')->all());
    }
}
