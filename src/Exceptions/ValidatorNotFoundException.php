<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class ValidatorNotFoundException extends ContextAwareException {
    public function __construct(string $validator, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::VALIDATOR_NOT_FOUND,
            sprintf('Validator "%s" not found.', $validator),
            array_merge(['validator' => $validator], $context),
            ErrorCodes::getSeverity(ErrorCodes::VALIDATOR_NOT_FOUND),
            0,
            $previous
        );
    }
}