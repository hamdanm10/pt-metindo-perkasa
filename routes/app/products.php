<?php

declare(strict_types=1);

use App\Livewire\Products\Editor;
use App\Livewire\Products\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('products')->name('products.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('create', Editor::class)->name('create');
    Route::get('{product}/edit', Editor::class)->name('edit');
});
