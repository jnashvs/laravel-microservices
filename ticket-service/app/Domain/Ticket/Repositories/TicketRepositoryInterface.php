<?php

namespace Domain\Ticket\Repositories;

use Domain\Ticket\Entities\Ticket;

interface TicketRepositoryInterface
{
    public function save(Ticket $ticket): void;
    public function find(string $id): ?Ticket;
    public function findAll(): array;
}
