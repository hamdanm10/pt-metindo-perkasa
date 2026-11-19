<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\ServiceController;
use Illuminate\Support\Facades\Route;

Route::resource('services', ServiceController::class)->only(['index', 'show']);
