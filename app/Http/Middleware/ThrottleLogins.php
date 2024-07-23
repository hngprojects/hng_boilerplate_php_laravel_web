<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ThrottleLogins
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param int $maxAttempts
     * @param int $decayMinutes
     * @return mixed
     */
    public function handle($request, Closure $next, $maxAttempts = 3, $decayMinutes = 1)
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            return response()->json([
                'message' => "Too many login attempts. Please try again in {$minutes} minutes."
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $response = $next($request);

        if ($response->status() === 401) {
            RateLimiter::hit($key, $decayMinutes * 60);
        }

        return $response;
    }
    

    /**
     * Get the throttle key for the given request.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function throttleKey($request)
    {
        return strtolower($request->input('email')) . '|' . $request->ip();
    }

    /**
     * Determine if the user has too many failed login attempts.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function tooManyAttempts($request)
    {
        $key = $this->throttleKey($request);

        return RateLimiter::tooManyAttempts($key, 3);
    }

    /**
     * Fire an event when a lockout occurs.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    protected function fireLockoutEvent($request)
    {
        event(new \Illuminate\Auth\Events\Lockout($request));
    }
}
