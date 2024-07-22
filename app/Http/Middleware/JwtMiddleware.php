<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Illuminate\Support\Facades\Log;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'Token has expired',
                'status_code' => 401
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'Token is invalid',
                'status_code' => 401
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'User not authenticated',
                'status_code' => 401
            ], 401);
        }

        $request->auth = $user;
       
       
        return $next($request);
    }
}
