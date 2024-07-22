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
$this->fireLockoutEvent($request);

$seconds = RateLimiter::availableIn($key);
$minutes = ceil($seconds / 60);

return response()->json(['message' => 'Too many login attempts. Please try again in ' . $minutes . ' minutes.'], Response::HTTP_FORBIDDEN);
}

RateLimiter::hit($key, $decayMinutes * 60);

$response = $next($request);

if ($this->tooManyAttempts($request)) {
RateLimiter::clear($key);
Cache::put('login_lockout_' . $request->input('email'), now()->addHour(), 3600);

Auth::logout();

return response()->json(['message' => 'Too many login attempts. You have been logged out for 1 hour.'], Response::HTTP_FORBIDDEN);
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
