<?php

namespace App\Providers;

use Domain\Ticket\Events\EventDispatcherInterface;
use Domain\Ticket\Events\EventPublisherInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Events\RedisStreamEventPublisher;
use Infrastructure\Events\SimpleEventDispatcher;
use Infrastructure\Listeners\PublishTicketCreatedListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Define the event listeners.
     */
    private const LISTENERS = [
        PublishTicketCreatedListener::class,
    ];

    public function register(): void
    {
        $this->app->singleton(EventPublisherInterface::class, RedisStreamEventPublisher::class);

        $this->app->singleton(EventDispatcherInterface::class, function ($app) {
            return new SimpleEventDispatcher(
                $app,
                self::LISTENERS
            );
        });
    }
}
