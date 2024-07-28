<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || $user->role !== 'admin' || !$user->is_active) {
            return response()->json([
                'status_code' => 401,
                'message' => 'Unauthorized',
                'error' => 'Bad Request'
            ], 401);
        }

        return $next($request);
    }
}
