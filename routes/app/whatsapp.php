<?php

declare(strict_types=1);

use App\Livewire\Whatsapp\Editor;
use App\Livewire\Whatsapp\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('whatsapp')->name('whatsapp.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('create', Editor::class)->name('create');
    Route::get('{agent}/edit', Editor::class)->whereNumber('agent')->name('edit');
});
