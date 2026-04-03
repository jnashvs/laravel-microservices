<?php

namespace Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Application\Notification\UseCases\ListNotificationsUseCase;

class NotificationController extends Controller
{
    private ListNotificationsUseCase $listUseCase;

    public function __construct(ListNotificationsUseCase $listUseCase)
    {
        $this->listUseCase = $listUseCase;
    }

    public function index(): JsonResponse
    {
        $notifications = $this->listUseCase->execute();

        $data = array_map(function ($notification) {
            return [
                'id' => $notification->getId(),
                'type' => $notification->getType(),
                'message' => $notification->getMessage(),
                'reference_id' => $notification->getReferenceId(),
                'created_at' => $notification->getCreatedAt(),
            ];
        }, $notifications);

        return response()->json($data);
    }
}
