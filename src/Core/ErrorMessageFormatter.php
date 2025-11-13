<?php

namespace Dino\Core;

use Dino\Contracts\ErrorMessageInterface;

/**
 * Utility for formatting consistent error messages
 */
class ErrorMessageFormatter
{
    /**
     * Format error message with standardized structure
     */
    public static function format(ErrorMessageInterface $error): string
    {
        return $error->getFormattedMessage() ?? sprintf(
            '[%s] %s',
            $error->getErrorCode(),
            $error->getErrorMessage()
        );
    }

    /**
     * Create developer-friendly error details
     */
    public static function createDetails(ErrorMessageInterface $error): array
    {
        return [
            'code' => $error->getErrorCode(),
            'message' => $error->getErrorMessage(),
            'severity' => $error->getSeverity(),
            'context' => $error->getContext(),
            'category' => ErrorCodes::getCategory($error->getErrorCode()),
            'timestamp' => date('c')
        ];
    }

    /**
     * Format validation errors specifically
     */
    public static function formatValidationErrors(array $errors): string
    {
        $formatted = [];
        foreach ($errors as $field => $messages) {
            $formatted[] = sprintf('%s: %s', $field, implode(', ', $messages));
        }
        
        return sprintf('Validation failed: %s', implode('; ', $formatted));
    }
}