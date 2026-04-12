<?php

namespace Infrastructure\Events;

use Domain\Ticket\Events\EventDispatcherInterface;
use Domain\Ticket\Events\EventListenerInterface;
use Illuminate\Contracts\Container\Container;

class SimpleEventDispatcher implements EventDispatcherInterface
{
    /**
     * @param Container $container
     * @param class-string<EventListenerInterface>[] $listeners
     */
    public function __construct(
        private readonly Container $container,
        private readonly array $listeners
    ) {
    }

    public function dispatch(object $event): void
    {
        foreach ($this->listeners as $listenerClass) {
            /** @var EventListenerInterface $listener */
            $listener = $this->container->make($listenerClass);

            if ($listener->supports($event)) {
                $listener->handle($event);
            }
        }
    }
}
