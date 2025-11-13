<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class ServiceResolutionException extends ContextAwareException {
    public function __construct(string $serviceName, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::SERVICE_RESOLUTION_FAILED,
            sprintf('Failed to resolve service "%s".', $serviceName),
            array_merge(['service' => $serviceName], $context),
            ErrorCodes::getSeverity(ErrorCodes::SERVICE_RESOLUTION_FAILED),
            0,
            $previous
        );
    }
}
