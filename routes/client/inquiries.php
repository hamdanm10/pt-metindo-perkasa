<?php

declare(strict_types=1);

use App\Http\Controllers\InquiryImageController;
use App\Livewire\Inquiries\Index;
use App\Livewire\Inquiries\Show;
use Illuminate\Support\Facades\Route;

/**
 * The read-only question pages of an account, defined once for the customer and the distributor area.
 * Each area has its own copy of the routes (`customer.inquiries.*`, `distributor.inquiries.*`).
 */
return function (string $area): void {
    Route::middleware(['auth', "role:{$area}"])->group(function (): void {
        Route::get('inquiries', Index::class)->name('inquiries.index');
        Route::get('inquiries/{inquiry}', Show::class)->whereNumber('inquiry')->name('inquiries.show');
        Route::get('inquiry-images/{image}', [InquiryImageController::class, 'show'])->whereNumber('image')->name('inquiry-images.show');
    });
};
