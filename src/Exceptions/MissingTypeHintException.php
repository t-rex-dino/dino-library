<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class MissingTypeHintException extends ContextAwareException {
    public function __construct(string $parameter, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::DI_MISSING_TYPE_HINT,
            sprintf('Missing type hint for parameter "%s".', $parameter),
            array_merge(['parameter' => $parameter], $context),
            ErrorCodes::getSeverity(ErrorCodes::DI_MISSING_TYPE_HINT),
            0,
            $previous
        );
    }
}
