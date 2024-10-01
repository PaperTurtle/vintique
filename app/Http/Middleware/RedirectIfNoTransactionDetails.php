<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to redirect the user if no transaction details are present in the session.
 *
 * This middleware checks if the session has 'transactionDetails' and if they are structured as an array.
 * If the session does not have valid 'transactionDetails', the user is redirected to the products index route.
 * This is typically used to ensure that a user can only access certain routes if they have completed a transaction.
 */
class RedirectIfNoTransactionDetails
{
    /**
     * Handle an incoming request.
     *
     * Checks the session for 'transactionDetails'. If not present or not an array,
     * it redirects to the products index page. Otherwise, it allows the request to proceed.
     *
     * @param  Request  $request the incoming request
     * @param  Closure  $next    the next middleware in the pipeline
     * @return Response the response after handling
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('transactionDetails') || !is_array(session('transactionDetails'))) {
            return redirect()->route('products.index');
        }

        return $next($request);
    }
}
