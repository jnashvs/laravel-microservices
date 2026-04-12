<?php

namespace Application\Notification\UseCases;

use Domain\Notification\Entities\Notification;
use Domain\Notification\Services\NotificationService;

class CreateNotificationUseCase
{
    public function __construct(private readonly NotificationService $service)
    {
    }

    public function execute(string $type, string $message, string $referenceId): Notification
    {
        return $this->service->createAndNotify(
            $type,
            $message,
            $referenceId
        );
    }
}
