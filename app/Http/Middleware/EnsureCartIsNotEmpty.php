<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to redirect the user if their cart is empty.
 */
class EnsureCartIsNotEmpty
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user->cart->isEmpty()) {
            return redirect()->route('checkout.index')->with('error', 'Your cart is empty.');
        }

        return $next($request);
    }
}
