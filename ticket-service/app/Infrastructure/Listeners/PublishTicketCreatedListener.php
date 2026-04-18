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

    public function handle(object $event): void
    {
        if (!$this->supports($event)) {
            return;
        }

        /** @var TicketCreated $event */
        $this->publisher->publish(
            TicketCreated::NAME,
            [
                'id' => $event->ticketId,
                'title' => $event->title,
                'description' => $event->description,
                'priority' => $event->priority->getValue(),
                'status' => $event->status->getValue(),
                'created_at' => $event->createdAt,
                'request_id' => $event->requestId,
                'traceparent' => $event->traceparent,
            ]
        );
    }
}
