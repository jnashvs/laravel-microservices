<?php

namespace Application\Ticket\UseCases;

use Domain\Ticket\Entities\Ticket;
use Domain\Ticket\Repositories\TicketRepositoryInterface;

class GetTicketUseCase
{
    private TicketRepositoryInterface $repository;

    public function __construct(TicketRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $id): ?Ticket
    {
        return $this->repository->find($id);
    }
}
