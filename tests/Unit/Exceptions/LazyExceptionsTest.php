<?php

namespace Dino\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Dino\Exceptions\LazyLoadingException;
use Dino\Exceptions\ServiceWrapperException;
use Dino\Core\ErrorCodes;

class LazyExceptionsTest extends TestCase
{
    public function testLazyLoadingException()
    {
        $exception = new LazyLoadingException('heavyService', ['memory_usage' => '95%']);
        $this->assertEquals(ErrorCodes::LAZY_LOADING_FAILED, $exception->getErrorCode());
        $this->assertStringContainsString('heavyService', $exception->getErrorMessage());
    }

    public function testServiceWrapperException()
    {
        $exception = new ServiceWrapperException('heavyService');
        $this->assertEquals(ErrorCodes::SERVICE_WRAPPER_ERROR, $exception->getErrorCode());
        $this->assertStringContainsString('heavyService', $exception->getErrorMessage());
    }
}
