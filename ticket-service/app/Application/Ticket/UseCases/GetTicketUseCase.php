<?php

namespace Application\Ticket\UseCases;

use Domain\Ticket\Repositories\TicketRepositoryInterface;
use Application\Ticket\DTOs\TicketResponseData;
use Application\Ticket\Exceptions\TicketNotFoundException;

class GetTicketUseCase
{
    public function __construct(
        private readonly TicketRepositoryInterface $repository
    ) {
    }

    /**
     * @throws TicketNotFoundException
     */
    public function execute(string $id): TicketResponseData
    {
        $ticket = $this->repository->find($id);

        if (!$ticket) {
            throw TicketNotFoundException::withId($id);
        }

        return TicketResponseData::fromEntity($ticket);
    }
}
