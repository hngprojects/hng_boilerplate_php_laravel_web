<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LoginAttempts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'login_attempts_' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'message' => 'Too Many login attempts. Please try again in one hour',
                'error' => 'too_many_attempts',
                'status_code' => 403
            ], 403);
        }

        RateLimiter::hit($key, 3600); // Lock out for an hour (3600 seconds)

        return $next($request);
    }
}
