<?php

namespace Dino\Validation\Rules;

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ConfigValidationException;

class RequiredValidator implements ValidatorInterface
{
    public function validate(mixed $value, array $context = []): void
    {
        $configKey = $context['configKey'] ?? 'unknown';

        if ($value === null || $value === '') {
            throw new ConfigValidationException(
                $configKey,
                array_merge($context, [
                    'reason' => 'Value is required and cannot be empty'
                ])
            );
        }
    }

    public function supports(string $rule): bool
    {
        return $rule === 'required';
    }
}
