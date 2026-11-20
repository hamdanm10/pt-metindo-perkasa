<?php

declare(strict_types=1);

use App\Livewire\ProcessCategories\Editor;
use App\Livewire\ProcessCategories\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('process-categories')->name('process-categories.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('create', Editor::class)->name('create');
    Route::get('{processCategory}/edit', Editor::class)->name('edit');
});
