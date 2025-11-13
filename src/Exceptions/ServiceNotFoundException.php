<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class ServiceNotFoundException extends ContextAwareException {
    public function __construct(string $serviceName, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::SERVICE_NOT_FOUND,
            sprintf('Service "%s" was not found in the container.', $serviceName),
            array_merge(['service' => $serviceName], $context),
            ErrorCodes::getSeverity(ErrorCodes::SERVICE_NOT_FOUND),
            0,
            $previous
        );
    }
}
