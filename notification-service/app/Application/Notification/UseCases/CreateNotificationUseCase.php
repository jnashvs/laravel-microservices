<?php

namespace Application\Notification\UseCases;

use Domain\Notification\Entities\Notification;
use Domain\Notification\Repositories\NotificationRepositoryInterface;
use Illuminate\Support\Str;

class CreateNotificationUseCase
{
    private NotificationRepositoryInterface $repository;

    public function __construct(NotificationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $type, string $message, string $referenceId): Notification
    {
        $notification = new Notification(
            Str::uuid()->toString(),
            $type,
            $message,
            $referenceId
        );

        $this->repository->save($notification);

        return $notification;
    }
}
