<?php

namespace Application\Notification\UseCases;

use Domain\Notification\Entities\Notification;
use Domain\Notification\Repositories\NotificationRepositoryInterface;
use Illuminate\Support\Str;

class CreateNotificationUseCase
{
    public function __construct(private readonly NotificationRepositoryInterface $repository)
    {
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
