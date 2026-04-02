<?php

return [
    'ticket' => [
        'url' => env('TICKET_SERVICE_URL', 'http://ticket-service:80/api'),
    ],
    'notification' => [
        'url' => env('NOTIFICATION_SERVICE_URL', 'http://notification-service:80/api'),
    ],
];
