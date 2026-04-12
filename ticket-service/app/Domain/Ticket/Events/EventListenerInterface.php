<?php

namespace Domain\Ticket\Events;

interface EventListenerInterface
{
    public function supports(object $event): bool;
    public function handle(object $event): void;
}
