<?php

namespace Application\Ticket\UseCases;

use Domain\Ticket\Services\TicketService;
use Application\Ticket\DTOs\TicketResponseData;
use Application\Ticket\Exceptions\TicketNotFoundException;

class GetTicketUseCase
{
    public function __construct(
        private readonly TicketService $service
    ) {
    }

    /**
     * @throws TicketNotFoundException
     */
    public function execute(string $id): TicketResponseData
    {
        $ticket = $this->service->find($id);

        if (!$ticket) {
            throw TicketNotFoundException::withId($id);
        }

        return TicketResponseData::fromEntity($ticket);
    }
}
