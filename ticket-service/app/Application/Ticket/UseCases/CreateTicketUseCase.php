<?php

namespace Application\Ticket\UseCases;

use Illuminate\Support\Facades\DB;
use Domain\Ticket\Services\TicketService;
use Application\Ticket\DTOs\CreateTicketData;
use Application\Ticket\DTOs\TicketResponseData;
use Application\Ticket\Exceptions\TicketCreationException;

class CreateTicketUseCase
{
    public function __construct(
        private readonly TicketService $service
    ) {
    }

    /**
     * @throws TicketCreationException
     */
    public function execute(CreateTicketData $data): TicketResponseData
    {
        try {
            $ticket = DB::transaction(function () use ($data) {
                return $this->service->create(
                    $data->title,
                    $data->description,
                    $data->priority
                );
            });

            return TicketResponseData::fromEntity($ticket);

        } catch (\Exception $e) {
            throw new TicketCreationException(
                "Failed to create ticket: {$e->getMessage()}",
                previous: $e
            );
        }
    }
}
