<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureInternalRequest
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('X-API-KEY') !== config('services.internal.key')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
