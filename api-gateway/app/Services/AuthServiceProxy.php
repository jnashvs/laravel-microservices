<?php

namespace App\Services;

class AuthServiceProxy extends BaseServiceProxy
{
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
        return $this->get('api/me', [
            'Authorization' => "Bearer {$token}",
        ]);
    }

    public function logout(string $token)
    {
        return $this->post('api/logout', [], [
            'Authorization' => "Bearer {$token}",
        ]);
    }
}
