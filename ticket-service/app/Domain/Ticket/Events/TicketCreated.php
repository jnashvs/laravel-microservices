<?php

namespace Domain\Ticket\Events;

use Domain\Ticket\ValueObjects\Priority;
use Domain\Ticket\ValueObjects\TicketStatus;

class TicketCreated
{
    public const NAME = 'ticket.created';

    public function __construct(
        public string $ticketId,
        public string $title,
        public string $description,
        public Priority $priority,
        public TicketStatus $status,
        public string $createdAt,
        public string $requestId,
        public ?string $traceparent
    ) {
    }
}
