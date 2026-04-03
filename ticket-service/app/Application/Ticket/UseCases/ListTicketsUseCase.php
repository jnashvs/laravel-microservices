<?php

namespace Application\Ticket\UseCases;

use Domain\Ticket\Repositories\TicketRepositoryInterface;
use Application\Ticket\DTOs\TicketResponseData;

class ListTicketsUseCase
{
    public function __construct(
        private readonly TicketRepositoryInterface $repository
    ) {
    }

    /**
     * @return TicketResponseData[]
     */
    public function execute(): array
    {
        $tickets = $this->repository->findAll();

        return TicketResponseData::fromCollection($tickets);
    }
}
