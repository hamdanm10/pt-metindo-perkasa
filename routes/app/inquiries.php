<?php

declare(strict_types=1);

use App\Http\Controllers\InquiryImageController;
use App\Livewire\Inquiries\Editor;
use App\Livewire\Inquiries\Index;
use App\Livewire\Inquiries\Show;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,staff'])->group(function (): void {
    Route::prefix('inquiries')->name('inquiries.')->group(function (): void {
        Route::get('/', Index::class)->name('index');
        Route::get('create', Editor::class)->name('create');
        Route::get('{inquiry}', Show::class)->whereNumber('inquiry')->name('show');
        Route::get('{inquiry}/edit', Editor::class)->whereNumber('inquiry')->name('edit');
    });

    Route::get('inquiry-images/{image}', [InquiryImageController::class, 'show'])->whereNumber('image')->name('inquiry-images.show');
});
