<?php

namespace Infrastructure\Events;

use Domain\Ticket\Events\EventDispatcherInterface;
use Illuminate\Support\Facades\Event;

class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function dispatch(object $event): void
    {
        Event::dispatch($event);
    }
}
