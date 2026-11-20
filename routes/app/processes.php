<?php

declare(strict_types=1);

use App\Livewire\Processes\Editor;
use App\Livewire\Processes\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('processes')->name('processes.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('create', Editor::class)->name('create');
    Route::get('{process}/edit', Editor::class)->name('edit');
});
