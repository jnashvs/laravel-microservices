<?php

namespace App\Http\Controllers;

use App\Exceptions\ServiceUnavailableException;
use App\Services\NotificationServiceProxy;
use Illuminate\Http\JsonResponse;

class NotificationProxyController extends Controller
{
    public function __construct(
        private readonly NotificationServiceProxy $notificationService
    ) {
    }

    public function index(): JsonResponse
    {
        try {
            $result = $this->notificationService->listNotifications();

            return response()->json($result['data'], $result['status']);
        } catch (ServiceUnavailableException $e) {
            return response()->json([
                'error' => 'Service Unavailable',
                'message' => $e->getMessage(),
            ], 503);
        }
    }
}
