<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Middleware\EnsureRole;
use App\Http\Middleware\SetDashboardLocale;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Livewire's own update requests skip the page route, so these must run again on them.
        Livewire::addPersistentMiddleware([EnsureRole::class, SetDashboardLocale::class]);

        // Only a request that searches is limited: a search scans the titles of one language, the plain list does not.
        RateLimiter::for('article-search', fn (Request $request): Limit => $request->query('q') === null
            ? Limit::none()
            : Limit::perMinute(60)->by((string) $request->ip()));

        DB::prohibitDestructiveCommands(
            ! $this->app->runningUnitTests() && ! config('database.allow_destructive_commands'),
        );
    }
}
