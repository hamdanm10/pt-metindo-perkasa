<?php

declare(strict_types=1);

use App\Livewire\Services\Editor;
use App\Livewire\Services\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('services')->name('services.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('create', Editor::class)->name('create');
    Route::get('{service}/edit', Editor::class)->name('edit');
});
