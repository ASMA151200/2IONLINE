<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles ): Response
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
        return response()->json([
            'success' => false,
            'message' => 'Accès non autorisé'
            ], 403);
        }

        return $next($request);
    }
}
