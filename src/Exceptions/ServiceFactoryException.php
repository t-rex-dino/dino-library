<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class ServiceFactoryException extends ContextAwareException {
    public function __construct(string $factoryClass, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::SERVICE_FACTORY_ERROR,
            sprintf('Service factory "%s" failed to create service.', $factoryClass),
            array_merge(['factory' => $factoryClass], $context),
            ErrorCodes::getSeverity(ErrorCodes::SERVICE_FACTORY_ERROR),
            0,
            $previous
        );
    }
}