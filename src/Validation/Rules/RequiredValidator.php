<?php

namespace Dino\Validation\Rules;

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ValidationException;

class RequiredValidator implements ValidatorInterface
{
    public function validate(mixed $value, array $context = []): void
    {
        if ($value === null || $value === '') {
            $configKey = $context['configKey'] ?? 'unknown';
            throw new ValidationException(
                "Configuration key '{$configKey}' is required and cannot be empty",
                $context
            );
        }
    }

    public function supports(string $rule): bool
    {
        return $rule === 'required';
    }
}