<?php

namespace Domain\Ticket\Entities;

use Domain\Ticket\ValueObjects\Priority;
use Domain\Ticket\ValueObjects\TicketStatus;
use Illuminate\Support\Str;

class Ticket
{
    private function __construct(
        private readonly string $id,
        private readonly string $title,
        private readonly string $description,
        private readonly Priority $priority,
        private readonly TicketStatus $status,
        private readonly string $createdAt,
    ) {
    }

    public static function create(string $title, string $description, Priority $priority): self
    {
        return new self(
            id: Str::uuid()->toString(),
            title: $title,
            description: $description,
            priority: $priority,
            status: new TicketStatus(TicketStatus::OPEN),
            createdAt: date('c'),
        );
    }

    public static function fromPersistence(
        string $id,
        string $title,
        string $description,
        Priority $priority,
        TicketStatus $status,
        string $createdAt,
    ): self {
        return new self(
            id: $id,
            title: $title,
            description: $description,
            priority: $priority,
            status: $status,
            createdAt: $createdAt,
        );
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getPriority(): Priority
    {
        return $this->priority;
    }
    public function getStatus(): TicketStatus
    {
        return $this->status;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
