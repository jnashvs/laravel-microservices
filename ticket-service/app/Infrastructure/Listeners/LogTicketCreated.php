<?php

namespace Infrastructure\Listeners;

use Domain\Ticket\Events\TicketCreated;
use Illuminate\Support\Facades\Log;
use Predis\Client;

class LogTicketCreated
{
    public function handle(TicketCreated $event): void
    {
        $payload = json_encode([
            'id' => $event->ticket->getId(),
            'title' => $event->ticket->getTitle(),
            'description' => $event->ticket->getDescription(),
            'priority' => $event->ticket->getPriority()->getValue(),
            'status' => $event->ticket->getStatus()->getValue(),
            'timestamp' => now()->toISOString(),
        ]);

        try {
            $redis = new Client([
                'scheme' => 'tcp',
                'host'   => config('database.redis.default.host', 'redis'),
                'port'   => (int) config('database.redis.default.port', 6379),
            ]);

            $redis->publish('ticket.created', $payload);

            Log::info('Ticket created and published to Redis', [
                'id' => $event->ticket->getId(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to publish to Redis', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
