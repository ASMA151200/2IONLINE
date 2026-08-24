<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Sanctum
        $middleware->statefulApi();

        // Middleware personnalisé
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Force une réponse JSON propre pour TOUTE requête vers /api/*,
        // même si l'en-tête "Accept" envoyé par le client n'est pas
        // exactement "application/json" — sans ça, Laravel peut renvoyer
        // sa page d'erreur HTML par défaut ("An error occurred..."),
        // que le frontend tente ensuite de parser comme du JSON et
        // plante avec "Unexpected token 'A', 'An error o'... is not
        // valid JSON".
        $exceptions->shouldRenderJsonWhen(function ($request, \Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (\Throwable $e, $request) {
            if (!$request->is('api/*') && !$request->expectsJson()) {
                return null; // laisse Laravel gérer normalement (pages web)
            }

            $status = 500;
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $status = 422;
            } elseif ($e instanceof \Illuminate\Auth\AuthenticationException) {
                $status = 401;
            } elseif ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                $status = 403;
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
                || $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                $status = 404;
            } elseif ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $status = $e->getStatusCode();
            }

            return response()->json([
                'success' => false,
                'message' => $status === 500 && !config('app.debug')
                    ? 'Une erreur est survenue sur le serveur.'
                    : $e->getMessage(),
                'errors' => $e instanceof \Illuminate\Validation\ValidationException ? $e->errors() : null,
            ], $status);
        });
    })->create();
