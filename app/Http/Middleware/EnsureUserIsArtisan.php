<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to redirect the user if their cart is empty.
 */
class EnsureUserIsArtisan
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isArtisan) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized action'], 403);
    }
}
