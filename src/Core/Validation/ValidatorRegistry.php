<?php

namespace Dino\Core\Validation;

use Dino\Contracts\Validation\ValidatorInterface;
use Dino\Exceptions\ValidatorNotFoundException;

class ValidatorRegistry
{
    /** @var ValidatorInterface[] */
    private array $validators = [];

    public function register(ValidatorInterface $validator): void
    {
        $this->validators[] = $validator;
    }

    /**
     * Check if any validator supports the given rule
     *
     * @param string $rule
     * @return bool
     */
    public function supports(string $rule): bool
    {
        // استخراج نام rule اصلی (قبل از :)
        $ruleName = $rule;
        if (str_contains($rule, ':')) {
            $ruleName = explode(':', $rule)[0];
        }
        
        foreach ($this->validators as $validator) {
            if ($validator->supports($ruleName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Find a validator that supports the given rule
     *
     * @param string $rule
     * @return ValidatorInterface
     * @throws ValidatorNotFoundException If no validator supports the rule
     */
    public function getValidatorForRule(string $rule): ValidatorInterface
    {
        // استخراج نام rule اصلی (قبل از :)
        $ruleName = $rule;
        if (str_contains($rule, ':')) {
            $ruleName = explode(':', $rule)[0];
        }
        
        foreach ($this->validators as $validator) {
            if ($validator->supports($ruleName)) {
                return $validator;
            }
        }
        
        throw new ValidatorNotFoundException($ruleName);
    }

    /**
     * Validate a value using the appropriate validator
     *
     * @param string $rule
     * @param mixed $value
     * @param array $context
     * @throws \Dino\Exceptions\ConfigValidationException
     */
    public function validate(string $rule, mixed $value, array $context = []): void
    {
        $validator = $this->getValidatorForRule($rule);
        $validator->validate($value, $context);
    }
}