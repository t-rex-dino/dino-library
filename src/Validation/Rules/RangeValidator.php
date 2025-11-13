<?php

namespace Dino\Validation\Rules;

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ConfigValidationException;

class RangeValidator implements ValidatorInterface
{
    public function validate(mixed $value, array $context = []): void
    {
        $configKey = $context['configKey'] ?? 'unknown';

        if (!is_numeric($value)) {
            throw new ConfigValidationException(
                $configKey,
                array_merge($context, [
                    'reason' => 'Value must be numeric for range validation'
                ])
            );
        }

        $min = $context['min'] ?? PHP_INT_MIN;
        $max = $context['max'] ?? PHP_INT_MAX;

        if ($value < $min || $value > $max) {
            throw new ConfigValidationException(
                $configKey,
                array_merge($context, [
                    'reason' => "Value must be between {$min} and {$max}, {$value} given"
                ])
            );
        }
    }

    public function supports(string $rule): bool
    {
        return $rule === 'range';
    }
}
