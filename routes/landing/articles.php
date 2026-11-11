<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\ArticleController;
use App\Http\Controllers\Landing\ArticleViewController;
use Illuminate\Support\Facades\Route;

Route::resource('articles', ArticleController::class)->only(['index', 'show']);

Route::resource('articles.views', ArticleViewController::class)
    ->only(['store'])
    ->middleware('throttle:30,1');
