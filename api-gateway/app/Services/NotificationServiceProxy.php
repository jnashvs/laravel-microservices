<?php

namespace App\Services;

class NotificationServiceProxy extends BaseServiceProxy
{
    protected function getBaseUrl(): string
    {
        return config('services.notification.url');
    }

    protected function getServiceName(): string
    {
        return 'notification-service';
    }

    public function listNotifications(): array
    {
        $response = $this->get('notifications');

        return [
            'data' => $response->json(),
            'status' => $response->status(),
        ];
    }

    public function health(): array
    {
        try {
            $response = $this->get('health');

            return [
                'status' => $response->successful() ? 'up' : 'down',
                'response_code' => $response->status(),
            ];
        } catch (\Exception) {
            return ['status' => 'down'];
        }
    }
}
