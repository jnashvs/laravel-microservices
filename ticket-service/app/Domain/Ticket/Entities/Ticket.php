<?php

namespace Domain\Ticket\Entities;

use Domain\Ticket\ValueObjects\Priority;
use Domain\Ticket\ValueObjects\TicketStatus;

class Ticket
{
    private string $id;
    private string $title;
    private string $description;
    private Priority $priority;
    private TicketStatus $status;

    public function __construct(string $id, string $title, string $description, Priority $priority, TicketStatus $status = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->priority = $priority;
        $this->status = $status ?? new TicketStatus(TicketStatus::OPEN);
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

    public function changeStatus(TicketStatus $status): void
    {
        $this->status = $status;
    }
}
