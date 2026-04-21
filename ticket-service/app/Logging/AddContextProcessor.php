<?php

namespace App\Logging;

class AddContextProcessor
{
    public function __invoke($logger)
    {
        $logger->pushProcessor(function ($record) {
            $requestId = app()->bound('request_id') ? app('request_id') : null;

            $context = $record->context;
            
            // Ensure service and request_id are in the context if not already set
            if (!isset($context['service'])) {
                $context['service'] = config('app.name');
            }
            
            if ($requestId && !isset($context['request_id'])) {
                $context['request_id'] = $requestId;
            }

            return $record->with(context: $context);
        });
    }
}
