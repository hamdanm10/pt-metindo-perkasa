<?php

declare(strict_types=1);

namespace App\Actions\Materials;

use App\Models\Material;
use App\Services\Materials\MaterialTdsStore;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Saves a material with its shop links and language tabs. A language missing from `$descriptions` is removed,
 * and the links are replaced by the given list, in order. A new TDS replaces the old file, and `$removeTds`
 * deletes it without a replacement.
 *
 * @phpstan-type MaterialInput array{brochure_url: string|null, video_url: string|null, price: int|null, is_active: bool, links: list<array{label: string, url: string}>, descriptions: array<string, string>}
 */
class SaveMaterial
{
    public function __construct(private readonly MaterialTdsStore $tds) {}

    /**
     * @param  MaterialInput  $data
     */
    public function execute(?Material $material, string $name, string $slug, array $data, ?UploadedFile $tds = null, bool $removeTds = false): Material
    {
        $newPath = $tds === null ? null : $this->tds->store($tds);
        $oldPath = $material?->tds_path;

        $saved = DB::transaction(function () use ($material, $name, $slug, $data, $newPath, $removeTds): Material {
            $material ??= new Material;

            $material->fill([
                'name' => $name,
                'slug' => $slug,
                'brochure_url' => $data['brochure_url'],
                'video_url' => $data['video_url'],
                'price' => $data['price'],
                'is_active' => $data['is_active'],
            ]);

            if ($newPath !== null) {
                $material->tds_path = $newPath;
            } elseif ($removeTds) {
                $material->tds_path = null;
            }

            $material->save();

            $material->translations()->whereNotIn('locale', array_keys($data['descriptions']))->delete();

            foreach ($data['descriptions'] as $locale => $description) {
                $material->translations()->updateOrCreate(['locale' => $locale], ['description' => $description]);
            }

            $material->links()->delete();

            foreach ($data['links'] as $position => $link) {
                $material->links()->create(['label' => $link['label'], 'url' => $link['url'], 'position' => $position]);
            }

            return $material;
        });

        if ($newPath !== null || $removeTds) {
            $this->tds->delete($oldPath);
        }

        return $saved;
    }
}
