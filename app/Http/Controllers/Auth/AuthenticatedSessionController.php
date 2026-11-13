<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthenticateUser;
use App\Actions\Users\UpdateLocale;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class AuthenticatedSessionController extends Controller
{
    /**
     * @return list<Role>
     */
    abstract protected function roles(): array;

    /** Route name of the login page of this area. */
    abstract protected function loginRoute(): string;

    abstract protected function titleKey(): string;

    public function create(): View
    {
        return view('auth.login', [
            'title' => __($this->titleKey()),
            'action' => route($this->loginRoute()),
            'localeAction' => route(explode('.', $this->loginRoute())[0].'.locale'),
        ]);
    }

    public function store(LoginRequest $request, AuthenticateUser $authenticate, UpdateLocale $updateLocale): RedirectResponse
    {
        $authenticate->execute(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
            $request->boolean('remember'),
            $this->roles(),
            (string) $request->ip(),
        );

        $request->session()->regenerate();

        $this->keepLanguageChosenBeforeSignIn($request, $updateLocale);

        return redirect()->to($this->destination($request, $this->homeUrl($request)));
    }

    /**
     * A language picked on the login page is the latest explicit choice, so it becomes
     * the account's language instead of being overridden by the stored one.
     */
    private function keepLanguageChosenBeforeSignIn(Request $request, UpdateLocale $updateLocale): void
    {
        $locale = $request->session()->get('locale');
        $user = $request->user();

        if ($user instanceof User && is_string($locale) && in_array($locale, config()->array('locales.dashboard'), true)) {
            $updateLocale->execute($user, $locale);
        }
    }

    /**
     * Where to go after signing in: the page the user was sent away from, but only when
     * it belongs to this area. A page of another area would just answer 403.
     */
    private function destination(Request $request, string $home): string
    {
        $intended = $request->session()->pull('url.intended');

        if (! is_string($intended)) {
            return $home;
        }

        $prefix = dirname((string) parse_url($home, PHP_URL_PATH));
        $sameHost = parse_url($intended, PHP_URL_HOST) === parse_url($home, PHP_URL_HOST);
        $path = (string) parse_url($intended, PHP_URL_PATH);

        return $sameHost && str_starts_with($path, $prefix.'/') ? $intended : $home;
    }

    /**
     * The home page of the signed-in account's own area.
     */
    private function homeUrl(Request $request): string
    {
        $user = $request->user();

        return route($user instanceof User ? $user->role->homeRoute() : $this->loginRoute());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($this->loginRoute());
    }
}
