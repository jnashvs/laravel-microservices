<?php

namespace Domain\Ticket\Events;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}
