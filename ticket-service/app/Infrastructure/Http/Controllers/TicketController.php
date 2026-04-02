<?php

namespace Infrastructure\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Application\Ticket\UseCases\CreateTicketUseCase;
use Application\Ticket\UseCases\ListTicketsUseCase;
use Application\Ticket\UseCases\GetTicketUseCase;
use Application\Ticket\DTOs\CreateTicketDTO;

class TicketController extends Controller
{
    public function __construct(
        private CreateTicketUseCase $createUseCase,
        private ListTicketsUseCase $listUseCase,
        private GetTicketUseCase $getUseCase
    ) {
        $this->createUseCase = $createUseCase;
        $this->listUseCase = $listUseCase;
        $this->getUseCase = $getUseCase;
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $dto = new CreateTicketDTO($validated);
        $ticket = $this->createUseCase->execute($dto);

        return response()->json([
            'id' => $ticket->getId(),
            'title' => $ticket->getTitle(),
            'description' => $ticket->getDescription(),
            'priority' => $ticket->getPriority()->getValue(),
            'status' => $ticket->getStatus()->getValue(),
        ], 201);
    }

    public function index(): JsonResponse
    {
        $tickets = $this->listUseCase->execute();

        $data = array_map(function ($ticket) {
            return [
                'id' => $ticket->getId(),
                'title' => $ticket->getTitle(),
                'description' => $ticket->getDescription(),
                'priority' => $ticket->getPriority()->getValue(),
                'status' => $ticket->getStatus()->getValue(),
            ];
        }, $tickets);

        return response()->json($data);
    }

    public function show(string $id): JsonResponse
    {
        $ticket = $this->getUseCase->execute($id);

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        return response()->json([
            'id' => $ticket->getId(),
            'title' => $ticket->getTitle(),
            'description' => $ticket->getDescription(),
            'priority' => $ticket->getPriority()->getValue(),
            'status' => $ticket->getStatus()->getValue(),
        ]);
    }
}
