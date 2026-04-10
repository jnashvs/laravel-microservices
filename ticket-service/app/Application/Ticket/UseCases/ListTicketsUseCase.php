<?php

namespace Application\Ticket\UseCases;

use Domain\Ticket\Services\TicketService;
use Application\Ticket\DTOs\TicketResponseData;

class ListTicketsUseCase
{
    public function __construct(
        private readonly TicketService $service
    ) {
    }

    /**
     * @return TicketResponseData[]
     */
    public function execute(): array
    {
        $tickets = $this->service->list();

        return TicketResponseData::fromCollection($tickets);
    }
}
