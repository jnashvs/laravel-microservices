<?php

namespace App\Providers;

use Domain\Notification\Repositories\NotificationRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Infrastructure\Repositories\FileNotificationRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            NotificationRepositoryInterface::class,
            FileNotificationRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
