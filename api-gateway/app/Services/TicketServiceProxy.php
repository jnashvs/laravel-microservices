<?php

namespace App\Services;

class TicketServiceProxy extends BaseServiceProxy
{
    protected function getBaseUrl(): string
    {
        return config('services.ticket.url');
    }

    protected function getServiceName(): string
    {
        return 'ticket-service';
    }

    public function createTicket(array $data): array
    {
        $response = $this->post('api/tickets', $data);

        return [
            'data' => $response->json(),
            'status' => $response->status(),
        ];
    }

    public function listTickets(): array
    {
        $response = $this->get('api/tickets');

        return [
            'data' => $response->json(),
            'status' => $response->status(),
        ];
    }

    public function getTicket(string $id): array
    {
        $response = $this->get("api/tickets/{$id}");

        return [
            'data' => $response->json(),
            'status' => $response->status(),
        ];
    }

    public function health(): array
    {
        try {
            $response = $this->get('health');

            return [
                'status' => $response->successful() ? 'up' : 'down',
                'response_code' => $response->status(),
            ];
        } catch (\Exception) {
            return ['status' => 'down'];
        }
    }
}
