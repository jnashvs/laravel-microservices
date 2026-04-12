<?php

namespace Domain\Notification\Services;

use Domain\Notification\Entities\Notification;
use Domain\Notification\Repositories\NotificationRepositoryInterface;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketNotificationMail;
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

        $this->sendEmail($type, $message, $referenceId);

        return $notification;
    }

    private function sendEmail(string $type, string $message, string $referenceId): void
    {
        try {
            Mail::to(config('mail.from.address'))
                ->send(new TicketNotificationMail($type, $message, $referenceId));
        } catch (\Exception $e) {
            Log::error("Failed to send notification email: {$e->getMessage()}");
        }
    }
}
