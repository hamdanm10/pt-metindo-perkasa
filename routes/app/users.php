<?php

declare(strict_types=1);

use App\Livewire\Users\Create;
use App\Livewire\Users\Edit;
use App\Livewire\Users\Index;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('users')->name('users.')->group(function (): void {
    Route::get('/', Index::class)->name('index');
    Route::get('create', Create::class)->name('create');
    Route::get('{user}/edit', Edit::class)->name('edit');
});
