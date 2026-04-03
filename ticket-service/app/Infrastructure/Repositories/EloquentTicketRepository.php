<?php

namespace Infrastructure\Repositories;

use Domain\Ticket\Entities\Ticket;
use Domain\Ticket\Repositories\TicketRepositoryInterface;
use Domain\Ticket\ValueObjects\Priority;
use Domain\Ticket\ValueObjects\TicketStatus;
use App\Models\Ticket as EloquentTicket;

class EloquentTicketRepository implements TicketRepositoryInterface
{
    public function save(Ticket $ticket): void
    {
        EloquentTicket::updateOrCreate(
            ['id' => $ticket->getId()],
            [
                'title' => $ticket->getTitle(),
                'description' => $ticket->getDescription(),
                'priority' => $ticket->getPriority()->getValue(),
                'status' => $ticket->getStatus()->getValue(),
            ]
        );
    }

    public function find(string $id): ?Ticket
    {
        $model = EloquentTicket::find($id);

        if (!$model) {
            return null;
        }

        return $this->toDomain($model);
    }

    public function findAll(): array
    {
        return EloquentTicket::all()
            ->map(fn ($model) => $this->toDomain($model))
            ->toArray();
    }

    private function toDomain(EloquentTicket $model): Ticket
    {
        return Ticket::fromPersistence(
            $model->id,
            $model->title,
            $model->description,
            new Priority($model->priority),
            new TicketStatus($model->status),
            $model->created_at->toISOString(),
        );
    }
}
