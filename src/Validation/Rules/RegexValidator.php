<?php

namespace Dino\Validation\Rules;

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ValidationException;

class RegexValidator implements ValidatorInterface
{
    public function validate(mixed $value, array $context = []): void
    {
        $pattern = $context['pattern'] ?? null;

        if ($pattern === null) {
            throw new ValidationException(
                "Regex pattern is required for regex validation",
                $context
            );
        }

        if (!is_string($value)) {
            $value = (string) $value;
        }

        if (!preg_match($pattern, $value)) {
            $configKey = $context['configKey'] ?? 'unknown';
            throw new ValidationException(
                "Configuration key '{$configKey}' does not match the required pattern",
                $context
            );
        }
    }

    public function supports(string $rule): bool
    {
        return $rule === 'regex';
    }
}