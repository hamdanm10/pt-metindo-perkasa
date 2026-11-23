<?php

declare(strict_types=1);

namespace Tests\Concerns;

use App\Models\Process;
use App\Models\ProcessCategory;
use App\Models\Product;
use App\Services\Landing\LandingProcesses;

trait CreatesCatalog
{
    /**
     * Five active categories as the admin would have set them. "Rack Zinc Plating" has four steps and eight
     * products on the first one, the other processes have no steps.
     *
     * @return list<array{slug: string, name: string, processes: list<string>}>
     */
    protected function createCatalog(): array
    {
        $catalog = [
            ['zinc-plating', 'Zinc Plating Chemicals', ['Rack Zinc Plating', 'Barrel Zinc Plating', 'Zinc-Nickel Alloy', 'Clear Passivation']],
            ['nickel-plating', 'Nickel Plating Chemicals', ['Bright Nickel', 'Satin Nickel']],
            ['copper-plating', 'Copper Plating Chemicals', ['Acid Copper Bath']],
            ['hard-chrome', 'Hard Chrome Chemicals', ['Hard Chrome Plating', 'Decorative Chrome']],
            ['tin-plating', 'Tin Plating Chemicals', ['Bright Tin']],
        ];
        $result = [];

        foreach ($catalog as $position => [$slug, $name, $processes]) {
            $category = ProcessCategory::factory()->create(['name' => $name, 'slug' => $slug, 'position' => $position]);
            $category->translations()->create(['locale' => 'en', 'description' => "{$name} description."]);

            foreach ($processes as $index => $processName) {
                $process = Process::factory()->create([
                    'process_category_id' => $category->id,
                    'name' => $processName,
                    'slug' => str($processName)->slug()->toString(),
                    'position' => $index,
                ]);
                $process->translations()->create(['locale' => 'en', 'description' => "{$processName} process flow."]);

                if ($processName === 'Rack Zinc Plating') {
                    $this->createSteps($process);
                }
            }

            $result[] = ['slug' => $slug, 'name' => $name, 'processes' => $processes];
        }

        app(LandingProcesses::class)->flush();

        return $result;
    }

    private function createSteps(Process $process): void
    {
        foreach (['Degreasing', 'Rinsing', 'Activation', 'Plating'] as $position => $name) {
            $step = $process->steps()->create(['name' => $name, 'position' => $position]);

            if ($position === 0) {
                foreach (range(1, 8) as $number) {
                    $step->products()->attach(Product::factory()->create(['name' => "Epclen {$number}", 'slug' => "epclen-{$number}"])->id, ['position' => $number]);
                }
            }
        }
    }
}
