<?php

namespace App\Http\Middleware\Prometheus;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowLocalhostOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->isLocalhost($request->ip())) {
            return response('Unauthorized.', 401);
        }
        return $next($request);
    }


    /**
     * Check if the IP address is localhost.
     *
     * @param  string  $ip
     * @return bool
     */
    protected function isLocalhost($ip)
    {
        return in_array($ip, ['127.0.0.1', '::1']);
    }
}
