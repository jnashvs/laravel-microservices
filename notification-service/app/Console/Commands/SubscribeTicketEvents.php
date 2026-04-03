<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Application\Notification\UseCases\CreateNotificationUseCase;

class SubscribeTicketEvents extends Command
{
    protected $signature = 'redis:subscribe-tickets';
    protected $description = 'Subscribe to ticket events from Redis';

    private CreateNotificationUseCase $createNotification;

    public function __construct(CreateNotificationUseCase $createNotification)
    {
        parent::__construct();
        $this->createNotification = $createNotification;
    }

    public function handle(): void
    {
        $this->info('Listening for ticket events on Redis...');

        $redis = new \Predis\Client([
            'scheme' => 'tcp',
            'host'   => env('REDIS_HOST', 'redis'),
            'port'   => env('REDIS_PORT', 6379),
        ]);

        $pubsub = $redis->pubSubLoop();
        $pubsub->subscribe('ticket.created');

        foreach ($pubsub as $message) {
            if ($message->kind === 'message') {
                $data = json_decode($message->payload, true);

                $this->info("Received ticket.created: {$data['id']}");

                $this->createNotification->execute(
                    'ticket_created',
                    "Novo ticket criado: {$data['title']} (Prioridade: {$data['priority']})",
                    $data['id']
                );

                $this->info("Notification saved for ticket: {$data['id']}");
            }
        }
    }
}
