<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\Landing\LandingContent;
use Illuminate\Contracts\View\View;

class ProcessDetailController extends Controller
{
    public function show(LandingContent $content, string $process): View
    {
        $current = $content->process($process);

        abort_if($current === null, 404);

        return view('landing.pages.processes.detail', ['process' => $current]);
    }
}
