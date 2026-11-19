<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\ProcessDetailController;
use Illuminate\Support\Facades\Route;

Route::resource('process', ProcessDetailController::class)
    ->only(['show'])
    ->parameters(['process' => 'process']);
