<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Predis\Client;

class SubscribeTicketEvents extends Command
{
    protected $signature = 'redis:subscribe-tickets';
    protected $description = 'Subscribe to ticket events via Redis Pub/Sub';

    public function handle(): void
    {
        $this->info('Connecting to Redis...');

        $redis = new Client([
            'scheme' => 'tcp',
            'host'   => env('REDIS_HOST', 'redis'),
            'port'   => (int) env('REDIS_PORT', 6379),
            'read_write_timeout' => 0, // Never timeout — required for Pub/Sub
        ]);

        $this->info('Listening for ticket events on Redis channel "ticket.created"...');

        try {
            $pubsub = $redis->pubSubLoop();
            $pubsub->subscribe('ticket.created');

            foreach ($pubsub as $message) {
                if ($message->kind === 'message') {
                    $this->processMessage($message->payload);
                }
            }
        } catch (\Exception $e) {
            $this->error('Redis subscriber error: ' . $e->getMessage());

            // Wait and let the process restart
            sleep(5);

            return;
        }
    }

    private function processMessage(string $payload): void
    {
        $this->info('Received event: ' . $payload);

        $data = json_decode($payload, true);

        if (!$data) {
            $this->error('Invalid JSON payload');
            return;
        }

        $notification = [
            'id' => uniqid(),
            'type' => 'ticket_created',
            'message' => "New ticket created: {$data['title']} (Priority: {$data['priority']})",
            'reference_id' => $data['id'],
            'created_at' => $data['timestamp'] ?? now()->toISOString(),
        ];

        $filePath = storage_path('app/notifications.json');
        $notifications = [];

        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $notifications = json_decode($content, true) ?? [];
        }

        $notifications[] = $notification;
        file_put_contents($filePath, json_encode($notifications, JSON_PRETTY_PRINT));

        $this->info("Notification saved: {$notification['id']}");
    }
}
