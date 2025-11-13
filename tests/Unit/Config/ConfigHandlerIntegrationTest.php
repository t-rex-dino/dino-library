<?php

namespace Dino\Tests\Unit\Config;

use PHPUnit\Framework\TestCase;
use Dino\Core\ConfigHandler;
use Dino\Exceptions\ConfigValidationException;
use Dino\Validation\Rules\RequiredValidator;
use Dino\Validation\Rules\TypeValidator;
use Dino\Validation\Rules\RangeValidator;
use Dino\Validation\Rules\RegexValidator;

class ConfigHandlerIntegrationTest extends TestCase
{
    public function testValidConfigurationPassesValidation(): void
    {
        $config = new ConfigHandler();
        
        $config->registerValidator(new RequiredValidator());
        $config->registerValidator(new TypeValidator());
        $config->registerValidator(new RangeValidator());
        $config->registerValidator(new RegexValidator());
        
        $config->setValidationRules([
            'app.name' => ['required'],
            'app.port' => ['type:int', 'range:1-65535'],
            'app.email' => ['regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/']
        ]);
        
        // این باید بدون exception اجرا شود
        $config->set('app.name', 'My Application');
        $config->set('app.port', 8080);
        $config->set('app.email', 'test@example.com');
        
        $this->assertEquals('My Application', $config->get('app.name'));
        $this->assertEquals(8080, $config->get('app.port'));
        $this->assertEquals('test@example.com', $config->get('app.email'));
    }

    public function testMissingRequiredValueThrowsException(): void
    {
        $config = new ConfigHandler();
        $config->registerValidator(new RequiredValidator());
        $config->setValidationRules(['app.name' => ['required']]);
        
        $this->expectException(ConfigValidationException::class);
        $config->set('app.name', '');
    }

    public function testInvalidTypeThrowsException(): void
    {
        $config = new ConfigHandler();
        $config->registerValidator(new TypeValidator());
        $config->setValidationRules(['app.port' => ['type:int']]);
        
        $this->expectException(ConfigValidationException::class);
        $config->set('app.port', 'not_an_integer');
    }

    public function testOutOfRangeThrowsException(): void
    {
        $config = new ConfigHandler();
        $config->registerValidator(new RangeValidator());
        $config->setValidationRules(['app.port' => ['range:1-65535']]);
        
        $this->expectException(ConfigValidationException::class);
        $config->set('app.port', 70000);
    }

    public function testRegexValidationFails(): void
    {
        $config = new ConfigHandler();
        $config->registerValidator(new RegexValidator());
        $config->setValidationRules([
            'app.email' => ['regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/']
        ]);
        
        $this->expectException(ConfigValidationException::class);
        $config->set('app.email', 'invalid-email');
    }
}