<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitMiddleware
{
    private const MAX_REQUESTS = 60;
    private const WINDOW_SECONDS = 60;

    public function handle(Request $request, Closure $next): Response
    {
        $key = 'rate_limit:' . ($request->header('X-API-Key') ?? $request->ip());
        $requests = (int) Cache::get($key, 0);

        if ($requests >= self::MAX_REQUESTS) {
            return response()->json([
                'error' => 'Too Many Requests',
                'message' => sprintf('Rate limit exceeded. Maximum %d requests per minute.', self::MAX_REQUESTS),
                'retry_after' => self::WINDOW_SECONDS,
            ], 429);
        }

        Cache::put($key, $requests + 1, self::WINDOW_SECONDS);

        $response = $next($request);

        $response->headers->set('X-RateLimit-Limit', (string) self::MAX_REQUESTS);
        $response->headers->set('X-RateLimit-Remaining', (string) max(0, self::MAX_REQUESTS - $requests - 1));

        return $response;
    }
}
