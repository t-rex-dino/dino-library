<?php

namespace Dino\Tests\Unit\Config;

use Dino\Core\ConfigHandler;
use Dino\Validation\Rules\RequiredValidator;
use Dino\Validation\Rules\TypeValidator;
use Dino\Validation\Rules\RangeValidator;
use Dino\Validation\Rules\RegexValidator;
use Dino\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

class ConfigHandlerIntegrationTest extends TestCase
{
    private ConfigHandler $config;

    protected function setUp(): void
    {
        $this->config = new ConfigHandler();

        // register ruls
        $this->config->setValidationRules([
            'app.name'  => ['required', 'type:string'],
            'app.port'  => ['required', 'type:int', 'range'],
            'app.email' => ['required', 'regex'],
        ]);

        // register validators in registry internal
        $this->config->registerValidator(new RequiredValidator());
        $this->config->registerValidator(new TypeValidator());
        $this->config->registerValidator(new RangeValidator());
        $this->config->registerValidator(new RegexValidator());
    }

    public function testValidConfigurationPassesValidation(): void
    {
        $this->expectNotToPerformAssertions();

        $this->config->set('app.name', 'Dino');
        $this->config->set('app.port', 8080, ['min' => 1, 'max' => 65535]);
        $this->config->set('app.email', 'user@example.com', ['pattern' => '/^[^@]+@[^@]+\.[^@]+$/']);
    }

    public function testMissingRequiredValueThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("is required");

        $this->config->set('app.name', null);
    }

    public function testInvalidTypeThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("must be of type 'int'");

        $this->config->set('app.port', 'not-an-int', ['expectedType' => 'int']);
    }

    public function testOutOfRangeThrowsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("must be between");

        $this->config->set('app.port', 70000, ['min' => 1, 'max' => 65535]);
    }

    public function testRegexValidationFails(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("does not match the required pattern");

        $this->config->set('app.email', 'invalid-email', ['pattern' => '/^[^@]+@[^@]+\.[^@]+$/']);
    }
}
