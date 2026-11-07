<?php

declare(strict_types=1);

use App\Http\Controllers\Landing\ContactController;
use Illuminate\Support\Facades\Route;

Route::get('contact', ContactController::class)->name('contact');
