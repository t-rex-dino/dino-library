<?php

namespace Dino\Contracts;

/**
 * Standardized error message contract
 * Provides consistent error messaging across the library
 */
interface ErrorMessageInterface
{
    /**
     * Get error code identifier
     */
    public function getErrorCode(): string;
    
    /**
     * Get human-readable error message
     */
    public function getErrorMessage(): string;
    
    /**
     * Get contextual data for debugging
     */
    public function getContext(): array;
    
    /**
     * Get error severity level
     */
    public function getSeverity(): string;
    
    /**
     * Get formatted error message with context
     */
    public function getFormattedMessage(): string;
}