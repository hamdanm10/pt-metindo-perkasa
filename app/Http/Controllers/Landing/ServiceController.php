<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\Landing\LandingContact;
use App\Services\Landing\LandingServices;
use Illuminate\Contracts\View\View;

class ServiceController extends Controller
{
    public function index(LandingServices $landing): View
    {
        $services = $landing->all(app()->getLocale());

        abort_if($services === [], 404);

        return view('landing.pages.services.index', ['services' => $services]);
    }

    public function show(LandingServices $landing, LandingContact $contact, string $service): View
    {
        $services = $landing->all(app()->getLocale());
        $current = $landing->find(app()->getLocale(), $service);

        abort_if($current === null, 404);

        $others = array_values(array_filter(
            $services,
            fn (array $item): bool => $item['slug'] !== $current['slug'],
        ));

        $index = array_search($current['slug'], array_column($services, 'slug'), true);

        return view('landing.pages.services.show', [
            'service' => $current,
            'tone' => (int) $index,
            'others' => $others,
            'agents' => $contact->agents(),
        ]);
    }
}
