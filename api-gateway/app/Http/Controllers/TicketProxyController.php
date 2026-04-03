<?php

namespace App\Http\Controllers;

use App\Exceptions\ServiceUnavailableException;
use App\Services\TicketServiceProxy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketProxyController extends Controller
{
    public function __construct(
        private readonly TicketServiceProxy $ticketService
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $result = $this->ticketService->createTicket($request->all());

            return response()->json($result['data'], $result['status']);
        } catch (ServiceUnavailableException $e) {
            return $this->serviceUnavailable($e);
        }
    }

    public function index(): JsonResponse
    {
        try {
            $result = $this->ticketService->listTickets();

            return response()->json($result['data'], $result['status']);
        } catch (ServiceUnavailableException $e) {
            return $this->serviceUnavailable($e);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $result = $this->ticketService->getTicket($id);

            return response()->json($result['data'], $result['status']);
        } catch (ServiceUnavailableException $e) {
            return $this->serviceUnavailable($e);
        }
    }

    private function serviceUnavailable(ServiceUnavailableException $e): JsonResponse
    {
        return response()->json([
            'error' => 'Service Unavailable',
            'message' => $e->getMessage(),
        ], 503);
    }
}
