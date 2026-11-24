<?php

declare(strict_types=1);

namespace App\Actions\Purchases;

use App\Models\Purchase;
use App\Services\Purchases\PurchaseFileStore;

class DeletePurchase
{
    public function __construct(private readonly PurchaseFileStore $files) {}

    public function execute(Purchase $purchase): void
    {
        /** @var list<string> $paths */
        $paths = $purchase->files()->pluck('path')->all();

        $purchase->delete();

        $this->files->delete($paths);
    }
}
