<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshTokenRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {
    }

    /**
     * POST /api/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $tokens = $this->authService->authenticate(
                $request->validated('email'),
                $request->validated('password'),
            );

            return response()->json([
                'message' => 'Authenticated successfully.',
                'data' => $tokens,
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid credentials.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * POST /api/refresh
     */
    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        try {
            $tokens = $this->authService->refreshToken(
                $request->validated('refresh_token'),
            );

            return response()->json([
                'message' => 'Token refreshed successfully.',
                'data' => $tokens,
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Unable to refresh token.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * GET /api/me
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($request->user()),
        ], Response::HTTP_OK);
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->revokeToken($request->user());

        return response()->json([
            'message' => 'Logged out successfully.',
        ], Response::HTTP_OK);
    }

    /**
     * POST /api/logout-all
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $this->authService->revokeAllTokens($request->user());

        return response()->json([
            'message' => 'All sessions revoked successfully.',
        ], Response::HTTP_OK);
    }

    public function validateToken(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'valid' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
            ],
        ]);
    }
}
