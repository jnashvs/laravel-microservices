<?php

namespace Domain\Ticket\ValueObjects;

class Priority
{
    public const LOW = 'low';
    public const MEDIUM = 'medium';
    public const HIGH = 'high';

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, [self::LOW, self::MEDIUM, self::HIGH])) {
            throw new \InvalidArgumentException("Invalid priority value");
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
