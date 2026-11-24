<?php

declare(strict_types=1);

namespace App\Actions\Processes;

use App\Models\Process;
use App\Services\Landing\LandingProcesses;
use Illuminate\Support\Facades\DB;

/**
 * Saves a process with its language tabs and its steps. A new process goes to the end of its category, and so does
 * one moved to another category. The steps are replaced by the given list, in order. A language missing from
 * `$descriptions` is removed from the process, and an empty step description is not stored.
 *
 * @phpstan-type StepInput array{name: string, descriptions: array<string, string>, products: list<int>, animation?: string|null}
 */
class SaveProcess
{
    public function __construct(private readonly LandingProcesses $landing) {}

    /**
     * @param  array<string, string>  $descriptions  keyed by locale
     * @param  list<StepInput>  $steps
     */
    public function execute(?Process $process, string $name, string $slug, int $categoryId, bool $active, array $descriptions, array $steps): Process
    {
        $saved = DB::transaction(function () use ($process, $name, $slug, $categoryId, $active, $descriptions, $steps): Process {
            $process ??= new Process;
            $moved = ! $process->exists || $process->process_category_id !== $categoryId;

            $process->fill(['name' => $name, 'slug' => $slug, 'process_category_id' => $categoryId, 'is_active' => $active]);

            if ($moved) {
                $process->fill(['position' => Process::query()->where('process_category_id', $categoryId)->exists()
                    ? (int) Process::query()->where('process_category_id', $categoryId)->max('position') + 1
                    : 0]);
            }

            $process->save();

            $process->translations()->whereNotIn('locale', array_keys($descriptions))->delete();

            foreach ($descriptions as $locale => $description) {
                $process->translations()->updateOrCreate(['locale' => $locale], ['description' => $description]);
            }

            $process->steps()->delete();

            foreach ($steps as $position => $input) {
                $step = $process->steps()->create(['name' => $input['name'], 'animation' => $input['animation'] ?? null, 'position' => $position]);

                foreach ($input['descriptions'] as $locale => $description) {
                    if ($description !== '') {
                        $step->translations()->create(['locale' => $locale, 'description' => $description]);
                    }
                }

                foreach ($input['products'] as $order => $productId) {
                    $step->products()->attach($productId, ['position' => $order]);
                }
            }

            return $process;
        });

        $this->landing->flush();

        return $saved;
    }
}
