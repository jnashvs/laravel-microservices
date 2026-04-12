<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use App\Services\AuthServiceProxy;

class AuthenticateViaAuthService
{
    public function __construct(
        private readonly AuthServiceProxy $auth
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->auth->validateToken($token);

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $request->headers->set('X-User-Id', (string) $user['id']);
        $request->headers->set('X-User-Email', $user['email']);
        $request->headers->set('X-User-Name', $user['name']);

        // Prevent clients from spoofing these headers
        $request->headers->set('X-Authenticated-Via', 'gateway');

        return $next($request);
    }
}
