<?php

namespace Dino\Validation\Rules;

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ConfigValidationException;

class TypeValidator implements ValidatorInterface
{
    public function supports(string $rule): bool
    {
        return str_starts_with($rule, 'type');
    }

    public function validate(mixed $value, array $context = []): void
    {
        $expectedType = $context['expectedType'] ?? null;
        
        if (!$expectedType) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => 'Expected type not specified in validation context',
                    'rule' => 'type'
                ])
            );
        }

        $actualType = gettype($value);
        
        // تبدیل نوع PHP به نام‌های خوانا
        $typeMap = [
            'boolean' => 'bool',
            'integer' => 'int',
            'double' => 'float',
            'string' => 'string',
            'array' => 'array',
            'object' => 'object',
            'NULL' => 'null'
        ];
        
        $actualType = $typeMap[$actualType] ?? $actualType;
        
        if ($actualType !== $expectedType) {
            throw new ConfigValidationException(
                $context['configKey'] ?? 'unknown',
                array_merge($context, [
                    'reason' => sprintf("Expected type '%s', got '%s'", $expectedType, $actualType),
                    'expectedType' => $expectedType,
                    'actualType' => $actualType,
                    'value' => $value,
                    'rule' => 'type'
                ])
            );
        }
    }
}