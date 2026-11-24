<?php

declare(strict_types=1);

use App\Http\Controllers\PurchaseFileController;
use App\Livewire\Purchases\Editor;
use App\Livewire\Purchases\Index;
use App\Livewire\Purchases\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,staff'])->group(function (): void {
    // Customers' and distributors' purchases are on separate pages that share the same components; the `kind` default picks the buyer role.
    foreach (['customer', 'distributor'] as $kind) {
        Route::prefix("{$kind}-purchases")->name("{$kind}-purchases.")->group(function () use ($kind): void {
            Route::get('/', Index::class)->name('index')->defaults('kind', $kind);
            Route::get('create', Editor::class)->name('create')->defaults('kind', $kind);
            Route::get('{purchase}', Show::class)->whereNumber('purchase')->name('show')->defaults('kind', $kind);
            Route::get('{purchase}/edit', Editor::class)->whereNumber('purchase')->name('edit')->defaults('kind', $kind);
        });
    }

    Route::get('purchase-files/{file}', [PurchaseFileController::class, 'show'])->whereNumber('file')->name('purchase-files.show');
});
