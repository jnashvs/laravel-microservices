<?php

namespace Application\Ticket\DTOs;

use Spatie\DataTransferObject\DataTransferObject;

class CreateTicketDTO extends DataTransferObject
{
    public string $title;
    public string $description;
    public string $priority; // low, medium, high
}
