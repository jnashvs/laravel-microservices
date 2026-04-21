<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Predis\Client;
use Application\Notification\UseCases\CreateNotificationUseCase;
use Illuminate\Support\Facades\Log;

class ConsumeTicketEventsStream extends Command
{
    protected $signature = 'redis:consume-ticket-stream';
    protected $description = 'Consume ticket events via Redis Streams';

    public function __construct(
        private CreateNotificationUseCase $createNotification
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        Log::info("Starting Redis Stream Consumer...");

        $redis = new Client([
            'scheme' => 'tcp',
            'host' => env('REDIS_HOST', 'redis'),
            'port' => 6379,
        ]);

        $stream = 'ticket.events';
        $group = 'notification-service';
        $consumer = gethostname() . '-' . getmypid();

        try {
            $redis->executeRaw(['XGROUP', 'CREATE', $stream, $group, '0', 'MKSTREAM']);
        } catch (\Exception) {
            // Group might already exist
        }

        $this->info("Listening to stream: {$stream}");

        while (true) {
            $messages = $redis->executeRaw([
                'XREADGROUP',
                'GROUP', $group, $consumer,
                'COUNT', '1',
                'BLOCK', '2000',
                'STREAMS', $stream, '>'
            ]);

            if (!$messages) {
                continue;
            }

            foreach ($messages as [$streamName, $events]) {
                foreach ($events as [$id, $fieldsRaw]) {
                    $fields = [];
                    for ($i = 0; $i < count($fieldsRaw); $i += 2) {
                        $fields[$fieldsRaw[$i]] = $fieldsRaw[$i + 1];
                    }

                    $this->process($redis, $stream, $group, $id, $fields);
                }
            }
        }
    }

    private function process($redis, $stream, $group, $id, $fields): void
    {
        try {
            $requestId = $fields['request_id'] ?? null;
            $traceparent = $fields['traceparent'] ?? null;

            $payload = json_decode($fields['payload'] ?? '{}', true);

            if (!$payload) {
                Log::warning('invalid_payload', [
                    'event_id' => $id,
                    'fields' => $fields,
                ]);
                return;
            }

            $this->restoreContext($requestId, $traceparent);

            Log::info('event_processing_started', [
                'event_id' => $id,
                'event' => $fields['event'] ?? 'unknown',
            ]);

            $eventLabel = strtoupper($fields['event'] ?? 'UNKNOWN');
            $title = $payload['title'] ?? 'No Title';
            $priority = $payload['priority'] ?? 'normal';

            $message = "[{$eventLabel}] Ticket: {$title} | Priority: {$priority}";

            $this->createNotification->execute(
                type: $fields['event'] ?? 'system',
                message: $message,
                referenceId: $payload['ticket_id'] ?? 'N/A'
            );

            // ACK
            $redis->executeRaw(['XACK', $stream, $group, $id]);

            Log::info('event_processed_successfully', [
                'event_id' => $id,
            ]);

        } catch (\Throwable $e) {
            Log::error('event_processing_failed', [
                'event_id' => $id,
                'error' => $e->getMessage(),
                'fields' => $fields,
            ]);
        }
    }

    private function restoreContext(?string $requestId, ?string $traceparent): void
    {
        if ($requestId) {
            app()->instance('request_id', $requestId);
        }

        Log::withContext([
            'request_id' => $requestId,
            'traceparent' => $traceparent,
            'service' => config('app.name'),
        ]);
    }
}
