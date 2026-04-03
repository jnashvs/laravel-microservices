<?php

namespace Domain\Notification\Repositories;

use Domain\Notification\Entities\Notification;

interface NotificationRepositoryInterface
{
    public function save(Notification $notification): void;
    public function findAll(): array;
}
