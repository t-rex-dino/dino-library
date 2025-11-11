<?php

namespace Dino\Validation\Rules;

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ValidationException;

class RangeValidator implements ValidatorInterface
{
    public function validate(mixed $value, array $context = []): void
    {
        if (!is_numeric($value)) {
            $configKey = $context['configKey'] ?? 'unknown';
            throw new ValidationException(
                "Configuration key '{$configKey}' must be numeric for range validation",
                $context
            );
        }

        $min = $context['min'] ?? PHP_INT_MIN;
        $max = $context['max'] ?? PHP_INT_MAX;

        if ($value < $min || $value > $max) {
            $configKey = $context['configKey'] ?? 'unknown';
            throw new ValidationException(
                "Configuration key '{$configKey}' must be between {$min} and {$max}, {$value} given",
                $context
            );
        }
    }

    public function supports(string $rule): bool
    {
        return $rule === 'range';
    }
}