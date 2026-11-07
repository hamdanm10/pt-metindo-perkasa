<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ServiceController extends Controller
{
    public function __invoke(): View
    {
        return view('landing.pages.services');
    }
}
