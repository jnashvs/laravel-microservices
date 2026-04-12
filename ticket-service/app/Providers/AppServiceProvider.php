<?php

namespace App\Providers;

use Domain\Ticket\Repositories\TicketRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Repositories\EloquentTicketRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TicketRepositoryInterface::class,
            EloquentTicketRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
