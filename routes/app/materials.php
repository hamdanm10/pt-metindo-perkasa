<?php

declare(strict_types=1);

use App\Livewire\Materials\Editor;
use App\Livewire\Materials\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('materials')->name('materials.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('create', Editor::class)->name('create');
    Route::get('{material}/edit', Editor::class)->name('edit');
});
