<?php

declare(strict_types=1);

namespace App\Http\Controllers\Portal;

use App\Actions\Dashboard\BuildHomeSummary;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request, BuildHomeSummary $summary): View
    {
        return view('dashboard.home', $summary->execute($request->user() ?? abort(403)));
    }
}
