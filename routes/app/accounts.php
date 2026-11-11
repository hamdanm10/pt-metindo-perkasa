<?php

declare(strict_types=1);

use App\Livewire\Users\Create;
use App\Livewire\Users\Edit;
use App\Livewire\Users\Index;
use Illuminate\Support\Facades\Route;

// Staff and customers share the same pages; the `kind` default picks the role. Admins are not listed here:
// there is only one admin and it is created with `php artisan admin:create`.
Route::middleware(['auth', 'role:admin'])->group(function (): void {
    foreach (['staff' => 'staff', 'customers' => 'customer'] as $path => $kind) {
        Route::prefix($path)->name($path.'.')->group(function () use ($kind): void {
            Route::get('/', Index::class)->name('index')->defaults('kind', $kind);
            Route::get('create', Create::class)->name('create')->defaults('kind', $kind);
            Route::get('{user}/edit', Edit::class)->name('edit')->defaults('kind', $kind);
        });
    }
});
