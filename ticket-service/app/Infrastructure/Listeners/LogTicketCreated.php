<?php

namespace Infrastructure\Listeners;

use Domain\Ticket\Events\TicketCreated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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

        // Publicar no Redis
        Redis::publish('ticket.created', $payload);

        Log::info('Ticket criado e publicado no Redis', [
            'id' => $event->ticket->getId(),
        ]);
    }
}
