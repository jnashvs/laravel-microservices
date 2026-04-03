<?php

namespace Application\Notification\UseCases;

use Domain\Notification\Repositories\NotificationRepositoryInterface;

class ListNotificationsUseCase
{
    private NotificationRepositoryInterface $repository;

    public function __construct(NotificationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): array
    {
        return $this->repository->findAll();
    }
}
