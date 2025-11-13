<?php

namespace Dino\Exceptions;

use Dino\Contracts\ErrorMessageInterface;
use Throwable;

/**
 * Base exception with context-aware error messaging
 */
class ContextAwareException extends \Exception implements ErrorMessageInterface
{
    private array $context;
    private string $errorCode;
    private string $severity;
    private string $errorMessage;

    public function __construct(
        string $errorCode,
        string $message,
        array $context = [],
        string $severity = 'ERROR',
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->errorCode = $errorCode;
        $this->context = $context;
        $this->severity = $severity;
        $this->errorMessage = $message;
        
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * Create formatted error message with context
     */
    public function getFormattedMessage(): string
    {
        $contextStr = !empty($this->context) 
            ? ' [Context: ' . json_encode($this->context) . ']'
            : '';

        return sprintf(
            '[%s] %s%s',
            $this->errorCode,
            $this->errorMessage,
            $contextStr
        );
    }
}