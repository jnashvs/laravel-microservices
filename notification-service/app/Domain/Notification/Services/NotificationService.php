<?php

namespace Domain\Notification\Services;

use Domain\Notification\Entities\Notification;
use Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Jobs\SendTicketNotificationJob;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repository
    ) {
    }

    public function createAndNotify(string $type, string $message, string $referenceId): Notification
    {
        $notification = new Notification(
            Str::uuid()->toString(),
            $type,
            $message,
            $referenceId
        );

        $this->repository->save($notification);

        // Dispatch job to queue for async processing
        SendTicketNotificationJob::dispatch($type, $message, $referenceId);

        return $notification;
    }
}
