<?php

namespace Dino\Core;

/**
 * Standardized error codes for consistent error handling
 */
class ErrorCodes
{
    // Service Container Errors (100-199)
    public const SERVICE_NOT_FOUND = 'SERVICE_100';
    public const SERVICE_CIRCULAR_DEPENDENCY = 'SERVICE_101';
    public const SERVICE_RESOLUTION_FAILED = 'SERVICE_102';
    public const SERVICE_FACTORY_ERROR = 'SERVICE_103';
    public const SERVICE_ALREADY_EXISTS = 'SERVICE_104';
    
    // Configuration Errors (200-299)
    public const CONFIG_VALIDATION_FAILED = 'CONFIG_200';
    public const CONFIG_KEY_NOT_FOUND = 'CONFIG_201';
    public const CONFIG_PARSER_ERROR = 'CONFIG_202';
    public const CONFIG_MERGE_ERROR = 'CONFIG_203';
    
    // Dependency Injection Errors (300-399)
    public const DI_UNRESOLVABLE_PARAMETER = 'DI_300';
    public const DI_MISSING_TYPE_HINT = 'DI_301';
    public const DI_INTERFACE_NOT_BOUND = 'DI_302';
    public const DI_CONSTRUCTOR_ERROR = 'DI_303';
    
    // Validation Errors (400-499)
    public const VALIDATION_FAILED = 'VALIDATION_400';
    public const VALIDATOR_NOT_FOUND = 'VALIDATION_401';
    public const VALIDATION_RULE_ERROR = 'VALIDATION_402';
    
    // Lazy Loading Errors (500-599)
    public const LAZY_LOADING_FAILED = 'LAZY_500';
    public const SERVICE_WRAPPER_ERROR = 'LAZY_501';
    
    public static function getCategory(string $errorCode): string
    {
        return explode('_', $errorCode)[0];
    }
    
    public static function getSeverity(string $errorCode): string
    {
        $severityMap = [
            self::SERVICE_CIRCULAR_DEPENDENCY => 'CRITICAL',
            self::DI_UNRESOLVABLE_PARAMETER => 'HIGH',
            self::CONFIG_VALIDATION_FAILED => 'HIGH',
            self::LAZY_LOADING_FAILED => 'HIGH',
            self::SERVICE_RESOLUTION_FAILED => 'MEDIUM',
            self::CONFIG_KEY_NOT_FOUND => 'MEDIUM',
            self::VALIDATION_FAILED => 'MEDIUM',
        ];
        
        return $severityMap[$errorCode] ?? 'ERROR';
    }
}