<?php

declare(strict_types=1);

use App\Livewire\Chat\Editor;
use App\Livewire\Chat\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('chat')->name('chat.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('create', Editor::class)->name('create');
    Route::get('{entry}/edit', Editor::class)->whereNumber('entry')->name('edit');
});
