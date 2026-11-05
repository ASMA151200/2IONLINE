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

        // Limites des routes d'authentification publiques.
        // PROBLÈME CORRIGÉ : connexion, inscription et mot de passe oublié sont
        // appelés par le serveur Next.js (Vercel), donc Laravel voit toujours la
        // MÊME adresse IP — celle de Vercel — pour tous les visiteurs. Avec
        // "throttle:10,1" (clé = IP), 10 connexions par minute au TOTAL, tous
        // utilisateurs confondus, suffisaient à bloquer tout le monde (429).
        // On limite donc PAR ADRESSE E-MAIL : un mot de passe ne peut pas être
        // deviné en masse sur un compte, sans pénaliser les autres. Une limite
        // globale plus large reste en place contre les inondations.
        $emailKey = fn (Request $r, string $name) => $name . '|' . strtolower(trim((string) $r->input('email')));

        RateLimiter::for('auth-login', fn (Request $r) => [
            Limit::perMinute(8)->by($emailKey($r, 'login')),
            Limit::perMinute(300)->by('login-global'),
        ]);

        RateLimiter::for('auth-register', fn (Request $r) => [
            Limit::perMinute(5)->by($emailKey($r, 'register')),
            Limit::perMinute(60)->by('register-global'),
        ]);

        RateLimiter::for('auth-forgot', fn (Request $r) => [
            Limit::perMinute(3)->by($emailKey($r, 'forgot')),
            Limit::perMinute(60)->by('forgot-global'),
        ]);

        RateLimiter::for('auth-reset', fn (Request $r) => [
            Limit::perMinute(5)->by($emailKey($r, 'reset')),
            Limit::perMinute(60)->by('reset-global'),
        ]);
    }
}
