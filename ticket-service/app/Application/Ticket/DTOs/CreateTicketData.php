<?php

namespace Application\Ticket\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;

class CreateTicketData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public readonly string $title,
        #[Required]
        public readonly string $description,
        #[Required, In('low', 'medium', 'high')]
        public readonly string $priority,
    ) {
    }
}
