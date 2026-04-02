<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class TicketProxyController extends Controller
{
    private string $ticketServiceUrl;

    public function __construct()
    {
        $this->ticketServiceUrl = rtrim(env('TICKET_SERVICE_URL', 'http://ticket-service:80/api'), '/');
    }

    public function store(Request $request): JsonResponse
    {
        $response = Http::post("{$this->ticketServiceUrl}/tickets", $request->all());
        return response()->json($response->json(), $response->status());
    }

    public function index(): JsonResponse
    {
        $response = Http::get("{$this->ticketServiceUrl}/tickets");
        return response()->json($response->json(), $response->status());
    }

    public function show(string $id): JsonResponse
    {
        $response = Http::get("{$this->ticketServiceUrl}/tickets/{$id}");
        return response()->json($response->json(), $response->status());
    }
}
