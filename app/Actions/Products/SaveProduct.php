<?php

declare(strict_types=1);

namespace App\Actions\Products;

use App\Models\Product;

class SaveProduct
{
    public function execute(?Product $product, string $name, ?string $description): Product
    {
        $product ??= new Product;

        $product->fill([
            'name' => $name,
            'description' => $description === null || $description === '' ? null : $description,
        ])->save();

        return $product;
    }
}
