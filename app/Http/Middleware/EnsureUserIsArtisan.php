<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (!auth()->user()->isArtisan) {
            return response()->json(['message' => 'Unauthorized action'], 403);
        }

        return $next($request);
    }
}
