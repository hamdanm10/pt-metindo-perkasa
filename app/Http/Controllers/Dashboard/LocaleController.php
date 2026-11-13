<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\Users\UpdateLocale;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\UpdateLocaleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function __invoke(UpdateLocaleRequest $request, UpdateLocale $updateLocale): RedirectResponse
    {
        $locale = $request->string('locale')->toString();
        $user = $request->user();

        $updateLocale->execute($user instanceof User ? $user : null, $locale);
        $request->session()->put('locale', $locale);

        $previous = url()->previous();
        $sameSite = parse_url($previous, PHP_URL_HOST) === $request->getHost();

        if ($sameSite) {
            return redirect()->to($previous);
        }

        $staffArea = $request->is('app', 'app/*');

        return redirect()->route($user instanceof User ? $user->role->homeRoute() : ($staffArea ? 'app.login' : 'portal.login'));
    }
}
