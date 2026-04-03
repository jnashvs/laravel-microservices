<?php

namespace App\Exceptions;

use RuntimeException;

class ServiceUnavailableException extends RuntimeException
{
    private string $serviceName;

    public static function forService(string $serviceName): self
    {
        $exception = new self("Service '{$serviceName}' is currently unavailable.");
        $exception->serviceName = $serviceName;

        return $exception;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }
}
