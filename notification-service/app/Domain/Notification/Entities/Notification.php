<?php

namespace Domain\Notification\Entities;

class Notification
{
    private string $id;
    private string $type;
    private string $message;
    private string $referenceId;
    private string $createdAt;

    public function __construct(string $id, string $type, string $message, string $referenceId, ?string $createdAt = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->message = $message;
        $this->referenceId = $referenceId;
        $this->createdAt = $createdAt ?? date('c');
    }

    public function getId(): string
    {
        return $this->id;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    public function getReferenceId(): string
    {
        return $this->referenceId;
    }
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
