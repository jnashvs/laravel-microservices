<?php

namespace Infrastructure\Events;

use Domain\Ticket\Events\EventPublisherInterface;
use Illuminate\Contracts\Redis\Factory as RedisFactory;
use Illuminate\Contracts\Redis\Connection;

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
        $correlationId = request()->header('X-Correlation-ID');

        // Use executeRaw to have full control over the command and avoid argument confusion
        // XADD stream_key ID field1 value1 field2 value2 ...
        // Note: We use the connection from the factory which supports executeRaw
        $this->redis->executeRaw([
            'XADD',
            'ticket.events',
            '*',
            'event', $eventName,
            'correlation_id', $correlationId,
            'payload', json_encode($payload, JSON_THROW_ON_ERROR),
        ]);
    }
}
