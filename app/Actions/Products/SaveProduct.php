<?php

declare(strict_types=1);

namespace App\Actions\Products;

use App\Models\Product;
use App\Services\Products\ProductTdsStore;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

/**
 * Saves a product with its shop links and language tabs. A language missing from `$descriptions` is removed,
 * and the links are replaced by the given list, in order. A new TDS replaces the old file, and `$removeTds`
 * deletes it without a replacement.
 *
 * @phpstan-type ProductInput array{brochure_url: string|null, video_url: string|null, price: int|null, is_active: bool, links: list<array{label: string, url: string}>, descriptions: array<string, string>}
 */
class SaveProduct
{
    public function __construct(private readonly ProductTdsStore $tds) {}

    /**
     * @param  ProductInput  $data
     */
    public function execute(?Product $product, string $name, string $slug, array $data, ?UploadedFile $tds = null, bool $removeTds = false): Product
    {
        $newPath = $tds === null ? null : $this->tds->store($tds);
        $oldPath = $product?->tds_path;

        $saved = DB::transaction(function () use ($product, $name, $slug, $data, $newPath, $removeTds): Product {
            $product ??= new Product;

            $product->fill([
                'name' => $name,
                'slug' => $slug,
                'brochure_url' => $data['brochure_url'],
                'video_url' => $data['video_url'],
                'price' => $data['price'],
                'is_active' => $data['is_active'],
            ]);

            if ($newPath !== null) {
                $product->tds_path = $newPath;
            } elseif ($removeTds) {
                $product->tds_path = null;
            }

            $product->save();

            $product->translations()->whereNotIn('locale', array_keys($data['descriptions']))->delete();

            foreach ($data['descriptions'] as $locale => $description) {
                $product->translations()->updateOrCreate(['locale' => $locale], ['description' => $description]);
            }

            $product->links()->delete();

            foreach ($data['links'] as $position => $link) {
                $product->links()->create(['label' => $link['label'], 'url' => $link['url'], 'position' => $position]);
            }

            return $product;
        });

        if ($newPath !== null || $removeTds) {
            $this->tds->delete($oldPath);
        }

        return $saved;
    }
}
