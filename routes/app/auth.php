<?php

declare(strict_types=1);

use App\Http\Controllers\App\Auth\LoginController;
use App\Http\Controllers\App\HomeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:30,1');
});

Route::middleware(['auth', 'role:admin,staff'])->group(function (): void {
    Route::redirect('/', '/app/dashboard')->name('home');
    Route::get('dashboard', HomeController::class)->name('dashboard');
    Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
});
