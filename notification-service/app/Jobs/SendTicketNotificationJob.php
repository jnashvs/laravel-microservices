<?php

namespace App\Jobs;

use App\Mail\TicketNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTicketNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = 60;

    public function __construct(
        private readonly string $type,
        private readonly string $messageText,
        private readonly string $referenceId,
        private readonly ?string $requestId = null
    ) {
    }

    public function handle(): void
    {
        if ($this->requestId) {
            app()->instance('request_id', $this->requestId);
            Log::withContext(['request_id' => $this->requestId]);
        }

        try {
            Mail::to(config('mail.from.address', 'admin@example.com'))
                ->send(new TicketNotificationMail(
                    $this->type,
                    $this->messageText,
                    $this->referenceId
                ));
            
            Log::info("[NotificationService] Async email sent", [
                'ref' => $this->referenceId,
                'type' => $this->type
            ]);
        } catch (\Exception $e) {
            Log::error("[NotificationService] Async email failed", [
                'error' => $e->getMessage(),
                'ref' => $this->referenceId
            ]);
            
            throw $e; // Re-throw to trigger retry mechanism
        }
    }
}
