<?php

declare(strict_types=1);

namespace App\Actions\Services;

use App\Models\Service;
use App\Services\Landing\LandingServices;
use App\Services\Services\ServiceImageStore;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Saves a service with all its language tabs. A language missing from `$translations` is removed.
 *
 * @phpstan-type TranslationInput array{title: string, summary: string, description: string, included: list<string>, fit: string}
 */
class SaveService
{
    public function __construct(
        private readonly ServiceImageStore $images,
        private readonly LandingServices $landing,
    ) {}

    /**
     * @param  array<string, TranslationInput>  $translations  keyed by locale
     */
    public function execute(
        ?Service $service,
        string $name,
        string $slug,
        int $position,
        bool $active,
        array $translations,
        ?UploadedFile $image = null,
    ): Service {
        $newPath = $image === null ? null : $this->images->store($image);
        $oldPath = $service?->image_path;

        $saved = DB::transaction(function () use ($service, $name, $slug, $position, $active, $translations, $newPath): Service {
            $service ??= new Service;

            $service->fill(['name' => $name, 'slug' => $slug, 'position' => $position, 'is_active' => $active]);

            if ($newPath !== null) {
                $service->image_path = $newPath;
            }

            $service->save();

            $service->translations()->whereNotIn('locale', array_keys($translations))->delete();

            foreach ($translations as $locale => $input) {
                $service->translations()->updateOrCreate(['locale' => $locale], [
                    'title' => $input['title'],
                    'summary' => $input['summary'] !== '' ? $input['summary'] : null,
                    'description' => $input['description'] !== '' ? $input['description'] : null,
                    'included' => $input['included'] !== [] ? $input['included'] : null,
                    'fit' => $input['fit'] !== '' ? $input['fit'] : null,
                ]);
            }

            return $service;
        });

        if ($newPath !== null) {
            $this->images->delete($oldPath);
        }

        $this->landing->flush();

        return $saved;
    }
}
