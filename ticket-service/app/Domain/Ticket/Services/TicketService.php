<?php

namespace Domain\Ticket\Services;

use Domain\Ticket\Entities\Ticket;
use Domain\Ticket\ValueObjects\Priority;
use Domain\Ticket\Repositories\TicketRepositoryInterface;
use Domain\Ticket\Events\EventDispatcherInterface;
use Domain\Ticket\Events\TicketCreated;

class TicketService
{
    public function __construct(
        private readonly TicketRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    public function create(
        string $title,
        string $description,
        string $priority
    ): Ticket {
        $ticket = Ticket::create(
            $title,
            $description,
            new Priority($priority)
        );

        $this->repository->save($ticket);

        $this->eventDispatcher->dispatch(
            new TicketCreated($ticket)
        );

        return $ticket;
    }

    public function find(string $id): ?Ticket
    {
        return $this->repository->find($id);
    }

    public function list(): array
    {
        return $this->repository->findAll();
    }
}
