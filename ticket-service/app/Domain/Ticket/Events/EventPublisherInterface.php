<?php

namespace Domain\Ticket\Events;

interface EventPublisherInterface
{
    public function publish(string $eventName, array $payload): void;
}
