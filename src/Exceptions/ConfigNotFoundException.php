<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class ConfigNotFoundException extends ContextAwareException {
    public function __construct(string $key, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::CONFIG_KEY_NOT_FOUND,
            sprintf('Configuration key "%s" not found.', $key),
            array_merge(['key' => $key], $context),
            ErrorCodes::getSeverity(ErrorCodes::CONFIG_KEY_NOT_FOUND),
            0,
            $previous
        );
    }
}
