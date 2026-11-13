<?php

declare(strict_types=1);

use App\Http\Controllers\Portal\Auth\LoginController;
use Illuminate\Support\Facades\Route;

/**
 * The one sign-in page shared by customers and distributors. After signing in each goes to their own area.
 */
Route::redirect('/', '/portal/login')->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:30,1');
});
