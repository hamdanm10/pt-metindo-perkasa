<?php

declare(strict_types=1);

use App\Http\Controllers\PurchaseFileController;
use App\Livewire\Purchases\Index;
use App\Livewire\Purchases\Show;
use Illuminate\Support\Facades\Route;

/**
 * The read-only purchase pages of an account, defined once for the customer and the distributor area.
 * Each area has its own copy of the routes (`customer.purchases.*`, `distributor.purchases.*`).
 */
return function (string $area): void {
    Route::middleware(['auth', "role:{$area}"])->group(function (): void {
        Route::get('purchases', Index::class)->name('purchases.index');
        Route::get('purchases/{purchase}', Show::class)->whereNumber('purchase')->name('purchases.show');
        Route::get('purchase-files/{file}', [PurchaseFileController::class, 'show'])->whereNumber('file')->name('purchase-files.show');
    });
};
