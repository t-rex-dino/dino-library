<?php

namespace Dino\Validation\Rules;

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ConfigValidationException;

class RegexValidator implements ValidatorInterface
{
    public function validate(mixed $value, array $context = []): void
    {
        $pattern = $context['pattern'] ?? null;
        $configKey = $context['configKey'] ?? 'unknown';

        if ($pattern === null) {
            throw new ConfigValidationException(
                $configKey,
                array_merge($context, [
                    'reason' => 'Regex pattern is required for regex validation'
                ])
            );
        }

        if (!is_string($value)) {
            $value = (string) $value;
        }

        if (!preg_match($pattern, $value)) {
            throw new ConfigValidationException(
                $configKey,
                array_merge($context, [
                    'reason' => "Value does not match the required regex pattern"
                ])
            );
        }
    }

    public function supports(string $rule): bool
    {
        return $rule === 'regex';
    }
}
