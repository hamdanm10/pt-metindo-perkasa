<?php

declare(strict_types=1);

use App\Http\Controllers\InquiryImageController;
use App\Livewire\Inquiries\Editor;
use App\Livewire\Inquiries\Index;
use App\Livewire\Inquiries\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,staff'])->group(function (): void {
    // Customers' and distributors' records are on separate pages that share the same components; the `kind` default picks the owner role.
    foreach (['customer', 'distributor'] as $kind) {
        Route::prefix("{$kind}-inquiries")->name("{$kind}-inquiries.")->group(function () use ($kind): void {
            Route::get('/', Index::class)->name('index')->defaults('kind', $kind);
            Route::get('create', Editor::class)->name('create')->defaults('kind', $kind);
            Route::get('{inquiry}', Show::class)->whereNumber('inquiry')->name('show')->defaults('kind', $kind);
            Route::get('{inquiry}/edit', Editor::class)->whereNumber('inquiry')->name('edit')->defaults('kind', $kind);
        });
    }

    Route::get('inquiry-images/{image}', [InquiryImageController::class, 'show'])->whereNumber('image')->name('inquiry-images.show');
});
