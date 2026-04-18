<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CorrelationIdMiddleware
{
    public function handle($request, Closure $next)
    {
        $requestId = $request->header('X-Request-ID') ?? Str::uuid()->toString();

        app()->instance('request_id', $requestId);

        Log::withContext([
            'request_id' => $requestId,
            'service' => config('app.name'),
        ]);

        $response = $next($request);

        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            $response->headers->set('X-Request-ID', $requestId);
        }

        return $response;
    }
}
