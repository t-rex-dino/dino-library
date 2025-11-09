<?php

declare(strict_types=1);

namespace Dino\Exceptions;

class ContainerException extends DinoException
{
    public function __construct(string $message = "Container error", int $code = 3001)
    {
        parent::__construct($message, $code);
    }
}
