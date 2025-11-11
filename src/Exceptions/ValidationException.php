<?php

namespace Dino\Exceptions;

use Exception;
use Throwable;

class ValidationException extends Exception
{
    private array $context;

    public function __construct(string $message = "", array $context = [], int $code = 0, ?Throwable $previous = null)
    {
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getConfigKey(): ?string
    {
        return $this->context['configKey'] ?? null;
    }

    public function getRule(): ?string
    {
        return $this->context['rule'] ?? null;
    }
}