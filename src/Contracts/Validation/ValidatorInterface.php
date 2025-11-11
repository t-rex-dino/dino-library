<?php

namespace Dino\Contracts\Validation;

use Dino\Exceptions\ValidationException;

interface ValidatorInterface
{
    /**
     * Validate a given value against a rule.
     *
     * @param mixed $value   The value to validate
     * @param array $context Optional context (e.g., config key, metadata)
     *
     * @throws ValidationException If validation fails
     */
    public function validate(mixed $value, array $context = []): void;

    /**
     * Check if this validator supports a given rule.
     *
     * @param string $rule The rule identifier (e.g., "required", "type:int")
     * @return bool
     */
    public function supports(string $rule): bool;
}