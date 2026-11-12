<?php

use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\SetDashboardLocale;
use App\Http\Middleware\SetLandingLocale;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware(['web', SetLandingLocale::class])
                ->prefix('{locale}')
                ->where(['locale' => implode('|', config('locales.landing'))])
                ->name('landing.')
                ->group(function (): void {
                    foreach (glob(base_path('routes/landing/*.php')) as $file) {
                        require $file;
                    }
                });

            foreach (['app', 'portal'] as $area) {
                Route::middleware(['web', SetDashboardLocale::class])
                    ->prefix($area)
                    ->name("{$area}.")
                    ->group(function () use ($area): void {
                        foreach (glob(base_path("routes/{$area}/*.php")) as $file) {
                            require $file;
                        }
                    });
            }
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['role' => EnsureRole::class]);

        $middleware->redirectGuestsTo(
            fn (Request $request): string => $request->is('portal', 'portal/*') ? route('portal.login') : route('app.login'),
        );

        $middleware->redirectUsersTo(function (): string {
            $user = Auth::user();

            return route($user instanceof User ? $user->role->homeRoute() : 'app.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
