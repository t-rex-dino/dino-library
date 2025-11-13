<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class LazyLoadingException extends ContextAwareException {
    public function __construct(string $serviceName, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::LAZY_LOADING_FAILED,
            sprintf('Lazy loading failed for service "%s".', $serviceName),
            array_merge(['service' => $serviceName], $context),
            ErrorCodes::getSeverity(ErrorCodes::LAZY_LOADING_FAILED),
            0,
            $previous
        );
    }
}