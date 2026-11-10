<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Services\Landing\LandingContact;
use App\Services\Landing\LandingContent;
use App\Services\Landing\LandingMap;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

class ContactController extends Controller
{
    public function __invoke(LandingContact $contact, LandingContent $content, LandingMap $map): View
    {
        $agents = $contact->agents();
        $details = array_filter(Arr::only(config()->array('landing.contact'), ['address', 'hours', 'phone', 'email']));

        return view('landing.pages.contact', [
            'contact' => $details + ($agents !== [] ? ['whatsapp' => $agents[0]['number']] : []),
            'agents' => $agents,
            'subsidiaries' => $map->pins($content->subsidiaries()),
        ]);
    }
}
