<?php

namespace Dino\Exceptions;

use Dino\Core\ErrorCodes;

/**
 * Exception thrown when a requested service is not found in the container
 */
class ConfigParserException extends ContextAwareException {
    public function __construct(string $file, array $context = [], \Throwable $previous = null) {
        parent::__construct(
            ErrorCodes::CONFIG_PARSER_ERROR,
            sprintf('Failed to parse configuration file "%s".', $file),
            array_merge(['file' => $file], $context),
            ErrorCodes::getSeverity(ErrorCodes::CONFIG_PARSER_ERROR),
            0,
            $previous
        );
    }
}