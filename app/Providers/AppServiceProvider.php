<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

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
        Schema::defaultStringLength(191);

        // Rate limiting global de l'API — aucune limite n'existait
        // jusqu'ici (Laravel 11 ne l'active pas par défaut avec la
        // nouvelle structure bootstrap/app.php). 120 requêtes/minute par
        // utilisateur connecté (ou par IP si non connecté) : généreux
        // pour un usage normal, mais bloque un script qui bombarderait
        // l'API.
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by($request->user()?->id ?: $request->ip());
        });
    }
}
