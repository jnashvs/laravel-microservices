<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function authenticate(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $this->issueTokens($email, $password);
    }

    public function refreshToken(string $refreshToken): array
    {
        $response = Http::asForm()->post($this->tokenEndpoint(), [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'scope' => '',
        ]);

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'refresh_token' => ['The refresh token is invalid or expired.'],
            ]);
        }

        $data = $response->json();

        return [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'token_type' => 'Bearer',
            'expires_in' => $data['expires_in'],
        ];
    }

    public function revokeToken(User $user): void
    {
        $token = $user->token();

        if ($token) {
            // revoke access token
            $token->revoke();

            // revoke refresh tokens diretamente na DB
            \DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $token->id)
                ->update(['revoked' => true]);
        }
    }

    public function revokeAllTokens(User $user): void
    {
        foreach ($user->tokens as $token) {
            $token->revoke();

            \DB::table('oauth_refresh_tokens')
                ->where('access_token_id', $token->id)
                ->update(['revoked' => true]);
        }
    }

    private function issueTokens(string $email, string $password): array
    {
        $response = Http::asForm()->post($this->tokenEndpoint(), [
            'grant_type' => 'password',
            'client_id' => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username' => $email,
            'password' => $password,
            'scope' => '',
        ]);

        if ($response->failed()) {
            throw ValidationException::withMessages([
                'email' => ['Unable to issue tokens. Please try again.'],
            ]);
        }

        $data = $response->json();

        return [
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'token_type' => 'Bearer',
            'expires_in' => $data['expires_in'],
        ];
    }

    private function tokenEndpoint(): string
    {
        return config('app.url') . '/oauth/token';
    }
}
