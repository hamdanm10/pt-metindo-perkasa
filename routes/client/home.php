<?php

declare(strict_types=1);

use App\Http\Controllers\Portal\Auth\LoginController;
use App\Http\Controllers\Portal\HomeController;
use Illuminate\Support\Facades\Route;

/**
 * Home and sign out of the customer and the distributor area. Signing in is the shared page `portal.login`.
 */
return function (string $area): void {
    Route::middleware(['auth', "role:{$area}"])->group(function () use ($area): void {
        Route::redirect('/', "/{$area}/dashboard")->name('home');
        Route::get('dashboard', HomeController::class)->name('dashboard');
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    });
};
