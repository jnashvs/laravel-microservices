<?php

namespace App\Providers;

use Domain\Ticket\Repositories\TicketRepositoryInterface;
use Domain\Ticket\Events\EventDispatcherInterface;
use Domain\Ticket\Events\TicketCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Events\LaravelEventDispatcher;
use Infrastructure\Listeners\LogTicketCreated;
use Infrastructure\Repositories\EloquentTicketRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TicketRepositoryInterface::class,
            EloquentTicketRepository::class
        );

        $this->app->bind(
            EventDispatcherInterface::class,
            LaravelEventDispatcher::class
        );
    }

    public function boot(): void
    {
        Event::listen(TicketCreated::class, LogTicketCreated::class);
    }
}
