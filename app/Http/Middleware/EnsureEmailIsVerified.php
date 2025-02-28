<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure user is authenticated
        if (! $request->user() || ! $request->user()->hasVerifiedEmail()) {
            return response()->json([
                'status_code' => 403,
                'message' => 'Email not verified. Please verify your email to continue.',
                'status' => 'error',
                'data' => []
            ], 403);
        }

        return $next($request);
    }
}
