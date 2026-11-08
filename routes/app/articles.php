<?php

declare(strict_types=1);

use App\Livewire\Articles\Editor;
use App\Livewire\Articles\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('articles')->name('articles.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('create', Editor::class)->name('create');
    Route::get('{article}/edit', Editor::class)->name('edit');
});
