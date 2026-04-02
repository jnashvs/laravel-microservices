<?php

namespace Application\Ticket\UseCases;

use Domain\Ticket\Entities\Ticket;
use Domain\Ticket\ValueObjects\Priority;
use Domain\Ticket\ValueObjects\TicketStatus;
use Domain\Ticket\Repositories\TicketRepositoryInterface;
use Application\Ticket\DTOs\CreateTicketDTO;
use Domain\Ticket\Events\TicketCreated;
use Illuminate\Support\Facades\Event;

class CreateTicketUseCase
{
    private TicketRepositoryInterface $repository;

    public function __construct(TicketRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CreateTicketDTO $dto): Ticket
    {
        $ticket = new Ticket(
            uniqid(),
            $dto->title,
            $dto->description,
            new Priority($dto->priority),
            new TicketStatus(TicketStatus::OPEN)
        );

        $this->repository->save($ticket);
        Event::dispatch(new TicketCreated($ticket));

        return $ticket;
    }
}
