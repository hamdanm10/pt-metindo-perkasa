<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\ProductController;
use Illuminate\Support\Facades\Route;

Route::resource('products', ProductController::class)
    ->only(['index', 'show'])
    ->parameters(['products' => 'category'])
    ->where(['category' => implode('|', array_column(config('landing.categories'), 'slug'))]);
