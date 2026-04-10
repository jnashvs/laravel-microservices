<?php

namespace Infrastructure\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Application\Ticket\UseCases\CreateTicketUseCase;
use Application\Ticket\UseCases\ListTicketsUseCase;
use Application\Ticket\UseCases\GetTicketUseCase;
use Application\Ticket\DTOs\CreateTicketData;
use Application\Ticket\Exceptions\TicketCreationException;
use Application\Ticket\Exceptions\TicketNotFoundException;

class TicketController extends Controller
{
    public function __construct(
        private readonly CreateTicketUseCase $createUseCase,
        private readonly ListTicketsUseCase $listUseCase,
        private readonly GetTicketUseCase $getUseCase
    ) {
    }

    public function store(CreateTicketData $data): JsonResponse
    {
        try {
            $ticket = $this->createUseCase->execute($data);

            return response()->json(
                $ticket->toArray(),
                201
            );

        } catch (TicketCreationException $e) {
            return response()->json([
                'error' => 'Failed to create ticket',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function index(): JsonResponse
    {
        $tickets = $this->listUseCase->execute();

        return response()->json(
            array_map(fn ($ticket) => $ticket->toArray(), $tickets)
        );
    }

    public function show(string $id): JsonResponse
    {
        try {
            $ticket = $this->getUseCase->execute($id);

            return response()->json(
                $ticket->toArray()
            );

        } catch (TicketNotFoundException $e) {
            return response()->json([
                'error' => 'Ticket not found',
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
