<?php

namespace Application\Ticket\DTOs;

use Domain\Ticket\Entities\Ticket;
use Spatie\LaravelData\Data;

class TicketResponseData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly string $description,
        public readonly string $priority,
        public readonly string $status,
        public readonly string $created_at,
    ) {
    }

    public static function fromEntity(Ticket $ticket): self
    {
        return new self(
            id: $ticket->getId(),
            title: $ticket->getTitle(),
            description: $ticket->getDescription(),
            priority: $ticket->getPriority()->getValue(),
            status: $ticket->getStatus()->getValue(),
            created_at: $ticket->getCreatedAt(),
        );
    }

    /**
     * @param Ticket[] $tickets
     * @return self[]
     */
    public static function fromCollection(array $tickets): array
    {
        return array_map(fn (Ticket $ticket) => self::fromEntity($ticket), $tickets);
    }
}
