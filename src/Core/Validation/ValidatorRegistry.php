<?php

namespace Dino\Core\Validation;

use Dino\Contracts\Validation\ValidatorInterface;

class ValidatorRegistry
{
    /** @var ValidatorInterface[] */
    private array $validators = [];

    public function register(ValidatorInterface $validator): void
    {
        $this->validators[] = $validator;
    }

    /**
     * Find a validator that supports the given rule.
     *
     * @param string $rule
     * @return ValidatorInterface|null
     */
    public function getValidatorForRule(string $rule): ?ValidatorInterface
    {
        foreach ($this->validators as $validator) {
            if ($validator->supports($rule)) {
                return $validator;
            }
        }
        return null;
    }
}
