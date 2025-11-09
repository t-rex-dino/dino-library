<?php

declare(strict_types=1);

namespace Dino\Exceptions;

class ConfigurationException extends DinoException
{
    public function __construct(string $message = "Configuration error", int $code = 1001)
    {
        parent::__construct($message, $code);
    }
}
