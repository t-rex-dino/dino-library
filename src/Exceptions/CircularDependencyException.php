<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class CircularDependencyException extends ContextAwareException {
    public function __construct(string $serviceName, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::SERVICE_CIRCULAR_DEPENDENCY,
            sprintf('Circular dependency detected for service "%s".', $serviceName),
            array_merge(['service' => $serviceName], $context),
            ErrorCodes::getSeverity(ErrorCodes::SERVICE_CIRCULAR_DEPENDENCY),
            0,
            $previous
        );
    }
}
