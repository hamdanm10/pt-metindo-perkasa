<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\ProcessController;
use Illuminate\Support\Facades\Route;

Route::resource('processes', ProcessController::class)
    ->only(['index', 'show'])
    ->parameters(['processes' => 'category'])
    ->where(['category' => '[a-z0-9]+(?:-[a-z0-9]+)*']);
