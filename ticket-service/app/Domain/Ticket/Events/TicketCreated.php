<?php

namespace Domain\Ticket\Events;

use Domain\Ticket\Entities\Ticket;

class TicketCreated
{
    public const NAME = 'ticket.created';

    public function __construct(
        public readonly Ticket $ticket
    ) {
    }
}
