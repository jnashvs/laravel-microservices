<?php

namespace Infrastructure\Repositories;

use Domain\Notification\Entities\Notification;
use Domain\Notification\Repositories\NotificationRepositoryInterface;

class FileNotificationRepository implements NotificationRepositoryInterface
{
    private string $path;

    public function __construct()
    {
        $this->path = storage_path('app/notifications.json');

        if (!file_exists($this->path)) {
            file_put_contents($this->path, json_encode([]));
        }
    }

    public function save(Notification $notification): void
    {
        $notifications = $this->readAll();

        $notifications[] = [
            'id' => $notification->getId(),
            'type' => $notification->getType(),
            'message' => $notification->getMessage(),
            'reference_id' => $notification->getReferenceId(),
            'created_at' => $notification->getCreatedAt(),
        ];

        file_put_contents($this->path, json_encode($notifications, JSON_PRETTY_PRINT));
    }

    public function findAll(): array
    {
        return array_map(function ($item) {
            return new Notification(
                $item['id'],
                $item['type'],
                $item['message'],
                $item['reference_id'],
                $item['created_at']
            );
        }, $this->readAll());
    }

    private function readAll(): array
    {
        return json_decode(file_get_contents($this->path), true) ?? [];
    }
}
