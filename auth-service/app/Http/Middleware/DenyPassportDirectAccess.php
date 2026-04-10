<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DenyPassportDirectAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // In Docker, the service calling itself resolves to the container IP,
        // not 127.0.0.1. Allow the Docker network subnet.
        $allowedIps = array_filter(array_map(
            'trim',
            explode(',', env('PASSPORT_ALLOWED_IPS', '127.0.0.1,::1'))
        ));

        if (! in_array($request->ip(), $allowedIps)) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
