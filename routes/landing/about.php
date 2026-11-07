<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\AboutController;
use Illuminate\Support\Facades\Route;

Route::get('about', AboutController::class)->name('about');
