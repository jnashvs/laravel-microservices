<?php

namespace Domain\Ticket\ValueObjects;

class TicketStatus
{
    public const OPEN = 'open';
    public const IN_PROGRESS = 'in_progress';
    public const CLOSED = 'closed';

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, [self::OPEN, self::IN_PROGRESS, self::CLOSED])) {
            throw new \InvalidArgumentException("Invalid ticket status");
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
