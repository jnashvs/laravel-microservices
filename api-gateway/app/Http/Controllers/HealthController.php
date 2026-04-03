<?php

namespace App\Http\Controllers;

use App\Services\TicketServiceProxy;
use App\Services\NotificationServiceProxy;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __construct(
        private readonly TicketServiceProxy $ticketService,
        private readonly NotificationServiceProxy $notificationService
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $services = [
            'ticket-service' => $this->ticketService->health(),
            'notification-service' => $this->notificationService->health(),
        ];

        $allUp = collect($services)->every(fn ($s) => $s['status'] === 'up');

        return response()->json([
            'status' => $allUp ? 'healthy' : 'degraded',
            'service' => 'api-gateway',
            'timestamp' => now()->toISOString(),
            'dependencies' => $services,
        ], $allUp ? 200 : 503);
    }
}
