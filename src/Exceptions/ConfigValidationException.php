<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class ConfigValidationException extends ContextAwareException {
    public function __construct(string $key, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::CONFIG_VALIDATION_FAILED,
            sprintf('Configuration validation failed for key "%s".', $key),
            array_merge(['key' => $key], $context),
            ErrorCodes::getSeverity(ErrorCodes::CONFIG_VALIDATION_FAILED),
            0,
            $previous
        );
    }
}
