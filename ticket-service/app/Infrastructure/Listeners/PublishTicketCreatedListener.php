<?php

namespace Infrastructure\Listeners;

use Domain\Ticket\Events\TicketCreated;
use Domain\Ticket\Events\EventPublisherInterface;
use Domain\Ticket\Events\EventListenerInterface;

class PublishTicketCreatedListener implements EventListenerInterface
{
    public function __construct(
        private EventPublisherInterface $publisher
    ) {
    }

    public function supports(object $event): bool
    {
        return $event instanceof TicketCreated;
    }

    /**
     * @param TicketCreated $event
     */
    public function handle(object $event): void
    {
        if (!$this->supports($event)) {
            return;
        }

        /** @var TicketCreated $event */
        $this->publisher->publish(TicketCreated::NAME, [
            'id' => $event->ticket->getId(),
            'title' => $event->ticket->getTitle(),
            'description' => $event->ticket->getDescription(),
            'priority' => $event->ticket->getPriority()->getValue(),
            'status' => $event->ticket->getStatus()->getValue(),
            'created_at' => $event->ticket->getCreatedAt(),
        ]);
    }
}
