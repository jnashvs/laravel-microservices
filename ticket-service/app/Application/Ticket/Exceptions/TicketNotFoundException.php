<?php

namespace Application\Ticket\Exceptions;

use RuntimeException;

class TicketNotFoundException extends RuntimeException
{
    public static function withId(string $id): self
    {
        return new self("Ticket with ID '{$id}' not found.");
    }
}
