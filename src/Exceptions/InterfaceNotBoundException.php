<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class InterfaceNotBoundException extends ContextAwareException {
    public function __construct(string $interface, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::DI_INTERFACE_NOT_BOUND,
            sprintf('Interface "%s" is not bound to any implementation.', $interface),
            array_merge(['interface' => $interface], $context),
            ErrorCodes::getSeverity(ErrorCodes::DI_INTERFACE_NOT_BOUND),
            0,
            $previous
        );
    }
}
