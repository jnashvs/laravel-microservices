<?php

namespace Infrastructure\Listeners;

use Domain\Ticket\Events\TicketCreated;
use Illuminate\Support\Facades\Log;

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
            $redis = new \Predis\Client([
                'scheme' => 'tcp',
                'host'   => env('REDIS_HOST', 'redis'),
                'port'   => env('REDIS_PORT', 6379),
            ]);

            $redis->publish('ticket.created', $payload);

            Log::info('Ticket criado e publicado no Redis', [
                'id' => $event->ticket->getId(),
            ]);
        } catch (\Exception $e) {
            Log::error('Falha ao publicar no Redis', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
