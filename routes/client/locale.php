<?php

declare(strict_types=1);

use App\Http\Controllers\Dashboard\LocaleController;
use Illuminate\Support\Facades\Route;

return function (string $area): void {
    Route::post('locale', LocaleController::class)->middleware('throttle:60,1')->name('locale');
};
