<?php

namespace Application\Ticket\UseCases;

use Domain\Ticket\Entities\Ticket;
use Domain\Ticket\ValueObjects\Priority;
use Domain\Ticket\Repositories\TicketRepositoryInterface;
use Domain\Ticket\Events\EventDispatcherInterface;
use Domain\Ticket\Events\TicketCreated;
use Application\Ticket\DTOs\CreateTicketData;
use Application\Ticket\DTOs\TicketResponseData;
use Application\Ticket\Exceptions\TicketCreationException;
use Illuminate\Support\Facades\DB;

class CreateTicketUseCase
{
    public function __construct(
        private readonly TicketRepositoryInterface $repository,
        private readonly EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws TicketCreationException
     */
    public function execute(CreateTicketData $data): TicketResponseData
    {
        try {
            return DB::transaction(function () use ($data) {
                $ticket = Ticket::create(
                    $data->title,
                    $data->description,
                    new Priority($data->priority)
                );

                $this->repository->save($ticket);

                $this->eventDispatcher->dispatch(new TicketCreated($ticket));

                return TicketResponseData::fromEntity($ticket);
            });
        } catch (\Exception $e) {
            throw new TicketCreationException(
                "Failed to create ticket: {$e->getMessage()}",
                previous: $e
            );
        }
    }
}
