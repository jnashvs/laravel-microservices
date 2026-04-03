<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class NotificationProxyController extends Controller
{
    private string $notificationServiceUrl;

    public function __construct()
    {
        $this->notificationServiceUrl = rtrim(env('NOTIFICATION_SERVICE_URL', 'http://notification-service:80/api'), '/');
    }

    public function index(): JsonResponse
    {
        $response = Http::get("{$this->notificationServiceUrl}/notifications");
        return response()->json($response->json(), $response->status());
    }
}
