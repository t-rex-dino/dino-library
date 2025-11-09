<?php

declare(strict_types=1);

namespace Dino\Exceptions;

class ServiceException extends DinoException
{
    public function __construct(string $message = "Service error", int $code = 2001)
    {
        parent::__construct($message, $code);
    }
}
