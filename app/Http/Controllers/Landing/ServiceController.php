<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\Landing\LandingContact;
use App\Services\Landing\LandingContent;
use Illuminate\Contracts\View\View;

class ServiceController extends Controller
{
    public function index(LandingContent $content): View
    {
        return view('landing.pages.services.index', ['services' => $content->services()]);
    }

    public function show(LandingContent $content, LandingContact $contact, string $service): View
    {
        $current = $content->service($service);

        abort_if($current === null, 404);

        $others = array_values(array_filter(
            $content->services(),
            fn (array $item): bool => $item['slug'] !== $current['slug'],
        ));

        $agents = $contact->agents();

        return view('landing.pages.services.show', [
            'service' => $current,
            'others' => $others,
            'whatsapp' => $agents !== [] ? $contact->url($agents[0]['number']) : null,
        ]);
    }
}
