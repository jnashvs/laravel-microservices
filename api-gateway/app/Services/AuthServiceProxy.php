<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthServiceProxy extends BaseServiceProxy
{
    private const CACHE_PREFIX = 'auth:token:';
    private const FAILURES_KEY = 'auth:failures';
    private const FAILURE_THRESHOLD = 5;
    private const FAILURE_WINDOW = 60;
    private const MAX_CACHE_TTL = 300;

    protected function getBaseUrl(): string
    {
        return config('services.auth.url');
    }
    protected function getServiceName(): string
    {
        return 'auth-service';
    }

    protected function getHeaders(): array
    {
        return array_merge(parent::getHeaders(), [
            'X-API-KEY' => config('services.internal.key'),
        ]);
    }

    public function login(array $data)
    {
        return $this->post('api/login', $data);
    }
    public function refresh(array $data)
    {
        return $this->post('api/refresh', $data);
    }
    public function me(string $token)
    {
        return $this->get('api/me', ['Authorization' => "Bearer {$token}"]);
    }

    public function logout(string $token)
    {
        $this->clearTokenCache($token);
        return $this->post('api/logout', [], ['Authorization' => "Bearer {$token}"]);
    }

    /**
     * Validate token with Redis caching, circuit breaker and structured logging.
     */
    public function validateToken(string $token): ?array
    {
        $tokenHash = hash('sha256', $token);
        $cacheKey = self::CACHE_PREFIX . $tokenHash;

        // Cache hit?
        if ($cached = Cache::get($cacheKey)) {
            Log::info("[{$this->getServiceName()}] Token cache hit", ['hash' => $tokenHash]);
            return $cached;
        }

        // Circuit broken
        if ($this->isCircuitBroken()) {
            return $this->fallback($cacheKey, 'Circuit breaker open', $tokenHash);
        }

        try {
            // Request validation
            $response = $this->get('api/token/validate', ['Authorization' => "Bearer {$token}"]);

            if ($response->successful() && $response->json('valid')) {
                return $this->cacheTokenResult($cacheKey, $token, $response->json('user'));
            }

            return $this->handleFailure($response);
        } catch (\Throwable $e) {
            return $this->fallback($cacheKey, $e->getMessage(), $tokenHash);
        }
    }

    /**
     * Global circuit breaker logic for all auth-service requests.
     */
    protected function request(string $method, string $path, array $data = [], array $headers = []): \Illuminate\Http\Client\Response
    {
        try {
            $response = parent::request($method, $path, $data, $headers);
            if ($response->successful()) {
                $this->resetCircuit();
            }
            return $response;
        } catch (\Throwable $e) {
            $this->recordFailure($e);
            throw $e;
        }
    }

    private function isCircuitBroken(): bool
    {
        return (int) Cache::get(self::FAILURES_KEY, 0) >= self::FAILURE_THRESHOLD;
    }

    private function resetCircuit(): void
    {
        Cache::forget(self::FAILURES_KEY);
    }

    private function recordFailure(\Throwable $e): void
    {
        Cache::increment(self::FAILURES_KEY, 1, self::FAILURE_WINDOW);
        Log::error("[{$this->getServiceName()}] Connection error", ['error' => $e->getMessage()]);
    }

    private function cacheTokenResult(string $cacheKey, string $token, array $userData): array
    {
        $ttl = $this->calculateCacheTtl($token);
        if ($ttl > 0) {
            Cache::put($cacheKey, $userData, $ttl);
            Log::info("[{$this->getServiceName()}] Token cached", ['ttl' => $ttl]);
        }
        return $userData;
    }

    private function handleFailure($response): ?array
    {
        Log::error("[{$this->getServiceName()}] Validation failed", [
            'status' => $response->status(),
            'body' => $response->json()
        ]);
        return null;
    }

    private function fallback(string $cacheKey, string $reason, string $hash): ?array
    {
        if ($cached = Cache::get($cacheKey)) {
            Log::warning("[{$this->getServiceName()}] Fallback to cache", ['reason' => $reason, 'hash' => $hash]);
            return $cached;
        }
        return null;
    }

    private function clearTokenCache(string $token): void
    {
        Cache::forget(self::CACHE_PREFIX . hash('sha256', $token));
    }

    private function calculateCacheTtl(string $token): int
    {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return 0;
            }

            $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
            $remaining = ($payload['exp'] ?? 0) - time();

            return ($remaining <= 0) ? 0 : min(self::MAX_CACHE_TTL, $remaining);
        } catch (\Throwable) {
            return 0;
        }
    }
}
