<?php

declare(strict_types=1);

namespace App\Actions\Products;

use App\Models\Product;
use App\Services\Landing\LandingProcesses;
use App\Services\Products\ProductTdsStore;

/**
 * Deletes a product with its translations, shop links, TDS file, and its place in process steps
 * (the database cascades).
 */
class DeleteProduct
{
    public function __construct(
        private readonly ProductTdsStore $tds,
        private readonly LandingProcesses $landing,
    ) {}

    public function execute(Product $product): void
    {
        $path = $product->tds_path;

        $product->delete();

        $this->tds->delete($path);
        $this->landing->flush();
    }
}
