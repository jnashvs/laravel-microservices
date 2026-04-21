<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HttpLoggingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('up') || $request->is('api/health')) {
            return $next($request);
        }

        $start = microtime(true);

        $response = $next($request);

        $duration = round((microtime(true) - $start) * 1000, 2);

        Log::info('http_request_finished', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'ip' => $request->ip(),
            'request_payload' => $this->shouldLogPayload($request) ? $request->except(['password', 'password_confirmation', 'token', 'client_secret']) : [],
        ]);

        return $response;
    }

    private function shouldLogPayload(Request $request): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH']);
    }
}
