<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');

        if (!$apiKey) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'API key is required. Provide it via X-API-Key header.',
            ], 401);
        }

        $validKeys = array_filter(explode(',', config('services.api_keys', '')));

        if (!in_array($apiKey, $validKeys, true)) {
            return response()->json([
                'error' => 'Forbidden',
                'message' => 'Invalid API key.',
            ], 403);
        }

        return $next($request);
    }
}
