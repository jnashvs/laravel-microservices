<?php

namespace Application\Ticket\UseCases;

use Domain\Ticket\Repositories\TicketRepositoryInterface;

class ListTicketsUseCase
{
    private TicketRepositoryInterface $repository;

    public function __construct(TicketRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}
