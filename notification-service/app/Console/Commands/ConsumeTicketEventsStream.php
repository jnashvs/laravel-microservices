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
            $redis->xgroup('CREATE', $stream, $group, '0', true);
        } catch (\Exception $e) {
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
            $payload = json_decode($fields['payload'], true);

            if (!$payload) {
                $this->error("Invalid payload");
                return;
            }

            Log::info("Received Event:", ["events" => $payload]);

            $this->createNotification->execute(
                type: $fields['event'],
                message: "New ticket: {$payload['title']} ({$payload['priority']})",
                referenceId: $payload['id']
            );

            $redis->xack($stream, $group, $id);

            $this->info("Processed message: {$id}");

        } catch (\Throwable $e) {
            $this->error("Failed processing {$id}: " . $e->getMessage());
        }
    }
}
