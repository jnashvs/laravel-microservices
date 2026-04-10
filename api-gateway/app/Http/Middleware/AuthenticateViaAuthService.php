<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateViaAuthService
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {

            $response = Http::withHeaders([
                'X-API-KEY' => config('services.internal.key'),
            ])
            ->withToken($token)
            ->timeout(5)
            ->retry(2, 100)
            ->get(config('services.auth.url') . '/api/token/validate');

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Authentication service unavailable.',
            ], Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if ($response->failed() || ! $response->json('valid')) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $response->json('user');

        $request->headers->set('X-User-Id', (string) $user['id']);
        $request->headers->set('X-User-Email', $user['email']);
        $request->headers->set('X-User-Name', $user['name']);

        // Prevent clients from spoofing these headers
        $request->headers->set('X-Authenticated-Via', 'gateway');

        return $next($request);
    }
}
