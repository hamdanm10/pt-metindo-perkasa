<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('services', ServiceController::class)->name('services');
