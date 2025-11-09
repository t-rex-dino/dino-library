<?php

declare(strict_types=1);

namespace Dino\Exceptions;

class DinoException extends \Exception
{
    protected int $errorCode;

    public function __construct(string $message = "Dino Library Exception", int $code = 1000)
    {
        parent::__construct($message, $code);
        $this->errorCode = $code;
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }
}
