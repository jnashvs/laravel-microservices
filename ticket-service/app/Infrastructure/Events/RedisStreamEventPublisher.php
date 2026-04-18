<?php

namespace Infrastructure\Events;

use Domain\Ticket\Events\EventPublisherInterface;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Contracts\Redis\Connection;
use Illuminate\Support\Facades\Log;

class RedisStreamEventPublisher implements EventPublisherInterface
{
    private readonly Connection $redis;

    public function __construct(
        RedisFactory $redisFactory
    ) {
        $this->redis = $redisFactory->connection();
    }

    public function publish(string $eventName, array $payload): void
    {
        $this->redis->executeRaw([
            'XADD',
            'ticket.events',
            '*',
            'event', $eventName,
            'request_id', $payload['request_id'] ?? null,
            'traceparent', $payload['traceparent'] ?? null,
            'payload', json_encode($payload, JSON_THROW_ON_ERROR),
        ]);

        Log::info('event_published', [
            'event' => $eventName,
            'request_id' => $payload['request_id'] ?? null,
        ]);
    }
}
