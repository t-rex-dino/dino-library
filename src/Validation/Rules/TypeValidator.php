<?php

namespace Dino\Validation\Rules;

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ValidationException;
use Dino\Validation\Utils\TypeNormalizer;

class TypeValidator implements ValidatorInterface
{
    public function validate(mixed $value, array $context = []): void
    {
        $rule = $context['rule'] ?? null;
        $expectedType = 'string';

        if ($rule !== null && str_starts_with($rule, 'type:')) {
            $expectedType = substr($rule, strlen('type:'));
        } elseif (isset($context['expectedType'])) {
            $expectedType = $context['expectedType'];
        }

        $actualType = gettype($value);

        // Use TypeNormalizer
        $normalizedExpected = TypeNormalizer::normalize($expectedType);

        if ($actualType !== $normalizedExpected) {
            $configKey = $context['configKey'] ?? 'unknown';
            throw new ValidationException(
                "Configuration key '{$configKey}' must be of type '{$expectedType}', '{$actualType}' given",
                $context
            );
        }
    }

    public function supports(string $rule): bool
    {
        return str_starts_with($rule, 'type:');
    }
}
