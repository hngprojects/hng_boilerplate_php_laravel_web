<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role !== 'superadmin') {
            return ResponseHelper::response('Unauthorized. Super Admin access only.', 403);
        }

        return $next($request);
    }
}
