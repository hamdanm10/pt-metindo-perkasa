<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\ArticleController;
use Illuminate\Support\Facades\Route;

Route::resource('articles', ArticleController::class)->only(['index', 'show']);
