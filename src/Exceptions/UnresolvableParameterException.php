<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class UnresolvableParameterException extends ContextAwareException {
    public function __construct(string $parameter, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::DI_UNRESOLVABLE_PARAMETER,
            sprintf('Unable to resolve parameter "%s".', $parameter),
            array_merge(['parameter' => $parameter], $context),
            ErrorCodes::getSeverity(ErrorCodes::DI_UNRESOLVABLE_PARAMETER),
            0,
            $previous
        );
    }
}
