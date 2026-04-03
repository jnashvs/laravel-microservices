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

    protected function get(string $path): Response
    {
        return $this->request('get', $path);
    }

    protected function post(string $path, array $data = []): Response
    {
        return $this->request('post', $path, $data);
    }

    private function request(string $method, string $path, array $data = []): Response
    {
        $url = rtrim($this->getBaseUrl(), '/') . '/' . ltrim($path, '/');

        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'X-Forwarded-By' => 'api-gateway',
                ])
                ->{$method}($url, $data);

            Log::channel('single')->info("[{$this->getServiceName()}] {$method} {$path}", [
                'status' => $response->status(),
            ]);

            return $response;
        } catch (ConnectionException $e) {
            Log::channel('single')->error("[{$this->getServiceName()}] Connection failed: {$path}", [
                'error' => $e->getMessage(),
            ]);

            throw ServiceUnavailableException::forService($this->getServiceName());
        }
    }
}
