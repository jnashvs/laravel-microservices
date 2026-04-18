<?php

namespace App\Logging;

class AddContextProcessor
{
    public function __invoke($logger)
    {
        $logger->pushProcessor(function ($record) {
            $requestId = app()->bound('request_id') ? app('request_id') : null;

            // Ensure service and request_id are in the context
            $record['context']['service'] = config('app.name');
            if ($requestId && !isset($record['context']['request_id'])) {
                $record['context']['request_id'] = $requestId;
            }

            return $record;
        });
    }
}
