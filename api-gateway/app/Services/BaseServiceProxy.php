<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceUnavailableException;

abstract class BaseServiceProxy
{
    abstract protected function getBaseUrl(): string;
    abstract protected function getServiceName(): string;

    protected function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'X-Forwarded-By' => 'api-gateway',
        ];
    }

    protected function get(string $path, array $headers = []): Response
    {
        return $this->request('get', $path, [], $headers);
    }

    protected function post(string $path, array $data = [], array $headers = []): Response
    {
        return $this->request('post', $path, $data, $headers);
    }

    protected function request(string $method, string $path, array $data = [], array $headers = []): Response
    {
        $url = rtrim($this->getBaseUrl(), '/') . '/' . ltrim($path, '/');

        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->retry(3, 100)
                ->withHeaders(array_merge($this->getHeaders(), $headers))
                ->{$method}($url, $data);

            Log::info("[{$this->getServiceName()}] {$method} {$path}", [
                'status' => $response->status(),
            ]);

            return $response;

        } catch (ConnectionException $e) {
            Log::error("[{$this->getServiceName()}] Connection failed: {$path}", [
                'error' => $e->getMessage(),
            ]);

            throw ServiceUnavailableException::forService($this->getServiceName());
        }
    }
}
